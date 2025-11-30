<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankGaransi extends Model
{
    use HasFactory;

    protected $table = 'bank_garansi';

    protected $fillable = [
        'customer_id', 'bg_number', 'bg_type', 'base_bg_id', 'bg_nominal',
        'issued_date', 'exp_date', 'status', 'created_by',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'exp_date' => 'date',
        'bg_nominal' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    public function baseBg()
    {
        return $this->belongsTo(self::class, 'base_bg_id');
    }

    public function details()
    {
        return $this->hasMany(BgDetail::class, 'bank_garansi_id');
    }

    public function histories()
    {
        return $this->hasMany(BgHistory::class, 'bank_garansi_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
