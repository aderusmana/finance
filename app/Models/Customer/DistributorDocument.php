<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorDocument extends Model
{
    use HasFactory;

    protected $table = 'distributor_documents';

    protected $fillable = [
        'distributor_id',
        'year',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    // Relation to parent Distributor
    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    // All physical file attachments under this year header
    public function attachments()
    {
        return $this->hasMany(DistributorDocumentAttachment::class, 'distributor_document_id');
    }

    // Specific attachments for Withholding Tax (BuPot)
    public function bupotAttachments()
    {
        return $this->hasMany(DistributorDocumentAttachment::class, 'distributor_document_id')
            ->where('doc_type', 'bupot');
    }

    // Specific attachments for TOP Incentive
    public function topInsentifAttachments()
    {
        return $this->hasMany(DistributorDocumentAttachment::class, 'distributor_document_id')
            ->where('doc_type', 'top_insentif');
    }

    // Specific attachments for Transfer Explanation
    public function transferAttachments()
    {
        return $this->hasMany(DistributorDocumentAttachment::class, 'distributor_document_id')
            ->where('doc_type', 'transfer');
    }
}
