<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\Customer;

class BgRecommendation extends Model
{
    use HasFactory;

    protected $table = 'bg_recommendations';

    protected $fillable = [
        'customer_id', 'tax_id', 'average', 'top', 'lead_time',
        'inflation', 'recommended_credit_limit', 'rounded_credit_limit',
        'fk_with_limit','current_bg','set_bg','credit_limit_updated','status','notes', 'token'
    ];

    protected $casts = [
        'average' => 'decimal:2',
        'average_increase_percent' => 'decimal:2',
        'inflation' => 'decimal:2',
        'recommended_credit_limit' => 'decimal:2',
        'rounded_credit_limit' => 'decimal:2',
        'fk_with_limit' => 'decimal:2',
        'current_bg' => 'decimal:2',
        'set_bg' => 'decimal:2',
        'credit_limit_updated' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function submissions()
    {
        return $this->hasMany(BgSubmission::class, 'bg_recommendation_id');
    }

    public function periods()
    {
        return $this->hasMany(BgPeriod::class, 'bg_recommendation_id');
    }
}
