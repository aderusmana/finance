<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['position_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'position_id', 'id');
    }
}
