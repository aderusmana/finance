<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_logs';

    protected $fillable = [
        'category',
        'related_id',
        'approver_nik',
        'status',
        'level',
        'token',
        'notes',
    ];


    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_nik', 'nik');
    }
}
