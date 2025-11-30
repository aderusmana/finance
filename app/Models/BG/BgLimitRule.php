<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgLimitRule extends Model
{
    use HasFactory;

    protected $table = 'bg_limit_rules';

    protected $fillable = [
        'min_year', 'max_year', 'percentage', 'description'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];
}
