<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
    protected $fillable = [
        'name_account_group', 'bank_garansi', 'ccar'
    ];
}
