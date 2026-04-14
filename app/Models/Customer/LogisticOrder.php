<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class LogisticOrder extends Model
{
    protected $fillable = ['customer_id', 'distributor_id', 'customer_ship_to_id', 'logistic_order_no', 'delivery_date', 'delivery_to', 'period', 'status', 'route_to'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function customerShipTo()
    {
        return $this->belongsTo(CustomerShipTo::class, 'customer_ship_to_id');
    }
}
