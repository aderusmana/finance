<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Model;

class BgPeriod extends Model
{
    protected $table = 'bg_periods';
    protected $fillable = ['bg_recommendation_id', 'period_date', 'amount'];
    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2'
    ];
}
