<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_logs';

    protected $fillable = [
        'category',
        'sub_category',
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

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer\Customer::class, 'related_id', 'id');
    }
}
