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
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
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

    private function generateInitials($string) {
        $string = strtoupper($string);
        $string = str_replace(['.', ',', '/', '-', '(', ')'], ' ', $string);
        $string = preg_replace('/[^A-Z0-9\s]/', '', $string);

        $words = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);
        $acronym = "";

        $entities = ['PT', 'CV', 'UD', 'TB', 'PD'];

        foreach ($words as $index => $w) {
            if ($index === 0 && (in_array($w, $entities) || strlen($w) <= 3)) {
                $acronym .= $w;
            } else {
                $acronym .= mb_substr($w, 0, 1);
            }
        }

        if (empty($acronym)) return 'GEN';

        return substr($acronym, 0, 7);
    }

    private function getRomanMonth($month) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[intval($month)] ?? 'I';
    }

    public function generatePkdPreview(Request $request)
    {
        $name = $request->name;

        if (empty($name)) {
            return response()->json(['success' => false, 'number' => '']);
        }

        $year = date('Y');
        $monthRoman = $this->getRomanMonth(date('n'));
        $initials = $this->generateInitials($name);

        $maxSequence = 0;
        $existingNumbers = Customer::where('no_pkd', 'LIKE', "%/{$year}")
                            ->pluck('no_pkd')
                            ->toArray();

        foreach ($existingNumbers as $no) {
            $parts = explode('/', $no);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $seq = intval($parts[0]);
                if ($seq > $maxSequence) {
                    $maxSequence = $seq;
                }
            }
        }

        $nextSequence = $maxSequence + 1;
        $pkdNumber = '';

        do {
            $sequenceStr = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
            $pkdNumber = sprintf("%s/PKD-%s/%s/%s", $sequenceStr, $initials, $monthRoman, $year);

            $exists = Customer::where('no_pkd', $pkdNumber)->exists();
            if ($exists) {
                $nextSequence++;
            }
        } while ($exists);

        return response()->json([
            'success' => true,
            'number' => $pkdNumber
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::leftJoin('customer_files', 'customers.id', '=', 'customer_files.customer_id')
                ->select(
                    'customers.*',
                        'customer_files.npwp_file as file_npwp',
                    'customer_files.nib_siup_file as file_nib',
                    'customer_files.ktp_file as file_ktp'
                )->orderBy('customers.created_at', 'desc');

            return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $tglNpwp = $row->tanggal_npwp ? Carbon::parse($row->tanggal_npwp)->format('Y-m-d') : '';
                $tglNppkp = $row->tanggal_nppkp ? Carbon::parse($row->tanggal_nppkp)->format('Y-m-d') : '';

                $dataAttrs = '';
                $dataAttrs .= ' data-id="' . $row->id . '"';
                $dataAttrs .= ' data-user_id="' . e($row->user_id) . '"';
                $dataAttrs .= ' data-code="' . e($row->code) . '"';
                $dataAttrs .= ' data-no_pkd="' . e($row->no_pkd) . '"';
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

                $pathNpwp = $row->file_npwp ? asset('storage/' . ltrim($row->file_npwp, '/')) : '';
                $pathNib  = $row->file_nib ? asset('storage/' . ltrim($row->file_nib, '/')) : '';
                $pathKtp  = $row->file_ktp ? asset('storage/' . ltrim($row->file_ktp, '/')) : '';

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
        // 1. Cek Apakah Approval Path Tersedia
        $category = 'Customer';
        $subCategory = 'CBD';

        $pathExists = ApprovalPath::where('category', $category)
            ->where(function ($q) use ($subCategory) {
                if (!empty($subCategory)) {
                    $q->where('sub_category', $subCategory)
                    ->orWhereNull('sub_category')
                    ->orWhere('sub_category', '');
                } else {
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

        // 2. Mulai Transaksi Database
        DB::transaction(function () use ($request, &$createdCustomer, &$logs, &$firstLog) {
            $user = Auth::user();

            // --- A. Persiapan Data Customer ---
            $customerData = $request->except(['file_npwp', 'file_nib', 'file_ktp', 'items']);
            $year = date('Y');
            $monthRoman = $this->getRomanMonth(date('n'));
            $initials = $this->generateInitials($request->name);
            $maxSequence = 0;

            // Generate No PKD
            $existingNumbers = Customer::where('no_pkd', 'LIKE', "%/{$year}")
                                ->pluck('no_pkd')
                                ->toArray();

            foreach ($existingNumbers as $no) {
                $parts = explode('/', $no);
                if (isset($parts[0]) && is_numeric($parts[0])) {
                    $seq = intval($parts[0]);
                    if ($seq > $maxSequence) {
                        $maxSequence = $seq;
                    }
                }
            }

            $nextSequence = $maxSequence + 1;
            $pkdNumber = '';

            do {
                $sequenceStr = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
                $pkdNumber = sprintf("%s/PKD-%s/%s/%s", $sequenceStr, $initials, $monthRoman, $year);
                $exists = Customer::where('no_pkd', $pkdNumber)->exists();
                if ($exists) {
                    $nextSequence++;
                }
            } while ($exists);

            $customerData['no_pkd'] = $pkdNumber;

            // Kalkulasi TOP & Lead Time
            if ($request->filled('top_calc')) {
                $customerData['top_calc'] = $request->top_calc;
            } else {
                $termVal = $request->term_of_payment;
                $customerData['top_calc'] = ($termVal === 'CBD') ? 0 : (int) $termVal;
            }

            if(empty($customerData['lead_time'])) {
                $customerData['lead_time'] = 0;
            }

            // Hitung Grand Total
            $grandTotal = 0;
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $qty = (float) ($item['quantity'] ?? 0);
                    $price = (float) ($item['price'] ?? 0);
                    $grandTotal += ($qty * $price);
                }
            }
            $customerData['customer_total'] = $grandTotal;

            // --- B. Create Customer ---
            $createdCustomer = Customer::create($customerData);

            // --- C. Notifikasi General (Ke Admin/Finance) ---
            try {
                $recipients = User::role(['super-admin', 'manager-finance', 'head-finance'])->get();
                Notification::send($recipients, new SystemNotification(
                    'New Customer',
                    "Customer baru <b>{$createdCustomer->name}</b> telah ditambahkan.",
                    route('customers.index'),
                    'ph-users',
                    'success'
                ));
            } catch (\Exception $e) {
                // Ignore error notif general agar tidak rollback transaksi
            }

            // --- D. Simpan Items ---
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
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

            // --- E. Activity Log ---
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

            // --- F. Upload Files ---
            $storageFolder = 'customer_files/' . $createdCustomer->id;
            $fileData = [
                'customer_id' => $createdCustomer->id,
                'npwp_file' => null,
                'nib_siup_file' => null,
                'ktp_file' => null
            ];

            if ($request->hasFile('file_npwp')) {
                $fileData['npwp_file'] = $request->file('file_npwp')->store($storageFolder, 'public');
            }
            if ($request->hasFile('file_nib')) {
                $fileData['nib_siup_file'] = $request->file('file_nib')->store($storageFolder, 'public');
            }
            if ($request->hasFile('file_ktp')) {
                $fileData['ktp_file'] = $request->file('file_ktp')->store($storageFolder, 'public');
            }
            CustomerFile::create($fileData);

            // --- G. Generate Approval Logs ---
            $subCategory = 'CBD';
            $logs = $this->generateApprovalLogs($user, $createdCustomer->id, 'Customer', $subCategory);

            // --- H. Cari First Approver & Kirim Notifikasi ---
            $firstLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $createdCustomer->id)
                ->orderBy('level', 'asc')
                ->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();

                if ($firstApprover) {
                    // 1. Update Route To Customer
                    $createdCustomer->update(['route_to' => $firstApprover->name, 'status_approval' => 'Pending']);
                    try {
                        Notification::send($firstApprover, new SystemNotification(
                            'Butuh Persetujuan', // Judul
                            "Customer Baru <b>{$createdCustomer->name}</b> menunggu persetujuan Anda.", // Pesan
                            route('customers.approval'), // Link
                            'ph-signature', // Icon
                            'warning' // Warna
                        ));
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim notif sistem ke approver level 1: " . $e->getMessage());
                    }

                } else {
                    $createdCustomer->update(['status_approval' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $createdCustomer->update(['status_approval' => 'Completed', 'route_to' => 'Finished (No Path)']);
            }

            try {
                $approvers = User::role(['manager-finance', 'head-finance'])->get();
                Notification::send($approvers, new SystemNotification(
                    'Approval Customer Baru',
                    "Customer Baru <b>{$createdCustomer->name}</b> telah dibuat & menunggu approval berjenjang.",
                    route('customers.approval'),
                    'ph-user-plus',
                    'info'
                ));
            } catch (\Exception $e) {
                \Log::error("Gagal notif customer baru: " . $e->getMessage());
            }
        });

        if (! $createdCustomer) {
            return response()->json(['success' => false, 'message' => 'Failed to create customer'], 500);
        }

        // 3. Dispatch Job Email (Di Luar Transaksi agar tidak block)
        try {
            $firstLogData = $logs->firstWhere('level', 1);

            if ($firstLogData) {
                $approverNik = $firstLogData['approver_nik'];
                $approverUser = User::where('nik', $approverNik)->first();

                if ($approverUser && $approverUser->email) {
                    $recipients = [
                        [
                            'nik' => $approverUser->nik,
                            'email' => $approverUser->email,
                            'name' => $approverUser->name,
                            'level' => $firstLogData['level'],
                            'is_first' => true,
                        ]
                    ];

                    $token = $firstLogData['token'];
                    CustomerJob::dispatch($createdCustomer->id, $recipients, $token, 'approval');
                    Log::info("Email approval level 1 dikirim ke: " . $approverUser->email);
                } else {
                    Log::warning("User Level 1 tidak punya email atau NIK tidak ditemukan: " . $approverNik);
                }
            }
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
            'attributes' => $request->all(),
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
        $log = ApprovalLog::where('token', $token)->first();

        if (!$log || $log->status !== 'Pending') {
            return view('page.customer.links.approval-invalid');
        }

        $customer = Customer::with(['user', 'accountGroup', 'customerClass'])
                    ->findOrFail($log->related_id);

        $preSelectedAction = $request->query('pre_action', 'approve');

        if ($preSelectedAction === 'approve') {
            return $this->processApprovalInternal($token, 'approve', null, $customer);
        }

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
                    if ($nextApproverUser) {

                        // A. Kirim Notifikasi Sistem (Lonceng)
                        try {
                            Notification::send($nextApproverUser, new SystemNotification(
                                "Butuh Persetujuan (Level {$nextLog->level})",
                                "Customer <b>{$customer->name}</b> menunggu persetujuan Anda.",
                                route('customers.approval'),
                                'ph-signature',
                                'warning'
                            ));
                        } catch (\Exception $e) {
                            Log::error("Gagal kirim notif sistem ke next approver: " . $e->getMessage());
                        }

                        // B. Kirim Email (Logika Existing)
                        if ($nextApproverUser->email) {
                            $recipients = [[
                                'nik' => $nextApproverUser->nik,
                                'email' => $nextApproverUser->email,
                                'name' => $nextApproverUser->name,
                                'level' => $nextLog->level,
                                'is_first' => false
                            ]];
                            CustomerJob::dispatch($customer->id, $recipients, $nextLog->token, 'approval');
                        }
                    } else {
                        Log::warning("User/Email tidak ditemukan untuk NIK: {$nextLog->approver_nik}");
                    }

                } else {
                    $customer->update([
                        'status_approval' => 'Approved',
                        'route_to' => 'Finished',
                        'status' => 'Active'
                    ]);

                    $requester = $customer->user;
                    if ($requester && $requester->email) {
                        $recipients = [[
                            'email' => $requester->email,
                            'name' => $requester->name,
                            'level' => 'Requester',
                            'is_first' => false
                        ]];

                        CustomerJob::dispatch($customer->id, $recipients, null, 'completed');
                        Log::info("APPROVAL FLOW: Customer #{$customer->id} COMPLETED (Approved). Email notifikasi dikirim ke Requester: {$requester->email}");
                    }
                }

            } elseif ($dbStatus === 'Rejected') {
                ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Canceled', 'token' => null]);

                $customer->update([
                    'status_approval' => 'Rejected',
                    'route_to' => 'Rejected by ' . $currentLog->approver_nik
                ]);

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

            return view('page.customer.links.approval-success', [
                'action' => $action,
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

    public function approvalPage()
    {
        $user = Auth::user();

        $logQuery = ApprovalLog::where('category', 'Customer');

        if (!$user->hasRole('super-admin')) {
            $logQuery->where('approver_nik', $user->nik);
        }

        $pendingCount = (clone $logQuery)->where('status', 'Pending')->count();
        $approvedCount = (clone $logQuery)->where('status', 'Approved')->count();

        $activeCount = Customer::where('status', 'Active')->count();
        $inactiveCount = Customer::where('status', 'Inactive')->count();

        $approvalStatuses = ApprovalLog::where('category', 'Customer')->distinct()->pluck('status');
        $accountStatuses = Customer::whereNotNull('status')->distinct()->pluck('status');

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

        $query->where('approval_logs.status', 'Pending');

        $query->whereIn('customers.status_approval', ['Pending', 'Processing']);

        if (!$currentUser->hasRole('super-admin')) {
            $query->where('approval_logs.approver_nik', $currentUser->nik);
        }

        $query->orderBy('customers.created_at', 'desc')
            ->orderBy('approval_logs.level', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('approver_nik', function ($row) {
                return '<span class="badge status-badge-lg bg-primary">' . e($row->approver_nik) . '</span>';
            })
            ->addColumn('level', function ($row) {
                return '<span class="badge status-badge-lg bg-info">Level ' . $row->level . '</span>';
            })
            ->addColumn('is_actionable', function ($row) {
                if ($row->level == 1) {
                    return true;
                }

                $prevLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $row->customer_id)
                    ->where('level', $row->level - 1)
                    ->first();

                if ($prevLog && $prevLog->status === 'Approved') {
                    return true;
                }

                return false;
            })
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

                if (!$canAction) {
                    return '<button type="button" class="btn btn-sm btn-secondary"
                            onclick="Swal.fire(\'Locked\', \'Anda harus menunggu '. $waitingMessage .' melakukan approval terlebih dahulu.\', \'warning\')">
                            <i class="ph-bold ph-lock-key text-white"></i> Locked
                            </button>';
                }

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
        $approvalLog = ApprovalLog::where('token', $token)->where('status', 'Pending')->first();

        if (!$approvalLog) {
            return response()->json(['success' => false, 'message' => 'This approval task is no longer valid.'], 404);
        }

        $customer = Customer::find($approvalLog->related_id);
        $approver = User::where('nik', $approvalLog->approver_nik)->first();

        if (!$customer || !$approver) {
            return response()->json(['success' => false, 'message' => 'Associated data not found.'], 404);
        }

        try {
            $newToken = Str::uuid()->toString();
            $approvalLog->update(['token' => $newToken]);

            $recipients = [[
                'nik' => $approver->nik,
                'email' => $approver->email,
                'name' => $approver->name,
                'level' => $approvalLog->level,
                'is_first' => false
            ]];

            CustomerJob::dispatch($customer->id, $recipients, $newToken, 'approval');

            activity()
                ->causedBy(Auth::user())
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

                // Avoid eager-loading `subject` because some activity records may reference
                // classes that no longer exist (causes MorphTo instantiation errors).
                $query = Activity::with('causer')
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
                $props = $log->properties ?? [];

                // Normalize empty
                if (empty($props) || (is_object($props) && method_exists($props, 'isEmpty') && $props->isEmpty())) {
                    return '<span class="text-muted small">-</span>';
                }

                $output = '<div class="small">';

                foreach ($props as $key => $value) {
                    // Skip raw 'old' payload to avoid noise
                    if ($key === 'old') continue;

                    $label = ucfirst(str_replace(['_', '-'], ' ', $key));

                    // Special: attributes -> provide a small "Details" button with JSON payload
                    if ($key === 'attributes') {
                        $json = json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                        $escaped = e($json);
                        $output .= '<div class="mb-1"><span class="text-muted">'.e($label).':</span> '
                                 . '<button class="btn btn-xs btn-outline-secondary btn-view-json ms-2" data-json="'. $escaped .'">View details</button></div>';
                        continue;
                    }

                    // If value is a Collection or array, render nicely
                    if (is_array($value) || $value instanceof \Illuminate\Support\Collection) {
                        // Approvers or lists -> badges
                        if (str_contains(strtolower($key), 'approver') || str_contains(strtolower($key), 'approvers')) {
                            // Render approver list as bullet points for readability
                            $output .= '<div class="mb-1"><span class=" text-muted text-dark">'.e($label).':</span>';
                            $output .= '<ul class="m-0 ps-3" style="font-size:0.9rem;">';
                            foreach ((array) $value as $v) {
                                if (is_array($v)) {
                                    $name = $v['name'] ?? reset($v);
                                } else {
                                    $name = $v;
                                }
                                $output .= '<li class="text-dark">'.e($name).'</li>';
                            }
                            $output .= '</ul></div>';
                            continue;
                        }

                        // Generic arrays -> comma separated preview
                        $preview = implode(', ', array_map(function ($i) {
                            if (is_array($i)) return json_encode($i, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                            return (string) $i;
                        }, (array) $value));

                        $output .= '<div class="mb-1"><span class="">'.e($label).':</span> <span class="fw-bold text-dark">'.e(Str::limit($preview, 80)).'</span></div>';
                        continue;
                    }

                    // If string that contains JSON, try to decode and render
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                            // If this property looks like approvers, render badges instead of JSON preview
                            if (str_contains(strtolower($key), 'approver') || str_contains(strtolower($key), 'approvers')) {
                                $output .= '<div class="mb-1"><span class="fw-bold text-dark">'.e($label).':</span>';
                                $output .= '<ul class="m-0 ps-3" style="font-size:0.9rem;">';
                                foreach ((array) $decoded as $v) {
                                    if (is_array($v)) {
                                        $name = $v['name'] ?? reset($v);
                                    } else {
                                        $name = $v;
                                    }
                                    $output .= '<li class="text-dark">'.e($name).'</li>';
                                }
                                $output .= '</ul></div>';
                                continue;
                            }

                            $preview = is_array($decoded) ? implode(', ', array_map(function ($i) {
                                return is_array($i) ? json_encode($i) : (string) $i;
                            }, $decoded)) : json_encode($decoded, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

                            $output .= '<div class="mb-1"><span class="">'.e($label).':</span> <span class="fw-bold text-dark">'.e(Str::limit($preview, 80)).'</span></div>';
                            continue;
                        }
                    }

                    // Default scalar rendering
                    $display = is_null($value) ? '-' : (string) $value;
                    $output .= '<div class="mb-1"><span class="text-muted">'.e($label).':</span> <span class="fw-bold text-dark">'.e(Str::limit($display, 80)).'</span></div>';
                }

                $output .= '</div>';

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
