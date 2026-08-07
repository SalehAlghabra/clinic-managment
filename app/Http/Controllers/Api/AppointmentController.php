<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorDetail;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Setting;
use App\Services\FirebaseService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    protected FirebaseService $firebase;
    protected WalletService $wallet;

    public function __construct(FirebaseService $firebase, WalletService $wallet)
    {
        $this->firebase = $firebase;
        $this->wallet   = $wallet;
    }

    // معاينة الحجز قبل التأكيد
    public function preview(Request $request)
    {
        $request->validate([
            'doctor_id'        => 'required|exists:doctor_details,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $doctor = DoctorDetail::with('user')->find($request->doctor_id);
        $consultationFee = (float) $doctor->consultation_fee;
        $walletBalance   = (float) $request->user()->wallet_balance;
        $afterBalance    = $walletBalance - $consultationFee;
        $hasSufficient   = $walletBalance >= $consultationFee;

        return response()->json([
            'booking_summary' => [
                'doctor_name'      => $doctor->user->name,
                'specialization'   => $doctor->specialization,
                'consultation_fee' => $consultationFee,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
            ],
            'payment_summary' => [
                'consultation_fee' => $consultationFee,
                'wallet_balance'   => $walletBalance,
                'balance_after'    => $hasSufficient ? $afterBalance : null,
                'has_sufficient'   => $hasSufficient,
            ],
            'message' => $hasSufficient
                ? "Consultation fee {$consultationFee} will be deducted from your wallet upon confirmation"
                : "Insufficient balance. Please charge your wallet with at least {$consultationFee}",
        ]);
    }

    // حجز موعد (المريض فقط)
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'        => 'required|exists:doctor_details,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes'            => 'nullable|string',
        ]);

        $doctor = DoctorDetail::with('user')->find($request->doctor_id);

        $dayOfWeek = strtolower(date('l', strtotime($request->appointment_date)));
        $schedule  = DoctorSchedule::where('doctor_id', $request->doctor_id)
                                   ->where('day_of_week', $dayOfWeek)
                                   ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Doctor is not available on this day'], 422);
        }

        $startTimeStr = substr($schedule->start_time, 0, 5);
        $endTimeStr   = substr($schedule->end_time, 0, 5);

        if ($request->appointment_time < $startTimeStr ||
            $request->appointment_time >= $endTimeStr) {
            return response()->json(['message' => 'Appointment time is outside doctor working hours'], 422);
        }

        $startTime       = Carbon::parse($schedule->start_time);
        $appointmentTime = Carbon::parse($request->appointment_time);
        $minutes         = $startTime->diffInMinutes($appointmentTime);

        if ($minutes % $schedule->duration_per_patient != 0) {
            return response()->json([
                'message' => 'Appointment time must be every ' . $schedule->duration_per_patient . ' minutes.'
            ], 422);
        }

        $exists = Appointment::where('doctor_id', $request->doctor_id)
                             ->where('appointment_date', $request->appointment_date)
                             ->where('appointment_time', $request->appointment_time)
                             ->whereIn('status', ['pending', 'confirmed'])
                             ->exists();

        if ($exists) {
            return response()->json(['message' => 'This time slot is already booked'], 422);
        }

        $patient = $request->user();
        if ($request->filled('patient_id') && in_array($request->user()->role, ['admin', 'receptionist'])) {
            $targetPatient = \App\Models\User::where('role', 'patient')->find($request->patient_id);
            if ($targetPatient) {
                $patient = $targetPatient;
            }
        }

        // التحقق من رصيد المحفظة
        $consultationFee = (float) $doctor->consultation_fee;
        if ($patient->wallet_balance < $consultationFee) {
            return response()->json([
                'message'        => 'Insufficient patient wallet balance',
                'required'       => $consultationFee,
                'current_balance'=> $patient->wallet_balance,
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'consultation_fee' => $consultationFee,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);

        // خصم رسوم الكشفية من المحفظة
        $this->wallet->deductBookingDeposit($patient, $appointment->id, $consultationFee);

        // إشعار للمريض
        app(\App\Services\NotificationService::class)->notify(
            $patient,
            'appointment_booked',
            'تم حجز الموعد بنجاح 📅',
            'Appointment Booked Successfully 📅',
            "تم حجز موعدك بتاريخ {$request->appointment_date} الساعة {$request->appointment_time}",
            "Your appointment was booked for {$request->appointment_date} at {$request->appointment_time}",
            'appointment',
            $appointment->id,
            ['appointment_id' => (string)$appointment->id]
        );

        // إشعار للدكتور
        $doctorUser = $doctor->user;
        if ($doctorUser) {
            app(\App\Services\NotificationService::class)->notify(
                $doctorUser,
                'appointment_booked',
                'طلب موعد جديد 📅',
                'New Appointment Request 📅',
                "قام المريض {$patient->name} بحجز موعد بتاريخ {$request->appointment_date} الساعة {$request->appointment_time}",
                "Patient {$patient->name} booked on {$request->appointment_date} at {$request->appointment_time}",
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        }

        return response()->json([
            'message'          => 'Appointment booked successfully',
            'appointment'      => $appointment,
            'consultation_fee' => $consultationFee,
            'wallet_balance'   => $patient->fresh()->wallet_balance,
        ], 201);
    }

    // الأوقات المتاحة
    public function availableSlots(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $dayOfWeek = strtolower(Carbon::parse($request->date)->format('l'));

        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Doctor is not available on this day'], 404);
        }

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn($time) => Carbon::parse($time)->format('H:i'))
            ->toArray();

        $availableSlots = [];
        $current        = Carbon::parse($schedule->start_time);
        $end            = Carbon::parse($schedule->end_time);

        while ($current->lt($end)) {
            $time = $current->format('H:i');
            if (!in_array($time, $bookedSlots)) {
                $availableSlots[] = $time;
            }
            $current->addMinutes($schedule->duration_per_patient);
        }

        return response()->json([
            'doctor_id'       => $doctorId,
            'date'            => $request->date,
            'available_slots' => $availableSlots,
        ]);
    }

    // عرض مواعيد المريض
    public function patientAppointments(Request $request)
    {
        $appointments = Appointment::with(['doctor.user'])
            ->where('patient_id', $request->user()->id)
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'doctor_name'      => $appointment->doctor->user->name,
                    'specialization'   => $appointment->doctor->specialization,
                    'consultation_fee' => (float) $appointment->consultation_fee,
                    'additional_cost'  => (float) $appointment->additional_cost,
                    'additional_note'  => $appointment->additional_note,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'status'           => $appointment->status,
                    'notes'            => $appointment->notes,
                ];
            });

        return response()->json($appointments);
    }

    // عرض مواعيد الدكتور
    public function doctorAppointments(Request $request)
    {
        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $appointments = Appointment::with(['patient'])
            ->where('doctor_id', $doctorDetail->id)
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'patient_name'     => $appointment->patient->name,
                    'patient_phone'    => $appointment->patient->phone,
                    'consultation_fee' => (float) $appointment->consultation_fee,
                    'additional_cost'  => (float) $appointment->additional_cost,
                    'additional_note'  => $appointment->additional_note,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'status'           => $appointment->status,
                    'notes'            => $appointment->notes,
                ];
            });

        return response()->json($appointments);
    }

    // تغيير حالة الموعد (الدكتور)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'          => 'required|in:confirmed,rejected,completed',
            'additional_cost' => 'nullable|numeric|min:0',
            'additional_note' => 'nullable|string',
        ]);

        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $appointment = Appointment::where('id', $id)
                                  ->where('doctor_id', $doctorDetail->id)
                                  ->first();

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        // إذا رفض الدكتور الموعد يرجع الكشفية كاملة
        if ($request->status === 'rejected') {
            $this->wallet->refundFull(
                $appointment->patient,
                $appointment->id,
                (float) $appointment->consultation_fee,
                'Refund: appointment rejected by doctor'
            );

            // إشعار للمريض
            app(\App\Services\NotificationService::class)->notify(
                $appointment->patient,
                'appointment_rejected',
                'تم رفض الموعد ❌',
                'Appointment Rejected ❌',
                'تم رفض طلب الموعد وإرجاع رسوم الكشفية إلى محفظتك.',
                'Your appointment was rejected. Your consultation fee has been refunded.',
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        } elseif ($request->status === 'completed') {
            $additionalCost = (float) ($request->additional_cost ?? 0);
            $additionalNote = $request->additional_note;

            $appointment->update([
                'status'          => 'completed',
                'additional_cost' => $additionalCost,
                'additional_note' => $additionalNote,
            ]);

            // الإنشاء التلقائي للفاتورة عند اكتمال الموعد
            $consultationFee = (float) $appointment->consultation_fee;
            $totalAmount     = $consultationFee + $additionalCost;
            $depositAmount   = $consultationFee; // مدفوع مسبقاً
            $remainingAmount = $additionalCost;
            $paymentStatus   = ($remainingAmount == 0) ? 'paid' : 'unpaid';
            $paymentMethod   = ($remainingAmount == 0) ? 'wallet' : null;

            Invoice::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'total_amount'     => $totalAmount,
                    'deposit_amount'   => $depositAmount,
                    'remaining_amount' => $remainingAmount,
                    'payment_status'   => $paymentStatus,
                    'payment_method'   => $paymentMethod,
                    'issued_at'        => now(),
                ]
            );

            // إشعار للمريض
            app(\App\Services\NotificationService::class)->notify(
                $appointment->patient,
                'appointment_completed',
                'تم إكمال الزيارة 🎉',
                'Appointment Completed 🎉',
                "تم إتمام زيارتك الطبية. المبلغ المتبقي: \${$remainingAmount}",
                "Your visit has been completed. Remaining balance: \${$remainingAmount}",
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        } else {
            // confirmed
            app(\App\Services\NotificationService::class)->notify(
                $appointment->patient,
                'appointment_confirmed',
                'تم تأكيد الموعد ✅',
                'Appointment Confirmed ✅',
                "تم تأكيد موعدك الطبي بتاريخ {$appointment->appointment_date}",
                "Your appointment on {$appointment->appointment_date} has been confirmed",
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        }

        if ($request->status !== 'completed') {
            $appointment->update(['status' => $request->status]);
        }

        return response()->json([
            'message'     => 'Appointment status updated successfully',
            'appointment' => $appointment->fresh(),
        ]);
    }

    // إلغاء موعد (المريض)
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);

        $user = $request->user();
        if (in_array($user->role, ['admin', 'receptionist'])) {
            $appointment = Appointment::with('patient')->find($id);
        } else {
            $appointment = Appointment::with('patient')
                                      ->where('id', $id)
                                      ->where('patient_id', $user->id)
                                      ->first();
        }

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Cannot cancel this appointment'], 422);
        }

        if ($user->role === 'receptionist' && $appointment->status === 'confirmed') {
            return response()->json(['message' => 'Receptionists cannot cancel confirmed appointments'], 403);
        }

        $patient = $appointment->patient;
        if (!$patient) {
            return response()->json(['message' => 'Patient account not found'], 404);
        }

        // التحقق من وقت الإلغاء
        $cancellationHours   = (int) Setting::get('cancellation_hours', 24);
        $dateStr             = is_a($appointment->appointment_date, \Carbon\Carbon::class)
            ? $appointment->appointment_date->format('Y-m-d')
            : substr((string)$appointment->appointment_date, 0, 10);
        $appointmentDateTime = Carbon::parse($dateStr . ' ' . $appointment->appointment_time);
        $hoursUntilAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);
        $consultationFee     = (float) $appointment->consultation_fee;

        if ($hoursUntilAppointment > $cancellationHours || in_array($user->role, ['admin', 'receptionist'])) {
            // إلغاء قبل الوقت المحدد أو بواسطة الموظف → استرداد كامل
            $this->wallet->refundFull(
                $patient,
                $appointment->id,
                $consultationFee,
                'Full refund: cancelled appointment #' . $appointment->id
            );
            $refundMessage = 'Full refund processed ✅';
        } else {
            // إلغاء بعد الوقت المحدد → استرداد مع غرامة
            $this->wallet->refundWithPenalty(
                $patient,
                $appointment->id,
                $consultationFee
            );
            $violationCount = $patient->fresh()->violation_count;
            $penaltyRate    = min($violationCount * 5, (float) Setting::get('max_penalty_percentage', 25));
            $refundMessage  = "Partial refund with {$penaltyRate}% penalty ⚠️";
        }

        $appointment->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by'        => $user->role,
            'cancelled_at'        => now(),
        ]);

        // إشعار للمريض عند الإلغاء
        app(\App\Services\NotificationService::class)->notify(
            $patient,
            'appointment_cancelled',
            'تم إلغاء الموعد ❌',
            'Appointment Cancelled ❌',
            "تم إلغاء موعدك بتاريخ {$appointment->appointment_date}",
            "Your appointment on {$appointment->appointment_date} has been cancelled",
            'appointment',
            $appointment->id,
            ['appointment_id' => (string)$appointment->id]
        );

        // إشعار للدكتور (فقط إذا لم يكن الدكتور هو من قام بالإلغاء بنفسه)
        $doctorUser = $appointment->doctor->user;
        if ($doctorUser && $user->id !== $doctorUser->id) {
            app(\App\Services\NotificationService::class)->notify(
                $doctorUser,
                'appointment_cancelled',
                'تم إلغاء الموعد ❌',
                'Appointment Cancelled ❌',
                "تم إلغاء الموعد رقم #{$appointment->id} بتاريخ {$appointment->appointment_date}",
                "Appointment #{$appointment->id} cancelled on {$appointment->appointment_date}",
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        }

        return response()->json([
            'message'        => 'Appointment cancelled successfully',
            'refund_status'  => $refundMessage,
            'wallet_balance' => $patient->fresh()->wallet_balance,
        ]);
    }

    // إعادة جدولة الموعد
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $user = $request->user();
        $appointment = Appointment::with(['patient', 'doctor.user'])->find($id);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        if ($appointment->status === 'completed' || $appointment->status === 'cancelled') {
            return response()->json(['message' => 'Cannot reschedule completed or cancelled appointments'], 422);
        }

        if ($user->role === 'receptionist' && $appointment->status === 'confirmed') {
            return response()->json(['message' => 'Receptionists cannot reschedule confirmed appointments'], 403);
        }

        // Slot availability
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
                             ->where('appointment_date', $request->appointment_date)
                             ->where('appointment_time', $request->appointment_time)
                             ->where('id', '!=', $appointment->id)
                             ->whereIn('status', ['pending', 'confirmed'])
                             ->exists();

        if ($exists) {
            return response()->json(['message' => 'This time slot is already booked'], 422);
        }

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        if ($appointment->patient) {
            app(\App\Services\NotificationService::class)->notify(
                $appointment->patient,
                'appointment_rescheduled',
                'تم إعادة جدولة الموعد 📅',
                'Appointment Rescheduled 📅',
                "تمت إعادة جدولة موعدك إلى {$request->appointment_date} الساعة {$request->appointment_time}",
                "Your appointment has been rescheduled to {$request->appointment_date} at {$request->appointment_time}",
                'appointment',
                $appointment->id,
                ['appointment_id' => (string)$appointment->id]
            );
        }

        return response()->json([
            'message'     => 'Appointment rescheduled successfully',
            'appointment' => $appointment->fresh(),
        ]);
    }

    // إلغاء مواعيد يوم محدد (الدكتور)
    public function cancelDayAppointments(Request $request)
    {
        $request->validate([
            'date'                => 'required|date|after_or_equal:today',
            'cancellation_reason' => 'nullable|string',
        ]);

        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $appointments = Appointment::where('doctor_id', $doctorDetail->id)
            ->where('appointment_date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('patient')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(['message' => 'No appointments found for this day'], 404);
        }

        $refundedCount = 0;

        foreach ($appointments as $appointment) {
            // استرداد كامل لكل المرضى
            $this->wallet->refundFull(
                $appointment->patient,
                $appointment->id,
                (float) $appointment->consultation_fee,
                'Full refund: doctor cancelled appointments for the day'
            );

            $appointment->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason ?? 'Doctor cancelled appointments for this day',
                'cancelled_by'        => 'doctor',
                'cancelled_at'        => now(),
            ]);

            // إشعار لكل مريض متأثر فقط (لا يتم إرسال إشعار للدكتور المنفّذ للإلغاء)
            if ($appointment->patient) {
                app(\App\Services\NotificationService::class)->notify(
                    $appointment->patient,
                    'doctor_cancelled',
                    'تم إلغاء الموعد من قبل الطبيب 📅',
                    'Appointment Cancelled 📅',
                    "تم إلغاء موعدك بتاريخ {$request->date} من قبل الطبيب. تم إرجاع المبلغ كاملاً إلى محفظتك.",
                    "Your appointment on {$request->date} has been cancelled by the doctor. Full refund processed.",
                    ['appointment_id' => (string)$appointment->id, 'type' => 'doctor_cancelled']
                );
            }

            $refundedCount++;
        }

        return response()->json([
            'message'        => 'All appointments cancelled successfully',
            'refunded_count' => $refundedCount,
        ]);
    }

    // عرض كل المواعيد (الأدمن والموظف)
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('doctor.user', function ($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->boolean('today')) {
            $query->whereDate('appointment_date', Carbon::today());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'patient_id'       => $appointment->patient_id,
                    'patient_name'     => $appointment->patient->name ?? 'Unknown',
                    'patient_email'    => $appointment->patient->email ?? '',
                    'patient_phone'    => $appointment->patient->phone ?? '',
                    'doctor_id'        => $appointment->doctor_id,
                    'doctor_name'      => $appointment->doctor->user->name ?? 'Unknown',
                    'specialization'   => $appointment->doctor->specialization ?? '',
                    'consultation_fee' => (float) $appointment->consultation_fee,
                    'additional_cost'  => (float) $appointment->additional_cost,
                    'additional_note'  => $appointment->additional_note,
                    'appointment_date' => is_a($appointment->appointment_date, \Carbon\Carbon::class)
                        ? $appointment->appointment_date->format('Y-m-d')
                        : (string)$appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'status'           => $appointment->status,
                    'notes'            => $appointment->notes,
                    'cancelled_by'     => $appointment->cancelled_by,
                    'cancelled_at'     => $appointment->cancelled_at,
                ];
            });

        return response()->json($appointments);
    }
}
