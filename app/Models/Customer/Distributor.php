<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = ['customer_id', 'code', 'name', 'email', 'bupot_email'];

    protected $appends = ['email_list', 'bupot_email_list'];

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

    /**
     * Get distributor bupot emails as an array of strings.
     *
     * @return array<string>
     */
    public function getBupotEmailListAttribute(): array
    {
        if (empty($this->bupot_email)) {
            return [];
        }

        $emails = is_array($this->bupot_email)
            ? $this->bupot_email
            : preg_split('/[;,]+/', (string) $this->bupot_email);

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

    // all distributor documents
    public function documents()
    {
        return $this->hasMany(DistributorDocument::class);
    }

    // transfer explanation running document
    public function transferDocuments()
    {
        return $this->hasMany(DistributorDocument::class)
            ->where('doc_type', 'transfer')
            ->whereNotNull('file_path')
            ->orderByDesc('transaction_date')
            ->orderByDesc('year')
            ->orderByDesc('created_at');
    }

    /**
     * Get monthly document summary (BuPot & TOP Insentif) for a specific year
     */
    public function getMonthlyDocumentSummary(int $year): array
    {
        // Only get BuPot and TOP Insentif for the selected year with uploaded files
        $docs = $this->documents()
            ->whereNotNull('file_path')
            ->whereIn('doc_type', ['bupot', 'top_insentif'])
            ->where('year', $year)
            ->get();

        $summary = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthDocs = $docs->where('month', $m);
            $hasBupot = $monthDocs->where('doc_type', 'bupot')->isNotEmpty();
            $hasTop = $monthDocs->where('doc_type', 'top_insentif')->isNotEmpty();

            $status = 'empty';
            if ($hasBupot && $hasTop) {
                $status = 'complete';
            } elseif ($hasBupot || $hasTop) {
                $status = 'partial';
            }

            $summary[$m] = [
                'total' => $monthDocs->count(),
                'has_bupot' => $hasBupot,
                'has_top_insentif' => $hasTop,
                'status' => $status, // complete (hijau), partial (kuning), empty (abu-abu)
            ];
        }

        return $summary;
    }
}
