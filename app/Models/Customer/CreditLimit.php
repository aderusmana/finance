<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditLimit extends Model
{
    use HasFactory;

    protected $table = 'credit_limits';

    protected $fillable = [
        'customer_id', 'bank_garansi_id', 'recommendation_id', 'requested_limit', 'approved_limit',
        'lampiran_d_version_id', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'requested_limit' => 'decimal:2',
        'approved_limit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    public function bankGaransi()
    {
        return $this->belongsTo(\App\Models\BG\BankGaransi::class, 'bank_garansi_id');
    }

    public function recommendation()
    {
        return $this->belongsTo(\App\Models\BG\BgRecommendation::class, 'recommendation_id');
    }

    public function lampiranVersion()
    {
        return $this->belongsTo(\App\Models\BG\LampiranDVersion::class, 'lampiran_d_version_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
