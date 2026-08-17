<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    // تقرير عام (Dashboard)
    public function dashboard()
    {
        $today = Carbon::today();

        return response()->json([
            // إحصائيات المستخدمين
            'users' => [
                'total_patients'      => User::where('role', 'patient')->count(),
                'total_doctors'       => User::where('role', 'doctor')->count(),
                'total_receptionists' => User::where('role', 'receptionist')->count(),
                'new_patients_today'  => User::where('role', 'patient')
                                            ->whereDate('created_at', $today)
                                            ->count(),
            ],

            // إحصائيات المواعيد
            'appointments' => [
                'total'                => Appointment::count(),
                'today'                => Appointment::whereDate('appointment_date', $today)->count(),
                'pending'              => Appointment::where('status', 'pending')->count(),
                'confirmed'            => Appointment::where('status', 'confirmed')->count(),
                'completed'            => Appointment::where('status', 'completed')->count(),
                'cancelled'            => Appointment::where('status', 'cancelled')->count(),
                'cancelled_by_doctor'  => Appointment::where('cancelled_by', 'doctor')->count(),
                'cancelled_by_patient' => Appointment::where('cancelled_by', 'patient')->count(),
            ],

            // إحصائيات مالية
            'financial' => [
                'total_invoices'        => Invoice::count(),
                'total_revenue'         => Invoice::where('payment_status', 'paid')->sum('total_amount'),
                'pending_payments'      => Invoice::where('payment_status', 'unpaid')->sum('total_amount'),
                'total_deposits'        => WalletTransaction::where('type', 'deposit')->sum('amount'),
                'total_penalties'       => WalletTransaction::where('type', 'penalty')->sum('amount'),
                'total_refunds'         => WalletTransaction::whereIn('type', ['refund_full', 'refund_partial'])->sum('amount'),
            ],
        ]);
    }

    // تقرير المواعيد (فلترة بالتاريخ)
    public function appointmentsReport(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $appointments = Appointment::with(['patient', 'doctor.user'])
            ->whereBetween('appointment_date', [$request->from, $request->to])
            ->get();

        $summary = [
            'period'    => ['from' => $request->from, 'to' => $request->to],
            'total'     => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'pending'   => $appointments->where('status', 'pending')->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
        ];

        $data = $appointments->map(function ($appointment) {
            return [
                'id'               => $appointment->id,
                'patient_name'     => $appointment->patient->name ?? 'Unknown Patient',
                'doctor_name'      => $appointment->doctor->user->name ?? 'Unknown Doctor',
                'consultation_fee' => $appointment->consultation_fee,
                'additional_cost'  => $appointment->additional_cost,
                'additional_note'  => $appointment->additional_note,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'status'           => $appointment->status,
                'cancelled_by'     => $appointment->cancelled_by,
            ];
        });

        return response()->json([
            'summary' => $summary,
            'data'    => $data,
        ]);
    }

    // تقرير الإيرادات (فلترة بالتاريخ)
    public function revenueReport(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $invoices = Invoice::with('appointment.doctor.user', 'appointment.patient')
            ->whereBetween('issued_at', [
                Carbon::parse($request->from)->startOfDay(),
                Carbon::parse($request->to)->endOfDay(),
            ])->get();

        $summary = [
            'period'           => ['from' => $request->from, 'to' => $request->to],
            'total_invoices'   => $invoices->count(),
            'total_revenue'    => $invoices->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => $invoices->where('payment_status', 'unpaid')->sum('total_amount'),
            'cash_payments'    => $invoices->where('payment_method', 'cash')->sum('total_amount'),
            'online_payments'  => $invoices->where('payment_method', 'online')->sum('total_amount'),
            'wallet_payments'  => $invoices->where('payment_method', 'wallet')->sum('total_amount'),
        ];

        $data = $invoices->map(function ($invoice) {
            return [
                'id'               => $invoice->id,
                'appointment_id'   => $invoice->appointment_id,
                'patient_name'     => $invoice->appointment->patient->name ?? 'N/A',
                'doctor_name'      => $invoice->appointment->doctor->user->name ?? 'N/A',
                'consultation_fee' => $invoice->appointment->consultation_fee ?? $invoice->deposit_amount,
                'total_amount'     => $invoice->total_amount,
                'already_paid'     => $invoice->deposit_amount,
                'remaining_amount' => $invoice->remaining_amount,
                'payment_status'   => $invoice->payment_status,
                'payment_method'   => $invoice->payment_method ?? 'cash',
                'issued_at'        => $invoice->issued_at,
            ];
        });

        return response()->json([
            'summary' => $summary,
            'data'    => $data,
        ]);
    }

    // تقرير أداء الأطباء
    public function doctorsReport(Request $request)
    {
        $from = $request->input('from', '2020-01-01');
        $to = $request->input('to', Carbon::now()->addYears(2)->toDateString());

        $doctors = DoctorDetail::with('user')->get()->map(function ($doctor) use ($from, $to) {
            $appointments = Appointment::where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [$from, $to])
                ->get();

            $revenue = Invoice::whereHas('appointment', function ($q) use ($doctor, $from, $to) {
                $q->where('doctor_id', $doctor->id)
                  ->whereBetween('appointment_date', [$from, $to]);
            })->where('payment_status', 'paid')->sum('total_amount');

            return [
                'id'                  => $doctor->id,
                'user_id'             => $doctor->user_id,
                'doctor_name'         => $doctor->user->name ?? 'N/A',
                'email'               => $doctor->user->email ?? null,
                'phone'               => $doctor->user->phone ?? null,
                'bio'                 => $doctor->bio ?? null,
                'profile_picture'     => $doctor->user->profile_picture ?? null,
                'profile_picture_url' => $doctor->user->profile_picture_url ?? null,
                'specialization'      => $doctor->specialization,
                'consultation_fee'    => $doctor->consultation_fee,
                'total_appointments'  => $appointments->count(),
                'completed'           => $appointments->where('status', 'completed')->count(),
                'cancelled'           => $appointments->where('status', 'cancelled')->count(),
                'revenue'             => $revenue,
            ];
        });

        return response()->json([
            'period'  => ['from' => $request->from, 'to' => $request->to],
            'doctors' => $doctors,
        ]);
    }

    // تقرير المخالفات
    public function violationsReport()
    {
        $patients = User::where('role', 'patient')
            ->where('violation_count', '>', 0)
            ->orderBy('violation_count', 'desc')
            ->get()
            ->map(function ($patient) {
                $penalties = WalletTransaction::where('user_id', $patient->id)
                                             ->where('type', 'penalty')
                                             ->sum('amount');
                return [
                    'id'                  => $patient->id,
                    'patient_name'        => $patient->name,
                    'profile_picture'     => $patient->profile_picture,
                    'profile_picture_url' => $patient->profile_picture_url,
                    'email'               => $patient->email,
                    'violation_count'     => $patient->violation_count,
                    'total_penalties'     => $penalties,
                    'penalty_rate'        => min($patient->violation_count * 5, 25) . '%',
                ];
            });

        return response()->json([
            'total_violators' => $patients->count(),
            'patients'        => $patients,
        ]);
    }

    // تقرير وقائمة المرضى (مع بحث)
    public function patientsReport(Request $request)
    {
        $query = User::where('role', 'patient');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')->get()->map(function ($patient) {
            $penalties = WalletTransaction::where('user_id', $patient->id)
                                         ->where('type', 'penalty')
                                         ->sum('amount');
            return [
                'id'                  => $patient->id,
                'patient_name'        => $patient->name,
                'profile_picture'     => $patient->profile_picture,
                'profile_picture_url' => $patient->profile_picture_url,
                'email'               => $patient->email,
                'phone'               => $patient->phone ?? '',
                'wallet_balance'      => (float) $patient->wallet_balance,
                'violation_count'     => (int) $patient->violation_count,
                'total_penalties'     => (float) $penalties,
            ];
        });

        return response()->json([
            'total'    => $patients->count(),
            'patients' => $patients,
        ]);
    }
}
