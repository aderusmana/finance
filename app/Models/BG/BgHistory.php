<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgHistory extends Model
{
    use HasFactory;

    protected $table = 'bg_histories';

    protected $fillable = [
        'bank_garansi_id', 'previous_nominal', 'new_nominal', 'previous_exp_date', 'new_exp_date', 'remarks', 'created_by'
    ];

    protected $casts = [
        'previous_nominal' => 'decimal:2',
        'new_nominal' => 'decimal:2',
        'previous_exp_date' => 'date',
        'new_exp_date' => 'date',
    ];

    public function bankGaransi()
    {
        return $this->belongsTo(BankGaransi::class, 'bank_garansi_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
