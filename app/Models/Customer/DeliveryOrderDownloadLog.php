<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrderDownloadLog extends Model
{
    protected $fillable = [
        'delivery_order_note_id',
        'downloaded_by',
    ];

    public function deliveryOrderNote()
    {
        return $this->belongsTo(DeliveryOrderNote::class);
    }
}
