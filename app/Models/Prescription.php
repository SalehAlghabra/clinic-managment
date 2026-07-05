<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'medication_name',
        'dosage',
        'duration',
        'instructions',
    ];

    // علاقة: الوصفة تنتمي لسجل طبي
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }
}
