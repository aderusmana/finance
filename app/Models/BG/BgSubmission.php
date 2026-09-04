<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BgSubmission extends Model
{
    use HasFactory;

    protected $table = 'bg_submissions';

    protected $fillable = [
        'bg_recommendation_id', 'form_code', 'signed_document_path',
        'custom_address', 'bg_number', 'bg_nominal', 'exp_date',
        'warkat_file_path', 'submission_type', 'validated_by', 'validated_at',
        'submitted_at', 'upload_completed_at', 'status', 'token'
    ];

    protected $casts = [
        'total_nominal' => 'decimal:2',
        'bg_nominal' => 'decimal:2',
        'exp_date' => 'date',
        'submitted_at' => 'datetime',
        'upload_completed_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function recommendation()
    {
        return $this->belongsTo(BgRecommendation::class, 'bg_recommendation_id');
    }

    public function lampiranD()
    {
        return $this->hasOne(LampiranD::class, 'bg_submission_id');
    }

    public function bankGaransi()
    {
        return $this->hasOne(BankGaransi::class, 'bg_submission_id');
    }

    public function validator()
    {
        return $this->belongsTo(\App\Models\User::class, 'validated_by');
    }
}
