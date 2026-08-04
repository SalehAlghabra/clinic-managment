<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'service_id',
        'consultation_fee',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'additional_cost',
        'additional_note',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'additional_cost'  => 'decimal:2',
        'appointment_date' => 'date:Y-m-d',
        'cancelled_at'     => 'datetime',
    ];


    // علاقة: الموعد ينتمي لمريض
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // علاقة: الموعد ينتمي لدكتور
    public function doctor()
    {
        return $this->belongsTo(DoctorDetail::class, 'doctor_id');
    }

    // علاقة: الموعد ينتمي لخدمة
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // علاقة: الموعد له سجل طبي
    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class, 'appointment_id');
    }

    // علاقة: الموعد له فاتورة
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'appointment_id');
    }
}
