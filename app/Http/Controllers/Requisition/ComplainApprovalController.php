<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Jobs\sendMailComplain;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\Tracking;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class ComplainApprovalController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        if(!$user->can('view approval-path')){
            abort(403);
        }
        return view('page.complain.approvals.index');
    }

    public function getData($id = null)
    {
        try{
            $user = Auth::user();

            $complainIds = Requisition::where('category', 'Complain')->pluck('id');

            if ($complainIds->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada requisition complain dalam sistem.',
                    'data' => []
                ], 200);
            }

            $query = ApprovalLog::whereIn('requisition_id', $complainIds);

            if (!$user->hasRole('super-admin')) {
                $query->where('approver_nik', $user->nik)
                    ->whereNotNull('token')
                    ->where(function ($query) {
                        $query->whereRaw('level = (
                            SELECT MIN(level) 
                            FROM approval_logs a2 
                            WHERE a2.requisition_id = approval_logs.requisition_id 
                            AND a2.token IS NOT NULL
                        )');
                    });
            } else {
                $query->whereRaw('level = (
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM approval_logs a2 
                            WHERE a2.requisition_id = approval_logs.requisition_id 
                            AND a2.token IS NOT NULL
                        )
                        THEN (
                            SELECT MIN(level) FROM approval_logs a2 
                            WHERE a2.requisition_id = approval_logs.requisition_id 
                            AND a2.token IS NOT NULL
                        )
                        ELSE (
                            SELECT MAX(level) FROM approval_logs a3 
                            WHERE a3.requisition_id = approval_logs.requisition_id
                        )
                    END
                )');
            }

            $data = $query->with([
                'requisition' => function ($query) {
                    $query->with(['customer', 'requester']);
                }
            ])
                ->get();
            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada requisition complain yang menunggu approval dari Anda.',
                    'data' => []
                ], 200);
            }

            // Format data untuk response
            $formattedData = $data->map(function ($approval) {
                $requisition = $approval->requisition;
                
                return [
                    'id' => $approval->id,
                    'requisition_id' => $approval->requisition_id,
                    'requisition_number' => $requisition->no_srs ?? null,
                    'approver_nik' => $approval->approver_nik,
                    'level' => $approval->level,
                    'status' => $approval->status,
                    'token' => $approval->token,
                    'updated_at' => $approval->updated_at,
                    'requisition_details' => [
                        'id' => $requisition->id ?? null,
                        'requisition_number' => $requisition->no_srs ?? null,
                        'customer_name' => $requisition->customer->name ?? 'N/A',
                        'requester_name' => $requisition->requester->name ?? 'N/A',
                        'route_to' => $requisition->route_to ?? 'N/A',
                        'customer_id' => $requisition->customer_id ?? null,
                        'category' => $requisition->category ?? null,
                        'updated_at' => $requisition->updated_at ?? null,
                        'status' => $requisition->status ?? null,
                    ]
                ];
            });

            return response()->json([
                'message' => 'Data approval complain berhasil ditemukan.',
                'data' => $formattedData,
                'total' => $formattedData->count(),
                'is_admin' => $user->hasRole('super-admin')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function resendApprovalEmail(Request $request, $token)
    {
        // Cari log approval yang masih pending berdasarkan token LAMA
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return response()->json(['success' => false, 'message' => 'This approval task is no longer valid or has been processed.'], 404);
        }

        // Ambil data yang diperlukan
        $requisition = $approvalLog->requisition;
        $approver = $approvalLog->approver;

        if (!$requisition || !$approver) {
            return response()->json(['success' => false, 'message' => 'Associated data not found.'], 404);
        }

        try {
            // Buat token baru dan update ke database
            $newToken = bin2hex(random_bytes(16));
            $approvalLog->update(['token' => $newToken]);

            // Kirim ulang email dengan men-dispatch job menggunakan TOKEN BARU
            sendMailComplain::dispatch($approver, $requisition, $approvalLog);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($requisition)
                ->inLog('complain')
                ->event('resend')
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent(), 'requisition_no' => $requisition->no_srs])
                ->log('Resent approval email to ' . $approver->name . ' with a new token.');

            return response()->json(['success' => true, 'message' => 'Approval email has been successfully resent to ' . $approver->name . '.']);
        } catch (\Exception $e) {
            FacadesLog::error('Failed to resend email for token ' . $token . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend email. Please check the system logs.'], 500);
        }
    }
}
