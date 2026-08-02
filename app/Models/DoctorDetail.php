<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'bio',
        'consultation_fee',
    ];

    protected $casts = [
        'consultation_fee' => 'decimal:2',
    ];


    // علاقة: تفاصيل الدكتور تنتمي لمستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة: الدكتور له جداول دوام
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id');
    }

    // علاقة: الدكتور له خدمات
    public function services()
    {
        return $this->hasMany(Service::class, 'doctor_id');
    }

    // علاقة: الدكتور له مواعيد
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // علاقة: الدكتور له سجلات طبية
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }
}
