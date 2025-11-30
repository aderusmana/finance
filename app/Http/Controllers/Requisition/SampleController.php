<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSampleRequisitionRequest;
use App\Http\Requests\UpdateSampleRequisitionRequest;
use App\Jobs\sendSample;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Master\ItemDetail;
use App\Models\Master\Revision;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\RequisitionSpecial;
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\traitRequisition;
use Spatie\Activitylog\Models\Activity;
use App\Notifications\RequisitionNotification;
use Illuminate\Notifications\Notification;

class SampleController extends Controller
{
    use traitRequisition;

    //======================================================================
    // PUBLIC FUNCTIONS (Controller Endpoints & AJAX Handlers)
    //======================================================================

    /**
     * Menampilkan halaman utama Sample Requisition.
     */

    public function index()
    {
        $customers = Customer::all();
        $materialTypes = ItemDetail::distinct()->pluck('material_type');
        $generatedSrs = $this->generateSrsNumber();
        $user = Auth::user();
        $userAccount = $user->department->code ?? null;
        $userDepartmentName = $user->department?->name ?? null;
        $allowedSubCategories = [];

        if ($user->hasRole('super-admin')) {
            $allowedSubCategories = ['Packaging', 'Finished Goods', 'Special Order'];
        } else {
            if (in_array($userAccount, ['5300'])) {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
                $allowedSubCategories[] = 'Special Order';
            }
            if ($userDepartmentName === 'QM & HSE') {
                $allowedSubCategories[] = 'Finished Goods';
            }
            if ($userDepartmentName === 'R&D') {
                $allowedSubCategories[] = 'Packaging';
                $allowedSubCategories[] = 'Finished Goods';
            }
        }

        $allowedSubCategories = array_unique($allowedSubCategories);

        return view('page.sample.index', compact(
            'customers', 'materialTypes', 'allowedSubCategories',
            'generatedSrs', 'userAccount', 'userDepartmentName'));
    }

    /**
     * Menyediakan data untuk DataTables.
     */

    public function getData(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('requisitions')
            ->leftJoin('users', 'requisitions.requester_nik', '=', 'users.nik')
            ->leftJoin('customers', 'requisitions.customer_id', '=', 'customers.id')
            ->where('requisitions.category', 'SAMPLE')
            ->select(
                'requisitions.id', 'requisitions.no_srs', 'requisitions.requester_nik', 'requisitions.request_date',
                'requisitions.sub_category', 'requisitions.route_to', 'requisitions.status',
                'users.name as requester_name', 'users.avatar', 'customers.name as customer_name', 'requisitions.created_at'
            );

        if ($request->filled('sub_category') && $request->sub_category != 'all') {
            $query->where('requisitions.sub_category', $request->sub_category);
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('requisitions.status', $request->status);
        }

        // [FIX] Mengubah total logika filter untuk non-admin
        if (!$user->hasRole('super-admin')) {
            $query->where(function ($q) use ($user) {
                // Kondisi 1: Tampilkan jika user adalah pembuat request
                $q->where('requisitions.requester_nik', $user->nik);

                // Kondisi 2: ATAU, tampilkan jika user adalah Head QA
                // dan ada request Special Order yang menunggunya.
                if ($user->hasRole('head-QA')) {
                    $q->orWhere(function ($subQuery) {
                        $subQuery->where('requisitions.sub_category', 'Special Order')
                                ->where('requisitions.status', 'Approved')
                                ->where('requisitions.route_to', 'Waiting for QA/QM Form');
                    });
                }
            });
        }

        $query->orderBy('requisitions.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_srs', fn($req) => '<span class="srs-badge"><i class="ph-bold ph-hash me-1"></i>' . e($req->no_srs ?? 'N/A') . '</span>')
            ->addColumn('requester_info', function ($requisition) {
                $avatar = $requisition->avatar ? asset($requisition->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $nik = e($requisition->requester_nik);

                return '
                    <div>
                        <div class="status-badge-lg bg-dark d-flex align-items-center">
                            <img src="' . $avatar . '" alt="av" class="img-fluid rounded-circle me-1" style="width: 20px; height: 20px; object-fit: cover;">
                            <span>' . $nik . '</span>
                        </div>
                    </div>
                ';
            })
            ->editColumn('request_date', fn($req) => Carbon::parse($req->created_at)->format('d M Y, H:i'))
            ->editColumn('sub_category', function ($requisition) {
                $subCategory = $requisition->sub_category;
                $badgeClass = 'bg-dark'; // Warna default
                $icon = 'ph-tag';        // Ikon default

                switch ($subCategory) {
                    case 'Packaging':
                        $badgeClass = 'bg-info';
                        $icon = 'ph-package';
                        break;
                    case 'Finished Goods':
                        $badgeClass = 'bg-primary';
                        $icon = 'ph-cube';
                        break;
                    case 'Special Order':
                        $badgeClass = 'bg-secondary';
                        $icon = 'ph-star';
                        break;
                }

                return '<span class="status-badge-lg ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($subCategory) . '</span>';
            })
            ->editColumn('route_to', fn($req) => '<span class="route-to-badge-lg bg-info"><i class="ph-bold ph-user-switch me-1"></i>' . e($req->route_to) . '</span>')
            ->editColumn('status', function ($requisition) {
                $status = $requisition->status;
                $badgeClass = 'bg-secondary'; // Warna default
                $icon = 'ph-question';       // Ikon default

                switch ($status) {
                    case 'Pending':
                    case 'Submitted':
                        $badgeClass = 'bg-warning';
                        $icon = 'ph-paper-plane-tilt';
                        break;
                    case 'In Progress':
                        $badgeClass = 'bg-info';
                        $icon = 'ph-arrows-clockwise';
                        break;
                    case 'Approved':
                    case 'Completed':
                        $badgeClass = 'bg-success';
                        $icon = 'ph-check-circle';
                        break;
                    case 'Rejected':
                        $badgeClass = 'bg-danger';
                        $icon = 'ph-x-circle';
                        break;
                    case 'Recalled':
                        $badgeClass = 'bg-secondary';
                        $icon = 'ph-prohibit';
                        break;
                }

                return '<span class="status-badge-lg ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $viewBtn = '<button type="button" class="btn btn-sm btn-info btn-view-requisition" data-id="' . $row->id . '" title="Show Detail"><i class="fa-solid fa-eye text-white"></i></button>';
                // $printBtn = '<a href="' . route('sample.report', $row->id) . '" target="_blank" class="btn btn-sm btn-secondary" title="Print Report"><i class="ph-bold ph-printer text-white"></i></a>';

                $actionButtons = '';

                // [MODIFIKASI] Tampilkan tombol Recall HANYA jika status Pending (Tombol Edit Dihapus)
                if ($row->status === 'Pending' && $row->requester_nik === $user->nik) {
                    $actionButtons .= '<button type="button" class="btn btn-sm btn-danger btn-recall-modal" data-id="' . $row->id . '" data-srs="' . $row->no_srs . '" title="Recall Requisition"><i class="fa-solid fa-rotate-left text-white"></i></button>';
                }

                // Tampilkan tombol Duplicate HANYA jika status Recalled
                if ($row->status === 'Recalled' && $row->requester_nik === $user->nik) {
                    $actionButtons = '<button type="button" class="btn btn-sm btn-warning btn-duplicate-requisition" data-id="' . $row->id . '" title="Duplicate Requisition"><i class="ph-bold ph-copy-simple text-white"></i></button>';
                }

                $qaFillBtn = '';
                $userHasQaRole = $user->hasRole('head-QA') || $user->hasRole('super-admin');
                $requisitionIsWaitingForQa = $row->sub_category === 'Special Order' &&
                                            $row->status === 'Approved' &&
                                            $row->route_to === 'Waiting for QA/QM Form';

                if ($userHasQaRole && $requisitionIsWaitingForQa) {
                    $qaFillBtn = '<button type="button" class="btn btn-sm btn-warning btn-qa-form" data-id="' . $row->id . '" title="Complete QA Form"><i class="fa-solid fa-pencil text-white"></i></button>';
                }

                return "<div class='d-flex gap-1'>{$viewBtn} {$qaFillBtn} {$actionButtons}</div>";
            })
            ->rawColumns(['no_srs', 'requester_info', 'sub_category', 'route_to', 'status', 'action'])
            ->make(true);
    }

    /**
     * Membuat requisition baru.
     */
    public function store(StoreSampleRequisitionRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $user = User::with('atasan')->find(Auth::id());

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => $this->generateSrsNumber(),
                'account' => $validated['account'] ?? 4401,
                'cost_center' => $validated['cost_center'],
                'request_date' => $validated['request_date'],
                'category' => 'SAMPLE',
                'sub_category' => $validated['sub_category'],
                'objectives' => $validated['objectives'],
                'estimated_potential' => $validated['estimated_potential'],
                'print_batch' => $validated['print_batch'] ?? false,
                'status' => 'Pending',
                'route_to' => 'N/A',
            ]);

