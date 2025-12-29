<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\AccountGroup;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerClass;
use App\Models\Customer\CustomerFile;
use App\Models\Customer\Sales;
use App\Models\Customer\TOP;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;
// Removed duplicate import of DataTables

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Master\ApprovalPath;
use App\Models\Master\ApprovalLog;
use App\Jobs\CustomerJob;
use Carbon\Carbon;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer\CustomerItem;
use App\Mail\CustomerWelcomeMail;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use App\Traits\ApprovalTrait;

class CustomerController extends Controller
{
    use ApprovalTrait;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query();
            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $tglNpwp = $row->tanggal_npwp ? Carbon::parse($row->tanggal_npwp)->format('Y-m-d') : '';
                $tglNppkp = $row->tanggal_nppkp ? Carbon::parse($row->tanggal_nppkp)->format('Y-m-d') : '';

                $dataAttrs = '';
                $dataAttrs .= ' data-id="' . $row->id . '"';
                $dataAttrs .= ' data-user_id="' . e($row->user_id) . '"';
                $dataAttrs .= ' data-code="' . e($row->code) . '"';
                $dataAttrs .= ' data-name="' . e($row->name) . '"';
                $dataAttrs .= ' data-sort_name="' . e($row->sort_name) . '"';
                $dataAttrs .= ' data-customer_class="' . e($row->customer_class) . '"';
                $dataAttrs .= ' data-account_group="' . e($row->account_group) . '"';
                $dataAttrs .= ' data-address1="' . e($row->address1) . '"';
                $dataAttrs .= ' data-address2="' . e($row->address2) . '"';
                $dataAttrs .= ' data-address3="' . e($row->address3) . '"';
                $dataAttrs .= ' data-surat_menyurat_address="' . e($row->surat_menyurat_address) . '"';
                $dataAttrs .= ' data-city="' . e($row->city) . '"';
                $dataAttrs .= ' data-postal_code="' . e($row->postal_code) . '"';
                $dataAttrs .= ' data-country="' . e($row->country) . '"';
                $dataAttrs .= ' data-shipping_to_name="' . e($row->shipping_to_name) . '"';
                $dataAttrs .= ' data-shipping_to_address="' . e($row->shipping_to_address) . '"';
                $dataAttrs .= ' data-purchasing_manager_name="' . e($row->purchasing_manager_name) . '"';
                $dataAttrs .= ' data-purchasing_manager_email="' . e($row->purchasing_manager_email) . '"';
                $dataAttrs .= ' data-finance_manager_name="' . e($row->finance_manager_name) . '"';
                $dataAttrs .= ' data-finance_manager_email="' . e($row->finance_manager_email) . '"';
                $dataAttrs .= ' data-penagihan_nama_kontak="' . e($row->penagihan_nama_kontak) . '"';
                $dataAttrs .= ' data-penagihan_telepon="' . e($row->penagihan_telepon) . '"';
                $dataAttrs .= ' data-penagihan_address="' . e($row->penagihan_address) . '"';
                $dataAttrs .= ' data-email="' . e($row->email) . '"';
                $dataAttrs .= ' data-tax_contact_name="' . e($row->tax_contact_name) . '"';
                $dataAttrs .= ' data-tax_contact_email="' . e($row->tax_contact_email) . '"';
                $dataAttrs .= ' data-tax_contact_phone="' . e($row->tax_contact_phone) . '"';
                $dataAttrs .= ' data-npwp="' . e($row->npwp) . '"';
                $dataAttrs .= ' data-tanggal_npwp="' . $tglNpwp . '"';
                $dataAttrs .= ' data-nppkp="' . e($row->nppkp) . '"';
                $dataAttrs .= ' data-tanggal_nppkp="' . $tglNppkp . '"';
                $dataAttrs .= ' data-output_tax="' . e($row->output_tax) . '"';
                $dataAttrs .= ' data-no_pengukuhan_kaber="' . e($row->no_pengukuhan_kaber ?? '-') . '"';
                $dataAttrs .= ' data-term_of_payment="' . e($row->term_of_payment) . '"';
                $dataAttrs .= ' data-lead_time="' . e($row->lead_time) . '"';
                $dataAttrs .= ' data-credit_limit="' . e($row->credit_limit) . '"';
                $dataAttrs .= ' data-ccar="' . e($row->ccar) . '"';
                $dataAttrs .= ' data-bank_garansi="' . e($row->bank_garansi) . '"';
                $dataAttrs .= ' data-area="' . e($row->area) . '"';
                $dataAttrs .= ' data-join_date="' . e($row->join_date) . '"';
                $dataAttrs .= ' data-status="' . e($row->status) . '"';
                $dataAttrs .= ' data-route_to="' . e($row->route_to) . '"';
                $dataAttrs .= ' data-status_approval="' . e($row->status_approval) . '"';
                $dataAttrs .= ' data-created_by="' . e($row->created_by) . '"';

                $pathNpwp = $row->file_npwp ? asset('storage/' . $row->file_npwp) : '';
                $pathNib  = $row->file_nib ? asset('storage/' . $row->file_nib) : '';
                $pathKtp  = $row->file_ktp ? asset('storage/' . $row->file_ktp) : '';

                $dataAttrs .= ' data-file_npwp_path="' . $pathNpwp . '"';
                $dataAttrs .= ' data-file_nib_path="' . $pathNib . '"';
                $dataAttrs .= ' data-file_ktp_path="' . $pathKtp . '"';

