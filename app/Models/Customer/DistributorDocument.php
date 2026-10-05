<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DistributorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'year',
        'month',
        'doc_type',
        'title',
        'file_name',
        'file_path',
        'file_ext',
        'file_size',
        'mime_type',
        'transaction_date',
        'notes',
        'uploaded_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'transaction_date' => 'date',
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url', 'human_file_size', 'type_label', 'month_name'];

    // Relation to Distributor
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    // Relation to User uploader
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessor: public file URL
    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // Accessor: Human readable file size (KB/MB)
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    // Accessor: Document type label (Bupot, Transfer, TOP Insentif)
    public function getTypeLabelAttribute(): string
    {
        return match ($this->doc_type) {
            'bupot' => 'Bukti Potong',
            'transfer' => 'Penjelasan Transfer',
            'top_insentif' => 'TOP Insentif',
            default => ucfirst($this->doc_type),
        };
    }

    // Accessor: Indonesian Month Name
    public function getMonthNameAttribute(): ?string
    {
        if (! $this->month) {
            return null;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $months[$this->month] ?? "Bulan {$this->month}";
    }

    // Scope: Filter by Monthly Period (Bupot & TOP Insentif)
    public function scopePeriod($query, int $year, ?int $month = null)
    {
        $query->where('year', $year);
        if ($month) {
            $query->where('month', $month);
        }

        return $query;
    }

    // Scope: Filter document type
    public function scopeOfType($query, string $type)
    {
        return $query->where('doc_type', $type);
    }

    // Scope: Get running transfer documents (cross-year from 2020 to present)
    public function scopeRunningTransfers($query, ?int $distributorId = null)
    {
        $query->where('doc_type', 'transfer');
        if ($distributorId) {
            $query->where('distributor_id', $distributorId);
        }

        return $query->orderByDesc('transaction_date')->orderByDesc('year')->orderByDesc('created_at');
    }
}
