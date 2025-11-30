<?php

namespace App\Models\Requisition;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalPath extends Model
{
    protected $table = 'approval_paths';

    protected $fillable = [
        'category',
        'sub_category',
        'sequence_approvers',
    ];

    protected $casts = [
        'sequence_approvers' => 'array',
    ];

    public function getNik()
    {
        $niks = $this->sequence_approvers;

        if (empty($niks) || !is_array($niks)) {
            return collect();
        }

        $users = User::whereIn('nik', $niks)->get();

        return $users->sortBy(function ($user) use ($niks) {
            return array_search($user->nik, $niks);
        });
    }

}
