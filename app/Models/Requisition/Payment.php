<?php

namespace App\Models\Requisition;

use App\Models\Requisition\Requisition;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'requisition_id',
        'payment_date',
        'document_url',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
