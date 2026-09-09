<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['customer_id', 'code', 'name', 'email'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'distributor_customers')
                    ->withPivot('logistic_fee')
                    ->withTimestamps();
    }
}
