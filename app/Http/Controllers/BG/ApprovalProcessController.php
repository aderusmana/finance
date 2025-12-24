<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\Master\ApprovalLog;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerBgReadyMail;

class ApprovalProcessController extends Controller
{
    public function process($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending') 
                          ->firstOrFail();
        
        if ($action == 'approve') {
            $log->update([
                'status' => 'Approved', 
                'updated_at' => now(),
                'token' => null
            ]);
            
            $this->finalizeSubmission($log->related_id);

            return view('page.customer_portal.form-success', ['type' => 'upload', 'title' => 'Approved Successfully']);
        }
        
        abort(404);
    }

    public function showForm($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->firstOrFail();
        
        $submission = BgSubmission::with('recommendation.customer')->findOrFail($log->related_id);
        
        $bg = BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                ->where('status', 'submitted')
                ->latest()
                ->first();

        return view('page.approval.action_lampiran', compact('token', 'action', 'submission', 'bg'));
    }

    public function submit(Request $request, $token)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->firstOrFail();

        $action = $request->action; 
        $status = ($action == 'reject') ? 'Rejected' : 'Approved';
        
        $log->update([
            'status' => $status,
            'notes' => $request->notes,
            'updated_at' => now(),
            'token' => null
        ]);

        if ($status == 'Rejected') {
            $sub = BgSubmission::find($log->related_id);
            if ($sub) {
                $sub->update(['status' => 'rejected_by_finance']);
            }
        } else {
            $this->finalizeSubmission($log->related_id);
        }

        return view('page.customer_portal.form-success', ['type' => 'upload', 'title' => 'Processed Successfully']);
    }

    private function finalizeSubmission($submissionId) {
        $sub = BgSubmission::with(['recommendation.customer', 'lampiranD'])->find($submissionId);
        
        if($sub) {
            $sub->update([
                'status' => 'completed',
                'reviewed_at' => now()
            ]);

            $customerEmail = $sub->recommendation->customer->email;
            
            $salesEmails = User::role('head-SNM')->pluck('email')->toArray();
            $financeEmails = User::role('manager-finance')->pluck('email')->toArray();

            $allRecipients = array_merge([$customerEmail], $salesEmails, $financeEmails);
            $recipients = array_unique(array_filter($allRecipients));

            foreach($recipients as $email) {
                if(!empty($email)) {
                    Mail::to($email)->queue(new CustomerBgReadyMail($sub));
                }
            }
        }
    }
}