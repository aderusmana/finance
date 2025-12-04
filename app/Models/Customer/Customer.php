<?php

namespace App\Models\Customer;

use App\Models\Requisition\Requisition;
use App\Models\Customer\CustomerFile;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\Customer\CreditLimit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id',
        'code', 'name','sort_name', 'customer_class', 'account_group',
        'address1', 'address2', 'address3', 'city', 'postal_code', 'country',
        'shipping_to_name', 'shipping_to_address',
        'purchasing_manager_name', 'purchasing_manager_email',
        'finance_manager_name', 'finance_manager_email',
        'penagihan_nama_kontak', 'penagihan_telepon', 'penagihan_address','surat_menyurat_address', 'email', 'tax_contact_name', 'tax_contact_email', 'tax_contact_phone',
        'npwp', 'tanggal_npwp', 'nppkp', 'tanggal_nppkp','no_pengukuhan_kaber', 'output_tax',
        'term_of_payment', 'lead_time', 'credit_limit', 'ccar', 'bank_garansi',
        'area', 'join_date', 'status', 'status_approval', 'route_to', 'created_by'
    ];

    protected $casts = [
        'tanggal_npwp' => 'date',
        'tanggal_nppkp' => 'date',
        'join_date' => 'date',
        'credit_limit' => 'decimal:2',
    ];

    protected $table = 'customers';


    public function requisitions()
    {
        return $this->hasMany(Requisition::class, 'customer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(CustomerFile::class, 'customer_id');
    }

    public function bankGaransis()
    {
        return $this->hasMany(BankGaransi::class, 'customer_id');
    }

    public function bgRecommendations()
    {
        return $this->hasMany(BgRecommendation::class, 'customer_id');
    }

    public function creditLimits()
    {
        return $this->hasMany(CreditLimit::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accountGroup()
    {
        return $this->belongsTo(AccountGroup::class, 'account_group');
    }

    public function customerClass()
    {
        return $this->belongsTo(CustomerClass::class, 'customer_class');
    }

}