                return '
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-info btn-show-customer" ' . $dataAttrs . '>
                        <i class="fa-solid fa-eye text-white"></i>
                    </button>
                    <form action="' . route('customers.destroy', $row->id) . '" method="POST" class="delete-form delete-customer-btn" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt text-white"></i>
                        </button>
                    </form>
                </div>
            ';
            })
            ->addColumn('credit_limit_formatted', function ($row) {
                return '<div class="badge status-badge-lg bg-warning">
                            IDR ' . number_format($row->credit_limit, 0, ',', '.') . '
                        </div>';
            })

            ->addColumn('financial_info', function ($row) {
                if ($row->bank_garansi === 'YA') {
                    $bgBadge = '<span class="srs-badge"><i class="fas fa-file-contract me-1"></i> BG: YES</span>';
                } else {
                    $bgBadge = '<span class="badge bg-secondary opacity-50" style="font-size: 0.75em;">BG: NO</span>';
                }

                return '<div class="d-flex flex-column">
                            <span class="fw-bold text-primary mb-1" style="font-size: 0.95em;">'. $row->term_of_payment .'</span>
                            <div>' . $bgBadge . '</div>
                        </div>';
            })

            ->addColumn('status_approval', function ($row) {
                $baseClass = match($row->status_approval) {
                    'Approved', 'Completed' => 'bg-success', // Hijau Gradient
                    'Rejected', 'Canceled' => 'bg-danger',   // Merah Gradient
                    'Processing' => 'bg-primary',            // Ungu Gradient (sesuai CSS Anda)
                    'Pending' => 'bg-warning',               // Oranye Gradient
                    default => 'bg-secondary'                // Abu Gradient
                };

                $icon = match($row->status_approval) {
                    'Approved', 'Completed' => '<i class="ph-bold ph-check-circle me-1"></i>',
                    'Rejected', 'Canceled' => '<i class="ph-bold ph-x-circle me-1"></i>',
                    'Processing' => '<i class="ph-bold ph-arrows-clockwise ph-spin me-1"></i>',
                    default => '<i class="ph-bold ph-clock me-1"></i>'
                };

                return '<span class="badge status-badge-lg ' . $baseClass . '">' . $icon . strtoupper($row->status_approval) . '</span>';
            })

            ->addColumn('route_to', function ($row) {
                 if($row->status_approval === 'Approved' || $row->status_approval === 'Completed'){
                    return '<span class="badge route-to-badge-lg bg-info text-white"><i class="ph-bold ph-check-circle me-1 text-white"></i>-</span>';
                }

                return '<span class="badge route-to-badge-lg bg-info text-white">
                            <i class="ph-bold ph-user me-1"></i> ' . strtoupper($row->route_to ?? '-') . '
                        </span>';
            })

            ->addColumn('status', function ($row) {
                $badge = $row->status === 'Active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge status-badge-lg ' . $badge . '">' . strtoupper($row->status) . '</span>';
            })
            ->rawColumns(['credit_limit_formatted', 'financial_info', 'status_approval', 'route_to', 'status', 'action'])
            ->make(true);
        }

        $sales = Sales::with(['user.position', 'branch', 'region'])->get();
        $top = TOP::all();
        $accountgroup = AccountGroup::all();
        $customerClass = CustomerClass::all();
        $pendingCount = Customer::whereIn('status_approval', ['Pending', 'Processing'])->count();
        $processingCount = Customer::where('status_approval', 'Processing')->count();
        $approvedCount = Customer::whereIn('status_approval', ['Approved', 'Completed'])->count();
        $activeCount = Customer::where('bank_garansi', 'YA')->count();
        $inactiveCount = Customer::where(function($q) {
            $q->where('bank_garansi', '!=', 'YA')
              ->orWhereNull('bank_garansi');
        })->count();
        $approvalStatuses = Customer::whereNotNull('status_approval')
                                ->distinct()
                                ->pluck('status_approval');

        $accountStatuses = Customer::whereNotNull('status')
                                ->distinct()
                                ->pluck('status');

        return view('page.customer.index', compact(
            'sales', 'top', 'accountgroup','customerClass', 'pendingCount',
            'processingCount', 'approvedCount', 'activeCount', 'inactiveCount', 'approvalStatuses', 'accountStatuses'));
    }

    public function show(Customer $customer)
    {
        return $customer->load(['sales.user', 'files', 'bankGaransis', 'bgRecommendations', 'creditLimits']);
    }

    public function store(CustomerRequest $request)
    {

        $category = 'Customer';
        $subCategory = 'CBD';

        $pathExists = ApprovalPath::where('category', $category)
            ->where(function ($q) use ($subCategory) {
                // Jika subCategory spesifik (CBD), cari yang match atau yang null (umum)
                if (!empty($subCategory)) {
                    $q->where('sub_category', $subCategory)
                    ->orWhereNull('sub_category')
                    ->orWhere('sub_category', '');
                } else {
                    // Jika subCategory kosong, cari yang null saja
                    $q->whereNull('sub_category')
                    ->orWhere('sub_category', '');
                }
            })
            ->exists();

        if (!$pathExists) {
            return response()->json([
                'success' => false,
                'message' => 'GAGAL: Proses alur Approval tidak ditemukan. Mohon hubungi Administrator untuk membuat alur persetujuan terlebih dahulu.'
            ], 422);
        }

        $createdCustomer = null;
        $logs = collect();
        $firstLog = null;

        // Wrap DB changes in a transaction to keep consistency
        DB::transaction(function () use ($request, &$createdCustomer, &$logs, &$firstLog) {
            $user = Auth::user();

            // 1. SIMPAN DATA CUSTOMER DULU (Agar dapat ID)
            $customerData = $request->except(['file_npwp', 'file_nib', 'file_ktp', 'items']);
            if ($request->filled('top_calc')) {
                $customerData['top_calc'] = $request->top_calc;
            } else {
                $termVal = $request->term_of_payment;
                $customerData['top_calc'] = ($termVal === 'CBD') ? 0 : (int) $termVal;
            }

            // Pastikan lead_time null atau 0 tersimpan sebagai 0 (Default database usually 0)
            if(empty($customerData['lead_time'])) {
                $customerData['lead_time'] = 0;
            }

            $grandTotal = 0;

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    // Pastikan data valid angka
                    $qty = (float) ($item['quantity'] ?? 0);
                    $price = (float) ($item['price'] ?? 0);

                    // Tambahkan ke Grand Total
                    $grandTotal += ($qty * $price);
                }
            }

            // Masukkan hasil hitungan ke array data yang akan disimpan
            $customerData['customer_total'] = $grandTotal;

            $createdCustomer = Customer::create($customerData);

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    // Pastikan data tidak kosong
                    if (!empty($item['item_name']) && !empty($item['quantity'])) {
                        CustomerItem::create([
                            'customer_id' => $createdCustomer->id,
                            'item_name' => $item['item_name'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'] ?? 0,
                        ]);
                    }
                }
            }

            activity()
                ->causedBy($user)
                ->performedOn($createdCustomer)
                ->useLog('customer')
                ->event('create')
                ->withProperties([
                    'name' => $createdCustomer->name,
                    'created_by' => $user->name
                ])
                ->log("Created new customer: {$createdCustomer->name}");

            // 2. PROSES UPLOAD FILE (Folder: customer_files/{ID})
            $storageFolder = 'customer_files/' . $createdCustomer->id;

            $fileData = [
                'customer_id' => $createdCustomer->id,
                'npwp_file' => null,
                'nib_siup_file' => null,
                'ktp_file' => null
            ];

            // Simpan File ke dalam folder ID tersebut
            if ($request->hasFile('file_npwp')) {
                $fileData['npwp_file'] = $request->file('file_npwp')->store($storageFolder, 'public');
            }

            if ($request->hasFile('file_nib')) {
                $fileData['nib_siup_file'] = $request->file('file_nib')->store($storageFolder, 'public');
            }

            if ($request->hasFile('file_ktp')) {
                $fileData['ktp_file'] = $request->file('file_ktp')->store($storageFolder, 'public');
            }

            // Simpan path ke database
            CustomerFile::create($fileData);

            $subCategory = 'CBD';

            $logs = $this->generateApprovalLogs($user, $createdCustomer->id, 'Customer', $subCategory);

            $firstLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $createdCustomer->id)
                ->orderBy('level', 'asc')
                ->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $createdCustomer->update(['route_to' => $firstApprover->name, 'status_approval' => 'Pending']);
                } else {
                    $createdCustomer->update(['status_approval' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $createdCustomer->update(['status_approval' => 'Completed', 'route_to' => 'Finished (No Path)']);
            }
        });

        // Ensure customer was created before proceeding
        if (! $createdCustomer) {
            return response()->json(['success' => false, 'message' => 'Failed to create customer'], 500);
        }

        try {
            $firstLog = $logs->firstWhere('level', 1);

            if ($firstLog) {
                $approverNik = $firstLog['approver_nik'];
                $approverUser = User::where('nik', $approverNik)->first();

                if ($approverUser && $approverUser->email) {
                    // Siapkan Data Penerima (Cuma 1 orang: Level 1)
                    $recipients = [
                        [
                            'nik' => $approverUser->nik,
                            'email' => $approverUser->email,
                            'name' => $approverUser->name,
                            'level' => $firstLog['level'], // Akses pakai array
                            'is_first' => true,
                        ]
                    ];

                    // Ambil Token Level 1 (Akses pakai array)
                    $token = $firstLog['token'];

                    // Dispatch Job
                    CustomerJob::dispatch($createdCustomer->id, $recipients, $token, 'approval');

                    Log::info("Email approval level 1 dikirim ke: " . $approverUser->email);
                } else {
                    Log::warning("User Level 1 tidak punya email atau NIK tidak ditemukan: " . $approverNik);
                }
            }

            // (Opsional) Notif ke Admin
            // $adminEmail = config('mail.from.address');
            // if ($adminEmail) {
            //     // Uncomment jika ingin kirim notif ke admin juga
            //     CustomerJob::dispatch($createdCustomer->id, [['nik' => null, 'email' => $adminEmail, 'name' => 'Admin', 'level' => null, 'is_first' => false]], null, 'notification');
            // }

        } catch (\Exception $e) {
            Log::error('Error dispatching CustomerJob', [
                'customer_id' => $createdCustomer->id ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Customer created successfully!', 'data' => $createdCustomer], 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->all());
        activity()
        ->causedBy(Auth::user())
        ->performedOn($customer)
        ->useLog('customer')
        ->event('update')
        ->withProperties([
            'attributes' => $request->all(), // Data baru
            // 'old' => $oldData // Jika ingin mencatat data lama
        ])
        ->log("Updated customer data: {$customer->name}");
        return response()->json(['success' => true, 'message' => 'Customer updated successfully!', 'data' => $customer]);
    }

    public function destroy(Customer $customer)
    {
        activity()
        ->causedBy(Auth::user())
        ->performedOn($customer)
        ->useLog('customer')
        ->event('delete')
        ->withProperties([
            'name' => $customer->name
        ])
        ->log("Deleted customer: {$customer->name}");
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
    }

    public function storeFile(Request $request, Customer $customer)
    {
        $file = $customer->files()->create($request->all());
        return response()->json(['success' => true, 'message' => 'File uploaded successfully!', 'data' => $file], 201);
    }

    public function updateFile(Request $request, Customer $customer, $fileId)
    {
        $file = CustomerFile::where('customer_id', $customer->id)->findOrFail($fileId);
        $file->update($request->all());
        return response()->json(['success' => true, 'message' => 'File updated successfully!', 'data' => $file]);
    }

    public function destroyFile(Customer $customer, $fileId)
    {
        $file = CustomerFile::where('customer_id', $customer->id)->findOrFail($fileId);
        $file->delete();
        return response()->json(['success' => true, 'message' => 'File deleted successfully!']);
    }

    public function viewApprovalPage(Request $request, $token)
    {
        // 1. Cek Token & Status
        // Token "Hangus" jika status log bukan 'Pending' lagi
        $log = ApprovalLog::where('token', $token)->first();

        if (!$log || $log->status !== 'Pending') {
            return view('page.customer.links.approval-invalid');
        }

        $customer = Customer::with(['user', 'accountGroup', 'customerClass'])
                    ->findOrFail($log->related_id);

        $preSelectedAction = $request->query('pre_action', 'approve');

        // Jika Quick Approve, langsung proses tanpa buka form
        if ($preSelectedAction === 'approve') {
            return $this->processApprovalInternal($token, 'approve', null, $customer);
        }

        // Jika Review/Reject, buka form
        return view('page.customer.links.approval-form', [
            'customer' => $customer,
            'token' => $token,
            'log' => $log,
            'preSelectedAction' => $preSelectedAction
        ]);
    }

    public function approvalAction(Request $request, $customerId)
    {
        $request->validate([
            'token' => 'required|string',
            'action' => 'required|in:approve,reject,review',
            'notes' => 'required|string',
        ]);

        $customer = Customer::findOrFail($customerId);

        return $this->processApprovalInternal(
            $request->input('token'),
            $request->input('action'),
            $request->input('notes'),
            $customer
        );
    }

    private function processApprovalInternal($token, $action, $notes, $customer)
    {
        // Kunci Data agar tidak race condition
        $currentLog = ApprovalLog::where('token', $token)
            ->where('related_id', $customer->id)
            ->where('category', 'Customer')
            ->where('status', 'Pending') // Pastikan hanya memproses yang Pending
            ->first();

        if (!$currentLog) {
            return view('page.customer.links.approval-invalid');
        }

        $actor = User::where('nik', $currentLog->approver_nik)->first();

        DB::beginTransaction();
        try {
            $isFinanceAdjuster = $actor && ($actor->hasRole('manager-finance') || $actor->hasRole('head-finance'));
            if ($action === 'review' && $isFinanceAdjuster) {
                if (request()->has('update_top') || request()->has('update_lead_time')) {
                    $customer->update([
                        'term_of_payment' => request('update_top'), // Update TOP
                        'lead_time'       => request('update_lead_time'), // Update Lead Time
                        'credit_limit'    => request('update_credit_limit_value') // Update Credit Limit Hasil Hitung Ulang
                    ]);

                    $notes .= "\n[System]: Terms & Limit adjusted by Finance (" . $actor->name . ")";
                }
            }
            if (($action === 'approve' || $action === 'review') && $actor && $actor->hasRole('it')) {

                if (!request()->filled('update_code') || !request()->filled('update_join_date')) {
                    throw new \Exception("Sebagai IT, Anda wajib mengisi Customer Code dan Join Date.");
                }

                $customer->update([
                    'code' => request('update_code'),
                    'join_date' => request('update_join_date'),
                    'status_approval' => 'Approved',
                    'route_to' => 'Finished',
                    'status' => 'Active'
                ]);

                $notes .= "\n[System]: Customer Code & Join Date set by IT (" . $actor->name . ")";

                if ($customer->email) {
                    try {
                        Mail::to($customer->email)
                            ->send(new CustomerWelcomeMail($customer));

                        $notes .= "\n[System]: Welcome Email sent to Customer ($customer->email).";
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim Welcome Email ke Customer: " . $e->getMessage());
                        $notes .= "\n[System Error]: Gagal kirim email ke customer.";
                    }
                }

                // Logic existing untuk notifikasi internal ke Sales bahwa proses selesai
                $requester = $customer->user;
                if ($requester && $requester->email) {
                    $recipients = [[
                        'email' => $requester->email,
                        'name' => $requester->name,
                        'level' => 'Requester',
                        'is_first' => false
                    ]];
                    CustomerJob::dispatch($customer->id, $recipients, null, 'completed');
                }
            }

            // --- 1. Tentukan Status & Notes ---
            $dbStatus = '';
            $dbNotes = '';

            if ($action === 'approve') {
                $dbStatus = 'Approved';
                $dbNotes = 'Approve tanpa notes'; // Default text
            }
            elseif ($action === 'review') {
                $dbStatus = 'Approved';
                $dbNotes = $notes; // Gunakan notes dari inputan
            }
            elseif ($action === 'reject') {
                $dbStatus = 'Rejected';
                $dbNotes = $notes;
            }

            $logMessage = '';
            switch ($action) {
                case 'approve':
                    $logMessage = "Approved Customer: {$customer->name} (Level {$currentLog->level})";
                    break;
                case 'review':
                    $logMessage = "Reviewed Customer: {$customer->name} with notes. (Level {$currentLog->level})";
                    break;
                case 'reject':
                    $logMessage = "Rejected Customer: {$customer->name}. Reason: {$notes}";
                    break;
            }

            activity()
                ->causedBy($actor) // Actor diambil dari user pemilik token (approver)
                ->performedOn($customer)
                ->useLog('customer')
                ->event($action) // approve, review, atau reject
                ->withProperties([
                    'level' => $currentLog->level,
                    'status' => $dbStatus,
                    'notes' => $notes,
                    'approver_name' => $actor->name ?? 'System'
                ])
                ->log($logMessage);

            // --- 2. Update Log (Token Hangus) ---
            $currentLog->update([
                'status' => $dbStatus,
                'notes' => $dbNotes,
                'updated_at' => now(),
                'token' => null,
            ]);

            // --- 3. Cek Alur Selanjutnya ---
            if ($dbStatus === 'Approved') {

                // Cek apakah ada level selanjutnya di database
                $nextLevel = $currentLog->level + 1;
                $nextLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('level', $nextLevel)
                    ->first();

                if ($nextLog) {
                    // --- KASUS A: MASIH ADA LEVEL SELANJUTNYA ---

                    $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                    $nextApproverName = $nextApproverUser ? $nextApproverUser->name : $nextLog->approver_nik;

                    // Update Status Customer jadi Processing
                    $customer->update([
                        'status_approval' => 'Processing',
                        'route_to' => $nextApproverName
                    ]);

                    // Kirim Email ke Next Approver
                    if ($nextApproverUser && $nextApproverUser->email) {
                        $recipients = [[
                            'nik' => $nextApproverUser->nik,
                            'email' => $nextApproverUser->email,
                            'name' => $nextApproverUser->name,
                            'level' => $nextLog->level,
                            'is_first' => false
                        ]];

                        // Dispatch Job untuk Next Approver
                        CustomerJob::dispatch($customer->id, $recipients, $nextLog->token, 'approval');
                        Log::info("APPROVAL FLOW: Customer #{$customer->id} lanjut ke Level {$nextLog->level}. Email dikirim ke: {$nextApproverUser->email}");
                    } else {
                        // [LOG TAMBAHAN] Warning jika user tidak punya email
                        Log::warning("APPROVAL FLOW: Gagal kirim email ke Level {$nextLog->level}. User/Email tidak ditemukan untuk NIK: {$nextLog->approver_nik}");
                    }

                } else {
                    // --- KASUS B: TIDAK ADA LEVEL SELANJUTNYA (FINISH) ---

                    $customer->update([
                        'status_approval' => 'Approved',
                        'route_to' => 'Finished',
                        'status' => 'Active' // Customer Resmi Aktif
                    ]);

                    // Kirim Email "Completed" ke Requester (Sales yang buat)
                    $requester = $customer->user; // Relasi ke User pembuat
                    if ($requester && $requester->email) {
                        $recipients = [[
                            'email' => $requester->email,
                            'name' => $requester->name,
                            'level' => 'Requester',
                            'is_first' => false
                        ]];

                        // Dispatch Job tipe 'completed'
                        CustomerJob::dispatch($customer->id, $recipients, null, 'completed');
                        Log::info("APPROVAL FLOW: Customer #{$customer->id} COMPLETED (Approved). Email notifikasi dikirim ke Requester: {$requester->email}");
                    }
                }

            } elseif ($dbStatus === 'Rejected') {
                // --- KASUS C: DITOLAK (REJECTED) ---

                // Batalkan semua log level diatasnya (jika ada)
                ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Canceled', 'token' => null]);

                $customer->update([
                    'status_approval' => 'Rejected',
                    'route_to' => 'Rejected by ' . $currentLog->approver_nik
                ]);

                // Opsional: Kirim notifikasi Reject ke Requester
                $requester = $customer->user;
                if ($requester && $requester->email) {
                     $recipients = [[
                        'email' => $requester->email,
                        'name' => $requester->name,
                    ]];
                    CustomerJob::dispatch($customer->id, $recipients, null, 'rejected');
                    Log::info("APPROVAL FLOW: Customer #{$customer->id} REJECTED oleh {$actor->name}. Email rejection dikirim ke: {$requester->email}");
                }
            }

            DB::commit();

            // Tampilkan Halaman Sukses
            return view('page.customer.links.approval-success', [
                'action' => $action, // kirim action asli (review/approve) utk pesan beda di view jika perlu
                'customerName' => $customer->name,
                'routeTo' => $customer->route_to,
                'statusApproval' => $customer->status_approval
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval Process Error: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat memproses approval.');
        }
    }

    /**
     * Menampilkan halaman daftar approval untuk user yang login.
     */
    public function approvalPage()
    {
        $user = Auth::user();

        // 1. Base Query ke Table ApprovalLog (Category Customer)
        $logQuery = ApprovalLog::where('category', 'Customer');

        // 2. Jika bukan Super Admin, filter berdasarkan NIK Approver
        if (!$user->hasRole('super-admin')) {
            $logQuery->where('approver_nik', $user->nik);
        }

        // 3. Hitung Counter Berdasarkan Status Log
        $pendingCount = (clone $logQuery)->where('status', 'Pending')->count();
        $approvedCount = (clone $logQuery)->where('status', 'Approved')->count();

        // 4. Data Pendukung Lainnya (Tetap dari table Customer untuk Active/Inactive)
        $activeCount = Customer::where('status', 'Active')->count();
        $inactiveCount = Customer::where('status', 'Inactive')->count();

        // Dropdown Filters
        $approvalStatuses = ApprovalLog::where('category', 'Customer')->distinct()->pluck('status');
        $accountStatuses = Customer::whereNotNull('status')->distinct()->pluck('status');

        // Data Master (Untuk modal view/filter)
        $sales = Sales::with(['user.position', 'branch', 'region'])->get();
        $top = TOP::all();
        $accountgroup = AccountGroup::all();
        $customerClass = CustomerClass::all();

        return view('page.customer.approval.index', compact(
            'sales', 'top', 'accountgroup', 'customerClass',
            'pendingCount', 'approvedCount',
            'activeCount', 'inactiveCount',
            'approvalStatuses', 'accountStatuses'
        ));
    }

    public function getApprovalData()
    {
        $currentUser = Auth::user();

        // 1. Base Query
        $query = ApprovalLog::with('approver')
            ->select(
                'approval_logs.*',
                'customers.id as customer_id',
                'customers.code',
                'customers.name as customer_name',
                'customers.status_approval as customer_status',
                'customers.route_to',
                'customers.created_by'
            )
            ->join('customers', 'approval_logs.related_id', '=', 'customers.id')
            ->where('approval_logs.category', 'Customer');

        // Hanya ambil log yang statusnya Pending (Tugas user)
        $query->where('approval_logs.status', 'Pending');

        // Pastikan status customer juga masih dalam proses (belum Reject/Cancel global)
        $query->whereIn('customers.status_approval', ['Pending', 'Processing']);

        if (!$currentUser->hasRole('super-admin')) {
            // Tampilkan semua task Pending milik user ini,
            // TANPA filter "orWhereExists" agar User Level 2 tetap bisa melihat barisnya meski dikunci.
            $query->where('approval_logs.approver_nik', $currentUser->nik);
        }

        $query->orderBy('customers.created_at', 'desc')
            ->orderBy('approval_logs.level', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()

            // --- KOLOM BARU: APPROVER NIK ---
            ->addColumn('approver_nik', function ($row) {
                return '<span class="badge status-badge-lg bg-primary">' . e($row->approver_nik) . '</span>';
            })

            // --- KOLOM BARU: LEVEL ---
            ->addColumn('level', function ($row) {
                return '<span class="badge status-badge-lg bg-info">Level ' . $row->level . '</span>';
            })

            // --- LOGIC VALIDASI: Cek apakah boleh action? ---
            ->addColumn('is_actionable', function ($row) {
                // Jika Level 1, pasti boleh action (karena dia awal)
                if ($row->level == 1) {
                    return true;
                }

                // Jika Level > 1, Cek apakah Level sebelumnya (level - 1) sudah Approved?
                $prevLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $row->customer_id)
                    ->where('level', $row->level - 1)
                    ->first();

                // Boleh action HANYA JIKA previous log ada DAN statusnya 'Approved'
                if ($prevLog && $prevLog->status === 'Approved') {
                    return true;
                }

                return false;
            })

            // --- Existing Columns ---
            ->addColumn('customer_name', function ($row) {
                return '<div>
                            <div class="fw-bold text-dark">' . e($row->customer_name) . '</div>
                        </div>';
            })

            ->addColumn('status_approval', function ($row) {
                $status = $row->customer_status;
                $baseClass = match($status) {
                    'Approved', 'Completed' => 'bg-success',
                    'Rejected', 'Canceled' => 'bg-danger',
                    'Processing' => 'bg-primary',
                    'Pending' => 'bg-warning',
                    default => 'bg-secondary'
                };
                return '<span class="badge status-badge-lg ' . $baseClass . '">' . strtoupper($status ?? 'N/A') . '</span>';
            })

            ->addColumn('route_to', function ($row) {
                return '<span class="badge route-to-badge-lg bg-info text-white">
                            <i class="ph-bold ph-user me-1"></i> ' . strtoupper($row->route_to ?? '-') . '
                        </span>';
            })

            ->addColumn('action', function ($row) {
                $token = $row->token;
                $customerName = e($row->customer_name);
                $customerId = $row->customer_id;

                $canAction = true;
                $waitingMessage = "";

                if ($row->level > 1) {
                    $prevLog = ApprovalLog::where('category', 'Customer')
                        ->where('related_id', $row->customer_id)
                        ->where('level', $row->level - 1)
                        ->first();

                    if (!$prevLog || $prevLog->status !== 'Approved') {
                        $canAction = false;
                        $waitingMessage = "Waiting for Level " . ($row->level - 1);
                    }
                }

                // Jika TIDAK BISA Action (Terkunci)
                if (!$canAction) {
                    return '<button type="button" class="btn btn-sm btn-secondary"
                            onclick="Swal.fire(\'Locked\', \'Anda harus menunggu '. $waitingMessage .' melakukan approval terlebih dahulu.\', \'warning\')">
                            <i class="ph-bold ph-lock-key text-white"></i> Locked
                            </button>';
                }

                // Jika BISA Action
                $btnApprove = '<button type="button" class="btn btn-sm btn-success action-btn"
                                data-id="'.$customerId.'"
                                data-token="'.$token.'"
                                data-action="approve"
                                data-name="'.$customerName.'"
                                data-bs-toggle="tooltip" title="Quick Approve">
                                <i class="ph-bold ph-thumbs-up text-white"></i></button>';

                $btnReview = '<button type="button" class="btn btn-sm btn-primary action-btn-modal"
                                data-id="'.$customerId.'"
                                data-token="'.$token.'"
                                data-action="review"
                                data-name="'.$customerName.'"
                                data-bs-toggle="tooltip" title="Review with Notes">
                                <i class="ph-bold ph-note-pencil text-white"></i></button>';

                $btnReject = '<button type="button" class="btn btn-sm btn-danger action-btn-modal"
                                data-id="'.$customerId.'"
                                data-token="'.$token.'"
                                data-action="reject"
                                data-name="'.$customerName.'"
                                data-bs-toggle="tooltip" title="Reject">
                                <i class="ph-bold ph-thumbs-down text-white"></i></button>';

                $btnResend = '';
                if ($row->status === 'Pending') {
                    $approverName = $row->approver ? $row->approver->name : $row->approver_nik;
                    $btnResend = '<button type="button" class="btn btn-sm btn-warning btn-resend-email"
                                    data-token="'.$token.'"
                                    data-approver-name="'.e($approverName).'"
                                    data-bs-toggle="tooltip" title="Resend Email Notification">
                                    <i class="ph-bold ph-paper-plane-tilt text-white"></i>
                                </button>';
                }

                return "<div class='d-flex gap-1 justify-content-center'>{$btnApprove} {$btnReview} {$btnReject} {$btnResend}</div>";
            })
            ->rawColumns(['approver_nik', 'level', 'customer_name', 'status_approval', 'route_to', 'action'])
            ->make(true);
    }

    public function resendApprovalEmail(Request $request, $token)
    {
        // Cari log approval yang masih pending
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return response()->json(['success' => false, 'message' => 'This approval task is no longer valid.'], 404);
        }

        // [FIX] Ambil Customer berdasarkan related_id, bukan Requisition
        $customer = Customer::find($approvalLog->related_id);

        // Ambil data approver
        $approver = User::where('nik', $approvalLog->approver_nik)->first();

        if (!$customer || !$approver) {
            return response()->json(['success' => false, 'message' => 'Associated data not found.'], 404);
        }

        try {
            // Buat token baru
            $newToken = Str::uuid()->toString();
            $approvalLog->update(['token' => $newToken]);

            // Siapkan recipients format untuk CustomerJob
            $recipients = [[
                'nik' => $approver->nik,
                'email' => $approver->email,
                'name' => $approver->name,
                'level' => $approvalLog->level,
                'is_first' => false
            ]];

            // Kirim ulang email
            CustomerJob::dispatch($customer->id, $recipients, $newToken, 'approval');

            // === [ACTIVITY LOG: RESEND] ===
            activity()
                ->causedBy(Auth::user()) // User yang mengklik tombol resend (Admin/Sales)
                ->performedOn($customer)
                ->useLog('customer')
                ->event('resend')
                ->withProperties([
                    'recipient_name' => $approver->name,
                    'recipient_email' => $approver->email,
                    'level' => $approvalLog->level
                ])
                ->log("Resent approval email to {$approver->name} (Level {$approvalLog->level})");

            return response()->json(['success' => true, 'message' => 'Approval email has been successfully resent to ' . $approver->name . '.']);

        } catch (\Exception $e) {
            Log::error('Failed to resend email: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resend email.'], 500);
        }
    }

    // public function reportsPage()
    // {
    //     return view('page.sample.report.index');
    // }

    // public function printMultipleReport(Request $request)
    // {
    //     $request->validate([
    //         'selected_ids'   => 'required|array',
    //         'selected_ids.*' => 'integer|exists:requisitions,id'
    //     ]);

    //     $requisitions = Requisition::with([
    //         'customer',
    //         'requester.department',
    //         'requisitionItems.itemMaster',
    //         'requisitionItems.itemDetail',
    //         'requisitionSpecial',
    //         'approvalLogs' => fn($q) => $q->orderBy('level', 'asc'),
    //         'approvalLogs.approver.roles'
    //     ])->whereIn('id', $request->selected_ids)->get();

    //     $revisionData = Revision::first();

    //     if ($requisitions->isEmpty()) {
    //         return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dicetak.');
    //     }

    //     $pdf = Pdf::loadView('page.sample.report.print', [
    //         'requisitions' => $requisitions,
    //         'revision'     => $revisionData
    //     ])->setPaper('a4', 'landscape');

    //     return $pdf->stream('Bulk-RS-Sample-' . now()->format('Y-m-d') . '.pdf');
    // }

    // public function getReportsData(Request $request)
    // {
    //     $query = Requisition::with(['requester', 'customer']) // Eager load relationships
    //         ->where('category', 'SAMPLE')
    //         ->select('requisitions.*');

    //     // Filter based on date range if provided
    //     if ($request->filled('start_date') && $request->filled('end_date')) {
    //         try {
    //             $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
    //             $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
    //             $query->whereBetween('request_date', [$startDate, $endDate]);
    //         } catch (\Exception $e) {
    //             Log::error('Invalid date format for report filter: ' . $e->getMessage());
    //         }
    //     }

    //     // Filter by user if not a super-admin
    //     if (!Auth::user()->hasRole('super-admin')) {
    //         $query->where('requester_nik', Auth::user()->nik);
    //     }

    //     // Return the DataTables response without column modifications
    //     return DataTables::of($query)->make(true);
    // }

    //======================================================================
    // [BARU] FUNGSI-FUNGSI UNTUK HALAMAN Logs INTERNAL
    //======================================================================

    /**
     * Menampilkan halaman daftar logs untuk user yang login.
     */
    public function logPage()
    {
        return view('page.customer.log.index');
    }

    public function getLogData()
    {
        // Pastikan model Activity di-import: use Spatie\Activitylog\Models\Activity;

        $query = Activity::with(['causer', 'subject'])
            ->where(function ($q) {
                $q->where('log_name', 'like', '%customer%')
                  ->orWhere('log_name', 'like', 'sample%')
                  ->orWhere('log_name', 'path%'); // Menangkap 'path - customer' dll
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            // --- 1. PERBAIKI LOG NAME (AMBIL DARI DB) ---
            ->editColumn('log_name', function ($log) {
                // Ambil nilai asli dari DB
                $logName = $log->log_name;

                // Tentukan warna badge
                $badgeClass = 'bg-secondary';
                $icon = 'ph-scroll';

                if (str_contains($logName, 'customer')) {
                    $badgeClass = 'bg-primary'; // Biru
                    $icon = 'ph-users-three';
                } elseif (str_contains($logName, 'path')) {
                    $badgeClass = 'bg-dark'; // Hitam
                    $icon = 'ph-git-branch';
                } elseif (str_contains($logName, 'sample')) {
                    $badgeClass = 'bg-info text-dark';
                    $icon = 'ph-flask';
                }

                // Tampilkan nama asli dari DB, hanya dirapikan kapitalisasinya
                // str_replace('-', ' ', ...) agar "path - customer" jadi "Path Customer" (opsional, sesuaikan selera)
                $displayText = ucwords(str_replace('-', ' ', $logName));

                return '<span class="badge ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($displayText) . '</span>';
            })
            // --- 2. TAMBAHKAN KOLOM PROPERTIES ---
            ->addColumn('properties', function ($log) {
                $props = $log->properties;

                if (empty($props) || $props->isEmpty()) {
                    return '<span class="text-muted small">-</span>';
                }

                // Format JSON menjadi list HTML rapi
                $output = '<ul class="m-0 p-0" style="list-style: none; font-size: 0.8rem;">';
                foreach ($props as $key => $value) {
                    // Skip atribut internal yang panjang (opsional)
                    if ($key === 'attributes' || $key === 'old') {
                        continue;
                    }

                    // Jika value array (seperti approvers list), encode jadi string
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $output .= "<li><span class='text-muted'>" . ucfirst($key) . ":</span> <span class='fw-bold text-dark'>" . Str::limit($value, 40) . "</span></li>";
                }

                // Jika ada 'attributes' (data yang berubah), tampilkan tombol detail kecil
                if (isset($props['attributes'])) {
                     $output .= "<li><span class='badge bg-light text-dark border mt-1'>+ Has detailed changes</span></li>";
                }

                $output .= '</ul>';

                return $output;
            })
            ->addColumn('subject_info', function ($log) {

                // 1. LOG CUSTOMER (Tetap seperti sebelumnya)
                if ($log->subject_type === Customer::class) {
                    $code = $log->subject ? $log->subject->code : ($log->properties['code'] ?? '-');
                    $name = $log->subject ? $log->subject->name : ($log->properties['name'] ?? '-');

                    return '
                        <div class="d-flex flex-column text-start">
                            <span class="fw-bold text-dark" style="font-size: 0.85em;">' . e($code) . '</span>
                            <span class="text-muted" style="font-size: 0.75em;">' . Str::limit(e($name), 25) . '</span>
                        </div>';
                }

                if ($log->subject_type === ApprovalPath::class) {

                    $category = $log->subject ? $log->subject->category : ($log->properties['category'] ?? 'General');
                    $sub = $log->subject ? $log->subject->sub_category : ($log->properties['sub_category'] ?? null);

                    $text = $category . ($sub ? ' - ' . $sub : '');

                    return '<span class="badge bg-secondary">' . e($text) . '</span>';
                }

                return '<span class="badge bg-light text-muted">N/A</span>';
            })
            ->addColumn('subject_id', function ($log) {
                $id = $log->subject_id;
                if (!$id) return '-';
                if ($log->subject_type === Customer::class) {
                    return '<span class="fw-bold text-primary">#' . $id . '</span>';
                }
                return e($id);
            })
            ->addColumn('causer_info', function ($log) {
                $causerName = optional($log->causer)->name ?? 'System';
                $icon = $causerName === 'System' ? 'ph-robot' : 'ph-user-circle';
                return '
                    <div class="d-flex align-items-center causer-info">
                        <i class="ph-bold ' . $icon . ' me-2 text-primary"></i>
                        <span class="fw-bold">' . e($causerName) . '</span>
                    </div>';
            })
            ->editColumn('event', function ($log) {
                $event = strtolower($log->event ?? 'N/A');
                $badgeClass = 'bg-secondary';
                $icon = 'ph-info';

                switch ($event) {
                    case 'create': $badgeClass = 'bg-success'; $icon = 'ph-plus-circle'; break;
                    case 'update': $badgeClass = 'bg-warning text-dark'; $icon = 'ph-pencil-simple'; break;
                    case 'delete': $badgeClass = 'bg-danger'; $icon = 'ph-trash'; break;
                    case 'approve': $badgeClass = 'bg-success'; $icon = 'ph-check-circle'; break;
                    case 'reject': $badgeClass = 'bg-danger'; $icon = 'ph-x-circle'; break;
                    case 'review': $badgeClass = 'bg-info'; $icon = 'ph-eye'; break;
                    case 'resend': $badgeClass = 'bg-primary'; $icon = 'ph-paper-plane-tilt'; break;
                }
                return '<span class="badge ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . ucfirst($event) . '</span>';
            })
            ->editColumn('created_at', function ($log) {
                return Carbon::parse($log->created_at)->format('d M Y, H:i');
            })
            // Tambahkan 'properties' ke rawColumns agar HTML list-nya terender
            ->rawColumns(['log_name', 'event', 'subject_info', 'causer_info', 'subject_id', 'properties'])
            ->make(true);
    }
}
