<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model
{
    use HasFactory;

    protected $table = 'customer_files';

    protected $fillable = [
        'customer_id', 'npwp_file', 'nib_siup_file', 'ktp_file'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }
}
