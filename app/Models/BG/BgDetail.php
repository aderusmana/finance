<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgDetail extends Model
{
    use HasFactory;

    protected $table = 'bg_details';

    protected $fillable = [
        'bank_garansi_id', 'bank_name', 'branch_name', 'bank_address', 'contact_person', 'nominal'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function bankGaransi()
    {
        return $this->belongsTo(BankGaransi::class, 'bank_garansi_id');
    }
}
