<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['code', 'name', 'email'];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'distributor_customers')
                    ->withPivot('logistic_fee')
                    ->withTimestamps();
    }
}
