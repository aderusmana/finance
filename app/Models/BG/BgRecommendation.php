<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgRecommendation extends Model
{
    use HasFactory;

    protected $table = 'bg_recommendations';

    protected $fillable = [
        'customer_id', 'average', 'average_increase_percent', 'top', 'lead_time',
        'inflation', 'increase_percent', 'recommended_credit_limit', 'rounded_credit_limit',
        'fk_with_limit','current_bg','set_bg','credit_limit_updated','status','notes'
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'average_increase_percent' => 'decimal:2',
        'inflation' => 'decimal:2',
        'increase_percent' => 'decimal:2',
        'recommended_credit_limit' => 'decimal:2',
        'rounded_credit_limit' => 'decimal:2',
        'fk_with_limit' => 'decimal:2',
        'current_bg' => 'decimal:2',
        'set_bg' => 'decimal:2',
        'credit_limit_updated' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }

    public function submissions()
    {
        return $this->hasMany(BgSubmission::class, 'bg_recommendation_id');
    }
}
