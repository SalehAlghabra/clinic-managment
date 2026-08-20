<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Appointment;
use App\Models\DoctorDetail;
use App\Models\Invoice;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // إنشاء سجل طبي والزيارة المتكاملة (الدكتور فقط)
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id'                  => 'required|exists:appointments,id',
            'symptoms'                        => 'nullable|string',
            'diagnosis'                       => 'nullable|string',
            'doctor_notes'                    => 'nullable|string',
            'additional_cost'                 => 'nullable|numeric|min:0',
            'additional_note'                 => 'nullable|string',
            'prescriptions'                   => 'nullable|array',
            'prescriptions.*.medication_name' => 'required|string|max:255',
            'prescriptions.*.dosage'          => 'required|string|max:255',
            'prescriptions.*.duration'        => 'required|string|max:255',
            'prescriptions.*.instructions'    => 'nullable|string',
        ]);

        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $appointment = Appointment::where('id', $request->appointment_id)
                                  ->where('doctor_id', $doctorDetail->id)
                                  ->first();

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        if ($appointment->status === 'cancelled' || $appointment->status === 'rejected') {
            return response()->json([
                'message' => 'Cannot create medical record for cancelled or rejected appointment'
            ], 422);
        }

        if (MedicalRecord::where('appointment_id', $request->appointment_id)->exists()) {
            return response()->json([
                'message' => 'Medical record already exists for this appointment'
            ], 422);
        }

        $record = MedicalRecord::create([
            'appointment_id' => $request->appointment_id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => $doctorDetail->id,
            'visit_date'     => $appointment->appointment_date,
            'symptoms'       => $request->symptoms,
            'diagnosis'      => $request->diagnosis,
            'doctor_notes'   => $request->doctor_notes,
        ]);

        // Insert batch prescriptions if provided
        $createdPrescriptions = [];
        if ($request->filled('prescriptions') && is_array($request->prescriptions)) {
            foreach ($request->prescriptions as $pData) {
                $p = Prescription::create([
                    'medical_record_id' => $record->id,
                    'medication_name'   => $pData['medication_name'],
                    'dosage'            => $pData['dosage'],
                    'duration'          => $pData['duration'],
                    'instructions'      => $pData['instructions'] ?? null,
                ]);
                $createdPrescriptions[] = $p;
            }
        }

        // Update appointment status and additional billing
        $additionalCost = (float) ($request->additional_cost ?? $appointment->additional_cost ?? 0);
        $additionalNote = $request->additional_note ?? $appointment->additional_note;

        $appointment->update([
            'status'          => 'completed',
            'additional_cost' => $additionalCost,
            'additional_note' => $additionalNote,
        ]);

        // Update/create invoice
        $consultationFee = (float) $appointment->consultation_fee;
        $totalAmount     = $consultationFee + $additionalCost;
        $depositAmount   = $consultationFee;
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
        if ($appointment->patient && $appointment->patient->fcm_token) {
            $this->firebase->sendNotification(
                $appointment->patient->fcm_token,
                'Medical Record Created 📋',
                'Your medical record has been created by Dr. ' . $request->user()->name,
                ['record_id' => (string)$record->id, 'type' => 'medical_record_created']
            );
        }

        return response()->json([
            'message' => 'Medical record created successfully',
            'record'  => [
                'id'            => $record->id,
                'visit_date'    => $record->visit_date,
                'doctor_name'   => $request->user()->name,
                'patient_name'  => $appointment->patient ? $appointment->patient->name : '',
                'symptoms'      => $record->symptoms,
                'diagnosis'     => $record->diagnosis,
                'doctor_notes'  => $record->doctor_notes,
                'prescriptions' => $createdPrescriptions,
            ],
        ], 201);
    }

    // إضافة وصفة طبية (الدكتور فقط)
    public function addPrescription(Request $request, $recordId)
    {
        $request->validate([
            'medication_name' => 'required|string|max:255',
            'dosage'          => 'required|string|max:255',
            'duration'        => 'required|string|max:255',
            'instructions'    => 'nullable|string',
        ]);

        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        $record = MedicalRecord::where('id', $recordId)
                               ->where('doctor_id', $doctorDetail->id)
                               ->first();

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        $prescription = Prescription::create([
            'medical_record_id' => $recordId,
            'medication_name'   => $request->medication_name,
            'dosage'            => $request->dosage,
            'duration'          => $request->duration,
            'instructions'      => $request->instructions,
        ]);

        // إشعار للمريض
        $patient = $record->patient;
        if ($patient->fcm_token) {
            $this->firebase->sendNotification(
                $patient->fcm_token,
                'New Prescription 💊',
                "Dr. {$request->user()->name} added a prescription: {$request->medication_name}",
                ['record_id' => (string)$recordId, 'type' => 'prescription_added']
            );
        }

        return response()->json([
            'message'      => 'Prescription added successfully',
            'prescription' => [
                'id'              => $prescription->id,
                'medication_name' => $prescription->medication_name,
                'dosage'          => $prescription->dosage,
                'duration'        => $prescription->duration,
                'instructions'    => $prescription->instructions,
            ],
        ], 201);
    }

    // عرض السجلات الطبية للمريض
    public function patientRecords(Request $request)
    {
        $records = MedicalRecord::with(['doctor.user', 'prescriptions'])
            ->where('patient_id', $request->user()->id)
            ->orderBy('visit_date', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'id'            => $record->id,
                    'visit_date'    => $record->visit_date,
                    'doctor_name'   => $record->doctor && $record->doctor->user ? $record->doctor->user->name : '',
                    'symptoms'      => $record->symptoms,
                    'diagnosis'     => $record->diagnosis,
                    'doctor_notes'  => $record->doctor_notes,
                    'prescriptions' => $record->prescriptions,
                ];
            });

        return response()->json($records);
    }

    // عرض السجلات الطبية لمريض معين من قبل الدكتور
    public function doctorPatientRecords(Request $request, $patientId)
    {
        $doctorDetail = DoctorDetail::where('user_id', $request->user()->id)->first();

        if (!$doctorDetail) {
            return response()->json(['message' => 'Doctor profile not found'], 404);
        }

        // Verify the doctor has an appointment relationship with this patient
        $hasRelationship = Appointment::where('doctor_id', $doctorDetail->id)
                                      ->where('patient_id', $patientId)
                                      ->exists();

        if (!$hasRelationship && !in_array($request->user()->role, ['admin', 'receptionist'])) {
            return response()->json(['message' => 'Unauthorized: No appointment history with this patient'], 403);
        }

        $records = MedicalRecord::with(['doctor.user', 'prescriptions'])
            ->where('patient_id', $patientId)
            ->where('doctor_id', $doctorDetail->id)
            ->orderBy('visit_date', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'id'            => $record->id,
                    'visit_date'    => $record->visit_date,
                    'doctor_name'   => $record->doctor && $record->doctor->user ? $record->doctor->user->name : '',
                    'symptoms'      => $record->symptoms,
                    'diagnosis'     => $record->diagnosis,
                    'doctor_notes'  => $record->doctor_notes,
                    'prescriptions' => $record->prescriptions,
                ];
            });

        return response()->json($records);
    }

    // عرض سجل محدد
    public function show(Request $request, $recordId)
    {
        $record = MedicalRecord::with(['doctor.user', 'patient', 'prescriptions'])
                               ->find($recordId);

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        if ($request->user()->role === 'patient' && $record->patient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized access to this medical record'], 403);
        }

        return response()->json([
            'id'            => $record->id,
            'visit_date'    => $record->visit_date,
            'patient_name'  => $record->patient ? $record->patient->name : '',
            'doctor_name'   => $record->doctor && $record->doctor->user ? $record->doctor->user->name : '',
            'symptoms'      => $record->symptoms,
            'diagnosis'     => $record->diagnosis,
            'doctor_notes'  => $record->doctor_notes,
            'prescriptions' => $record->prescriptions,
        ]);
    }
}
