<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDetail extends Model
{
    use HasFactory;

    protected $table = 'item_details';

    protected $fillable = [
        'item_master_id',
        'component_item_master_id',
        'material_type',
        'item_detail_code',
        'item_detail_name',
        'unit',
        'net_weight',
    ];

    public function itemMaster()
    {
        return $this->belongsTo(ItemMaster::class, 'item_master_id');
    }
}
