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
        'customer_id', 'parent_recommendation_id', 'tax_id', 'average', 'top', 'lead_time',
        'inflation', 'recommended_credit_limit', 'rounded_credit_limit',
        'fk_with_limit','current_bg','set_bg','credit_limit_updated','status','notes', 'rejection_reason',
        'sales_approved_by', 'sales_approved_at', 'token'
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
        'sales_approved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function parentRecommendation()
    {
        return $this->belongsTo(self::class, 'parent_recommendation_id');
    }

    public function childRecommendations()
    {
        return $this->hasMany(self::class, 'parent_recommendation_id');
    }

    public function salesApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'sales_approved_by');
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
