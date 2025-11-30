<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class TOP extends Model
{
    protected $table = 'tops';
    protected $fillable = ['name_top', 'desc_top'];
}
