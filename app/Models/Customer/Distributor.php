<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['customer_id', 'code', 'name', 'email'];

    protected $appends = ['email_list'];

    /**
     * Get distributor emails as an array of strings.
     *
     * @return array<string>
     */
    public function getEmailListAttribute(): array
    {
        if (empty($this->email)) {
            return [];
        }

        $emails = is_array($this->email)
            ? $this->email
            : preg_split('/[;,]+/', (string) $this->email);

        return array_values(array_filter(array_map('trim', (array) $emails)));
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'distributor_customers')
                    ->withPivot('logistic_fee')
                    ->withTimestamps();
    }
}
