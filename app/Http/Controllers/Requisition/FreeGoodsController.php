<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFreeGoodsRequest;
use App\Http\Requests\UpdateFreeGoodsRequest;
use App\Jobs\sendFreeGoods;
use App\Mail\MailRejectFreeGoods;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Traits\ApprovalTrait;
use App\Traits\traitRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class FreeGoodsController extends Controller
{
    use traitRequisition;

    private function generateFgNumber()
    {
        // Format: FG YY MM XXX
        $prefix = 'FG';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";

        $lastRequisition = Requisition::where('category', 'FREE GOODS')
                                    ->where('no_srs', 'LIKE', $currentPrefix . ' %')
                                    ->orderBy('no_srs', 'desc')
                                    ->first();

        $runningNumber = 1;
        if ($lastRequisition) {
            $lastParts = explode(' ', $lastRequisition->no_srs);
            $lastRunningNumber = end($lastParts);
            $runningNumber = intval($lastRunningNumber) + 1;
        }

        return $currentPrefix . ' ' . sprintf('%03d', $runningNumber);
    }

    public function getNextFgNumber()
    {
        return response()->json(['next_fg_number' => $this->generateFgNumber()]);
    }

    public function getAllItemMasters()
    {
        $masters = ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get();
        return response()->json($masters);
    }

    public function index()
    {
        $customers = Customer::all();
        $generatedFg = $this->generateFgNumber();
        $user = Auth::user();
        $userDepartmentName = $user->department?->name ?? null;

        return view('page.freegoods.index', compact(
            'customers', 'generatedFg', 'userDepartmentName'));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'FREE GOODS')
            ->select('requisitions.id', 'requisitions.no_srs', 'requisitions.requester_nik', 'requisitions.request_date', 'requisitions.created_at', 'requisitions.cost_center', 'requisitions.sub_category', 'requisitions.route_to', 'requisitions.status', 'users.name as requester_name', 'users.avatar', 'customers.name as customer_name');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('requisitions.status', $request->status);
        }

        if (!$user->hasRole('super-admin')) {
             $query->where('requisitions.requester_nik', $user->nik);
        }

        $query->orderBy('requisitions.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('no_srs', fn($req) => $req->no_srs ? '<span class="badge-custom badge-fg-no"># ' . e($req->no_srs) . '</span>' : '-')
            ->addColumn('requester_info', fn($req) => '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . e($req->requester_name) . '</span></div>')
            ->editColumn('request_date', fn($req) => Carbon::parse($req->created_at)->format('d M Y, H:i'))
            ->editColumn('sub_category', fn($req) => '<span class="badge-custom badge-category">' . e($req->sub_category) . '</span>')
            ->editColumn('route_to', fn($req) => '<span class="badge-custom badge-route-to"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')

            ->editColumn('status', function ($req) {
                $status = $req->status;
                $badgeClass = '';
                if (in_array($status, ['Submitted', 'Pending'])) {
                    $badgeClass = 'badge-status-pending';
                } elseif ($status === 'In Progress' || $status === 'Processing') {
                    $badgeClass = 'badge-status-progress';
                } elseif (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'bg-success';
                } elseif (in_array($status, ['Rejected', 'Recalled'])) {
                    $badgeClass = 'bg-danger';
                }
                return '<span class="badge-custom ' . $badgeClass . '">' . e($status) . '</span>';
            })

            ->addColumn('action', function ($row) {
                $viewBtn = '<button type="button" class="btn btn-info btn-sm action-btn-hover btn-view-requisition" data-id="' . $row->id . '" data-tooltip="View Details"><i class="ph-bold ph-eye"></i></button>';
                $recallBtn = '';
                $duplicateBtn = '';

                if ($row->status === 'Pending') {
                    $recallBtn = '<button type="button" class="btn btn-danger btn-sm action-btn-hover btn-recall-requisition" data-id="' . $row->id . '" data-tooltip="Recall"><i class="ph-bold ph-x"></i></button>';
                }

                if ($row->status === 'Recalled') {
                    $duplicateBtn = '<button type="button" class="btn btn-primary btn-sm action-btn-hover btn-duplicate-requisition" data-id="' . $row->id . '" data-tooltip="Duplicate"><i class="ph-bold ph-copy"></i></button>';
                }

                return '<div class="action-btn-group">' . $viewBtn . $recallBtn . $duplicateBtn . '</div>';
            })
            ->rawColumns(['no_srs', 'requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    public function store(StoreFreeGoodsRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();

            $user = User::with('atasan', 'department')->find(Auth::id());
            $userAccount = $user->department->code ?? null;

            if ($userAccount === '5300') {
                $pathSubCategory = 'SNM_PATH';
                $subCategoryLabel = 'SnM Request';
            } else {
                $pathSubCategory = 'NON_SNM_PATH';
                $subCategoryLabel = 'General Request';
            }


            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateFgNumber(),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'] ?? null,
                'request_date' => $validated['request_date'],
                'category' => 'FREE GOODS',
                'sub_category' => $subCategoryLabel,
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id' => $requisition->id,
                    'item_master_id' => $itemMasterId,
                    'material_type' => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued' => $itemData['quantity_issued'] ?? null,
                ]);
            }

            Log::info("Memulai proses approval untuk Free Goods Requisition #{$requisition->id}. Path: {$pathSubCategory}");

            $this->generateApprovalLogs(
                $user,
                $requisition->id,
                'FREE GOODS',
                $pathSubCategory
            );

            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $requisition->update(['route_to' => $firstApprover->name]);
                    sendFreeGoods::dispatch($requisition, $firstApprover, $firstLog->token, ['mail_type' => 'approval']);
                } else {
                    $requisition->update(['status' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval Free Goods yang cocok. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();
            $nextFgNumber = $this->generateFgNumber();

            return response()->json([
                'success' => true,
                'message' => 'Free Goods Requisition berhasil dibuat dan permintaan persetujuan telah dikirim.',
                'next_fg_number' => $nextFgNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat Free Goods requisition: ' . $e->getMessage() . ' di baris ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function update(UpdateFreeGoodsRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();

        $subCategoryLabel = $requisition->sub_category ?? 'General Request';

        DB::beginTransaction();
        try {

            $requisition->update($validated);
            $requisition->requisitionItems()->delete();

            foreach ($validated['items'] as $itemMasterId => $itemData) {
                RequisitionItem::create([
                    'requisition_id'    => $requisition->id,
                    'item_master_id'    => $itemMasterId,
                    'material_type'     => $subCategoryLabel,
                    'quantity_required' => $itemData['quantity_required'],
                    'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                ]);
            }

            $message = 'Free Goods Requisition berhasil diubah.';

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengubah Free Goods requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem. Silakan cek log.'], 500);
        }
    }

    public function approvalPage()
    {
        return view('page.freegoods.approval.index');
    }
public function getApprovalData(Request $request)
    {
        $user = Auth::user();

        // [PERBAIKAN 1] Memuat relasi 'requisition' DAN 'requisition.requester'
        $query = ApprovalLog::where('approver_nik', $user->nik)
            ->whereHas('requisition', function ($q) {
                $q->where('category', 'FREE GOODS');
            })
            ->where('status', 'Pending')
            ->with([
                'requisition' => function ($q) {
                    // Pastikan 'requester_nik' ada di select agar relasi 'requester' bisa dimuat
                    $q->select('id', 'no_srs', 'request_date', 'sub_category', 'status', 'requester_nik');
                },
                'requisition.requester' => function ($q) {
                    // Muat nama requester
                    $q->select('nik', 'name');
                }
            ])
            ->select('approval_logs.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_srs', fn($row) => $row->requisition->no_srs ?? 'N/A')

            // [PERBAIKAN 2] Menambahkan kolom 'requester' yang diharapkan oleh DataTables
            ->addColumn('requester', function ($row) {
                // Mengakses data nama dari relasi yang sudah dimuat
                return $row->requisition->requester->name ?? 'Unknown';
            })

            ->addColumn('request_date', fn($row) => Carbon::parse($row->requisition->request_date)->format('d M Y'))
            ->addColumn('sub_category', fn($row) => '<span class="badge bg-info">' . e($row->requisition->sub_category ?? '-') . '</span>')
            ->editColumn('status', function ($row) {
                $status = $row->requisition->status ?? 'N/A';
                $badgeClass = 'bg-warning';
                return '<span class="badge ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $token = $row->token;
                $srs = $row->requisition->no_srs;
                $id = $row->requisition->id;

                $approveBtn = '<button class="btn btn-success btn-sm action-btn" data-token="'.$token.'" data-srs="'.$srs.'" data-tooltip="Quick Approve"><i class="ph-bold ph-check-circle"></i></button>';
                $reviewBtn = '<button class="btn btn-info btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="review" data-tooltip="Review & Approve"><i class="ph-bold ph-pencil-simple"></i></button>';
                $rejectBtn = '<button class="btn btn-danger btn-sm action-btn-modal" data-id="'.$id.'" data-token="'.$token.'" data-srs="'.$srs.'" data-action="reject" data-tooltip="Reject"><i class="ph-bold ph-x-circle"></i></button>';

                return '<div class="action-btn-group">' . $approveBtn . $reviewBtn . $rejectBtn . '</div>';
            })
            ->rawColumns(['action', 'status', 'sub_category'])
            ->make(true);
    }

    public function reports()
    {
        return view('page.freegoods.report.index');
    }

    public function getReportData(Request $request)
    {
        $query = Requisition::with(['requester:nik,name', 'customer:id,name'])
            ->where('category', 'FREE GOODS');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('request_date', [$startDate, $endDate]);
        }

        return DataTables::of($query)
            ->addColumn('checkbox', function ($requisition) {
                return '<input type="checkbox" class="form-check-input requisition-checkbox" value="' . $requisition->id . '">';
            })
            ->editColumn('no_srs', function ($req) {
                return '<span class="badge-custom badge-fg-no"># ' . e($req->no_srs) . '</span>';
            })
            ->addColumn('requester_info', function ($requisition) {
                $name = e($requisition->requester->name ?? 'N/A');
                return '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . $name . '</span></div>';
            })
            ->addColumn('customer_name', fn ($req) => e($req->customer->name ?? 'N/A'))
            ->editColumn('request_date', fn($req) => Carbon::parse($req->request_date)->format('d M Y'))
            ->editColumn('sub_category', function ($requisition) {
                return '<span class="badge-custom badge-category">' . e($requisition->sub_category) . '</span>';
            })
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'bg-secondary';
                if (in_array($status, ['Approved', 'Completed'])) {
                    $badgeClass = 'bg-success';
                } elseif (in_array($status, ['Rejected', 'Recalled'])) {
                    $badgeClass = 'bg-danger';
                } elseif (in_array($status, ['Submitted', 'Pending', 'In Progress'])) {
                    $badgeClass = 'badge-status-pending';
                }
                return '<span class="badge-custom ' . $badgeClass . '">' . e($status) . '</span>';
            })
            ->rawColumns(['checkbox', 'no_srs', 'requester_info', 'sub_category', 'status'])
            ->make(true);
    }

    public function printBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $requisitionIds = $request->input('ids');

        $requisitions = Requisition::with([
            'customer',
            'requester.department',
            'requisitionItems.itemMaster',
            'approvals.approver'
        ])->whereIn('id', $requisitionIds)
          ->orderBy('no_srs', 'asc')
          ->get();

        if ($requisitions->isEmpty()) {
            return back()->with('error', 'No requisitions selected or found for printing.');
        }

        return view('page.freegoods.report.print', compact('requisitions'));
    }

    public function destroy($id)
    {
        try {
            $requisition = Requisition::findOrFail($id);
            $requisition->delete();

            return response()->json(['success' => true, 'message' => 'Free Goods Requisition was successfully deleted.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus requisition.'], 500);
        }
    }

    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $originalAction = $action;
        $validActions = ['approve', 'review', 'reject', 'submit'];

        if (!in_array($action, $validActions)) {
            return view('page.freegoods.invalid', ['message' => 'Invalid action.']);
        }

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->whereNull('last_updated')->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.freegoods.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        if ($action === 'approve' && !$tracking) {
            $request->merge(['token' => $token, 'action' => 'approve', 'notes' => 'Approved via quick action link.']);
            return $this->processApproval($request);
        }

        $requisition = $approvalLog ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'customer', 'requisitionItems.itemMaster', 'approvalLogs.approver');

        $isWarehouseProcess = (bool)$tracking;
        $pageTitle = $isWarehouseProcess ? ($tracking->current_position ?? 'Warehouse Process') : 'Approval Action';

        if ($action === 'reject') {
            $action = 'review';
        }

        $viewData = [
            'token' => $token,
            'action' => $action,
            'requisition' => $requisition,
            'pageTitle' => $pageTitle,
            'isWarehouseProcess' => $isWarehouseProcess,
            'originalAction' => $originalAction,
        ];

        return view('page.freegoods.response-form', $viewData);
    }

    public function processApproval(Request $request)
    {
        if ($request->input('action') === 'approve' &&
            $request->input('notes') === 'Approved via quick action link.')
        {
            $validated = $request->all();
        } else {
            $validated = $request->validate([
                'token' => 'required|string',
                'action' => 'required|string|in:approve,review,reject,submit',
                'notes' => 'nullable|string|max:500|required_if:action,review,reject',
            ]);
        }

        $token = $validated['token'];
        $action = $validated['action'];
        $notes = $validated['notes'] ?? null;

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if ($approvalLog) {
            return $this->processApprovalStep($approvalLog, $action, $notes);
        }

        $tracking = Tracking::where('token', $token)->whereNull('last_updated')->first();

        if ($tracking) {
            DB::beginTransaction();
            try {
                $requisition = $tracking->requisition;
                Log::info("Processing warehouse step for Free Goods Requisition #{$requisition->id}. Current position: {$tracking->current_position}.");

                $updateData = ['token' => null, 'last_updated' => now()];

                $defaultNote = "Proses {$tracking->current_position} berhasil disubmit tanpa notes.";
                $updateData['notes'] = $notes ?: $defaultNote;

                $tracking->update($updateData);

                $newStatus = $this->advanceWarehouseStep($requisition);

                DB::commit();

                $requisition->load('customer');

                return redirect()->route('fg.approval.success')
                    ->with('card_class', 'success')->with('title', 'Warehouse Step Completed')
                    ->with('message', 'Warehouse process step has been recorded.')
                    ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                    ->with('action_text', 'Processed')->with('approver_name', $tracking->current_position)
                    ->with('new_status', $newStatus);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal melanjutkan proses warehouse Free Goods: " . $e->getMessage());
                return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred. Please check the system logs.');
            }
        }

        return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request')->withMessage('This approval request is invalid or has already been processed.');
    }

    private function processApprovalStep(ApprovalLog $approvalLog, string $action, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $requisition = $approvalLog->requisition->load('customer', 'requester');
            $approverName = $approvalLog->approver->name ?? 'Approver';
            $finalNotes = $notes;

            $logStatus = ($action === 'reject') ? 'Rejected' : 'Approved';
            $cardClass = ($logStatus === 'Rejected') ? 'reject' : 'success';

            if ($logStatus === 'Rejected') {
                $finalNotes = $notes ?: 'Rejected without reason';
            } elseif (empty($notes)) {
                $finalNotes = 'Approved by ' . $approverName;
            }

            $approvalLog->update([
                'status'       => $logStatus,
                'notes'        => $finalNotes,
                'responded_at' => now(),
                'token'        => null,
            ]);

            $title = 'Action Submitted';
            $actionText = ucfirst($logStatus);
            $newStatus = 'In Progress';

            if ($logStatus === 'Rejected') {
                $requisition->update(['status' => 'Rejected', 'route_to' => 'Finished (Rejected)']);

                if ($requisition->requester?->email) {
                    Mail::to($requisition->requester->email)->send(new MailRejectFreeGoods($requisition, $approverName, $finalNotes));
                }
                $title = 'Requisition Rejected';
                $newStatus = 'Rejected';

            } else { // Approved
                if (!str_starts_with($finalNotes, 'Approved by')) {
                    $title = 'Approved with Review';
                    $actionText = 'Approved with Review';
                }

                $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                                ->where('level', '>', $approvalLog->level)
                                                ->orderBy('level', 'asc')->first();

                if ($nextApprovalLog) {
                    $nextApprover = User::where('nik', $nextApprovalLog->approver_nik)->first();
                    if ($nextApprover) {
                        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
                        dispatch(new sendFreeGoods($requisition, $nextApprover, $nextApprovalLog->token, ['mail_type' => 'approval']));
                        $newStatus = "Waiting for {$nextApprover->name}";
                    } else {
                        Log::error("Approver berikutnya dengan NIK {$nextApprovalLog->approver_nik} tidak ditemukan.");
                        $requisition->update(['status' => 'Error', 'route_to' => 'Error: Approver Not Found']);
                        $newStatus = 'Error';
                        $cardClass = 'reject';
                    }
                } else {
                    $requisition->update(['status' => 'Approved']);
                    $newStatus = $this->startPostApprovalProcess($requisition);
                }
            }

            DB::commit();

            return redirect()->route('fg.approval.success')
                ->with('card_class', $cardClass)
                ->with('title', $title)
                ->with('message', 'Your response has been successfully recorded.')
                ->with('no_srs', $requisition->no_srs)->with('customer_name', $requisition->customer->name ?? 'N/A')
                ->with('action_text', $actionText)
                ->with('approver_name', $approverName)
                ->with('new_status', $newStatus);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal proses approval Free Goods #{$approvalLog->id}: " . $e->getMessage());
            return redirect()->route('fg.approval.success')->with('card_class', 'reject')->with('title', 'System Error')->with('message', 'An unexpected error occurred during approval process.');
        }
    }

    private function startPostApprovalProcess(Requisition $requisition)
    {
        $newStatus = 'Processing';
        Log::info("Approval path selesai untuk Free Goods Requisition #{$requisition->id}. Memulai proses warehouse.");

        $stepName = 'Outward WH Supervisor';
        $this->createOrUpdateTracking($requisition, $stepName, "Menunggu proses oleh outward.");

        return $stepName;
    }

    private function advanceWarehouseStep(Requisition $requisition)
    {
        $requisition->load('tracking');
        $currentPosition = $requisition->tracking->current_position ?? '';

        if (str_contains($currentPosition, 'Outward WH Supervisor')) {
            return $this->notifyRequesterAsCompleted($requisition);
        }

        Log::warning("advanceWarehouseStep dipanggil untuk FG requisition #{$requisition->id} tanpa alur yang cocok.");
        return $this->notifyRequesterAsCompleted($requisition);
    }

    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $statusText = 'Completed';
        $requisition->update(['status' => $statusText, 'route_to' => 'Finished']);

        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $statusText,
                'notes'            => "Free Goods process is complete and ready for the requester.",
                'last_updated'     => now(),
                'token'            => null,
            ]
        );

        if ($requisition->requester?->email) {
            dispatch(new sendFreeGoods($requisition, $requisition->requester, null, [
                'mail_type' => 'completed_notification'
            ]));
        }
        Log::info("Free Goods Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return $statusText;
    }

    private function createOrUpdateTracking(Requisition $requisition, string $currentPosition, string $notes)
    {
        $token = Str::uuid()->toString();

        Tracking::updateOrCreate(
            ['requisition_id' => $requisition->id],
            [
                'current_position' => $currentPosition,
                'notes'            => $notes,
                'last_updated'     => null,
                'token'            => $token,
            ]
        );

        $requisition->update(['status' => 'Processing', 'route_to' => $currentPosition]);

        $user = $this->findUserForStep($currentPosition);
        if ($user) {
            dispatch(new sendFreeGoods($requisition, $user, $token, [
                'mail_type'    => 'warehouse_process',
                'process_step' => $currentPosition
            ]));
        }
    }

    private function findUserForStep(string $stepName)
    {
        if (str_contains($stepName, 'Outward WH Supervisor')) {
            $user = User::where('name', 'like', '%' . 'Outward WH Supervisor' . '%')->first();
            return $user ?: User::where('nik', 'WH0002')->first();
        }
        return null;
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
        ])->findOrFail($id);

        $responseData = $requisition->toArray();

        $selectedMasterIds = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();

        $productOptions = ItemMaster::select('id', 'item_master_code', 'item_master_name')
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
            ->toArray();

        $responseData['selected_master_ids'] = $selectedMasterIds;
        $responseData['product_options'] = $productOptions;

        return response()->json($responseData);
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email',
            'requisitionItems:requisition_id,item_master_id,quantity_required,quantity_issued',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'approvalLogs' => function ($query) {
                $query->orderBy('level', 'asc');
            },
            'approvalLogs.approver:nik,name',
            'tracking'
        ])->findOrFail($id);

        $history = [];

        $history[] = [
            'actor' => $requisition->requester->name ?? 'System',
            'action' => 'Created',
            'notes' => 'Requisition has been submitted.',
            'timestamp' => $requisition->created_at->toDateTimeString(),
        ];

        foreach ($requisition->approvalLogs as $log) {
            if ($log->status !== 'Pending') {
                $action_text = $log->status;
                if ($log->status === 'Approved' && Str::contains($log->notes, 'Review')) {
                    $action_text = 'Approved with Review';
                }

                $history[] = [
                    'actor' => $log->approver->name ?? 'Unknown Approver',
                    'action' => $action_text,
                    'notes' => $log->notes,
                    'timestamp' => $log->responded_at ? $log->responded_at->toDateTimeString() : $log->updated_at->toDateTimeString(),
                ];
            }
        }

        if ($requisition->tracking && $requisition->tracking->last_updated) {
            $tracking = $requisition->tracking;
            $history[] = [
                'actor' => $tracking->current_position,
                'action' => 'Completed Step',
                'notes' => $tracking->notes,
                'timestamp' => $tracking->last_updated->toDateTimeString(),
            ];
        }

        usort($history, function ($a, $b) {
            return strtotime($a['timestamp']) - strtotime($b['timestamp']);
        });

        $responseData = $requisition->toArray();
        $responseData['history'] = $history;

        return response()->json($responseData);
    }

    public function showSuccessPage()
    {
        if (!session('title')) {
            return redirect('/');
        }
        return view('page.freegoods.response-success');
    }

    public function log()
    {
        return view('page.freegoods.log.index');
    }

    public function getLogData()
    {
        $query = Activity::with(['causer', 'subject'])
            ->where('subject_type', Requisition::class)
            ->whereHas('subject', function ($query) {
                $query->where('category', 'FREE GOODS');
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('event', function ($log) {
                $event = e(ucfirst($log->event));
                $badgeClass = 'bg-secondary';
                if ($log->event === 'created') $badgeClass = 'bg-success';
                if ($log->event === 'updated') $badgeClass = 'bg-warning';
                if ($log->event === 'deleted') $badgeClass = 'bg-danger';
                return '<span class="badge ' . $badgeClass . '">' . $event . '</span>';
            })
            ->addColumn('subject_info', function ($log) {
                if ($log->subject && $log->subject->no_srs) {
                    return '<span class="badge-custom badge-fg-no"># ' . e($log->subject->no_srs) . '</span>';
                }
                return '<span class="badge bg-light text-dark">N/A</span>';
            })
            ->addColumn('causer_info', function ($log) {
                if ($log->causer && $log->causer->name) {
                    return '<div class="requester-badge"><i class="ph-bold ph-user-circle"></i><span>' . e($log->causer->name) . '</span></div>';
                }
                return '<span class="badge bg-light text-dark">System</span>';
            })
            ->editColumn('created_at', fn($log) => Carbon::parse($log->created_at)->format('d M Y, H:i:s'))
            ->rawColumns(['event', 'subject_info', 'causer_info'])
            ->make(true);
    }

    // [UPDATE] Seluruh method recallRequisition diganti
    public function recallRequisition(Request $request, $id)
    {
        // Validasi untuk memastikan alasan recall diisi
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $requisition = Requisition::with('requester')->findOrFail($id);

            if ($requisition->status !== 'Pending') {
                return response()->json(['success' => false, 'message' => 'Requisition can no longer be recalled.'], 403);
            }

            $firstLog = ApprovalLog::where('requisition_id', $id)->orderBy('level', 'asc')->first();
            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    Log::info("Mengirim notifikasi recall untuk FG #{$id} ke {$firstApprover->name}");

                    // Mengirimkan alasan recall ke notifikasi email
                    dispatch(new sendFreeGoods($requisition, $firstApprover, null, [
                        'mail_type' => 'recalled_notification',
                        'notes'     => $request->input('notes')
                    ]));
                }
            }

            $requisition->update(['status' => 'Recalled', 'route_to' => 'Recalled by Requester']);
            ApprovalLog::where('requisition_id', $id)->delete();

            // Mencatat aktivitas recall ke dalam log
            activity()
                ->performedOn($requisition)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => ['status' => 'Recalled'], 'reason' => $request->input('notes')])
                ->log('Requisition was recalled');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Requisition has been successfully recalled.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to recall Free Goods Requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while recalling the requisition.'], 500);
        }
    }
}
