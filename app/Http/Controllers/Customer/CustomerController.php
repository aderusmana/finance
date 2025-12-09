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
use App\Traits\ApprovalTrait;
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
            // 1. CREDIT LIMIT (Format Uang)
            ->addColumn('credit_limit_formatted', function ($row) {
                return '<div class="badge status-badge-lg bg-warning">
                            IDR ' . number_format($row->credit_limit, 0, ',', '.') . '
                        </div>';
            })

            // 2. FINANCIAL INFO (TOP & BG Status)
            ->addColumn('financial_info', function ($row) {
                // Gunakan class .srs-badge dari CSS sample-table (Blue Gradient)
                if ($row->bank_garansi === 'YA') {
                    $bgBadge = '<span class="srs-badge"><i class="fas fa-file-contract me-1"></i> BG: YES</span>';
                } else {
                    // Gunakan style badge secondary biasa tapi tipis
                    $bgBadge = '<span class="badge bg-secondary opacity-50" style="font-size: 0.75em;">BG: NO</span>';
                }

                // TOP menggunakan teks tebal biru tua (sesuai tema)
                return '<div class="d-flex flex-column">
                            <span class="fw-bold text-primary mb-1" style="font-size: 0.95em;">'. $row->term_of_payment .'</span>
                            <div>' . $bgBadge . '</div>
                        </div>';
            })

            // 3. STATUS APPROVAL (Gunakan .status-badge-lg)
            ->addColumn('status_approval', function ($row) {
                // Mapping status ke class Bootstrap dasar
                // CSS sample-table akan mengubah bg-primary jadi Ungu, bg-warning jadi Oranye, dll.
                $baseClass = match($row->status_approval) {
                    'Approved', 'Completed' => 'bg-success', // Hijau Gradient
                    'Rejected', 'Canceled' => 'bg-danger',   // Merah Gradient
                    'Processing' => 'bg-primary',            // Ungu Gradient (sesuai CSS Anda)
                    'Pending' => 'bg-warning',               // Oranye Gradient
                    default => 'bg-secondary'                // Abu Gradient
                };

                // Icon logic
                $icon = match($row->status_approval) {
                    'Approved', 'Completed' => '<i class="ph-bold ph-check-circle me-1"></i>',
                    'Rejected', 'Canceled' => '<i class="ph-bold ph-x-circle me-1"></i>',
                    'Processing' => '<i class="ph-bold ph-arrows-clockwise ph-spin me-1"></i>',
                    default => '<i class="ph-bold ph-clock me-1"></i>'
                };

                // Perhatikan penambahan class 'status-badge-lg'
                return '<span class="badge status-badge-lg ' . $baseClass . '">' . $icon . strtoupper($row->status_approval) . '</span>';
            })

            // 4. ROUTE TO (Gunakan .route-to-badge-lg)
            ->addColumn('route_to', function ($row) {
                 if($row->status_approval === 'Approved' || $row->status_approval === 'Completed'){
                    return '<span class="badge route-to-badge-lg bg-info text-white"><i class="ph-bold ph-check-circle me-1 text-white"></i>-</span>';
                }

                // Gunakan bg-info + route-to-badge-lg untuk efek Gold/Emas sesuai CSS Anda
                return '<span class="badge route-to-badge-lg bg-info text-white">
                            <i class="ph-bold ph-user me-1"></i> ' . strtoupper($row->route_to ?? '-') . '
                        </span>';
            })

            // 5. STATUS ACTIVE/INACTIVE
            ->addColumn('status', function ($row) {
                // Gunakan status-badge-lg juga agar konsisten bentuknya
                $badge = $row->status === 'Active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge status-badge-lg ' . $badge . '">' . strtoupper($row->status) . '</span>';
            })
            ->rawColumns(['credit_limit_formatted', 'financial_info', 'status_approval', 'route_to', 'status', 'action'])
            ->make(true);
        }

        // eager-load relations so view can access user position, branch and region without extra queries
        $sales = Sales::with(['user.position', 'branch', 'region'])->get();
        $top = TOP::all();
        $accountgroup = AccountGroup::all();
        $customerClass = CustomerClass::all();


        return view('page.customer.index', compact('sales', 'top', 'accountgroup','customerClass'));
    }


    public function show(Customer $customer)
    {
        return $customer->load(['files', 'bankGaransis', 'bgRecommendations', 'creditLimits']);
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
        return response()->json(['success' => true, 'message' => 'Customer updated successfully!', 'data' => $customer]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
    }

    // Manage customer files via CustomerController
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
            'action' => 'required|in:reject,review',
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

            // --- 2. Update Log (Token Hangus) ---
            $currentLog->update([
                'status' => $dbStatus,
                'notes' => $dbNotes,
                'updated_at' => now(),
                'token' => null,
            ]);

            // --- 3. Cek Alur Selanjutnya ---

            // JIKA STATUSNYA APPROVED (Baik Approve biasa maupun Review)
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
}
