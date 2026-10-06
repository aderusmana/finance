<?php

namespace App\Models\Customer;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DistributorDocumentAttachment extends Model
{
    use HasFactory;

    protected $table = 'distributor_document_attachments';

    protected $fillable = [
        'distributor_document_id',
        'distributor_id',
        'doc_type',
        'month',
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
        'month' => 'integer',
        'transaction_date' => 'date',
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url', 'human_file_size', 'type_label', 'month_name'];

    // Relation to Header DistributorDocument (registration year)
    public function document()
    {
        return $this->belongsTo(DistributorDocument::class, 'distributor_document_id');
    }

    // Relation to Distributor directly
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    // Relation to User uploader
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessor: document year (from parent header or transaction date)
    public function getYearAttribute(): ?int
    {
        if ($this->relationLoaded('document') && $this->document) {
            return $this->document->year;
        }

        if ($this->transaction_date) {
            return (int) $this->transaction_date->format('Y');
        }

        return $this->document?->year ?? ($this->created_at ? (int) $this->created_at->format('Y') : null);
    }

    // Accessor: public URL file
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    // Accessor: Human readable file size (KB/MB)
    public function getHumanFileSizeAttribute(): string
    {
        if (! $this->file_path || $this->file_size <= 0) {
            return '-';
        }

        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    // Accessor: Document type Label
    public function getTypeLabelAttribute(): string
    {
        return match ($this->doc_type) {
            'bupot' => 'Bukti Potong',
            'transfer' => 'Penjelasan Transfer',
            'top_insentif' => 'TOP Insentif',
            default => ucfirst((string) $this->doc_type),
        };
    }

    // Accessor: Indonesian month name
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

    // Scope: Document type Filter
    public function scopeOfType($query, string $type)
    {
        return $query->where('doc_type', $type);
    }

    // Scope: Get running transfer documents (cross-year from archive to present)
    public function scopeRunningTransfers($query, ?int $distributorId = null)
    {
        $query->where('doc_type', 'transfer')
            ->whereNotNull('file_path');

        if ($distributorId) {
            $query->where('distributor_id', $distributorId);
        }

        return $query->orderByDesc('transaction_date')->orderByDesc('created_at');
    }
}
