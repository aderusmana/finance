<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderNote extends Model
{
    protected $fillable = ['logistic_order_id', 'delivery_order_no', 'status', 'download_count'];

    public function logisticOrder() { return $this->belongsTo(LogisticOrder::class); }
}
