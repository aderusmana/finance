<?php

namespace App\Models\Master;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'slug',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($department) {
            if (isset($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });

        static::updating(function ($department) {
            if (isset($department->name)) {
                $department->slug = Str::slug($department->name);
            }
        });
    }


}
