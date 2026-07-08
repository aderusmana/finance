<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class DistributorCustomer extends Model
{
    protected $table = 'distributor_customers';
    protected $fillable = ['distributor_id', 'customer_id', 'logistic_fee', 'route_to', 'status', 'proposed_fee'];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
