<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CustomerShipTo extends Model
{
    protected $table = 'customer_ship_toes';
    protected $fillable = ['customer_id', 'user_id', 'ship_to_code', 'ship_to_name', 'ship_to_address_1', 'ship_to_address_2', 'ship_to_address_3', 'ship_to_city'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