            if ($validated['sub_category'] === 'Packaging') {
                $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                foreach ($validated['items'] as $itemDetailId => $itemData) {
                    if (isset($itemDetails[$itemDetailId])) {
                        $itemDetail = $itemDetails[$itemDetailId];
                        RequisitionItem::create([
                            'requisition_id' => $requisition->id,
                            'item_master_id' => $itemDetail->item_master_id,
                            'item_detail_id' => $itemDetail->id,
                            'material_type' => $itemDetail->material_type,
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued' => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }
            } else { // Finished Goods & Special Order
                foreach ($validated['items'] as $itemMasterId => $itemData) {
                    RequisitionItem::create([
                        'requisition_id' => $requisition->id,
                        'item_master_id' => $itemMasterId,
                        'material_type' => $validated['sub_category'],
                        'quantity_required' => $itemData['quantity_required'],
                        'quantity_issued' => $itemData['quantity_issued'] ?? null,
                    ]);
                }
            }

            if ($validated['sub_category'] === 'Special Order') {
                $itemMasterIds = array_keys($validated['items']);
                $productNames = ItemMaster::whereIn('id', $itemMasterIds)->pluck('item_master_name')->implode(', ');

                RequisitionSpecial::create([
                    'requisition_id' => $requisition->id,
                    'products' => $productNames,
                    'requested_date' => $validated['request_date'],
                    'end_date' => $validated['end_date'],
                    'weight_selection' => $validated['weight_selection'],
                    'packaging_selection' => $validated['packaging_selection'],
                    'sample_count' => $validated['sample_count'],
                    'purpose' => $validated['purpose'],
                    'coa_required' => $validated['coa_required'] ?? false,
                    'shipment_method' => $validated['shipment_method'],
                ]);
            }

            $this->generateApprovalLogs($user, $requisition->id, 'SAMPLE', $validated['sub_category']);
            $firstLog = ApprovalLog::where('requisition_id', $requisition->id)->orderBy('level', 'asc')->first();

            if ($firstLog && $firstApprover = User::where('nik', $firstLog->approver_nik)->first()) {
                $requisition->update(['route_to' => $firstApprover->name]);
                sendSample::dispatch($requisition, $firstApprover, $firstLog->token)->delay(now()->addSeconds(3));

                $notificationData = [
                    'requisition_id' => $requisition->id,
                    'srs_number'     => $requisition->no_srs,
                    'message'        => "Requisition #{$requisition->no_srs} dari {$user->name} menunggu approval Anda.",
                    'url'            => route('sample-form.approval'), // Arahkan ke halaman approval
                ];
                $firstApprover->notify(new RequisitionNotification($notificationData, $user));

            } else {
                $requisition->update(['status' => 'Completed', 'route_to' => 'No Path']);
                Log::warning("Tidak ada alur approval. Auto-complete Requisition ID {$requisition->id}.");
            }

            DB::commit();

            $logMessage = "Membuat Sample Requisition baru #{$requisition->no_srs} untuk customer {$requisition->customer->name}.";
            $properties = [
                'srs_number' => $requisition->no_srs,
                'customer' => $requisition->customer->name,
                'sub_category' => $requisition->sub_category
            ];

            activity()
                ->causedBy($user)
                ->performedOn($requisition)
                ->useLog('sample - ' . strtolower($requisition->sub_category))
                ->event('create')
                ->withProperties($properties)
                ->log($logMessage);

            return response()->json([
                'success' => true,
                'message' => 'Sample Requisition was successfully created.',
                'next_srs_number' => $this->generateSrsNumber()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat sample requisition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Mengupdate requisition yang ada, baik untuk edit biasa maupun submit form QA.
     */
    public function update(UpdateSampleRequisitionRequest $request, $id)
    {
        $requisition = Requisition::findOrFail($id);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Jika ada 'source', berarti ini adalah submit dari form QA/QM
            if (isset($validated['source'])) {
                RequisitionSpecial::updateOrCreate(['requisition_id' => $requisition->id], $validated);
                $tracking = Tracking::where('requisition_id', $requisition->id)
                                    ->where('current_position', 'Waiting for QA/QM Form')
                                    ->whereNull('last_updated')
                                    ->first();

                if ($tracking) {
                    $headQaUser = Auth::user(); // Gunakan user yang sedang login sebagai causer/actor

                    // Isi last_updated, hapus token, dan tambahkan notes
                    $tracking->update([
                        'token'        => null,
                        'last_updated' => now(),
                        'notes'        => 'Form has been completed by QA via internal system.', // Notes yang jelas
                    ]);

                    // Log aktivitas tracking
                    activity()
                        ->causedBy($headQaUser)
                        ->performedOn($requisition)
                        ->useLog('sample - special order')
                        ->event('tracking')
                        ->withProperties([
                            'step'    => 'QA/QM Form',
                            'notes'   => 'Form has been completed by QA via internal system.',
                            'details' => $validated,
                        ])
                        ->log('Submitted the QA/QM & HSE form via internal system.');
                }

                // 3. Ubah status Requisition menjadi Completed & Kirim Notifikasi
                $this->notifyRequesterAsCompleted($requisition);
                $message = 'QM & HSE form has been successfully submitted and Requisition completed.';

            } else { // Jika tidak, ini adalah edit biasa oleh requester
                $requisition->update($validated);
                $requisition->requisitionItems()->delete();

                if ($validated['sub_category'] === 'Packaging') {
                    $itemDetails = ItemDetail::whereIn('id', array_keys($validated['items']))->get()->keyBy('id');
                    foreach ($validated['items'] as $itemDetailId => $itemData) {
                        if (isset($itemDetails[$itemDetailId])) {
                            $itemDetail = $itemDetails[$itemDetailId];
                            RequisitionItem::create([
                                'requisition_id'    => $requisition->id,
                                'item_master_id'    => $itemDetail->item_master_id,
                                'item_detail_id'    => $itemDetail->id,
                                'material_type'     => $itemDetail->material_type,
                                'quantity_required' => $itemData['quantity_required'],
                                'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                            ]);
                        }
                    }
                } else { // Finished Goods & Special Order
                    foreach ($validated['items'] as $itemMasterId => $itemData) {
                        RequisitionItem::create([
                            'requisition_id'    => $requisition->id,
                            'item_master_id'    => $itemMasterId,
                            'material_type'     => $validated['sub_category'],
                            'quantity_required' => $itemData['quantity_required'],
                            'quantity_issued'   => $itemData['quantity_issued'] ?? null,
                        ]);
                    }
                }

                if ($validated['sub_category'] === 'Special Order') {
                    $itemMasterIds = array_keys($validated['items']);
                    $productNames = ItemMaster::whereIn('id', $itemMasterIds)->pluck('item_master_name')->implode(', ');

                    RequisitionSpecial::updateOrCreate(
                        ['requisition_id' => $requisition->id],
                        array_merge($validated, ['products' => $productNames])
                    );
                } else {
                    $requisition->requisitionSpecial()->delete();
                }
                $message = 'Sample Requisition berhasil diubah.';
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Membatalkan requisition.
     */
    public function recallRequisition(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:500', // Validasi notes untuk alasan recall
        ]);

        DB::beginTransaction();
        try {
            $requisition = Requisition::findOrFail($id);
            $requester = Auth::user();

            if ($requisition->status !== 'Pending' || $requisition->requester_nik !== $requester->nik) {
                return response()->json(['success' => false, 'message' => 'Cannot recall. This requisition is already in progress or not your request.'], 403);
            }

            // Cari approver pertama yang sedang menunggu
            $pendingLog = ApprovalLog::where('requisition_id', $id)
                                    ->where('status', 'Pending')
                                    ->with('approver')
                                    ->orderBy('level', 'asc')
                                    ->first();

            $notes = $request->input('notes');

            // Lanjutkan proses pembatalan
            $requisition->update([
                'status' => 'Recalled',
                'route_to' => '-',
            ]);

            // Update ApprovalLog pertama untuk mencatat alasan recall
            if ($pendingLog) {
                $pendingLog->update([
                    'notes' => "RECALLED by Requester: " . $notes,
                    'status' => 'Recalled',
                ]);
            }

            // Revoke SEMUA tokens yang masih ada
            ApprovalLog::where('requisition_id', $id)->whereNotNull('token')->update(['token' => null]);
            Tracking::where('requisition_id', $id)->whereNotNull('token')->update(['token' => null]);

            $logMessage = "Menarik kembali (recall) Sample Requisition #{$requisition->no_srs}.";
            $properties = [
                'srs_number' => $requisition->no_srs,
                'reason' => $notes
            ];

            activity()
                ->causedBy($requester)
                ->performedOn($requisition)
                ->useLog('sample - ' . strtolower($requisition->sub_category))
                ->event('recall')
                ->withProperties($properties)
                ->log($logMessage . " Alasan: \"{$notes}\"");

            // Kirim email dan notifikasi SISTEM ke approver yang tadinya menunggu
            if ($pendingLog && $pendingLog->approver) {
                $approver = $pendingLog->approver;

                // 1. Kirim Email Notifikasi Recalled
                dispatch(new sendSample(
                    $requisition,
                    $approver,
                    null, // Tidak perlu token, karena notifikasi
                    ['mail_type' => 'recallation_notification', 'rejection_notes' => $notes, 'approver_name' => $requester->name]
                ));

                // 2. Kirim Notifikasi Sistem ke Approver
                $approver->notify(new RequisitionNotification([
                    'requisition_id' => $requisition->id,
                    'srs_number'     => $requisition->no_srs,
                    'message'        => "Requisition #{$requisition->no_srs} telah di-RECALL oleh {$requester->name}. Alasan: {$notes}",
                    'url'            => route('sample-form.approval'),
                ], $requester));
            }


            DB::commit();
            return response()->json(['success' => true, 'message' => 'Requisition has been successfully Recalled with notes.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membatalkan Requisition #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'A system error occurred.'], 500);
        }
    }

    //======================================================================
    // EMAIL RESPONSE HANDLING
    //======================================================================

    /**
     * Menampilkan form respons dari link email (approval, review, QA form, etc.).
     */
    public function showResponseForm(Request $request, $token)
    {
        $action = $request->query('action');
        $originalAction = $action;

        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();
        $tracking = !$approvalLog ? Tracking::where('token', $token)->whereNull('last_updated')->first() : null;

        if (!$approvalLog && !$tracking) {
            return view('page.sample.links.invalid', ['message' => 'This request is invalid or has been processed.']);
        }

        $requisition = ($approvalLog) ? $approvalLog->requisition : $tracking->requisition;
        $requisition->load('requester.department', 'customer', 'requisitionItems.itemMaster', 'requisitionItems.itemDetail', 'requisitionSpecial');

        $isQaForm = ($action === 'qa_form');
        $isWarehouseProcess = (bool)$tracking;

        $pageTitle = 'Approval Action';
        if ($isQaForm) $pageTitle = 'QA/QM Form Completion';
        elseif ($isWarehouseProcess) $pageTitle = $tracking->current_position ?? 'Warehouse Process';

        // Arahkan 'quick reject' ke halaman 'review' untuk mengisi notes
        if ($action === 'reject') {
            $action = 'review';
        }

        return view('page.sample.links.response-form', compact('token', 'action', 'originalAction', 'requisition', 'pageTitle', 'isQaForm', 'isWarehouseProcess'));
    }

    /**
     * Memproses semua jenis aksi dari form respons email.
     */
    public function processApproval(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'action' => 'required|string|in:approve,review,reject,submit,qa_submit',
            'notes' => 'nullable|string|max:500|required_if:action,review,reject',
            'source' => 'required_if:action,qa_submit|string|max:255',
            'description' => 'required_if:action,qa_submit|string|max:255',
            'production_date' => 'required_if:action,qa_submit|date',
            'preparation_method' => 'required_if:action,qa_submit|string|max:255',
            'sample_notes' => 'required_if:action,qa_submit|string|max:255',
        ]);

        // Cek jika ini adalah submit form QA/QM
        if ($validated['action'] === 'qa_submit') {
            $tracking = Tracking::where('token', $validated['token'])->firstOrFail();
            // Logika ini sudah redirect, kita biarkan saja karena dari halaman terpisah
            return $this->processQaFormSubmit($tracking, $validated);
        }

        // Cari log approval atau tracking
        $approvalLog = ApprovalLog::where('token', $validated['token'])->where('status', 'Pending')->first();
        if ($approvalLog) {
            // [MODIFIKASI] Kita ubah cara pemanggilan fungsi di bawah
            return $this->processApprovalStep($request, $approvalLog, $validated['action'], $validated['notes'] ?? null);
        }

        $tracking = Tracking::where('token', $validated['token'])->whereNull('last_updated')->first();
        if ($tracking) {
            // [MODIFIKASI] Kita ubah cara pemanggilan fungsi di bawah
            return $this->processWarehouseStep($request, $tracking, $validated['notes'] ?? null);
        }

        // Jika tidak ditemukan, respons sesuai tipe request
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Request is invalid or has been processed.'], 422);
        }
        return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Request');
    }

    //======================================================================
    // PRIVATE FUNCTIONS (Business Logic & Helpers)
    //======================================================================

    private function notifyRelevantUsers(User $targetUser, Notification $notification)
    {
        // 1. Kirim notifikasi ke pengguna target utama.
        $targetUser->notify($notification);

        // 2. Ambil semua pengguna dengan role 'super-admin'.
        $superAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->get();

        // 3. Kirim notifikasi ke setiap superadmin.
        foreach ($superAdmins as $admin) {
            // Pastikan kita tidak mengirim notifikasi dua kali jika
            // pengguna target utama juga seorang superadmin.
            if ($admin->id !== $targetUser->id) {
                $admin->notify($notification);
            }
        }
    }

    /**
     * Menangani submit form QA/QM.
     */
    private function processQaFormSubmit(Tracking $tracking, array $validated)
    {
        DB::beginTransaction();
        try {
            RequisitionSpecial::updateOrCreate(
                ['requisition_id' => $tracking->requisition_id],
                $validated
            );
            $tracking->update(['token' => null, 'last_updated' => now(), 'notes' => 'Form has been completed by QA.']);

            $headQaUser = User::whereHas('department', fn ($q) => $q->where('name', 'QM & HSE'))
                              ->whereHas('roles', fn ($q) => $q->where('name', 'head-QA'))
                              ->first();
            activity()
                ->causedBy($headQaUser) // Asumsi Head QA yang melakukan aksi
                ->performedOn($tracking->requisition)
                ->useLog('sample - special order')
                ->event('tracking')
                ->withProperties([
                    'step'    => 'QA/QM Form',
                    'notes'   => 'Form has been completed by QA.',
                    'details' => $validated,
                ])
                ->log('Submitted the QA/QM & HSE form.');

            $this->notifyRequesterAsCompleted($tracking->requisition);
            DB::commit();

            // Di dalam fungsi processQaFormSubmit()
            $requisition = $tracking->requisition->load('customer'); // Ambil requisition & load customer

            return redirect()->route('approval.success')->with([
                'card_class'    => 'success',
                'title'         => 'Form QA/QM HSE Submitted',
                'no_srs'        => $requisition->no_srs,
                'customer_name' => $requisition->customer->name ?? 'N/A',
                'action_text'   => 'Submitted', // <-- Teks aksi baru
                'approver_name' => 'QA/QM HSE Team' // <-- Nama pengisi form
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal saat submit form QM & HSE: ' . $e->getMessage());
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    /**
     * Memproses satu langkah persetujuan (approve/reject).
     */

    private function processApprovalStep(Request $request, ApprovalLog $approvalLog, string $action, ?string $notes)
    {
        DB::beginTransaction();
        try {
            $currentLevel = $approvalLog->level;
            if ($currentLevel > 1) {
                $previousLevelLog = ApprovalLog::where('requisition_id', $approvalLog->requisition_id)
                                                ->where('level', $currentLevel - 1)
                                                ->first();

                // Jika log level sebelumnya tidak ada ATAU statusnya BUKAN 'Approved'
                if (!$previousLevelLog || $previousLevelLog->status !== 'Approved') {
                    $errorMessage = "Approval level {$currentLevel} cannot be processed because level " . ($currentLevel - 1) . " has not been approved yet.";

                    // Rollback transaksi jika ada (walaupun belum ada operasi DB)
                    DB::rollBack();

                    // Kirim respons error yang sesuai
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 422); // 422 Unprocessable Entity
                    }
                    // Fallback jika request bukan AJAX
                    return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'Invalid Action')->with('message', $errorMessage);
                }
            }

            $requisition = $approvalLog->requisition->load('requester', 'customer');
            $approver = $approvalLog->approver;
            $redirectData = [];

            if ($action === 'reject') {
                $this->handleRejection($requisition, $approvalLog, $approver, $notes);
                $redirectData = [
                    'card_class' => 'reject',
                    'title'      => 'Requisition Rejected',
                    'new_status' => 'Rejected',
                ];
            } else {
                // Untuk 'approve' atau 'review'
                $this->handleApproval($requisition, $approvalLog, $approver, $notes);
                $nextStep = $this->getNextStep($requisition, $approvalLog->level);

                if ($nextStep['type'] === 'approver') {
                    $this->notifyNextApprover($requisition, $nextStep['log'], $nextStep['user']);
                    $this->notifyRequesterOfProgress($requisition, $approver);
                    $redirectData = ['new_status' => 'In Progress'];
                } else { // Approval selesai
                    $newStatus = $this->handlePostApprovalFlow($requisition);
                    $this->notifyRequesterOfFinalApproval($requisition, $approver);
                    $redirectData = ['new_status' => $newStatus];
                }

                $redirectData = array_merge($redirectData, [
                    'card_class' => 'success',
                    'title'      => !empty($notes) ? 'Approved with Review' : 'Approved without Review',
                ]);
            }

            // Logging terpusat
            $this->logApprovalActivity($requisition, $approver, $action, $notes, $approvalLog->level);

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Decision has been recorded successfully.']);
            }

            $finalRedirectData = array_merge($redirectData, [
                'no_srs'        => $requisition->no_srs,
                'customer_name' => $requisition->customer->name ?? 'N/A',
                'action_text'   => $action === 'reject' ? 'Rejected' : (!empty($notes) ? 'Approved with Review' : 'Approved'),
                'approver_name' => $approver->name,
            ]);

            return redirect()->route('approval.success')->with($finalRedirectData);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal proses approval #{$approvalLog->id}: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'A system error occurred.'], 500);
            }
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    // --- [BARU] HELPER FUNCTIONS FOR processApprovalStep ---

    private function handleRejection(Requisition $requisition, ApprovalLog $approvalLog, User $approver, ?string $notes)
    {
        $requisition->update(['status' => 'Rejected', 'route_to' => '-']);
        $approvalLog->update([
            'status'     => 'Rejected',
            'notes'      => $notes ?? 'Rejected without reason',
            'updated_at' => now(),
            'token'      => null,
        ]);

        // 1. Kirim notifikasi sistem (yang sudah ada sebelumnya)
        $this->notifyRequesterOfRejection($requisition, $approver);

        // [FIX] 2. Tambahkan dispatch job untuk mengirim NOTIFIKASI EMAIL
        if ($requester = $requisition->requester) {
            sendSample::dispatch(
                $requisition,
                $requester, // Penerima email adalah requester
                null,       // Tidak perlu token untuk notifikasi
                [
                    'mail_type'       => 'rejection_notification',
                    'approver_name'   => $approver->name,
                    'rejection_notes' => $notes,
                ]
            );
        }
    }

    private function handleApproval(Requisition $requisition, ApprovalLog $approvalLog, User $approver, ?string $notes)
    {
        $finalNotes = $notes ?? 'Approved without Review (Quick Action)';

        $approvalLog->update([
            'status'     => 'Approved',
            'notes'      => $finalNotes, // Selalu ada catatan
            'updated_at' => now(),
            'token'      => null,
        ]);
    }

    private function getNextStep(Requisition $requisition, int $currentLevel)
    {
        $nextApprovalLog = ApprovalLog::where('requisition_id', $requisition->id)
                                    ->where('level', '>', $currentLevel)
                                    ->orderBy('level', 'asc')->first();

        if ($nextApprovalLog && $nextApprover = $nextApprovalLog->approver) {
            return ['type' => 'approver', 'log' => $nextApprovalLog, 'user' => $nextApprover];
        }
        return ['type' => 'finished'];
    }

    // --- [BARU] HELPER FUNCTIONS FOR NOTIFICATIONS ---

    private function notifyRequesterOfRejection(Requisition $requisition, User $approver)
    {
        if ($requester = $requisition->requester) {
            $requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "Requisition #{$requisition->no_srs} Anda telah di-reject oleh {$approver->name}.",
                'url'            => route('sample-form.index'),
            ], $approver));
        }
    }

    private function notifyNextApprover(Requisition $requisition, ApprovalLog $nextApprovalLog, User $nextApprover)
    {
        $requisition->update(['status' => 'In Progress', 'route_to' => $nextApprover->name]);
        sendSample::dispatch($requisition, $nextApprover, $nextApprovalLog->token);
        $nextApprover->notify(new RequisitionNotification([
            'requisition_id' => $requisition->id,
            'srs_number'     => $requisition->no_srs,
            'message'        => "Requisition #{$requisition->no_srs} dari {$requisition->requester->name} menunggu approval Anda.",
            'url'            => route('sample-form.approval'),
        ], $requisition->requester));
    }

    private function notifyRequesterOfProgress(Requisition $requisition, User $approver)
    {
        if ($requester = $requisition->requester) {
            $requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "Requisition #{$requisition->no_srs} telah di-approve oleh {$approver->name}.",
                'url'            => route('sample-form.index'),
            ], $approver));
        }
    }

    private function notifyRequesterOfFinalApproval(Requisition $requisition, User $approver)
    {
        if ($requester = $requisition->requester) {
            $requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "Requisition #{$requisition->no_srs} Anda telah sepenuhnya di-approve.",
                'url'            => route('sample-form.index'),
            ], $approver));
        }
    }

    // --- [BARU] HELPER FUNCTION FOR LOGGING ---

    private function logApprovalActivity(Requisition $requisition, User $approver, string $action, ?string $notes, int $level)
    {
        $logMessage = '';
        $properties = [];

        if ($action === 'reject') {
            $logMessage = "Menolak (reject) Requisition #{$requisition->no_srs} pada level {$level}.";
            $properties = ['srs_number' => $requisition->no_srs, 'level' => $level, 'reason' => $notes];
            if($notes) $logMessage .= " Alasan: \"{$notes}\"";
        } else { // approve atau review
            $logMessage = "Menyetujui (approve) Requisition #{$requisition->no_srs} pada level {$level}.";
            $properties = ['srs_number' => $requisition->no_srs, 'level' => $level, 'notes' => $notes];
            if($notes) $logMessage .= " Dengan catatan: \"{$notes}\"";
        }

        activity()
            ->causedBy($approver)
            ->performedOn($requisition)
            ->useLog('sample - ' . strtolower($requisition->sub_category))
            ->event($action)
            ->withProperties($properties)
            ->log($logMessage);
    }

    /**
     * Memproses satu langkah di gudang (warehouse).
     */
    private function processWarehouseStep(Request $request, Tracking $tracking, ?string $notes)
    {
        DB::beginTransaction();
        try {
            // [MODIFIKASI] Buat pesan default yang lebih dinamis
            $defaultNote = "Proses {$tracking->current_position} berhasil disubmit tanpa notes.";
            $finalNotes = $notes ?: $defaultNote;

            $tracking->update([
                'token'        => null,
                'last_updated' => now(),
                'notes'        => $notes ?: $defaultNote, // <-- BARIS INI YANG DIUBAH
            ]);

            $causer = $this->findUserForStep($tracking->current_position);
            activity()
                ->causedBy($causer)
                ->performedOn($tracking->requisition)
                ->useLog('sample - ' . strtolower($tracking->requisition->sub_category))
                ->event('tracking')
                ->withProperties(['step'  => $tracking->current_position, 'notes' => $finalNotes])
                ->log("Completed warehouse step: {$tracking->current_position}.");

            $newStatus = $this->advanceWarehouseStep($tracking->requisition);
            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Decision has been recorded successfully.']);
            }

            $requisition = $tracking->requisition->load('customer');

            return redirect()->route('approval.success')->with([
                'card_class'    => 'success',
                'title'         => 'Warehouse Submitted Successfully',
                'new_status'    => $newStatus,
                'no_srs'        => $requisition->no_srs,
                'customer_name' => $requisition->customer->name ?? 'N/A',
                'action_text'   => 'Submitted',
                'approver_name' => $tracking->current_position
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal melanjutkan proses warehouse: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'A system error occurred.'], 500);
            }
            return redirect()->route('approval.success')->with('card_class', 'reject')->with('title', 'System Error');
        }
    }

    /**
     * Menangani alur kerja SETELAH semua approval manajerial selesai.
     */
    private function handlePostApprovalFlow(Requisition $requisition)
    {
        Log::info("Approval path selesai untuk Requisition #{$requisition->id}. Memulai alur proses.");

        $requisition->load('requester.department');
        $requesterDepartment = optional($requisition->requester)->department->name ?? null;

        switch ($requisition->sub_category) {
            case 'Packaging':
                $steps = [];
                if ($requesterDepartment === 'R&D') {
                    $steps = ['Inward WH Supervisor (Final Check)'];
                    Log::info("Requisition #{$requisition->id} dari R&D, alur langsung ke Final Check.");
                } else {
                    $steps = $requisition->print_batch
                        ? ['Inward WH Supervisor (Initial Check)', 'Material Support Supervisor', 'Inward WH Supervisor (Final Check)']
                        : ['Inward WH Supervisor (Final Check)'];
                    Log::info("Requisition #{$requisition->id} dari {$requesterDepartment}, alur berdasarkan print_batch.");
                }
                if (empty($steps)) return $this->notifyRequesterAsCompleted($requisition);
                foreach ($steps as $stepName) {
                    Tracking::create(['requisition_id' => $requisition->id, 'current_position' => $stepName, 'token' => Str::uuid()->toString()]);
                }
                return $this->advanceWarehouseStep($requisition);

            case 'Finished Goods':
                $steps = ['Outward WH Supervisor'];
                if (empty($steps)) return $this->notifyRequesterAsCompleted($requisition);
                foreach ($steps as $stepName) {
                    Tracking::create(['requisition_id' => $requisition->id, 'current_position' => $stepName, 'token' => Str::uuid()->toString()]);
                }
                return $this->advanceWarehouseStep($requisition);

            case 'Special Order':
                $headQaUser = User::whereHas('department', fn ($q) => $q->where('name', 'QM & HSE'))
                            ->whereHas('roles', fn ($q) => $q->where('name', 'head-QA'))
                            ->first();

                if ($headQaUser) {
                    $stepName = 'Waiting for QA/QM Form';
                    $token = Str::uuid()->toString();
                    Tracking::create([
                        'requisition_id'   => $requisition->id,
                        'current_position' => $stepName,
                        'notes'            => "Waiting for form to be filled by {$headQaUser->name}",
                        'token'            => $token,
                    ]);
                    $requisition->update(['status' => 'Approved', 'route_to' => $stepName]);

                    // [BARU] Logika untuk mengirim notifikasi DI DALAM SISTEM
                    $notificationData = [
                        'requisition_id' => $requisition->id,
                        'srs_number'     => $requisition->no_srs,
                        'message'        => "Form QA/QM untuk Requisition #{$requisition->no_srs} perlu dilengkapi.",
                        'url'            => route('sample-form.index', ['open_form' => $requisition->id]),
                    ];
                    // Mengirim notifikasi ke Head QA, dengan info "From" dari requester asli
                    $headQaUser->notify(new RequisitionNotification($notificationData, $requisition->requester));

                    // Logika email (yang sudah ada sebelumnya) tetap dijalankan
                    $formUrl = route('approval.response', ['token' => $token, 'action' => 'qa_form']);
                    dispatch(new sendSample($requisition, $headQaUser, $token, [
                        'mail_type' => 'qa_form_notification',
                        'form_url'  => $formUrl
                    ]))->delay(now()->addSeconds(3));

                    return $stepName;
                }
                Log::warning("Head of Department QA/QM tidak ditemukan untuk Requisition #{$requisition->id}.");

            default:
                Log::warning("Tidak ada alur proses untuk sub-category: {$requisition->sub_category}. Menyelesaikan requisition.");
                return $this->notifyRequesterAsCompleted($requisition);
        }
    }

    /**
     * Memajukan proses ke langkah gudang berikutnya atau menyelesaikan jika sudah selesai.
     */
    private function advanceWarehouseStep(Requisition $requisition)
    {
        $nextStep = Tracking::where('requisition_id', $requisition->id)
                            ->whereNull('last_updated')
                            ->orderBy('id', 'asc')
                            ->first();

        if ($nextStep) {
            $userForNextStep = $this->findUserForStep($nextStep->current_position);
            if ($userForNextStep) {
                dispatch(new sendSample($requisition, $userForNextStep, $nextStep->token, [
                    'mail_type'    => 'warehouse_process',
                    'process_step' => $nextStep->current_position,
                ]))->delay(now()->addSeconds(3));

                $requisition->update(['status' => 'Approved', 'route_to' => $nextStep->current_position]);
                return $nextStep->current_position;
            }
            Log::error("User tidak ditemukan untuk langkah: {$nextStep->current_position} di Requisition #{$requisition->id}.");
        }

        return $this->notifyRequesterAsCompleted($requisition);
    }

    /**
     * Mengubah status menjadi 'Completed' dan mengirim notifikasi ke requester.
     */
    private function notifyRequesterAsCompleted(Requisition $requisition)
    {
        $requisition->load('requester', 'approvalLogs.approver'); // Eager load relasi yang dibutuhkan
        $requisition->update(['status' => 'Completed', 'route_to' => '-']);

        // Hapus sisa token yang mungkin masih aktif
        Tracking::where('requisition_id', $requisition->id)->whereNotNull('token')->update(['token' => null]);

        if ($requester = $requisition->requester) {
            // 1. Kirim notifikasi EMAIL (ini sudah ada sebelumnya)
            dispatch(new sendSample($requisition, $requester, null, ['mail_type' => 'completed_notification']))->delay(now()->addSeconds(3));

            // 2. Kirim notifikasi SISTEM (ini yang ditambahkan)
            // Cari approver terakhir sebagai 'causer' notifikasi
            $lastApproverLog = $requisition->approvalLogs->where('status', 'Approved')->sortByDesc('level')->first();

            // Jika ada approver, gunakan dia. Jika tidak (misal: auto-complete), gunakan requester sebagai fallback.
            $causer = optional($lastApproverLog)->approver ?? $requester;

            $requester->notify(new RequisitionNotification([
                'requisition_id' => $requisition->id,
                'srs_number'     => $requisition->no_srs,
                'message'        => "Requisition #{$requisition->no_srs} Anda telah selesai diproses.",
                'url'            => route('sample-form.index'),
            ], $causer));
        }

        Log::info("Requisition #{$requisition->id} selesai. Notifikasi dikirim ke requester.");
        return 'Completed';
    }

    /**
     * Helper untuk membuat nomor SRS baru.
     */
    private function generateSrsNumber()
    {
        $prefix = 'S';
        $year = date('y');
        $month = date('m');
        $currentPrefix = "$prefix $year $month";
        $lastRequisition = Requisition::where('no_srs', 'LIKE', $currentPrefix . ' %')->orderBy('no_srs', 'desc')->first();
        $runningNumber = $lastRequisition ? (int)substr($lastRequisition->no_srs, -3) + 1 : 1;
        return $currentPrefix . ' ' . sprintf('%03d', $runningNumber);
    }

    /**
     * Helper untuk mencari user berdasarkan nama step proses.
     */
    private function findUserForStep(string $stepName)
    {
        if (str_contains($stepName, 'Inward WH Supervisor'))    return $this->findWarehouseUser('Inward WH Supervisor', 'WH0001');
        if (str_contains($stepName, 'Material Support Supervisor'))  return $this->findWarehouseUser('Material Support Supervisor', 'MS0001');
        if (str_contains($stepName, 'Outward WH Supervisor'))   return $this->findWarehouseUser('Outward WH Supervisor', 'WH0002');
        return null;
    }

    /**
     * Helper untuk mencari user gudang berdasarkan nama atau NIK fallback.
     */
    private function findWarehouseUser(string $name, string $fallbackNik)
    {
        return User::where('name', 'like', '%' . $name . '%')->first() ?? User::where('nik', $fallbackNik)->first();
    }

    //======================================================================
    // AJAX FUNCTIONS FOR FORM
    //======================================================================

    public function getAllItemMasters()
    {
        return response()->json(ItemMaster::select('id', 'item_master_code', 'item_master_name', 'unit')->get());
    }

    public function getItemDetailsByProducts(Request $request)
    {
        $request->validate(['product_ids' => 'required|array']);
        return response()->json(ItemDetail::whereIn('item_master_id', $request->product_ids)->get());
    }

    public function show($id)
    {
        $requisition = Requisition::with([
            'customer:id,name,address',
            'requester:nik,name,email,avatar',
            'requisitionItems.itemMaster:id,item_master_code,item_master_name,unit',
            'requisitionItems.itemDetail:id,item_detail_code,item_detail_name,unit',
            'requisitionSpecial',
            'approvalLogs' => fn($q) => $q->with('approver:nik,name,avatar')->orderBy('updated_at', 'asc'),
            'trackings' => fn($q) => $q->orderBy('last_updated', 'asc'),
        ])->findOrFail($id);

        $approvalPath = ApprovalPath::where('category', $requisition->category)
                        ->where('sub_category', $requisition->sub_category)
                        ->first();

        $history = [];

        // 1. Kejadian: Pembuatan Requisition
        $history[] = [
            'actor' => $requisition->requester->name ?? 'Requester',
            'avatar' => $requisition->requester->avatar ? asset($requisition->requester->avatar) : null,
            'action' => 'Created Requisition',
            'notes' => $requisition->objectives,
            'timestamp' => $requisition->created_at,
        ];

        // 2. Kejadian: Approval & Rejection
        foreach ($requisition->approvalLogs as $log) {
            if ($log->status !== 'Pending') {
                $actionText = 'Unknown';
                if ($log->status === 'Approved') {
                    $isDefaultNote = in_array($log->notes, ['Approved without Review', 'Approved without Review (Quick Action)']);

                    if (!empty($log->notes) && !$isDefaultNote && !str_starts_with($log->notes, 'Approved by')) {
                        $actionText = 'Approved with Review'; // Ini adalah review sungguhan
                    } else {
                        $actionText = 'Approved not Review'; // Ini adalah Quick Approve
                    }
                }
                $history[] = [
                    'actor' => $log->approver->name ?? 'Approver',
                    'avatar' => $log->approver->avatar ? asset($log->approver->avatar) : null,
                    'action' => $actionText,
                    'notes' => $log->notes,
                    'timestamp' => $log->updated_at,
                ];
            }
        }

        // 3. Kejadian: Proses Warehouse & QA
        foreach ($requisition->trackings as $tracking) {
            if ($tracking->last_updated) {
                $actorName = ($tracking->current_position === 'Waiting for QA/QM Form') ? 'QA/QM HSE Team' : $tracking->current_position;
                $history[] = [
                    'actor' => $actorName,
                    'avatar' => null, // [MODIFIKASI] Tim tidak punya avatar
                    'action' => 'Completed Step: ' . $tracking->current_position,
                    'notes' => $tracking->notes,
                    'timestamp' => $tracking->last_updated,
                ];
            }
        }

        // 4. Kejadian: Pembatalan
        if ($requisition->status === 'Recalled') {
            $history[] = [
                'actor' => $requisition->requester->name ?? 'Requester',
                'avatar' => $requisition->requester->avatar ? asset($requisition->requester->avatar) : null, // [MODIFIKASI]
                'action' => 'Recalled Requisition',
                'notes' => 'Requisition was Recalled by the requester.',
                'timestamp' => $requisition->updated_at,
            ];
        }

        usort($history, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        $responseData = $requisition->toArray();
        $responseData['history'] = $history;
        $responseData['sequence_approvers'] = $approvalPath ? $approvalPath->sequence_approvers : [];

        return response()->json($responseData);
    }

    public function edit($id)
    {
        $requisition = Requisition::with([
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial'
        ])->findOrFail($id);

        $responseData = $requisition->toArray();
        $responseData['selected_master_ids'] = $requisition->requisitionItems->pluck('item_master_id')->unique()->values()->all();
        $responseData['attached_material_types'] = $requisition->requisitionItems->pluck('material_type')->unique()->values()->all();
        $responseData['product_options'] = ItemMaster::select('id', 'item_master_code', 'item_master_name')
            ->get()
            ->map(fn($item) => ['id' => $item->id, 'text' => "[{$item->item_master_code}] {$item->item_master_name}"])
            ->toArray();

        if (request()->query('mode') === 'duplicate') {
            $responseData['new_srs'] = $this->generateSrsNumber();
        }

        return response()->json($responseData);
    }

    //======================================================================
    // OTHER PUBLIC FUNCTIONS
    //======================================================================

    public function showSuccessPage()
    {
        return session('title') ? view('page.sample.links.response-success') : redirect('/');
    }

    //======================================================================
    // [BARU] FUNGSI UNTUK HALAMAN REPORT
    //======================================================================

    public function reportsPage()
    {
        return view('page.sample.report.index');
    }

    public function printMultipleReport(Request $request)
    {
        $request->validate([
            'selected_ids'   => 'required|array',
            'selected_ids.*' => 'integer|exists:requisitions,id'
        ]);

        $requisitions = Requisition::with([
            'customer',
            'requester.department',
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial',
            'approvalLogs' => fn($q) => $q->orderBy('level', 'asc'),
            'approvalLogs.approver.roles'
        ])->whereIn('id', $request->selected_ids)->get();

        $revisionData = Revision::first();

        if ($requisitions->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dicetak.');
        }

        $pdf = Pdf::loadView('page.sample.report.print', [
            'requisitions' => $requisitions,
            'revision'     => $revisionData
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Bulk-RS-Sample-' . now()->format('Y-m-d') . '.pdf');
    }

    public function getReportsData(Request $request)
    {
        $query = Requisition::with(['requester', 'customer']) // Eager load relationships
            ->where('category', 'SAMPLE')
            ->select('requisitions.*');

        // Filter based on date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
                $query->whereBetween('request_date', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::error('Invalid date format for report filter: ' . $e->getMessage());
            }
        }

        // Filter by user if not a super-admin
        if (!Auth::user()->hasRole('super-admin')) {
            $query->where('requester_nik', Auth::user()->nik);
        }

        // Return the DataTables response without column modifications
        return DataTables::of($query)->make(true);
    }

    //======================================================================
    // [BARU] FUNGSI-FUNGSI UNTUK HALAMAN Logs INTERNAL
    //======================================================================

    /**
     * Menampilkan halaman daftar logs untuk user yang login.
     */
    public function logPage()
    {
        // Fungsi ini hanya me-return view. Logika ada di getlogsData().
        return view('page.sample.log.index');
    }

    public function getLogData()
    {
        $query = Activity::with(['causer', 'subject'])
            ->where(function ($q) {

                // 1. Ambil log baru Requisition (cth: 'sample-packaging')
                $q->where('log_name', 'like', 'sample%')

                // 2. Ambil log baru Approval Path (cth: 'path - sample')
                ->orWhere('log_name', 'path - sample')

                // 3. Ambil log lama (default) TAPI HANYA JIKA subject-nya
                //    adalah Requisition DENGAN KATEGORI "Sample"
                ->orWhere(function ($subQ) {
                    $subQ->where('log_name', 'default')
                         ->where('subject_type', Requisition::class)
                         ->whereHasMorph('subject', [Requisition::class], function ($reqQuery) {
                             $reqQuery->where('category', 'Sample');
                         });
                })

                // 4. Ambil log lama (default) TAPI HANYA JIKA subject-nya
                //    adalah ApprovalPath DENGAN KATEGORI "Sample"
                ->orWhere(function ($subQ) {
                    $subQ->where('log_name', 'default')
                         ->where('subject_type', ApprovalPath::class)
                         ->whereHasMorph('subject', [ApprovalPath::class], function ($pathQuery) {
                             $pathQuery->where('category', 'Sample');
                         });
                });
            })
            ->orderBy('created_at', 'desc');

        // Terapkan styling dari prompt pengguna
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('log_name', function ($log) {
                $logName = $log->log_name;
                $badgeClass = 'bg-dark';
                $icon = 'ph-scroll';

                // Logika untuk 'path - sample' atau 'default' (jika subject-nya ApprovalPath)
                if (str_starts_with($logName, 'path') || $log->subject_type === ApprovalPath::class) {
                    $logName = 'path - sample';
                    $badgeClass = 'bg-dark'; // Badge untuk approval path
                    $icon = 'ph-git-branch';
                }
                // Logika untuk 'sample - ...' atau 'default' (jika subject-nya Requisition)
                elseif (str_starts_with($logName, 'sample') || $log->subject_type === Requisition::class) {
                    if ($log->subject) {
                        $subCategory = strtolower($log->subject->sub_category);
                        $logName = 'sample - ' . $subCategory; // Standarkan nama log
                        switch ($subCategory) {
                            case 'packaging':
                                $badgeClass = 'bg-warning text-dark'; $icon = 'ph-package'; break;
                            case 'finished goods':
                                $badgeClass = 'bg-info'; $icon = 'ph-cube'; break;
                            case 'special order':
                                $badgeClass = 'bg-secondary'; $icon = 'ph-star'; break;
                            default:
                                $badgeClass = 'bg-primary'; $icon = 'ph-tag'; break;
                        }
                    } else {
                        $logName = 'sample - (unknown)'; // Jika subject terhapus
                    }
                }

                // Fallback untuk log 'default' yang tidak punya subject (seperti log ID 6 di screenshot Anda)
                if ($logName === 'default') {
                    $logName = 'System Log';
                }

                return '<span class="status-badge-lg ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($logName) . '</span>';
            })
            ->addColumn('subject_info', function ($log) {
                // Tampilkan No. SRS jika subject-nya Requisition
                if ($log->subject_type === Requisition::class && $log->subject) {
                    return '<span class="srs-badge">' . e($log->subject->no_srs) . '</span>';
                }

                // [FIX] Tampilkan Info Path dari relasi ATAU dari properties (jika subject sudah dihapus)
                if ($log->subject_type === ApprovalPath::class) {
                    // Coba ambil dari relasi dulu
                    $subCategory = optional($log->subject)->sub_category;

                    // Jika relasi null (karena subject dihapus), coba ambil dari properties
                    if (!$subCategory) {
                        $subCategory = $log->properties->get('sub_category');
                    }

                    $pathInfo = $subCategory ?? 'Non-Subcategory';
                    return '<span class="srs-badge" style="background: linear-gradient(135deg, #6c757d 0%, #343a40 100%);">PATH: ' . e($pathInfo) . '</span>';
                }

                return '<span class="status-badge-lg bg-secondary">N/A</span>';
            })
            // [BARU] Menambahkan kolom Subject ID yang bisa diklik
            ->addColumn('subject_id', function ($log) {
                $id = $log->subject_id;
                if (!$id) {
                    return 'N/A';
                }

                $subjectExists = !is_null($log->subject);

                if ($log->subject_type === Requisition::class) {
                    // Buat link ke halaman sample form, target _blank untuk buka tab baru
                    $url = route('sample-form.index');
                    return '<a href="' . $url . '" target="_blank" class="srs-badge" title="View in Sample Requisition Page">' . e($id) . '</a>';

                } elseif ($log->subject_type === ApprovalPath::class) {
                    // Buat link ke halaman approval path
                    $url = route('requisition.path');
                    $style = $subjectExists ? 'background-color: #5a6268;' : 'background-color: #dc3545; text-decoration: line-through;';
                    $title = $subjectExists ? 'View in Approval Path Page' : 'Subject has been deleted';
                    return '<a href="' . $url . '" target="_blank" class="srs-badge" style="' . $style . '" title="' . $title . '">' . e($id) . '</a>';
                }

                return e($id); // Fallback jika tipe tidak dikenali
            })
            ->addColumn('causer_info', function ($log) {
                $causerName = optional($log->causer)->name ?? 'System';
                $icon = $causerName === 'System' ? 'ph-robot' : 'ph-user-circle';
                return '
                    <div class="d-flex align-items-center causer-info">
                        <i class="ph-bold ' . $icon . ' me-2"></i>
                        <span>' . e($causerName) . '</span>
                    </div>';
            })
            ->editColumn('event', function ($log) {
                $event = strtolower($log->event ?? 'N/A');
                $badgeClass = 'bg-secondary';
                $icon = 'ph-info';
                switch ($event) {
                    case 'create': $badgeClass = 'bg-primary'; $icon = 'ph-plus-circle'; break;
                    case 'update': $badgeClass = 'bg-warning text-dark'; $icon = 'ph-pencil-simple'; break;
                    case 'delete': $badgeClass = 'bg-danger'; $icon = 'ph-trash'; break;
                    case 'approve': $badgeClass = 'bg-success'; $icon = 'ph-thumbs-up'; break;
                    case 'reject': $badgeClass = 'bg-danger'; $icon = 'ph-thumbs-down'; break;
                    case 'recall': $badgeClass = 'bg-danger'; $icon = 'ph-prohibit'; break;
                    case 'tracking': $badgeClass = 'bg-info'; $icon = 'ph-path'; break;
                    case 'resend': $badgeClass = 'bg-warning text-dark'; $icon = 'ph-paper-plane-tilt'; break;
                }
                return '<span class="status-badge-lg ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e(ucfirst($event)) . '</span>';
            })
            ->editColumn('created_at', function ($log) {
                return Carbon::parse($log->created_at)->format('d M Y, H:i:s');
            })
            ->rawColumns(['log_name', 'event', 'subject_info', 'causer_info', 'subject_id'])
            ->make(true);
    }

    //======================================================================
    // [BARU] FUNGSI-FUNGSI UNTUK HALAMAN APPROVAL INTERNAL
    //======================================================================

    /**
     * Menampilkan halaman daftar approval untuk user yang login.
     */
    public function approvalPage()
    {
        // Fungsi ini hanya me-return view. Logika ada di getApprovalData().
        return view('page.sample.approval.index');
    }

    public function getApprovalData()
    {
        $currentUser = Auth::user();

        $query = ApprovalLog::with([
            'requisition.requester:nik,name,avatar',
            'requisition:id,no_srs,sub_category,status,requester_nik',
            'approver:nik,name,avatar'
        ])
        ->join('requisitions', 'approval_logs.requisition_id', '=', 'requisitions.id');

        // =================================================================
        // LOGIKA INTI: Dibagi berdasarkan Role User
        // =================================================================

        if ($currentUser->hasRole('super-admin')) {

        } else {
            $query->where('approval_logs.approver_nik', $currentUser->nik);
            $query->where('approval_logs.status', 'Pending');
            $query->whereIn('requisitions.status', ['Pending', 'In Progress', 'Approved']);
            $query->where(function ($q) {
                $q->where('approval_logs.level', 1)
                ->orWhereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                            ->from('approval_logs as prev_log')
                            ->whereColumn('prev_log.requisition_id', 'approval_logs.requisition_id')
                            ->whereColumn('prev_log.level', DB::raw('approval_logs.level - 1'))
                            ->where('prev_log.status', 'Approved');
                });
            });
        }

        $query->select('approval_logs.*')->orderBy('approval_logs.id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_srs', fn($log) => '<span class="srs-badge"><i class="ph-bold ph-hash me-1"></i>' . e($log->requisition->no_srs ?? 'N/A') . '</span>')
            ->addColumn('requester', function($log) {
                $requester = optional($log->requisition)->requester;
                $avatar = $requester && $requester->avatar ? asset($requester->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $nik = e($requester->nik ?? '-');

                return '
                    <div>
                        <div class="status-badge-lg bg-dark d-flex align-items-center">
                            <img src="' . $avatar . '" alt="av" class="img-fluid rounded-circle me-1" style="width: 20px; height: 20px; object-fit: cover;">
                            <span>' . $nik . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('request_date', fn($log) => Carbon::parse($log->created_at)->format('d M Y, H:i'))
            ->addColumn('sub_category', function ($log) {
                $subCategory = $log->requisition->sub_category ?? 'N/A';
                $badgeClass = 'bg-secondary';
                if ($subCategory == 'Packaging') $badgeClass = 'bg-info';
                elseif ($subCategory == 'Finished Goods') $badgeClass = 'bg-warning';
                elseif ($subCategory == 'Special Order') $badgeClass = 'bg-secondary';
                return '<span class="status-badge-lg ' . $badgeClass . '">' . e($subCategory) . '</span>';
            })
            ->addColumn('level', function($log) { // Kolom ini dikembalikan
                $level = $log->level;
                return '<span class="status-badge-lg bg-primary"><i class="ph-bold ph-star me-1"></i>Lvl ' . $level . '</span>';
            })
            ->addColumn('status', function ($log) {
                $status = $log->requisition->status ?? 'N/A';
                $badgeClass = 'bg-secondary';
                $icon = 'ph-question'; // Ikon default

                switch ($status) {
                    case 'Pending':
                    case 'Submitted':
                        $badgeClass = 'bg-warning';
                        $icon = 'ph-paper-plane-tilt';
                        break;
                    case 'In Progress':
                        $badgeClass = 'bg-info';
                        $icon = 'ph-arrows-clockwise';
                        break;
                    case 'Approved':
                    case 'Completed':
                        $badgeClass = 'bg-success';
                        $icon = 'ph-check-circle';
                        break;
                    case 'Rejected':
                        $badgeClass = 'bg-danger';
                        $icon = 'ph-x-circle';
                        break;
                    case 'Recalled':
                        $badgeClass = 'bg-secondary';
                        $icon = 'ph-prohibit';
                        break;
                }
                return '<span class="status-badge-lg ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($status) . '</span>';
            })
            ->addColumn('approver', function($log) {
                $approver = optional($log)->approver;
                // Jika approver tidak ditemukan (misalnya NIK tidak valid), tampilkan NIK-nya saja
                if (!$approver) {
                    return e($log->approver_nik ?? 'N/A');
                }

                $avatar = $approver->avatar ? asset($approver->avatar) : asset('assets/images/logo/sinarmeadow.png');
                $nik = e($approver->nik ?? '-');

                return '
                    <div>
                        <div class="status-badge-lg bg-dark d-flex align-items-center">
                            <img src="' . $avatar . '" alt="av" class="img-fluid rounded-circle me-1" style="width: 20px; height: 20px; object-fit: cover;">
                            <span>' . $nik . '</span>
                        </div>
                    </div>
                ';
            })
            ->addColumn('action', function ($log) use ($currentUser) {
                $logStatus = $log->status;

                if ($logStatus === 'Pending') {
                    $requisitionStatus = $log->requisition->status;
                    if (in_array($requisitionStatus, ['Rejected', 'Recalled'])) {
                        $icon = $requisitionStatus === 'Rejected' ? 'ph-x-circle text-danger' : 'ph-prohibit text-secondary';
                        $title = 'Requisition ' . $requisitionStatus;
                        return '<div class="action-icon-container" data-bs-toggle="tooltip" title="' . $title . '"><i class="ph-bold ' . $icon . ' fs-4"></i></div>';
                    }

                    $token = $log->token;
                    $srs = $log->requisition->no_srs;
                    $requisitionId = $log->requisition_id;

                    $approveNoReviewBtn = '<button type="button" class="btn btn-sm btn-success action-btn" data-token="' . $token . '" data-action="approve" data-srs="' . $srs . '" data-bs-toggle="tooltip" title="Approve (No Review)"><i class="ph-bold ph-thumbs-up text-white"></i></button>';
                    $approveWithReviewBtn = '<button type="button" class="btn btn-sm btn-primary action-btn-modal" data-id="' . $requisitionId . '" data-token="' . $token . '" data-action="review" data-srs="' . $srs . '" data-bs-toggle="tooltip" title="Approve with Review"><i class="ph-bold ph-note-pencil text-white"></i></button>';
                    $rejectBtn = '<button type="button" class="btn btn-sm btn-danger action-btn-modal" data-id="' . $requisitionId . '" data-token="' . $token . '" data-action="reject" data-srs="' . $srs . '" data-bs-toggle="tooltip" title="Reject"><i class="ph-bold ph-thumbs-down text-white"></i></button>';
                    $resendBtn = ''; // Inisialisasi sebagai string kosong
                    if ($currentUser->hasRole('super-admin')) {
                        $resendBtn = '<button type="button" class="btn btn-sm btn-warning btn-resend-email" data-token="' . $log->token . '" data-bs-toggle="tooltip" title="Resend Email Notification"><i class="ph-bold ph-paper-plane-tilt text-dark"></i></button>';
                    }
                    return "<div class='d-flex gap-1 justify-content-center'>{$approveNoReviewBtn} {$approveWithReviewBtn} {$rejectBtn} {$resendBtn}</div>";

                } elseif ($logStatus === 'Approved') {
                    $approvalDate = Carbon::parse($log->updated_at)->format('d M Y H:i');
                    $title = 'Approved on ' . $approvalDate;
                    return '<div class="action-icon-container" data-bs-toggle="tooltip" title="' . $title . '"><i class="ph-bold ph-check-circle text-success fs-4"></i></div>';
                } elseif ($logStatus === 'Rejected') {
                    $rejectionDate = Carbon::parse($log->updated_at)->format('d M Y H:i');
                    $title = 'Rejected on ' . $rejectionDate;
                    return '<div class="action-icon-container" data-bs-toggle="tooltip" title="' . $title . '"><i class="ph-bold ph-x-circle text-danger fs-4"></i></div>';
                }
                return '<div class="action-icon-container" data-bs-toggle="tooltip" title="No Action Required"><i class="ph-bold ph-minus-circle text-muted fs-4"></i></div>';
            })
            ->rawColumns(['requester', 'no_srs', 'sub_category', 'level', 'status', 'approver', 'action'])
            ->make(true);
    }

    public function resendApprovalEmail(Request $request, $token)
    {
        // Cari log approval yang masih pending berdasarkan token LAMA
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return response()->json(['success' => false, 'message' => 'This approval task is no longer valid or has been processed.'], 404);
        }

        $currentLevel = $approvalLog->level;
            if ($currentLevel > 1) {
                $previousLevelLog = ApprovalLog::where('requisition_id', $approvalLog->requisition_id)
                                                ->where('level', $currentLevel - 1)
                                                ->first();

                // Jika log level sebelumnya belum 'Approved'
                if (!$previousLevelLog || $previousLevelLog->status !== 'Approved') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email cannot be resent. The approver at the previous level has not yet completed their action.'
                    ], 422); // 422 Unprocessable Entity
                }
            }

        // Ambil data yang diperlukan
        $requisition = $approvalLog->requisition;
        $approver = $approvalLog->approver;

        if (!$requisition || !$approver) {
            return response()->json(['success' => false, 'message' => 'Associated data not found.'], 404);
        }

        try {
            // Buat token baru dan update ke database
            $newToken = Str::uuid()->toString();
            $approvalLog->update(['token' => $newToken]);

            // Kirim ulang email dengan men-dispatch job menggunakan TOKEN BARU
            sendSample::dispatch($requisition, $approver, $newToken);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($requisition)
                ->useLog('sample - ' . strtolower($requisition->sub_category))
                ->event('resend')
                ->withProperties(['recipient' => $approver->name, 'level' => $approvalLog->level])
                ->log('Resent approval email to ' . $approver->name . ' with a new token.');

            return response()->json(['success' => true, 'message' => 'Approval email has been successfully resent to ' . $approver->name . '.']);

        } catch (\Exception $e) {
            Log::error('Failed to resend email for token ' . $token . ': ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend email. Please check the system logs.'], 500);
        }
    }
}
