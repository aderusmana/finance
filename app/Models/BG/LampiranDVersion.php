<?php

namespace App\Models\BG;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LampiranDVersion extends Model
{
    use HasFactory;

    protected $table = 'lampiran_d_versions';

    protected $fillable = [
        'lampiran_d_id', 'version_no', 'data_snapshot', 'file_path', 'generated_by', 'generated_at', 'remarks'
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'generated_at' => 'datetime',
    ];

    public function lampiranD()
    {
        return $this->belongsTo(LampiranD::class, 'lampiran_d_id');
    }

    public function generator()
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by');
    }
}
