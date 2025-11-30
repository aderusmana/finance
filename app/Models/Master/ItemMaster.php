<?php

namespace App\Models\Master;

use App\Models\Requisition\Requisition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMaster extends Model
{
    use HasFactory;
    protected $table = 'item_masters';

    protected $fillable = [
        'item_master_code',
        'item_master_name',
        'unit',
    ];

    public function ItemDetails()
    {
        return $this->hasMany(ItemDetail::class, 'item_master_id');
    }

    public function requisition()
    {
        return $this->hasMany(Requisition::class, 'item_master_id');
    }
}
