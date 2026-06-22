<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class LogisticOrder extends Model
{
    protected $fillable = [
        'distributor_id', 'customer_id', 'customer_ship_to_id', 'logistic_order_no',
        'no_po', 'delivery_date', 'attention', 'date_of_po', 'created_by', 'cancel_reason', 'canceled_at'
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function distributor() { return $this->belongsTo(Distributor::class); }
    public function customerShipTo() { return $this->belongsTo(CustomerShipTo::class, 'customer_ship_to_id'); }

    // Relasi Baru
    public function note() { return $this->hasOne(DeliveryOrderNote::class, 'logistic_order_id'); }
    public function items() { return $this->hasMany(LogisticOrderItem::class, 'logistic_order_id'); }
}
