<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;

class RequisitionSpecial extends Model
{
    protected $table = 'requisition_specials';

    protected $fillable = [
        'requisition_id',
        'requested_date',
        'end_date',
        'products',
        'weight_selection',
        'packaging_selection',
        'sample_count',
        'purpose',
        'coa_required',
        'shipment_method',
        'source',
        'sample_notes',
        'production_date',
        'preparation_method',
        'description',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
