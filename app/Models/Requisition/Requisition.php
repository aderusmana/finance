<?php

namespace App\Models\Requisition;

use App\Models\Master\Customer;
use App\Models\Master\Revision;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // [DITAMBAHKAN] Baris ini memperbaiki error
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Requisition extends Model
{
    // [DITAMBAHKAN] Trait yang menyebabkan error kini sudah diimpor dengan benar
    use HasFactory, LogsActivity;

    protected $table = 'requisitions';

    protected $fillable = [
        'requester_nik',
        'customer_id',
        'no_srs',
        'account',
        'cost_center',
        'request_date',
        'end_date',
        'revision_id',
        'category',
        'sub_category',
        'route_to',
        'status',
        'objectives',
        'estimated_potential',
        'reason_for_replacement',
        'print_batch',
    ];

    protected $casts = [
        'request_date' => 'date',
        'end_date' => 'date',
        'print_batch' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'route_to', 'objectives', 'estimated_potential'])
            ->setDescriptionForEvent(fn(string $eventName) => "Requisition has been {$eventName}")
            ->useLogName('Requisition');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke Revision
    public function revision()
    {
        return $this->belongsTo(Revision::class, 'revision_id');
    }

    // Relasi ke RequisitionItem
    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'requisition_id');
    }

    // Relasi ke User (requester)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_nik', 'nik');
    }

    public function requisitionSpecial()
    {
        return $this->hasOne(RequisitionSpecial::class);
    }

     public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalLog::class, 'requisition_id', 'id')->orderBy('level', 'asc');
    }

    // Relasi ke Tracking (untuk status terakhir)
    public function tracking()
    {
        return $this->hasOne(Tracking::class);
    }

    // Relasi ini untuk mendapatkan semua riwayat tracking
    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }

    // Relasi ke Payment
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Relasi ke ComplainImage
    public function complainImages()
    {
        return $this->hasMany(ComplainImage::class);
    }
}