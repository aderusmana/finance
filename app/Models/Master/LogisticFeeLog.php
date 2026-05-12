<?php
namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\DistributorCustomer;
use App\Models\User;

class LogisticFeeLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function distributorCustomer()
    {
        return $this->belongsTo(DistributorCustomer::class, 'distributor_customer_id');
    }

    // Relasi ke User menggunakan NIK
    public function user()
    {
        return $this->belongsTo(User::class, 'action_by', 'nik');
    }
}