<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'user_id', 'account_group_id', 'branch_id', 'region_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function accountGroup() {
        return $this->belongsTo(AccountGroup::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function region() {
        return $this->belongsTo(Regions::class);
    }
}
