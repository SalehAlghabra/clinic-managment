<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'total_amount',
        'deposit_amount',
        'remaining_amount',
        'payment_status',
        'payment_method',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    // علاقة: الفاتورة تنتمي لموعد
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
