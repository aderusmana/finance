<?php

namespace App\Models\Requisition;

use App\Models\Requisition\Requisition;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'trackings';

    protected $fillable = [
        'requisition_id',
        'current_position',
        'last_updated',
        'notes',
        'token',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
