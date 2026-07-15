<?php

namespace App\Models\Customer;

use App\Models\Requisition\Requisition;
use App\Models\Customer\CustomerFile;
use App\Models\BG\BankGaransi;
use App\Models\BG\BgRecommendation;
use App\Models\Customer\CreditLimit;
use App\Models\Customer\AccountGroup;
use App\Models\Customer\Sales;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $customer) {

            if (is_string($customer->bank_garansi) && strtoupper($customer->bank_garansi) === 'YA') {
                if (!$customer->approved_credit_limit) {
                    $customer->approved_credit_limit = $customer->credit_limit;
                }
            }
        });
    }

    protected $fillable = ['user_id',
        'code', 'no_pkd', 'pic', 'name','sort_name', 'customer_class', 'account_group',
        'address1', 'address2', 'address3', 'city', 'postal_code', 'country',
        'shipping_to_name', 'shipping_to_address',
        'purchasing_manager_name', 'purchasing_manager_email', 'purchasing_manager_telepon',
        'finance_manager_name', 'finance_manager_email', 'finance_manager_telepon',
        'penagihan_nama_kontak', 'penagihan_telepon', 'penagihan_address','surat_menyurat_address', 'email', 'tax_contact_name', 'tax_contact_email', 'tax_contact_phone',
        'npwp', 'tanggal_npwp', 'nppkp', 'tanggal_nppkp','no_pengukuhan_kaber', 'output_tax',
        'term_of_payment', 'lead_time', 'credit_limit', 'ccar', 'bank_garansi',
        'area', 'join_date', 'status', 'status_approval', 'route_to', 'pembagian', 'customer_total',
        'virtual_account',
        'payment_days',
        'payment_date',
        'faktur_days',
        'faktur_date',
        'created_by',
        'approved_credit_limit'
    ];

    protected $casts = [
       'tanggal_npwp' => 'date',
        'tanggal_nppkp' => 'date',
        'join_date' => 'date',
        'credit_limit' => 'decimal:2',
        'payment_days' => 'array',
        'payment_date' => 'array',
        'faktur_days' => 'array',
        'faktur_date' => 'array',
        'approved_credit_limit' => 'decimal:2',
    ];

    protected $table = 'customers';


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

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'user_id');
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

    public function items() {
        return $this->hasMany(CustomerItem::class, 'customer_id');
    }

    public function scopeAllowedForUser($query, $user)
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $query;
        }

        $salesAccountGroupIds = Sales::where('user_id', $user->id)
            ->pluck('account_group_id')
            ->toArray();

        if (empty($salesAccountGroupIds)) {
            // Jika bukan admin dan tidak punya pemetaan di tabel sales, fallback ke data buatan sendiri
            return $query->where('customers.created_by', $user->id);
        }

        $accountGroups = AccountGroup::whereIn('id', $salesAccountGroupIds)->get();
        
        $allowedAccountGroupIds = [];
        $isWest = false;
        $isEast = false;
        
        foreach ($accountGroups as $ag) {
            $allowedAccountGroupIds[] = $ag->id;
            $name = strtoupper($ag->name_account_group);
            if (str_starts_with($name, 'REGION 1') || str_starts_with($name, 'REGION 3')) {
                $isWest = true;
            }
            if (str_starts_with($name, 'REGION 2') || str_starts_with($name, 'REGION 4')) {
                $isEast = true;
            }
        }
        
        if ($isWest) {
            $westIds = AccountGroup::where('name_account_group', 'LIKE', 'REGION 1%')
                ->orWhere('name_account_group', 'LIKE', 'REGION 3%')
                ->pluck('id')->toArray();
            $allowedAccountGroupIds = array_merge($allowedAccountGroupIds, $westIds);
        }
        
        if ($isEast) {
            $eastIds = AccountGroup::where('name_account_group', 'LIKE', 'REGION 2%')
                ->orWhere('name_account_group', 'LIKE', 'REGION 4%')
                ->pluck('id')->toArray();
            $allowedAccountGroupIds = array_merge($allowedAccountGroupIds, $eastIds);
        }
        
        $allowedAccountGroupIds = array_unique($allowedAccountGroupIds);

        return $query->whereIn('customers.account_group', $allowedAccountGroupIds);
    }
}
