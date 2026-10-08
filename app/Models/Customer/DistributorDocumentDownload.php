<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class DistributorDocumentDownload extends Model
{
    use HasFactory;

    protected $table = 'distributor_document_downloads';

    public $timestamps = false;

    protected $fillable = [
        'distributor_document_attachment_id',
        'distributor_id',
        'ip_address',
        'user_agent',
        'downloaded_via',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(DistributorDocumentAttachment::class, 'distributor_document_attachment_id');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    /**
     * Record a document download audit log.
     */
    public static function record(
        int $attachmentId,
        int $distributorId,
        string $via,
        ?Request $request = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'distributor_document_attachment_id' => $attachmentId,
            'distributor_id' => $distributorId,
            'ip_address' => $ip ?? $request?->ip(),
            'user_agent' => $userAgent ?? $request?->userAgent(),
            'downloaded_via' => $via,
            'downloaded_at' => now(),
        ]);
    }
}
