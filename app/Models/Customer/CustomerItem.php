<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerItem extends Model
{
    protected $table = 'customer_items';

    protected $fillable = [
        'customer_id',
        'item_name',
        'item_description',
        'quantity',
        'price',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
