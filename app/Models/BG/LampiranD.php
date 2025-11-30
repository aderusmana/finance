<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LampiranD extends Model
{
    use HasFactory;

    protected $table = 'lampiran_d';

    protected $fillable = [
        'bg_submission_id', 'version_latest', 'active_version_id', 'created_by'
    ];

    public function submission()
    {
        return $this->belongsTo(BgSubmission::class, 'bg_submission_id');
    }

    public function versions()
    {
        return $this->hasMany(LampiranDVersion::class, 'lampiran_d_id');
    }

    public function activeVersion()
    {
        return $this->belongsTo(LampiranDVersion::class, 'active_version_id');
    }
}
