<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorDetail;
use App\Models\DoctorSchedule;
use App\Models\Service;
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
        'service_id'       => 'required|exists:services,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required|date_format:H:i',
    ]);

    $depositAmount  = $this->wallet->getDepositAmount();
    $walletBalance  = $request->user()->wallet_balance;
    $afterBalance   = $walletBalance - $depositAmount;
    $hasSufficient  = $walletBalance >= $depositAmount;

    $service = Service::find($request->service_id);

    return response()->json([
        'booking_summary' => [
            'service_name'      => $service->service_name,
            'service_price'     => $service->price,
            'appointment_date'  => $request->appointment_date,
            'appointment_time'  => $request->appointment_time,
        ],
        'payment_summary' => [
            'deposit_required'  => $depositAmount,
            'wallet_balance'    => $walletBalance,
            'balance_after'     => $hasSufficient ? $afterBalance : null,
            'remaining_at_visit'=> $service->price - $depositAmount,
            'has_sufficient'    => $hasSufficient,
        ],
        'message' => $hasSufficient
            ? "Amount {$depositAmount} will be deducted from your wallet upon confirmation"
            : "Insufficient balance. Please charge your wallet with at least {$depositAmount}",
    ]);
}

    // حجز موعد (المريض فقط)
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id'        => 'required|exists:doctor_details,id',
            'service_id'       => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes'            => 'nullable|string',
        ]);

        $service = Service::where('id', $request->service_id)
                          ->where('doctor_id', $request->doctor_id)
                          ->first();

        if (!$service) {
            return response()->json(['message' => 'This service does not belong to this doctor'], 422);
        }

        $dayOfWeek = strtolower(date('l', strtotime($request->appointment_date)));
        $schedule  = DoctorSchedule::where('doctor_id', $request->doctor_id)
                                   ->where('day_of_week', $dayOfWeek)
                                   ->first();

        if (!$schedule) {
            return response()->json(['message' => 'Doctor is not available on this day'], 422);
        }

        if ($request->appointment_time < $schedule->start_time ||
            $request->appointment_time >= $schedule->end_time) {
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

        // التحقق من رصيد المحفظة
        $depositAmount = $this->wallet->getDepositAmount();
        if ($request->user()->wallet_balance < $depositAmount) {
            return response()->json([
                'message'        => 'Insufficient wallet balance',
                'required'       => $depositAmount,
                'current_balance'=> $request->user()->wallet_balance,
            ], 422);
        }

        $appointment = Appointment::create([
            'patient_id'       => $request->user()->id,
            'doctor_id'        => $request->doctor_id,
            'service_id'       => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);

        // خصم المبلغ المبدئي من المحفظة
        $this->wallet->deductBookingDeposit($request->user(), $appointment->id);

        // إشعار للدكتور
        $doctorUser = $appointment->doctor->user;
        if ($doctorUser->fcm_token) {
            $this->firebase->sendNotification(
                $doctorUser->fcm_token,
                'New Appointment Request 📅',
                "Patient {$request->user()->name} booked on {$request->appointment_date} at {$request->appointment_time}",
                ['appointment_id' => (string)$appointment->id, 'type' => 'new_appointment']
            );
        }

        return response()->json([
            'message'         => 'Appointment booked successfully',
            'appointment'     => $appointment,
            'deposit_paid'    => $depositAmount,
            'wallet_balance'  => $request->user()->fresh()->wallet_balance,
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
        $appointments = Appointment::with(['doctor.user', 'service'])
            ->where('patient_id', $request->user()->id)
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'doctor_name'      => $appointment->doctor->user->name,
                    'specialization'   => $appointment->doctor->specialization,
                    'service'          => $appointment->service->service_name,
                    'price'            => $appointment->service->price,
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

        $appointments = Appointment::with(['patient', 'service'])
            ->where('doctor_id', $doctorDetail->id)
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'patient_name'     => $appointment->patient->name,
                    'patient_phone'    => $appointment->patient->phone,
                    'service'          => $appointment->service->service_name,
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
            'status' => 'required|in:confirmed,rejected,completed',
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

        // إذا رفض الدكتور الموعد يرجع المبلغ كاملاً
        if ($request->status === 'rejected') {
            $this->wallet->refundFull(
                $appointment->patient,
                $appointment->id,
                'Refund: appointment rejected by doctor'
            );

            // إشعار للمريض
            if ($appointment->patient->fcm_token) {
                $this->firebase->sendNotification(
                    $appointment->patient->fcm_token,
                    'Appointment Rejected ❌',
                    'Your appointment was rejected. Your deposit has been refunded.',
                    ['appointment_id' => (string)$appointment->id, 'type' => 'appointment_rejected']
                );
            }
        } else {
            // إشعار للمريض بالتأكيد أو الاكتمال
            $statusMessages = [
                'confirmed' => 'Your appointment has been confirmed ✅',
                'completed' => 'Your visit has been completed 🎉',
            ];

            if ($appointment->patient->fcm_token) {
                $this->firebase->sendNotification(
                    $appointment->patient->fcm_token,
                    'Appointment Update',
                    $statusMessages[$request->status],
                    ['appointment_id' => (string)$appointment->id, 'type' => 'appointment_status', 'status' => $request->status]
                );
            }
        }

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'message'     => 'Appointment status updated successfully',
            'appointment' => $appointment,
        ]);
    }

    // إلغاء موعد (المريض)
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);

        $appointment = Appointment::where('id', $id)
                                  ->where('patient_id', $request->user()->id)
                                  ->first();

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Cannot cancel this appointment'], 422);
        }

        // التحقق من وقت الإلغاء
        $cancellationHours   = (int) Setting::get('cancellation_hours', 24);
        $appointmentDateTime = Carbon::parse($appointment->appointment_date . ' ' . $appointment->appointment_time);
        $hoursUntilAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);

        if ($hoursUntilAppointment > $cancellationHours) {
            // إلغاء قبل الوقت المحدد → استرداد كامل
            $this->wallet->refundFull(
                $request->user(),
                $appointment->id,
                'Full refund: cancelled before deadline'
            );
            $refundMessage = 'Full refund processed ✅';
        } else {
            // إلغاء بعد الوقت المحدد → استرداد مع غرامة
            $this->wallet->refundWithPenalty(
                $request->user(),
                $appointment->id
            );
            $violationCount = $request->user()->fresh()->violation_count;
            $penaltyRate    = min($violationCount * 5, (float) Setting::get('max_penalty_percentage', 25));
            $refundMessage  = "Partial refund with {$penaltyRate}% penalty ⚠️";
        }

        $appointment->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by'        => 'patient',
            'cancelled_at'        => now(),
        ]);

        // إشعار للدكتور
        $doctorUser = $appointment->doctor->user;
        if ($doctorUser->fcm_token) {
            $this->firebase->sendNotification(
                $doctorUser->fcm_token,
                'Appointment Cancelled ❌',
                "Patient {$request->user()->name} cancelled the appointment on {$appointment->appointment_date}",
                ['appointment_id' => (string)$appointment->id, 'type' => 'appointment_cancelled']
            );
        }

        return response()->json([
            'message'        => 'Appointment cancelled successfully',
            'refund_status'  => $refundMessage,
            'wallet_balance' => $request->user()->fresh()->wallet_balance,
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
                'Full refund: doctor cancelled appointments for the day'
            );

            $appointment->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason ?? 'Doctor cancelled appointments for this day',
                'cancelled_by'        => 'doctor',
                'cancelled_at'        => now(),
            ]);

            // إشعار لكل مريض
            if ($appointment->patient->fcm_token) {
                $this->firebase->sendNotification(
                    $appointment->patient->fcm_token,
                    'Appointment Cancelled 📅',
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

    // عرض كل المواعيد (الأدمن)
    public function index()
    {
        $appointments = Appointment::with(['patient', 'doctor.user', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id'               => $appointment->id,
                    'patient_name'     => $appointment->patient->name,
                    'doctor_name'      => $appointment->doctor->user->name,
                    'service'          => $appointment->service->service_name,
                    'appointment_date' => $appointment->appointment_date,
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
