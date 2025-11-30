<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgSubmission extends Model
{
    use HasFactory;

    protected $table = 'bg_submissions';

    protected $fillable = [
        'bg_recommendation_id', 'form_code', 'total_nominal', 'signed_document_path',
        'submitted_at', 'upload_completed_at', 'status'
    ];

    protected $casts = [
        'total_nominal' => 'decimal:2',
        'submitted_at' => 'datetime',
        'upload_completed_at' => 'datetime',
    ];

    public function recommendation()
    {
        return $this->belongsTo(BgRecommendation::class, 'bg_recommendation_id');
    }

    public function lampiranD()
    {
        return $this->hasOne(LampiranD::class, 'bg_submission_id');
    }
}
