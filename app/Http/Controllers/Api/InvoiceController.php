<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // إنشاء فاتورة (الأدمن أو الموظف)
public function store(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'total_amount'   => 'required|numeric|min:0',
        'payment_method' => 'nullable|in:cash,online',
    ]);

    $appointment = Appointment::find($request->appointment_id);

    if ($appointment->status !== 'completed') {
        return response()->json([
            'message' => 'Appointment must be completed before creating invoice'
        ], 422);
    }

    if (Invoice::where('appointment_id', $request->appointment_id)->exists()) {
        return response()->json([
            'message' => 'Invoice already exists for this appointment'
        ], 422);
    }

    // حساب المبلغ المبدئي والمتبقي
    $depositAmount   = (float) \App\Models\Setting::get('booking_deposit', 50);
    $remainingAmount = $request->total_amount - $depositAmount;

    $invoice = Invoice::create([
        'appointment_id'  => $request->appointment_id,
        'total_amount'    => $request->total_amount,
        'deposit_amount'  => $depositAmount,
        'remaining_amount'=> $remainingAmount,
        'payment_status'  => 'unpaid',
        'payment_method'  => $request->payment_method,
        'issued_at'       => now(),
    ]);

    // إشعار للمريض
    if ($appointment->patient->fcm_token) {
        $this->firebase->sendNotification(
            $appointment->patient->fcm_token,
            'New Invoice 🧾',
            "Total: {$request->total_amount} | Deposit paid: {$depositAmount} | Remaining: {$remainingAmount}",
            ['invoice_id' => (string)$invoice->id, 'type' => 'invoice_created']
        );
    }

    return response()->json([
        'message'          => 'Invoice created successfully',
        'invoice'          => $invoice,
        'payment_summary'  => [
            'total_amount'    => $request->total_amount,
            'deposit_paid'    => $depositAmount,
            'remaining_amount'=> $remainingAmount,
        ],
    ], 201);
}

    // عرض فاتورة موعد محدد
    public function show($appointmentId)
    {
        $invoice = Invoice::with(
            'appointment.patient',
            'appointment.doctor.user',
            'appointment.service'
        )->where('appointment_id', $appointmentId)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return response()->json([
            'id'             => $invoice->id,
            'patient_name'   => $invoice->appointment->patient->name,
            'doctor_name'    => $invoice->appointment->doctor->user->name,
            'service'        => $invoice->appointment->service->service_name,
            'visit_date'     => $invoice->appointment->appointment_date,
            'total_amount'   => $invoice->total_amount,
            'payment_status' => $invoice->payment_status,
            'payment_method' => $invoice->payment_method,
            'issued_at'      => $invoice->issued_at,
        ]);
    }

    // تحديث حالة الدفع
    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
            'payment_method' => 'required_if:payment_status,paid|in:cash,online',
        ]);

        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $invoice->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        // إشعار للمريض عند الدفع
        $patient = $invoice->appointment->patient;
        if ($request->payment_status === 'paid' && $patient->fcm_token) {
            $this->firebase->sendNotification(
                $patient->fcm_token,
                'Payment Confirmed ✅',
                "Your payment of {$invoice->total_amount} has been confirmed",
                ['invoice_id' => (string)$invoice->id, 'type' => 'payment_confirmed']
            );
        }

        return response()->json([
            'message' => 'Payment updated successfully',
            'invoice' => $invoice,
        ]);
    }

    // عرض فواتير المريض
    public function patientInvoices(Request $request)
    {
        $invoices = Invoice::with('appointment.doctor.user', 'appointment.service')
            ->whereHas('appointment', function ($query) use ($request) {
                $query->where('patient_id', $request->user()->id);
            })
            ->orderBy('issued_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id'             => $invoice->id,
                    'doctor_name'    => $invoice->appointment->doctor->user->name,
                    'service'        => $invoice->appointment->service->service_name,
                    'visit_date'     => $invoice->appointment->appointment_date,
                    'total_amount'   => $invoice->total_amount,
                    'payment_status' => $invoice->payment_status,
                    'payment_method' => $invoice->payment_method,
                    'issued_at'      => $invoice->issued_at,
                ];
            });

        return response()->json($invoices);
    }

    // عرض كل الفواتير (الأدمن)
    public function index()
    {
        $invoices = Invoice::with(
            'appointment.patient',
            'appointment.doctor.user',
            'appointment.service'
        )->orderBy('issued_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id'             => $invoice->id,
                    'patient_name'   => $invoice->appointment->patient->name,
                    'doctor_name'    => $invoice->appointment->doctor->user->name,
                    'service'        => $invoice->appointment->service->service_name,
                    'visit_date'     => $invoice->appointment->appointment_date,
                    'total_amount'   => $invoice->total_amount,
                    'payment_status' => $invoice->payment_status,
                    'payment_method' => $invoice->payment_method,
                    'issued_at'      => $invoice->issued_at,
                ];
            });

        return response()->json($invoices);
    }
}
