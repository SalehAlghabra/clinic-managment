<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Services\FirebaseService;
use App\Services\WalletService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected FirebaseService $firebase;
    protected WalletService $wallet;

    public function __construct(FirebaseService $firebase, WalletService $wallet)
    {
        $this->firebase = $firebase;
        $this->wallet   = $wallet;
    }

    // عرض فاتورة موعد محدد
    public function show($appointmentId)
    {
        $invoice = Invoice::with(
            'appointment.patient',
            'appointment.doctor.user'
        )->where('appointment_id', $appointmentId)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return response()->json([
            'id'               => $invoice->id,
            'appointment_id'   => $invoice->appointment_id,
            'patient_name'     => $invoice->appointment->patient->name,
            'doctor_name'      => $invoice->appointment->doctor->user->name,
            'visit_date'       => $invoice->appointment->appointment_date,
            'consultation_fee' => (float) $invoice->deposit_amount,
            'additional_cost'  => (float) $invoice->remaining_amount,
            'total_amount'     => (float) $invoice->total_amount,
            'already_paid'     => (float) $invoice->deposit_amount,
            'remaining_amount' => (float) $invoice->remaining_amount,
            'payment_status'   => $invoice->payment_status,
            'payment_method'   => $invoice->payment_method,
            'issued_at'        => $invoice->issued_at,
        ]);
    }

    // تحديث حالة الدفع وتحصيل المبلغ المتبقي (الموظف والأدمن)
    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
            'payment_method' => 'required_if:payment_status,paid|in:cash,wallet,online',
        ]);

        $invoice = Invoice::with('appointment.patient')->find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        if ($request->payment_status === 'paid' && $invoice->payment_status !== 'paid') {
            $remainingAmount = (float) $invoice->remaining_amount;

            // إذا كان الدفع عن طريق المحفظة
            if ($request->payment_method === 'wallet' && $remainingAmount > 0) {
                $patient = $invoice->appointment->patient;
                $success = $this->wallet->payInvoiceFromWallet(
                    $patient,
                    $invoice->appointment_id,
                    $remainingAmount,
                    "Remaining invoice payment for appointment #{$invoice->appointment_id}"
                );

                if (!$success) {
                    return response()->json([
                        'message'          => 'Insufficient patient wallet balance',
                        'current_balance'  => (float) $patient->wallet_balance,
                        'remaining_amount' => $remainingAmount,
                    ], 422);
                }
            }

            $invoice->update([
                'payment_status'   => 'paid',
                'payment_method'   => $request->payment_method,
                'remaining_amount' => 0.00,
            ]);

            // إشعار للمريض عند التأكيد
            $patient = $invoice->appointment->patient;
            if ($patient->fcm_token) {
                $this->firebase->sendNotification(
                    $patient->fcm_token,
                    'Payment Confirmed ✅',
                    "Your payment of {$remainingAmount} has been confirmed via {$request->payment_method}",
                    ['invoice_id' => (string)$invoice->id, 'type' => 'payment_confirmed']
                );
            }
        } else {
            $invoice->update([
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
            ]);
        }

        return response()->json([
            'message' => 'Payment updated successfully',
            'invoice' => $invoice->fresh(),
        ]);
    }

    // عرض فواتير المريض
    public function patientInvoices(Request $request)
    {
        $invoices = Invoice::with('appointment.doctor.user')
            ->whereHas('appointment', function ($query) use ($request) {
                $query->where('patient_id', $request->user()->id);
            })
            ->orderBy('issued_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id'               => $invoice->id,
                    'appointment_id'   => $invoice->appointment_id,
                    'doctor_name'      => $invoice->appointment->doctor->user->name,
                    'visit_date'       => $invoice->appointment->appointment_date,
                    'consultation_fee' => (float) $invoice->deposit_amount,
                    'total_amount'     => (float) $invoice->total_amount,
                    'already_paid'     => (float) $invoice->deposit_amount,
                    'remaining_amount' => (float) $invoice->remaining_amount,
                    'payment_status'   => $invoice->payment_status,
                    'payment_method'   => $invoice->payment_method,
                    'issued_at'        => $invoice->issued_at,
                ];
            });

        return response()->json($invoices);
    }

    // عرض كل الفواتير (الأدمن)
    public function index()
    {
        $invoices = Invoice::with(
            'appointment.patient',
            'appointment.doctor.user'
        )->orderBy('issued_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id'               => $invoice->id,
                    'appointment_id'   => $invoice->appointment_id,
                    'patient_name'     => $invoice->appointment->patient->name,
                    'doctor_name'      => $invoice->appointment->doctor->user->name,
                    'visit_date'       => $invoice->appointment->appointment_date,
                    'consultation_fee' => (float) $invoice->deposit_amount,
                    'total_amount'     => (float) $invoice->total_amount,
                    'already_paid'     => (float) $invoice->deposit_amount,
                    'remaining_amount' => (float) $invoice->remaining_amount,
                    'payment_status'   => $invoice->payment_status,
                    'payment_method'   => $invoice->payment_method,
                    'issued_at'        => $invoice->issued_at,
                ];
            });

        return response()->json($invoices);
    }
}
