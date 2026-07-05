<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'service_name',
        'price',
    ];

    // علاقة: الخدمة تنتمي لدكتور
    public function doctor()
    {
        return $this->belongsTo(DoctorDetail::class, 'doctor_id');
    }

    // علاقة: الخدمة لها مواعيد
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id');
    }
}
