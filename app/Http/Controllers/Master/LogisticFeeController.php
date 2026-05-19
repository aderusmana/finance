<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\DistributorCustomer;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;
use App\Mail\LogisticFeeMail;
use App\Jobs\SendLogisticFee;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\ApprovalLog;
use App\Models\Master\LogisticFeeLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LogisticFeeController extends Controller
{
    use ApprovalTrait;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DistributorCustomer::with(['distributor', 'customer'])
                        ->select('distributor_customers.*')
                        ->orderBy('updated_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('distributor_code', function($row) {
                    $code = $row->distributor->code ?? '-';
                    return '
                        <div class="d-inline-flex align-items-center gap-1" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 8px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 1px 2px rgba(0,0,0,0.02);">
                            <i class="ph-bold ph-barcode" style="color: #64748b; font-size: 1rem;"></i>
                            <span class="fw-bold" style="color: #475569; font-size: 0.85rem; letter-spacing: 0.5px;">' . $code . '</span>
                        </div>
                    ';
                })
                ->addColumn('distributor_name', function($row) {
                    $name = $row->distributor->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15); flex-shrink: 0;">
                                <i class="ph-fill ph-buildings" style="font-size: 1.2rem;"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.95rem;">' . $name . '</span>
                        </div>
                    ';
                })
                ->addColumn('customer_code', function($row) {
                    $code = $row->customer->customer_code ?? $row->customer->code ?? '-';
                    return '
                        <div class="d-inline-flex align-items-center gap-1" style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 8px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.8), 0 1px 2px rgba(0,0,0,0.02);">
                            <i class="ph-bold ph-qr-code" style="color: #64748b; font-size: 1rem;"></i>
                            <span class="fw-bold" style="color: #475569; font-size: 0.85rem; letter-spacing: 0.5px;">' . $code . '</span>
                        </div>
                    ';
                })
                ->addColumn('customer_name', function($row) {
                    $name = $row->customer->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 38px; height: 38px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.15); flex-shrink: 0;">
                                <i class="ph-fill ph-storefront" style="font-size: 1.2rem;"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.95rem;">' . $name . '</span>
                        </div>
                    ';
                })
                ->editColumn('logistic_fee', function($row) {
                    $fee = 'Rp ' . number_format($row->logistic_fee, 0, ',', '.');

                    if($row->status == 'Pending') {
                        $proposedFee = 'Rp ' . number_format($row->proposed_fee, 0, ',', '.');
                        return '
                            <div style="background: #fffbeb; border: 1.5px dashed #fcd34d; padding: 8px 12px; border-radius: 12px; min-width: 160px; box-shadow: inset 0 2px 4px rgba(255,255,255,0.8);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-decoration-line-through fw-bold" style="color: #94a3b8; font-size: 0.8rem;">' . $fee . '</span>
                                    <i class="ph-fill ph-warning-circle" style="color: #f59e0b; font-size: 1.1rem; animation: pulse 2s infinite;"></i>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bolder" style="color: #b45309; font-size: 1.05rem;">' . $proposedFee . '</span>
                                </div>
                            </div>
                        ';
                    }

                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="background: #f0fdfa; border: 1px solid #ccfbf1; padding: 6px; border-radius: 8px; color: #0f766e;">
                                <i class="ph-bold ph-currency-circle-dollar fs-5"></i>
                            </div>
                            <span class="fw-bolder" style="color: #0f766e; font-size: 1.05rem;">' . $fee . '</span>
                        </div>
                    ';
                })
                ->addColumn('status', function($row) {
                    if ($row->status == 'Pending') {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 2rem; box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);">
                                <i class="ph-fill ph-hourglass-high me-1" style="color: #d97706; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #b45309; font-size: 0.8rem;">Pending</span>
                            </div>
                        ';
                    } elseif ($row->status == 'Rejected') {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fca5a5; border-radius: 2rem; box-shadow: 0 2px 5px rgba(220, 38, 38, 0.1);">
                                <i class="ph-fill ph-x-circle me-1" style="color: #dc2626; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #b91c1c; font-size: 0.8rem;">Rejected</span>
                            </div>
                        ';
                    }
                    return '
                        <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 2rem; box-shadow: 0 2px 5px rgba(22, 163, 74, 0.1);">
                            <i class="ph-fill ph-check-circle me-1" style="color: #16a34a; font-size: 0.9rem;"></i>
                            <span class="fw-bold" style="color: #15803d; font-size: 0.8rem;">Aktif</span>
                        </div>
                    ';
                })
                ->addColumn('route_to', function($row) {
                    if ($row->status == 'Pending' && !empty($row->route_to)) {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #7dd3fc; border-radius: 2rem; box-shadow: 0 2px 5px rgba(2, 132, 199, 0.1);">
                                <i class="ph-fill ph-paper-plane-tilt me-1" style="color: #0284c7; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #0369a1; font-size: 0.8rem;">' . $row->route_to . '</span>
                            </div>
                        ';
                    }
                    return '<div class="d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #94a3b8; border-radius: 2rem; padding: 2px 18px; box-shadow: 0 2px 5px rgba(148, 163, 184, 0.15);">
                                <span style="color: #334155; font-weight: 900; font-size: 0.95rem;">-</span>
                            </div>';
                })
                ->addColumn('action', function($row){
                    if($row->status == 'Pending') {
                        return '
                            <button class="btn d-inline-flex align-items-center justify-content-center" disabled style="background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #94a3b8; font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 0.75rem; cursor: not-allowed; width: 110px;">
                                <i class="ph-bold ph-hourglass-high me-2"></i> Waiting
                            </button>
                        ';
                    }
                    return '
                        <button class="btn btn-edit d-inline-flex align-items-center justify-content-center" data-id="'.$row->id.'" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 700; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 0.75rem; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3); width: 110px; transition: all 0.2s;">
                            <i class="ph-bold ph-pencil-simple me-2"></i> Edit
                        </button>
                    ';
                })

                ->rawColumns(['distributor_code', 'distributor_name', 'customer_code', 'customer_name', 'logistic_fee', 'status', 'route_to', 'action'])
                ->with([
                    'total_active' => DistributorCustomer::where('status', '!=', 'Pending')->count(),
                    'total_pending' => DistributorCustomer::where('status', 'Pending')->count(),
                ])
                ->make(true);
        }

        $distributors = Distributor::orderBy('name', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();

        return view('page.master.logistic-fee.index', compact('distributors', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'customer_id'    => 'required|exists:customers,id',
            'logistic_fee'   => 'required|numeric|min:0',
        ]);

        $record = DistributorCustomer::firstOrCreate(
            ['distributor_id' => $request->distributor_id, 'customer_id' => $request->customer_id],
            ['logistic_fee' => 0]
        );

        $oldFee = $record->logistic_fee;
        $newFee = $request->logistic_fee;

        $record->update(['status' => 'Pending', 'proposed_fee' => $newFee]);

        LogisticFeeLog::create([
            'distributor_customer_id' => $record->id,
            'old_fee'   => $oldFee,
            'new_fee'   => $newFee,
            'status'    => 'Requested',
            'action_by' => Auth::user()->nik,
            'notes'     => 'Request for price changes (via create new)'
        ]);

        $logs = $this->generateApprovalLogs(Auth::user(), $record->id, 'Customer', 'Logistic Fee');

        if ($logs->isNotEmpty()) {
            $levelOne = $logs->firstWhere('level', 1) ?? $logs->first();
            $firstApproverLog = (!empty($levelOne['token']))
                ? ApprovalLog::where('token', $levelOne['token'])->where('status', 'Pending')->first()
                : null;

            if ($firstApproverLog) {
                $firstApproverUser = User::where('nik', $firstApproverLog->approver_nik)->first();
                $approverName = $firstApproverUser ? $firstApproverUser->name : 'Atasan';

                $record->update([
                    'route_to' => $approverName
                ]);

                dispatch(new SendLogisticFee($firstApproverLog, $record));

                return response()->json([
                    'success' => true,
                    'message' => 'Logistic Fee application has been successfully sent to <b>' . $approverName . '</b> for approval!',
                    'approver' => $approverName
                ]);
            }
        } else {
            $record->update(['status' => 'Approved', 'logistic_fee' => $newFee, 'route_to' => '-']);

            LogisticFeeLog::create([
                'distributor_customer_id' => $record->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Approved',
                'action_by' => 'System',
                'notes'     => 'Auto-approved (Approval path not found)'
            ]);

            return response()->json(['success' => true, 'message' => 'Logistic Fee application has been automatically approved as no approval path was found.']);
        }

        return response()->json(['success' => true, 'message' => 'Logistic Fee application has been successfully sent to the approver!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['logistic_fee' => 'required|numeric|min:0']);
        $record = DistributorCustomer::findOrFail($id);

        $oldFee = $record->logistic_fee;
        $newFee = $request->logistic_fee;

        if ($oldFee == $newFee) {
            return response()->json(['success' => true, 'message' => 'There is no change in price. No approval needed.']);
        }

        $record->update(['status' => 'Pending', 'proposed_fee' => $newFee]);
        LogisticFeeLog::create([
            'distributor_customer_id' => $record->id,
            'old_fee'   => $oldFee,
            'new_fee'   => $newFee,
            'status'    => 'Requested',
            'action_by' => Auth::user()->nik,
            'notes'     => 'Request for price changes (via create new)'
        ]);

        $logs = $this->generateApprovalLogs(Auth::user(), $record->id, 'Customer', 'Logistic Fee');

        if ($logs->isNotEmpty()) {
            $levelOne = $logs->firstWhere('level', 1) ?? $logs->first();
            $firstApproverLog = (!empty($levelOne['token']))
                ? ApprovalLog::where('token', $levelOne['token'])->where('status', 'Pending')->first()
                : null;
            if ($firstApproverLog) {
                $firstApproverUser = User::where('nik', $firstApproverLog->approver_nik)->first();
                $approverName = $firstApproverUser ? $firstApproverUser->name : 'Atasan';
                $record->update(['route_to' => $approverName]);
                dispatch(new SendLogisticFee($firstApproverLog, $record));

                return response()->json(['success' => true, 'message' => 'Logistic Fee application has been successfully sent to <b>' . $approverName . '</b> for approval!', 'approver' => $approverName]);
            }
        } else {
            $record->update(['status' => 'Approved', 'logistic_fee' => $newFee, 'route_to' => '-']);

            LogisticFeeLog::create([
                'distributor_customer_id' => $record->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Approved',
                'action_by' => 'System',
                'notes'     => 'Auto-approved (Approval path not found)'
            ]);

            return response()->json(['success' => true, 'message' => 'Logistic Fee application has been automatically approved as no approval path was found.']);
        }
        return response()->json(['success' => true, 'message' => 'Logistic Fee application successfully saved.']);
    }

    public function show($id)
    {
        $logisticFee = DistributorCustomer::with(['distributor', 'customer'])->findOrFail($id);

        return response()->json([
            'id' => $logisticFee->id,
            'distributor_info' => ($logisticFee->distributor->code ?? '-') . ' - ' . ($logisticFee->distributor->name ?? '-'),
            'customer_info' => ($logisticFee->customer->customer_code ?? $logisticFee->customer->code ?? '-') . ' - ' . ($logisticFee->customer->name ?? '-'),
            'logistic_fee' => $logisticFee->status === 'Pending' ? $logisticFee->proposed_fee : $logisticFee->logistic_fee,
        ]);
    }

    public function showApprovalForm($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.master.logistic-fee.links.invalid-token');
        }

        $logisticData = DistributorCustomer::with(['distributor', 'customer'])->find($log->related_id);

        if (!$logisticData) {
            return response('Logistic Fee data not found.', 404);
        }

        return view('page.master.logistic-fee.links.form-approval', compact('log', 'logisticData', 'action'));
    }

    public function processApproval(Request $request, $token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.master.logistic-fee.links.invalid-token');
        }

        $logisticData = DistributorCustomer::find($log->related_id);

        if (!$logisticData) {
            return response('Logistic Fee data not found.', 404);
        }

        $notes = $request->input('notes');
        $isApprove = in_array($action, ['approve', 'approve_with_review']);

        if ($isApprove) {
            $log->update([
                'status' => 'Approved',
                'token' => null,
                'notes' => $notes
            ]);

            $nextLevel = $log->level + 1;
            $nextLog = ApprovalLog::where('related_id', $log->related_id)
                                  ->where('category', 'Customer')
                                  ->where('sub_category', 'Logistic Fee')
                                  ->where('level', $nextLevel)
                                  ->first();

            if ($nextLog) {
                $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                $logisticData->update([
                    'route_to' => $nextApproverUser ? $nextApproverUser->name : 'Unknown Approver'
                ]);

                dispatch(new SendLogisticFee($nextLog, $logisticData));

                return view('page.master.logistic-fee.links.success-approval')
                    ->with('successMessage', 'Approval successful! The request has been forwarded to the next approver: <b>' . ($nextApproverUser->name ?? 'Unknown Approver') . '</b>.');
            } else {
                $oldFee = $logisticData->logistic_fee;
                $newFee = $logisticData->proposed_fee;

                $logisticData->update([
                    'logistic_fee' => $newFee,
                    'proposed_fee' => $newFee,
                    'status'       => 'Approved',
                    'route_to'     => '-'
                ]);

                LogisticFeeLog::create([
                    'distributor_customer_id' => $logisticData->id,
                    'old_fee' => $oldFee,
                    'new_fee' => $newFee,
                    'status' => 'Approved',
                    'action_by' => $log->approver_nik,
                    'notes' => $notes
                ]);

                $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');
                $approverEmails = ApprovalLog::where('related_id', $logisticData->id)
                                    ->where('category', 'Customer')
                                    ->where('sub_category', 'Logistic Fee')
                                    ->get()
                                    ->map(function($item) {
                                        return User::where('nik', $item->approver_nik)->value('email');
                                    })
                                    ->filter()
                                    ->toArray();

                $allRecipients = array_filter(array_unique(array_merge([$requesterEmail], $approverEmails)));

                if (!empty($allRecipients)) {
                    try {
                        Mail::to($allRecipients)->queue(new LogisticFeeMail(
                            'completed',
                            $logisticData,
                            [
                                'oldFee' => $oldFee,
                                'newFee' => $newFee,
                                'notes' => $notes
                            ]
                        ));
                    } catch (\Exception $e) {
                        Log::error('Failed to send Final Approval email:' . $e->getMessage());
                    }
                }

                return view('page.master.logistic-fee.links.success-approval')
                    ->with('successMessage', 'Approval successful! The Logistic Fee has been updated and all relevant parties have been notified.');
            }

        } elseif ($action === 'reject') {
            $log->update([
                'status' => 'Rejected',
                'token' => null,
                'notes' => $notes
            ]);

            $oldFee = $logisticData->logistic_fee;
            $newFee = $logisticData->proposed_fee;

            $logisticData->update([
                'status'   => 'Rejected',
                'route_to' => 'Selesai (Ditolak)'
            ]);

            LogisticFeeLog::create([
                'distributor_customer_id' => $logisticData->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Rejected',
                'action_by' => $log->approver_nik,
                'notes'     => $notes
            ]);

            $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

            if (!empty($requesterEmail)) {
                try {
                    Mail::to($requesterEmail)->queue(new LogisticFeeMail(
                        'rejected',
                        $logisticData,
                        [
                            'notes' => $notes
                        ]
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send Rejected email:' . $e->getMessage());
                }
            }

            return view('page.master.logistic-fee.links.success-approval')
                ->with('successMessage', 'Logistic Fee application has been rejected and notification has been sent to the applicant.');
        }

        abort(404);
    }

    public function approvalList(Request $request)
    {
        if ($request->ajax()) {
            $data = ApprovalLog::where('category', 'Customer')
                               ->where('sub_category', 'Logistic Fee')
                               ->where('status', 'Pending')
                               ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    $date = $row->created_at->format('d M Y, H:i');
                    return '<div class="d-flex align-items-center gap-2"><i class="ph-fill ph-calendar-blank" style="color: #94a3b8; font-size: 1.1rem;"></i><span style="color: #475569; font-weight: 600; font-size: 0.85rem;">' . $date . '</span></div>';
                })
                ->addColumn('distributor', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    $name = $dc->distributor->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph-fill ph-buildings fs-6"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.9rem;">' . $name . '</span>
                        </div>
                    ';
                })
                ->addColumn('customer', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    $name = $dc->customer->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 32px; height: 32px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph-fill ph-storefront fs-6"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.9rem;">' . $name . '</span>
                        </div>
                    ';
                })
                ->addColumn('old_fee', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    $fee = 'Rp ' . number_format($dc->logistic_fee ?? 0, 0, ',', '.');
                    return '<span class="text-decoration-line-through" style="color: #94a3b8; font-weight: 600; font-size: 0.85rem;">' . $fee . '</span>';
                })
                ->addColumn('new_fee', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    $fee = 'Rp ' . number_format($dc->proposed_fee ?? 0, 0, ',', '.');
                    return '
                        <div class="d-inline-flex align-items-center px-2 py-1" style="background-color: #fffbeb; border: 1.5px dashed #fbbf24; border-radius: 0.5rem; box-shadow: inset 0 2px 4px rgba(255,255,255,0.8);">
                            <i class="ph-fill ph-warning-circle me-1" style="color: #d97706; font-size: 1rem;"></i>
                            <span class="fw-bolder" style="color: #b45309; font-size: 0.95rem;">' . $fee . '</span>
                        </div>
                    ';
                })
                ->addColumn('action', function($row) {
                    $btnTinjau = '<button class="btn btn-detail d-inline-flex align-items-center justify-content-center me-2" data-id="'.$row->id.'" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; border: none; font-weight: 600; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.2s;"><i class="ph-bold ph-eye me-1"></i> Tinjau</button>';

                    $btnResend = '<button class="btn btn-resend d-inline-flex align-items-center justify-content-center" data-id="'.$row->id.'" style="background: #f8fafc; color: #475569; border: 1px solid #cbd5e1; font-weight: 600; font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"><i class="ph-bold ph-paper-plane-right me-1"></i> Resend</button>';

                    return '<div class="d-flex align-items-center justify-content-center">' . $btnTinjau . $btnResend . '</div>';
                })
                ->rawColumns(['date', 'distributor', 'customer', 'old_fee', 'new_fee', 'action'])
                ->make(true);
        }
        return view('page.master.logistic-fee.approval');
    }

    public function approvalDetail($id)
    {
        $log = ApprovalLog::findOrFail($id);
        $data = DistributorCustomer::with(['distributor', 'customer'])->findOrFail($log->related_id);

        return response()->json([
            'log_id' => $log->id,
            'distributor' => $data->distributor->name ?? '-',
            'customer' => $data->customer->name ?? '-',
            'old_fee' => 'Rp ' . number_format($data->logistic_fee, 0, ',', '.'),
            'new_fee' => 'Rp ' . number_format($data->proposed_fee, 0, ',', '.'),
        ]);
    }

    public function systemProcessApproval(Request $request, $id){

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'required_if:action,reject|nullable|string'
        ], [
            'notes.required_if' => 'Notes / Reason is required if you reject the request.'
        ]);

        $log = ApprovalLog::findOrFail($id);
        $action = $request->action;
        $logisticData = DistributorCustomer::find($log->related_id);

        try {
            DB::beginTransaction();

            if ($action === 'approve') {
                $log->update(['status' => 'Approved', 'token' => null, 'notes' => $request->notes]);

                $nextLevel = $log->level + 1;
                $nextLog = ApprovalLog::where('related_id', $log->related_id)->where('sub_category', 'Logistic Fee')->where('level', $nextLevel)->first();

                if ($nextLog) {
                    $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                    $logisticData->update(['route_to' => $nextApproverUser ? $nextApproverUser->name : 'Unknown Approver']);
                    dispatch(new SendLogisticFee($nextLog, $logisticData));
                    $message = 'Successfully approved. Forwarded to: ' . ($nextApproverUser->name ?? '-');
                } else {
                    $oldFee = $logisticData->logistic_fee;
                    $newFee = $logisticData->proposed_fee;

                    $logisticData->update(['logistic_fee' => $newFee, 'status' => 'Approved', 'route_to' => '-']);

                    // Catat ke History Log
                    LogisticFeeLog::create([
                        'distributor_customer_id' => $logisticData->id,
                        'old_fee'   => $oldFee,
                        'new_fee'   => $newFee,
                        'status'    => 'Approved',
                        'action_by' => Auth::user()->nik,
                        'notes'     => $request->notes
                    ]);

                    // =========================================================
                    // TAMBAHAN: KIRIM EMAIL FINAL APPROVAL KE SEMUA PIHAK
                    // =========================================================
                    $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

                    $approverEmails = ApprovalLog::where('related_id', $logisticData->id)
                                        ->where('category', 'Customer')
                                        ->where('sub_category', 'Logistic Fee')
                                        ->get()
                                        ->map(function($item) {
                                            return User::where('nik', $item->approver_nik)->value('email');
                                        })
                                        ->filter()
                                        ->toArray();

                    $allRecipients = array_filter(array_unique(array_merge([$requesterEmail], $approverEmails)));

                    if (!empty($allRecipients)) {
                        try {
                            Mail::to($allRecipients)->queue(new LogisticFeeMail(
                                'completed',
                                $logisticData,
                                [
                                    'oldFee' => $oldFee,
                                    'newFee' => $newFee,
                                    'notes' => $request->notes
                                ]
                            ));
                        } catch (\Exception $e) {
                            Log::error('Failed to send Final Approval email (System): ' . $e->getMessage());
                        }
                    }

                    $message = 'Successfully approved. The Logistic Fee has been updated and all relevant parties have been notified.';
                }
            } elseif ($action === 'reject') {
                $log->update(['status' => 'Rejected', 'token' => null, 'notes' => $request->notes]);

                $oldFee = $logisticData->logistic_fee;
                $newFee = $logisticData->proposed_fee;

                $logisticData->update(['status' => 'Rejected', 'route_to' => '-']);

                // Catat ke History Log
                LogisticFeeLog::create([
                    'distributor_customer_id' => $logisticData->id,
                    'old_fee'   => $oldFee,
                    'new_fee'   => $newFee,
                    'status'    => 'Rejected',
                    'action_by' => Auth::user()->nik,
                    'notes'     => $request->notes
                ]);

                // =========================================================
                // TAMBAHAN: KIRIM EMAIL REJECTED KE REQUESTER
                // =========================================================
                $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

                if (!empty($requesterEmail)) {
                    try {
                        Mail::to($requesterEmail)->queue(new LogisticFeeMail(
                            'rejected',
                            $logisticData,
                            [
                                'notes' => $request->notes
                            ]
                        ));
                    } catch (\Exception $e) {
                        Log::error('Failed to send Rejected email (System): ' . $e->getMessage());
                    }
                }
                // =========================================================

                $message = 'Request successfully rejected.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function resendEmail($id)
    {
        $log = ApprovalLog::findOrFail($id);
        $logisticData = DistributorCustomer::find($log->related_id);

        if($log->status === 'Pending' && $logisticData) {
            dispatch(new SendLogisticFee($log, $logisticData));
            return response()->json(['success' => true, 'message' => 'Email Approval successfully resent to the Approver!']);
        }
        return response()->json(['success' => false, 'message' => 'Invalid data for email resend.'], 400);
    }


    // ==============================================================
    // 4. FITUR LOG HISTORY (HISTORI PERUBAHAN HARGA)
    // ==============================================================

    public function logList(Request $request)
    {
        if ($request->ajax()) {
            $data = LogisticFeeLog::with(['distributorCustomer.distributor', 'distributorCustomer.customer', 'user'])
                                  ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()

                // --- KOLOM TANGGAL (Dengan Icon Clock) ---
                ->addColumn('date', function($row) {
                    $date = $row->created_at->format('d M Y, H:i');
                    return '<div class="d-flex align-items-center gap-2"><i class="ph-fill ph-clock-counter-clockwise" style="color: #94a3b8; font-size: 1.1rem;"></i><span style="color: #475569; font-weight: 600; font-size: 0.85rem;">' . $date . '</span></div>';
                })

                // --- KOLOM DISTRIBUTOR ---
                ->addColumn('distributor', function($row) {
                    $name = $row->distributorCustomer->distributor->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph-fill ph-buildings fs-6"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.85rem;">' . $name . '</span>
                        </div>
                    ';
                })

                // --- KOLOM CUSTOMER ---
                ->addColumn('customer', function($row) {
                    $name = $row->distributorCustomer->customer->name ?? '-';
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #16a34a; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph-fill ph-storefront fs-6"></i>
                            </div>
                            <span class="fw-bolder" style="color: #1e293b; font-size: 0.85rem;">' . $name . '</span>
                        </div>
                    ';
                })

                // --- KOLOM HARGA LAMA ---
                ->addColumn('old_fee', function($row) {
                    $fee = 'Rp ' . number_format($row->old_fee, 0, ',', '.');
                    return '<span class="text-decoration-line-through" style="color: #94a3b8; font-weight: 600; font-size: 0.85rem;">' . $fee . '</span>';
                })

                // --- KOLOM HARGA BARU ---
                ->addColumn('new_fee', function($row) {
                    $fee = 'Rp ' . number_format($row->new_fee, 0, ',', '.');
                    return '<span class="fw-bolder" style="color: #7e22ce; font-size: 0.95rem;">' . $fee . '</span>';
                })

                // --- KOLOM STATUS (Glossy Pill Badge) ---
                ->addColumn('status_badge', function($row) {
                    if($row->status == 'Requested') {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 2rem; box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);">
                                <i class="ph-fill ph-paper-plane-tilt me-1" style="color: #d97706; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #b45309; font-size: 0.8rem;">Requested</span>
                            </div>
                        ';
                    }
                    elseif ($row->status == 'Approved') {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #86efac; border-radius: 2rem; box-shadow: 0 2px 5px rgba(22, 163, 74, 0.1);">
                                <i class="ph-fill ph-check-circle me-1" style="color: #16a34a; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #15803d; font-size: 0.8rem;">Approved</span>
                            </div>
                        ';
                    }
                    else {
                        return '
                            <div class="d-inline-flex align-items-center px-3 py-1" style="background: linear-gradient(180deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #fca5a5; border-radius: 2rem; box-shadow: 0 2px 5px rgba(220, 38, 38, 0.1);">
                                <i class="ph-fill ph-x-circle me-1" style="color: #dc2626; font-size: 0.9rem;"></i>
                                <span class="fw-bold" style="color: #b91c1c; font-size: 0.8rem;">Rejected</span>
                            </div>
                        ';
                    }
                })

                // --- KOLOM ACTION BY (User Avatar) ---
                ->addColumn('action_by', function($row) {
                    $name = $row->user->name ?? $row->action_by ?? 'System';
                    $iconColor = $name === 'System' ? '#ef4444' : '#64748b'; // Merah jika system, abu jika user
                    $bgColor = $name === 'System' ? '#fee2e2' : '#f1f5f9';
                    $icon = $name === 'System' ? 'ph-robot' : 'ph-user';

                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 24px; height: 24px; background: ' . $bgColor . '; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph-fill ' . $icon . '" style="color: ' . $iconColor . '; font-size: 0.85rem;"></i>
                            </div>
                            <span style="color: #334155; font-weight: 600; font-size: 0.85rem;">' . $name . '</span>
                        </div>
                    ';
                })

                // --- KOLOM CATATAN (Dibuat rapi dan italic jika kosong) ---
                ->addColumn('notes', function($row) {
                    $note = $row->notes ?? '-';
                    if ($note === '-') {
                        return '<span style="color: #cbd5e1; font-style: italic; font-size: 0.8rem;">Tidak ada catatan</span>';
                    }
                    return '<div style="color: #64748b; font-size: 0.8rem; line-height: 1.4; max-width: 200px; white-space: normal;">' . $note . '</div>';
                })

                ->rawColumns(['date', 'distributor', 'customer', 'old_fee', 'new_fee', 'status_badge', 'action_by', 'notes'])
                ->make(true);
        }
        return view('page.master.logistic-fee.log');
    }
}
