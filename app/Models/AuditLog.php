<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'performed_by_id',
        'target_user_id',
        'action',
        'old_value',
        'new_value',
        'notes',
    ];

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
