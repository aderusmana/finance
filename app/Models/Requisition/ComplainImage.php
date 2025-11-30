<?php

namespace App\Models\Requisition;

use Illuminate\Database\Eloquent\Model;

class ComplainImage extends Model
{
    protected $fillable = [
        'requisition_id',
        'image_path',
    ];
    
    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'requisition_id');
    }
}
