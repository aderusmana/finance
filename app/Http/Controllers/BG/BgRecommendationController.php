<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BgPeriod;
use App\Models\BG\BgRecommendation;
use App\Models\BG\BgSubmission;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use App\Models\BG\Tax;
use App\Models\BG\BgLimitRule;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerFillFormNotification;
use App\Models\User;
use App\Models\Master\ApprovalLog;
use App\Mail\SalesRecommendationApprovalMail;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class BgRecommendationController extends Controller
{
    private function getLimitRulePercent($customer) {
        if (!$customer || !$customer->join_date) {
            return 0;
        }

        $joinDate = Carbon::parse($customer->join_date);
        $years    = (int) abs($joinDate->diffInYears(Carbon::now()));

        $rule = BgLimitRule::where('min_year', '<=', $years)
                        ->where('max_year', '>=', $years)
                        ->orderBy('min_year', 'desc')
                        ->first();

        return $rule ? (float)$rule->percentage : 0;
    }

    public function index(Request $request)
    {
        $taxConfig = Tax::first();
        if(!$taxConfig) {
            $taxConfig = (object)['id' => null, 'value' => 0.11];
        }

        if ($request->ajax()) {
            if ($request->has('type') && $request->type == 'expiring') {
                $query = BgRecommendation::with(['customer'])
                    ->where('bg_recommendations.status', '=', 'pending');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('bg_number', function($row) {
                        $bg = BankGaransi::where('customer_id', $row->customer_id)->latest()->first();
                        $num = $bg ? $bg->bg_number : '-';
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="ph-bold ph-file-text fs-5"></i>
                                    </div>
                                    <span class="fw-bold text-dark fs-6">'.$num.'</span>
                                </div>';
                    })
                    ->addColumn('customer_name', function($row) {
                        $name = $row->customer->name ?? '-';
                        $initial = substr($name, 0, 1);
                        return '<div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold border border-warning border-opacity-25 shadow-sm" style="width: 36px; height: 36px; font-size: 15px;">'.$initial.'</div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">'.$name.'</h6>
                                    </div>
                                </div>';
                    })
                    ->editColumn('current_bg', function($row) {
                        return '<div class="d-flex align-items-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 shadow-sm" style="font-size: 0.9rem;">
                                        <i class="ph-bold ph-wallet me-1"></i> Rp ' . number_format($row->current_bg, 0, ',', '.') . '
                                    </span>
                                </div>';
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="d-flex justify-content-center"><button type="button" class="btn btn-primary btn-process text-white fw-bold d-flex align-items-center gap-2" data-id="'.$row->id.'" style="border-radius: 6px;"><i class="ph-bold ph-calculator fs-5"></i> Process</button></div>';
                    })
                    ->rawColumns(['bg_number', 'customer_name', 'current_bg', 'action'])->make(true);
            }

            if ($request->has('type') && $request->type == 'history') {
                $query = BgRecommendation::with('customer')
                    ->where('bg_recommendations.status', '!=', 'pending')
                    ->orderBy('updated_at', 'desc');

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->addColumn('bg_number', function($row) {
                        $bg = BankGaransi::where('customer_id', $row->customer_id)->latest()->first();
                        $num = $bg ? $bg->bg_number : '-';
                        return '<div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 32px; height: 32px;">
                                        <i class="ph-bold ph-file-text"></i>
                                    </div>
                                    <span class="fw-bold text-dark">'.$num.'</span>
                                </div>';
                    })
                    ->addColumn('customer_name', function($row) {
                        $name = $row->customer->name ?? '-';
                        $initial = substr($name, 0, 1);
                        return '<div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold border border-info border-opacity-25 shadow-sm" style="width: 32px; height: 32px; font-size: 13px;">'.$initial.'</div>
                                    <span class="fw-bold text-dark">'.$name.'</span>
                                </div>';
                    })
                    ->editColumn('average', fn($row) => '<span class="text-muted fw-bold">Rp ' . number_format($row->average, 0, ',', '.') . '</span>')
                    ->editColumn('recommended_credit_limit', fn($row) => '<span class="text-dark fw-bold">Rp ' . number_format($row->recommended_credit_limit, 0, ',', '.') . '</span>')
                    ->editColumn('set_bg', fn($row) => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="ph-bold ph-check-circle me-1"></i> Rp ' . number_format($row->set_bg, 0, ',', '.') . '</span>')
                    ->editColumn('status', function($row){
                        if ($row->status == 'waiting_approval_sales') {
                            return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-hourglass me-1"></i>Waiting Sales Approval</span>';
                        }
                        if ($row->status == 'rejected_by_sales') {
                            $reasonAttr = htmlspecialchars($row->rejection_reason ?? 'No reason specified', ENT_QUOTES);
                            return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill cursor-pointer btn-view-reject-reason" data-reason="'.$reasonAttr.'" title="Click to view reason"><i class="ph-bold ph-x-circle me-1"></i>Rejected by Sales</span>';
                        }
                        if ($row->status == 'process') {
                            return '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-paper-plane-tilt me-1"></i>Sent to Customer</span>';
                        }
                        if ($row->status == 'waiting_upload') {
                            return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-upload-simple me-1"></i>Waiting Upload</span>';
                        }
                        $color = ($row->status == 'completed' || $row->status == 'approved') ? 'success' : 'primary';
                        $icon = ($row->status == 'completed' || $row->status == 'approved') ? 'ph-check-circle' : 'ph-spinner-gap';
                        return '<span class="badge bg-'.$color.' bg-opacity-10 text-'.$color.' border border-'.$color.' border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold '.$icon.' me-1"></i>'.ucfirst(str_replace('_', ' ', $row->status)).'</span>';
                    })
                    ->addColumn('action', function($row){
                        $user = auth()->user();
                        $btn = '<div class="d-flex justify-content-center align-items-center gap-1">';

                        if ($row->status == 'waiting_approval_sales') {
                            $isSales = $user && ($user->hasRole(['dep-SNM', 'head-SNM', 'super-admin']) || str_contains(strtolower($user->email), 'ronal'));
                            if ($isSales) {
                                $btn .= '<button type="button" class="btn btn-sm btn-warning btn-sales-review text-white fw-bold d-flex align-items-center gap-1 px-2 py-1 shadow-sm" data-id="'.$row->id.'" style="border-radius: 6px; font-size: 0.8rem;"><i class="ph-bold ph-stamp"></i> Review Sales</button>';
                            } else {
                                $btn .= '<span class="badge bg-light text-muted border px-2 py-1"><i class="ph-bold ph-hourglass me-1"></i>Waiting Sales</span>';
                            }
                        } elseif ($row->status == 'rejected_by_sales') {
                            $btn .= '<button type="button" class="btn btn-sm btn-danger btn-resubmit-duplicate text-white fw-bold d-flex align-items-center gap-1 px-2 py-1 shadow-sm" data-id="'.$row->id.'" style="border-radius: 6px; font-size: 0.8rem;"><i class="ph-bold ph-copy"></i> Resubmit (Duplicate)</button>';
                        } else {
                            $btn .= '<button type="button" class="btn btn-sm btn-warning btn-edit-rec text-white fw-bold d-flex align-items-center gap-1 px-2 py-1 shadow-sm" data-id="'.$row->id.'" style="border-radius: 6px; font-size: 0.8rem;"><i class="ph-bold ph-pencil-simple"></i> Edit</button>';
                        }

                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['bg_number', 'customer_name', 'average', 'recommended_credit_limit', 'set_bg', 'status', 'action'])
                    ->make(true);
            }
        }

        $customers = Customer::orderBy('name')->get();
        return view('page.bg.bg_recommendations.index', compact('taxConfig', 'customers'));
    }

    public function show($id) {
        $rec = BgRecommendation::with(['customer', 'tax', 'periods'])->findOrFail($id);

        if ($rec->top == 0 && $rec->customer) {
            $rec->top = $rec->customer->term_of_payment;
        }
        if ($rec->lead_time == 0 && $rec->customer) {
            $rec->lead_time = $rec->customer->lead_time;
        }

        $data = $rec->toArray();
        $data['calculated_rule_percent'] = $this->getLimitRulePercent($rec->customer);
        $data['tax_value'] = $rec->tax ? $rec->tax->value : 0.11;
        $data['raw_current_bg'] = $rec->current_bg;

        return response()->json($data);
    }

    public function savePeriods(Request $request, $id)
    {
        $request->validate([
            'periods' => 'required|array',
            'periods.*.date' => 'required|date',
            'periods.*.amount' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            // Hapus periode lama untuk ID rekomendasi ini
            BgPeriod::where('bg_recommendation_id', $id)->delete();

            $totalAmount = 0;
            $periodsData = [];

            // Siapkan data untuk bulk insert (lebih efisien)
            foreach ($request->periods as $period) {
                $periodsData[] = [
                    'bg_recommendation_id' => $id,
                    'period_date'          => $period['date'], // Format Y-m-d
                    'amount'               => $period['amount'],
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
                $totalAmount += $period['amount'];
            }

            BgPeriod::insert($periodsData);

            // Update average sementara di tabel parent agar sinkron
            $rec = BgRecommendation::findOrFail($id);
            $rec->fill(['average' => $totalAmount])->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Period details have been successfully saved.',
                'total_average' => $totalAmount // Kembalikan nilai total untuk JS
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'average' => 'required|numeric',
            'set_bg'  => 'required|numeric',
            'credit_limit_updated' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $rec = BgRecommendation::with(['customer', 'tax'])->findOrFail($id);

            $top        = (float)$rec->top;
            if($top == 0 && $rec->customer) $top = $rec->customer->term_of_payment;

            $leadTime   = (float)$rec->lead_time;
            if($leadTime == 0 && $rec->customer) $leadTime = $rec->customer->lead_time;

            $inflation  = 130;
            $avg        = (float)$request->average;
            $setBg      = (float)$request->set_bg;
            $taxRate    = $rec->tax ? ($rec->tax->value / 100) : 0.11;
            if ($rec->tax && $rec->tax->value >= 1) {
                 $taxRate = $rec->tax->value / 100;
            } elseif ($rec->tax) {
                 $taxRate = $rec->tax->value;
            }
            $rulePercent = $this->getLimitRulePercent($rec->customer);

            $estPpnValue = $avg * $taxRate;
            $timeFactor = $top > 0 ? ($top + $leadTime) / $top : 1;
            $inflationFactor = $inflation / 100;
            $recLimit = $estPpnValue * $timeFactor * $inflationFactor;
            
            $activeRule = $rulePercent > 0 ? $rulePercent : 100;
            $fkLimit = $recLimit * ($activeRule / 100);
            $rounded = round($fkLimit, -6);

            if ($request->filled('credit_limit_updated')) {
                $limitUpdated = (float) $request->credit_limit_updated;
            } else {
                $limitUpdated = $setBg / ($activeRule / 100);
            }

            $notes = $request->notes;
            if (empty($notes)) {
                $notes = "Auto-generated on: " . Carbon::now()->format('d M Y');
            }

            // Route to Sales Approval (Pak Ronal)
            $rec->update([
                'average'                   => $avg,
                'top'                       => $top,
                'inflation'                 => $inflation,
                'lead_time'                 => $leadTime,
                'recommended_credit_limit'  => $recLimit,
                'fk_with_limit'             => $fkLimit,
                'rounded_credit_limit'      => $rounded,
                'set_bg'                    => $setBg,
                'credit_limit_updated'      => $limitUpdated,
                'status'                    => 'waiting_approval_sales',
                'notes'                     => $notes,
                'token'                     => null, // Will be generated upon Pak Ronal approval
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($rec)
                ->useLog('bg_recommendation')
                ->event('submit_to_sales')
                ->withProperties([
                    'customer' => $rec->customer->name,
                    'calculated_limit' => $rounded,
                    'final_credit_limit' => $limitUpdated,
                    'set_bg_nominal' => $setBg,
                    'inflation_rate' => $inflation . '%'
                ])
                ->log("Admin submitted BG recommendation (SET BG: Rp " . number_format($setBg, 0, ',', '.') . ") for Sales Approval (Pak Ronal)");

            // Send Email Approval & Notification to Pak Ronal (dep-SNM)
            try {
                $ronal = User::role(['dep-SNM', 'head-SNM'])->first();
                if (!$ronal) {
                    $ronal = User::where('name', 'like', '%ronal%')->orWhere('email', 'like', '%ronal%')->first();
                }

                if ($ronal) {
                    // Create or update ApprovalLog for Email Quick Action
                    $approvalLog = ApprovalLog::create([
                        'category'     => 'BG',
                        'sub_category' => 'Sales Recommendation',
                        'related_id'   => $rec->id,
                        'approver_nik' => $ronal->nik ?? 'RONAL',
                        'status'       => 'Pending',
                        'level'        => 1,
                        'token'        => Str::random(60),
                    ]);

                    // Send Email to Pak Ronal directly
                    if ($ronal->email && filter_var($ronal->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($ronal->email)->send(new SalesRecommendationApprovalMail($approvalLog, $rec, $ronal));
                        } catch (\Exception $mailEx) {
                            \Log::error('Sales Approval Email Error: ' . $mailEx->getMessage());
                        }
                    }

                    // Immediate in-app notification (bypassing delayed queue)
                    $salesApprovers = User::role(['dep-SNM', 'head-SNM'])->get();
                    if ($salesApprovers->isEmpty()) {
                        $salesApprovers = collect([$ronal]);
                    }

                    Notification::sendNow($salesApprovers, new SystemNotification(
                        'Permohonan Approval Rekomendasi BG',
                        "Rekomendasi BG untuk <b>{$rec->customer->name}</b> (SET BG: Rp " . number_format($setBg, 0, ',', '.') . ") memerlukan persetujuan Anda.",
                        route('bg-recommendations.index'),
                        'ph-signature',
                        'warning'
                    ));
                }
            } catch (\Exception $notifEx) {
                \Log::error('Notif Sales Approver Error: ' . $notifEx->getMessage());
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Recommendation submitted for Sales Approval (Pak Ronal).'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function salesApprove(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['dep-SNM', 'head-SNM', 'super-admin']) && !str_contains(strtolower($user->email), 'ronal')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only dep-SNM (Pak Ronal) can approve.'], 403);
        }

        DB::beginTransaction();
        try {
            $rec = BgRecommendation::with(['customer', 'periods', 'tax'])->findOrFail($id);

            if ($rec->status !== 'waiting_approval_sales') {
                return response()->json(['success' => false, 'message' => 'Recommendation is not waiting for Sales approval.'], 400);
            }

            $token = Str::random(64);
            $rec->update([
                'status'            => 'process',
                'token'             => $token,
                'sales_approved_by' => $user->id,
                'sales_approved_at' => now(),
            ]);

            activity()
                ->causedBy($user)
                ->performedOn($rec)
                ->useLog('bg_recommendation')
                ->event('sales_approve')
                ->withProperties([
                    'customer' => $rec->customer->name,
                    'set_bg'   => $rec->set_bg,
                    'credit_limit' => $rec->credit_limit_updated
                ])
                ->log("Sales (Pak Ronal) approved BG Recommendation. Token generated and link sent to customer.");

            // Send email to customer with portal link
            $custEmail = $rec->customer->email ?? null;
            if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($custEmail)->queue(new CustomerFillFormNotification($rec));
            }

            // Notify Admin-RTM
            try {
                $admins = User::role(['admin-rtm', 'super-admin'])->get();
                Notification::send($admins, new SystemNotification(
                    'BG Recommendation Approved by Sales',
                    "Recommendation for <b>{$rec->customer->name}</b> has been approved by Pak Ronal and sent to the customer.",
                    route('bg-recommendations.index'),
                    'ph-check-circle',
                    'success'
                ));
            } catch (\Exception $notifEx) {
                \Log::error('Notif Admin Error: ' . $notifEx->getMessage());
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Recommendation approved successfully & link sent to customer.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function salesReject(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['dep-SNM', 'head-SNM', 'super-admin']) && !str_contains(strtolower($user->email), 'ronal')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only dep-SNM (Pak Ronal) can reject.'], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:3'
        ]);

        DB::beginTransaction();
        try {
            $rec = BgRecommendation::with(['customer'])->findOrFail($id);

            if ($rec->status !== 'waiting_approval_sales') {
                return response()->json(['success' => false, 'message' => 'Recommendation is not waiting for Sales approval.'], 400);
            }

            $rec->update([
                'status'           => 'rejected_by_sales',
                'rejection_reason' => $request->rejection_reason,
                'token'            => null
            ]);

            activity()
                ->causedBy($user)
                ->performedOn($rec)
                ->useLog('bg_recommendation')
                ->event('sales_reject')
                ->withProperties([
                    'customer' => $rec->customer->name,
                    'reason'   => $request->rejection_reason
                ])
                ->log("Sales (Pak Ronal) rejected BG Recommendation. Reason: " . $request->rejection_reason);

            // Notify Admin-RTM
            try {
                $admins = User::role(['admin-rtm', 'super-admin'])->get();
                Notification::send($admins, new SystemNotification(
                    'BG Recommendation Rejected by Sales',
                    "Recommendation for <b>{$rec->customer->name}</b> was rejected by Pak Ronal. Reason: <i>\"{$request->rejection_reason}\"</i>",
                    route('bg-recommendations.index'),
                    'ph-x-circle',
                    'danger'
                ));
            } catch (\Exception $notifEx) {
                \Log::error('Notif Admin Error: ' . $notifEx->getMessage());
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Recommendation rejected. Reason recorded for Admin-RTM revision.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function resubmitDuplicate($id)
    {
        DB::beginTransaction();
        try {
            $oldRec = BgRecommendation::with('periods')->findOrFail($id);

            if ($oldRec->status !== 'rejected_by_sales') {
                return response()->json(['success' => false, 'message' => 'Only rejected recommendations can be duplicated for resubmit.'], 400);
            }

            $newRec = BgRecommendation::create([
                'customer_id'              => $oldRec->customer_id,
                'parent_recommendation_id' => $oldRec->id,
                'tax_id'                   => $oldRec->tax_id,
                'average'                  => $oldRec->average,
                'top'                      => $oldRec->top,
                'lead_time'                => $oldRec->lead_time,
                'inflation'                => $oldRec->inflation,
                'recommended_credit_limit' => $oldRec->recommended_credit_limit,
                'fk_with_limit'            => $oldRec->fk_with_limit,
                'rounded_credit_limit'     => $oldRec->rounded_credit_limit,
                'current_bg'               => $oldRec->current_bg,
                'set_bg'                   => $oldRec->set_bg,
                'credit_limit_updated'     => $oldRec->credit_limit_updated,
                'status'                   => 'pending',
                'notes'                    => 'Resubmitted duplicate from #' . $oldRec->id . ' (Prev: ' . $oldRec->rejection_reason . ')',
            ]);

            foreach ($oldRec->periods as $p) {
                BgPeriod::create([
                    'bg_recommendation_id' => $newRec->id,
                    'period_date'          => $p->period_date,
                    'amount'               => $p->amount,
                ]);
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($newRec)
                ->useLog('bg_recommendation')
                ->event('duplicate_resubmit')
                ->log("Admin duplicated rejected recommendation #{$oldRec->id} into new draft #{$newRec->id} for resubmission.");

            DB::commit();
            return response()->json([
                'success' => true,
                'new_id'  => $newRec->id,
                'message' => 'Duplicate recommendation created successfully. Please adjust values and resubmit.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        BgRecommendation::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data deleted']);
    }
}
