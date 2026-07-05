<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Appointment;
use App\Models\DoctorDetail;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // إنشاء سجل طبي (الدكتور فقط)
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'symptoms'       => 'nullable|string',
            'diagnosis'      => 'nullable|string',
            'doctor_notes'   => 'nullable|string',
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

        if ($appointment->status !== 'confirmed') {
            return response()->json([
                'message' => 'Appointment must be confirmed before creating medical record'
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

        $appointment->update(['status' => 'completed']);

        // إشعار للمريض
        if ($appointment->patient->fcm_token) {
            $this->firebase->sendNotification(
                $appointment->patient->fcm_token,
                'Medical Record Created 📋',
                'Your medical record has been created by Dr. ' . $request->user()->name,
                ['record_id' => (string)$record->id, 'type' => 'medical_record_created']
            );
        }

        return response()->json([
            'message' => 'Medical record created successfully',
            'record'  => $record,
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
            'prescription' => $prescription,
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
                    'doctor_name'   => $record->doctor->user->name,
                    'symptoms'      => $record->symptoms,
                    'diagnosis'     => $record->diagnosis,
                    'doctor_notes'  => $record->doctor_notes,
                    'prescriptions' => $record->prescriptions,
                ];
            });

        return response()->json($records);
    }

    // عرض سجل محدد
    public function show($recordId)
    {
        $record = MedicalRecord::with(['doctor.user', 'patient', 'prescriptions'])
                               ->find($recordId);

        if (!$record) {
            return response()->json(['message' => 'Medical record not found'], 404);
        }

        return response()->json([
            'id'            => $record->id,
            'visit_date'    => $record->visit_date,
            'patient_name'  => $record->patient->name,
            'doctor_name'   => $record->doctor->user->name,
            'symptoms'      => $record->symptoms,
            'diagnosis'     => $record->diagnosis,
            'doctor_notes'  => $record->doctor_notes,
            'prescriptions' => $record->prescriptions,
        ]);
    }
}
