<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'bg_taxs';

    protected $fillable = [
        'name', 'value'
    ];
}
