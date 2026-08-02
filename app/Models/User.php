<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profile_picture',
        'fcm_token',
        'wallet_balance',
        'violation_count',
        'otp_code',
        'otp_expires_at',
        'email_verified_at',
    ];

    protected $appends = [
        'profile_picture_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'otp_expires_at'    => 'datetime',
            'wallet_balance'    => 'decimal:2',
            'violation_count'   => 'integer',
        ];
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
                return $this->profile_picture;
            }
            return asset('storage/' . ltrim($this->profile_picture, '/'));
        }

        return asset('images/default-avatar.png');
    }

    // علاقة: المستخدم له تفاصيل دكتور
    public function doctorDetail()
    {
        return $this->hasOne(DoctorDetail::class);
    }

    // علاقة: المستخدم (مريض) له مواعيد
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // علاقة: المستخدم (مريض) له سجلات طبية
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }
}

