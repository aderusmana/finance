<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class LogisticOrderItem extends Model
{
    protected $fillable = [
        'logistic_order_id',
        'ship_to_code',
        'order_item_code',
        'order_item_name',
        'order_quantity',
        'order_amount',
        'price_list'
    ];

    public function logisticOrder()
    {
        return $this->belongsTo(LogisticOrder::class);
    }
}
