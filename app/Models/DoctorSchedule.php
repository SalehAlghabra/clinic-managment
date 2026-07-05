<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'duration_per_patient',
    ];

    // علاقة: الجدول ينتمي لدكتور
    public function doctor()
    {
        return $this->belongsTo(DoctorDetail::class, 'doctor_id');
    }
}
