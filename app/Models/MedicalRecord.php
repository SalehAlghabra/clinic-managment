<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'visit_date',
        'symptoms',
        'diagnosis',
        'doctor_notes',
    ];

    // علاقة: السجل ينتمي لموعد
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    // علاقة: السجل ينتمي لمريض
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // علاقة: السجل ينتمي لدكتور
    public function doctor()
    {
        return $this->belongsTo(DoctorDetail::class, 'doctor_id');
    }

    // علاقة: السجل له وصفات طبية
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'medical_record_id');
    }
}
