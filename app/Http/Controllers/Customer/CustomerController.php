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
use App\Services\CustomerService;
use Illuminate\Http\UploadedFile;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
        // $this->middleware('permission:view customer', ['only' => ['index', 'show', 'logPage', 'getLogData']]);
        // $this->middleware('permission:create customer', ['only' => ['store', 'storeFile']]);
        // $this->middleware('permission:update customer', ['only' => ['update', 'updateFile', 'recall']]);
        // $this->middleware('permission:delete customer', ['only' => ['destroy', 'destroyFile']]);
        // $this->middleware('permission:view approval', ['only' => ['approvalPage', 'getApprovalData', 'viewApprovalPage']]);
    }

    private function getRomanMonth($month)
    {
        $map = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$month] ?? 'I';
    }

    private function generateInitials($string)
    {
        $string = strtoupper(preg_replace('/[^A-Z0-9\s]/', '', $string));
        $words = explode(' ', $string);
        $initials = '';
        foreach ($words as $w) {
            $initials .= $w[0] ?? '';
        }
        return substr($initials, 0, 5);
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
                    'customer_files.ktp_file as file_ktp',
                    'customer_files.akte_file as file_akte',
                    'customer_files.company_profile_file as file_company_profile'
                );

            if ($request->has('status') && $request->status !== 'all') {
                if ($request->status === 'Active') {
                    $query->where('customers.bank_garansi', 'YA');
                } elseif ($request->status === 'Inactive') {
                    $query->where(function ($q) {
                        $q->where('customers.bank_garansi', '!=', 'YA')
                            ->orWhereNull('customers.bank_garansi');
                    });
                }
            }

            if ($request->has('approval_status') && $request->approval_status !== 'all') {
                $query->where('customers.status_approval', $request->approval_status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $row->load('items');
                    $rowData = $row->toArray();

                    $lastRejectLog = ApprovalLog::where('category', 'Customer')
                        ->where('related_id', $row->id)
                        ->where('status', 'Rejected')
                        ->latest('updated_at')
                        ->first();

                    $rowData['reject_note'] = $lastRejectLog ? $lastRejectLog->notes : 'Tidak ada catatan rejection.';

                    $rowData['tanggal_npwp']  = $row->tanggal_npwp ? Carbon::parse($row->tanggal_npwp)->format('Y-m-d') : null;
                    $rowData['tanggal_nppkp'] = $row->tanggal_nppkp ? Carbon::parse($row->tanggal_nppkp)->format('Y-m-d') : null;

                    $rowData['file_npwp_path'] = $row->file_npwp ? asset('storage/' . ltrim($row->file_npwp, '/')) : null;
                    $rowData['file_nib_path']  = $row->file_nib ? asset('storage/' . ltrim($row->file_nib, '/')) : null;
                    $rowData['file_ktp_path']  = $row->file_ktp ? asset('storage/' . ltrim($row->file_ktp, '/')) : null;
                    $rowData['file_akte_path'] = $row->file_akte ? asset('storage/' . ltrim($row->file_akte, '/')) : null;
                    $rowData['file_company_profile_path'] = $row->file_company_profile ? asset('storage/' . ltrim($row->file_company_profile, '/')) : null;
                    
                    $jsonRow = htmlspecialchars(json_encode($rowData), ENT_QUOTES, 'UTF-8');

                    $tglNpwp = $row->tanggal_npwp ? Carbon::parse($row->tanggal_npwp)->format('Y-m-d') : '';
                    $tglNppkp = $row->tanggal_nppkp ? Carbon::parse($row->tanggal_nppkp)->format('Y-m-d') : '';

                    // Ensure payment / faktur fields are strings (DB may store JSON/array in longtext)
                    $paymentDaysVal = $row->payment_days ?? '';
                    $paymentDateVal = $row->payment_date ?? '';
                    $fakturDaysVal = $row->faktur_days ?? '';
                    $fakturDateVal = $row->faktur_date ?? '';

                    if (is_array($paymentDaysVal) || is_object($paymentDaysVal)) {
                        $paymentDaysVal = json_encode($paymentDaysVal);
                    }
                    if (is_array($paymentDateVal) || is_object($paymentDateVal)) {
                        $paymentDateVal = json_encode($paymentDateVal);
                    }
                    if (is_array($fakturDaysVal) || is_object($fakturDaysVal)) {
                        $fakturDaysVal = json_encode($fakturDaysVal);
                    }
                    if (is_array($fakturDateVal) || is_object($fakturDateVal)) {
                        $fakturDateVal = json_encode($fakturDateVal);
                    }

                    $dataAttrs = '';
                    $dataAttrs .= ' data-id="' . $row->id . '"';
                    $dataAttrs .= ' data-user_id="' . e($row->user_id) . '"';
                    $dataAttrs .= ' data-code="' . e($row->code) . '"';
                    $dataAttrs .= ' data-no_pkd="' . e($row->no_pkd) . '"';
                    $dataAttrs .= ' data-pic="' . e($row->pic) . '"';
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
                    $dataAttrs .= ' data-file_npwp_path="' . $rowData['file_npwp_path'] . '"';
                    $dataAttrs .= ' data-file_nib_path="' . $rowData['file_nib_path'] . '"';
                    $dataAttrs .= ' data-file_ktp_path="' . $rowData['file_ktp_path'] . '"';
                    $dataAttrs .= ' data-file_akte_path="' . $rowData['file_akte_path'] . '"';
                    $dataAttrs .= ' data-file_company_profile_path="' . $rowData['file_company_profile_path'] . '"';
                    $dataAttrs .= ' data-payment_days="' . e($paymentDaysVal) . '"';
                    $dataAttrs .= ' data-payment_date="' . e($paymentDateVal) . '"';
                    $dataAttrs .= ' data-faktur_days="' . e($fakturDaysVal) . '"';
                    $dataAttrs .= ' data-faktur_date="' . e($fakturDateVal) . '"';

                    $btn = '<div class="d-flex gap-2">';

                    if ($row->status_approval === 'Rejected' && $user->can('update customer')) {
                        $btn .= '<button type="button" class="btn btn-warning btn-recall-customer shadow-sm"
                                data-json="' . $jsonRow . '"
                                data-bs-toggle="tooltip"
                                title="Recall / Revisi Pengajuan">
                                <i class="ph-bold ph-arrow-u-up-left text-white"></i>
                             </button>';
                    }

                    $btn .= '<button type="button" class="btn btn-info btn-show-customer" ' . $dataAttrs . ' title="Lihat Detail">
                            <i class="fa-solid fa-eye text-white"></i>
                        </button>';

                    if ($row->status_approval === 'Pending' || $row->status_approval === 'Rejected' && $user->can('delete customer')) {
                        $btn .= '<form action="' . route('customers.destroy', $row->id) . '" method="POST" class="delete-form delete-customer-btn" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger" title="Hapus Data">
                                <i class="fas fa-trash-alt text-white"></i>
                                </button>
                            </form>';
                    } else {
                        $btn .= '<button type="button" class="btn btn-secondary" title="Tidak bisa dihapus (Approval sedang berjalan/selesai)" onclick="Swal.fire(\'Action Locked\', \'Data tidak dapat dihapus karena proses approval sudah berjalan (Status: ' . $row->status_approval . ').\', \'info\')">
                                <i class="fas fa-lock text-white"></i>
                             </button>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })
                ->addColumn('credit_limit', function ($row) {
                    return '<div class="badge status-badge-lg bg-warning">
                            IDR ' . number_format($row->credit_limit, 0, ',', '.') . '
                        </div>';
                })
                ->editColumn('financial_info', function ($row) {
                    if ($row->bank_garansi === 'YA') {
                        $bgBadge = '<span class="srs-badge"><i class="fas fa-file-contract me-1"></i> BG: YES</span>';
                    } else {
                        $bgBadge = '<span class="badge bg-secondary opacity-50" style="font-size: 0.75em;">BG: NO</span>';
                    }

                    return '<div class="d-flex flex-column">
                            <span class="fw-bold text-primary mb-1" style="font-size: 0.95em;">' . $row->term_of_payment . '</span>
                            <div>' . $bgBadge . '</div>
                        </div>';
                })
                ->editColumn('status_approval', function ($row) {
                    $baseClass = match ($row->status_approval) {
                        'Approved', 'Completed' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Processing' => 'bg-primary',
                        'Pending' => 'bg-warning',
                        default => 'bg-secondary'
                    };

                    $icon = match ($row->status_approval) {
                        'Approved', 'Completed' => '<i class="ph-bold ph-check-circle me-1"></i>',
                        'Rejected' => '<i class="ph-bold ph-x-circle me-1"></i>',
                        'Processing' => '<i class="ph-bold ph-arrows-clockwise ph-spin me-1"></i>',
                        default => '<i class="ph-bold ph-clock me-1"></i>'
                    };

                    return '<span class="badge status-badge-lg ' . $baseClass . '">' . $icon . strtoupper($row->status_approval) . '</span>';
                })
                ->editColumn('route_to', function ($row) {
                    if ($row->status_approval === 'Approved' || $row->status_approval === 'Completed') {
                        return '<span class="badge route-to-badge-lg bg-info text-white"><i class="ph-bold ph-check-circle me-1 text-white"></i>-</span>';
                    }

                    return '<span class="badge route-to-badge-lg bg-info text-white">
                            <i class="ph-bold ph-user me-1"></i> ' . strtoupper($row->route_to ?? '-') . '
                        </span>';
                })
                ->editColumn('status', function ($row) {
                    $badge = $row->status === 'Active' ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge status-badge-lg ' . $badge . '">' . strtoupper($row->status) . '</span>';
                })
                ->rawColumns(['credit_limit', 'financial_info', 'status_approval', 'route_to', 'status', 'action'])
                ->make(true);
        }

        $sales = Sales::with(['user.position', 'branch', 'region', 'accountGroup'])->get();
        $top = TOP::all();
        $accountgroup = AccountGroup::all();
        $customerClass = CustomerClass::all();

        $pendingCount = Customer::whereIn('status_approval', ['Pending', 'Processing'])->count();
        $processingCount = Customer::where('status_approval', 'Processing')->count();
        $approvedCount = Customer::whereIn('status_approval', ['Approved', 'Completed'])->count();
        $activeCount = Customer::where('bank_garansi', 'YA')->count();
        $inactiveCount = Customer::where(function ($q) {
            $q->where('bank_garansi', '!=', 'YA')
                ->orWhereNull('bank_garansi');
        })->count();

        $approvalStatuses = Customer::whereNotNull('status_approval')->distinct()->pluck('status_approval');
        $accountStatuses = Customer::whereNotNull('status')->distinct()->pluck('status');

        return view('page.customer.index', compact(
            'sales',
            'top',
            'accountgroup',
            'customerClass',
            'pendingCount',
            'processingCount',
            'approvedCount',
            'activeCount',
            'inactiveCount',
            'approvalStatuses',
            'accountStatuses'
        ));
    }

    public function recall(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->status_approval !== 'Rejected') {
            return response()->json(['success' => false, 'message' => 'Hanya data dengan status Rejected yang bisa diajukan ulang (Recall).'], 403);
        }

        $dynamicFileRule = function ($attribute, $value, $fail) {
            if (!$value instanceof UploadedFile) return;
            $extension = strtolower($value->getClientOriginalExtension());
            $sizeInKb = $value->getSize() / 1024;

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                if ($sizeInKb > 1024) $fail("File {$attribute} (Gambar) maks 1MB.");
            } elseif ($extension === 'pdf') {
                if ($sizeInKb > 5120) $fail("File {$attribute} (PDF) maks 5MB.");
            } else {
                $fail("Format {$attribute} salah.");
            }
        };

        $request->validate([
            'file_npwp' => ['nullable', 'file', $dynamicFileRule],
            'file_nib'  => ['nullable', 'file', $dynamicFileRule],
            'file_ktp'  => ['nullable', 'file', $dynamicFileRule],
            'file_akte' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'file_company_profile' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $newLogs = null;

        DB::transaction(function () use ($request, $customer, &$newLogs) {
            $user = Auth::user();
            $customerData = $request->except(['file_npwp', 'file_nib', 'file_ktp', 'items', '_token']);
            $grandTotal = 0;
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $qty = (float) ($item['quantity'] ?? 0);
                    $price = (float) ($item['price'] ?? 0);
                    $grandTotal += ($qty * $price);
                }
            }
            $customerData['customer_total'] = $grandTotal;
            $customerData['status_approval'] = 'Pending';

            $customer->update($customerData);
            CustomerItem::where('customer_id', $customer->id)->delete();
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    if (!empty($item['item_name']) && !empty($item['quantity'])) {
                        CustomerItem::create([
                            'customer_id' => $customer->id,
                            'item_name'   => $item['item_name'],
                            'quantity'    => $item['quantity'],
                            'price'       => $item['price'] ?? 0,
                        ]);
                    }
                }
            }

            $storageFolder = 'customer_files/' . $customer->id;
            $fileData = [];
            $existingFiles = CustomerFile::where('customer_id', $customer->id)->first();

            if ($request->hasFile('file_npwp')) {
                $fileData['npwp_file'] = $request->file('file_npwp')->store($storageFolder, 'public');
            }
            if ($request->hasFile('file_nib')) {
                $fileData['nib_siup_file'] = $request->file('file_nib')->store($storageFolder, 'public');
            }
            if ($request->hasFile('file_ktp')) {
                $fileData['ktp_file'] = $request->file('file_ktp')->store($storageFolder, 'public');
            }
            if ($request->hasFile('file_company_profile')) {
                $fileData['company_profile_file'] = $request->file('file_company_profile')->store($storageFolder, 'public');
            }

            if (!empty($fileData)) {
                if ($existingFiles) {
                    $existingFiles->update($fileData);
                } else {
                    $fileData['customer_id'] = $customer->id;
                    CustomerFile::create($fileData);
                }
            }

            ApprovalLog::where('related_id', $customer->id)
                ->where('category', 'Customer')
                ->update(['status' => 'Rejected']);

            $subCategory = 'CBD';
            $newLogs = $this->generateApprovalLogs($user, $customer->id, 'Customer', $subCategory);

            $firstLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customer->id)
                ->where('status', 'Pending')
                ->orderBy('level', 'asc')
                ->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                $customer->update(['route_to' => $firstApprover ? $firstApprover->name : 'Unknown User']);

                if ($firstApprover) {
                    try {
                        $admins = User::role('super-admin')->get();
                        Notification::send($firstApprover, new SystemNotification(
                            'Butuh Persetujuan (Revisi)',
                            "Customer <b>{$customer->name}</b> telah direvisi dan diajukan ulang oleh user.",
                            route('customers.approval'),
                            'ph-arrow-u-up-left',
                            'warning'
                        ));
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim notif sistem recall: " . $e->getMessage());
                    }
                }
            } else {
                $customer->update(['status_approval' => 'Error', 'route_to' => 'No Approver Found']);
            }

            activity()
                ->causedBy($user)
                ->performedOn($customer)
                ->useLog('customer')
                ->event('recall')
                ->withProperties(['name' => $customer->name])
                ->log("Recalled and resubmitted customer: {$customer->name}");
        });

        try {
            if ($newLogs) {
                $firstLogData = $newLogs->firstWhere('level', 1);

                if ($firstLogData) {
                    $approverNik = $firstLogData['approver_nik'];
                    $approverUser = User::where('nik', $approverNik)->first();
                    $token = $firstLogData['token'] ?? $firstLogData->token;

                    if ($approverUser && $approverUser->email) {
                        $recipients = [
                            [
                                'nik' => $approverUser->nik,
                                'email' => $approverUser->email,
                                'name' => $approverUser->name,
                                'level' => 1,
                                'is_first' => true,
                                'is_it' => $approverUser->hasRole('it')
                            ]
                        ];

                        CustomerJob::dispatch($customer->id, $recipients, $token, 'approval');
                        Log::info("Email recall (Level 1) dikirim ke: " . $approverUser->email);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email recall: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Data berhasil diajukan ulang! Status kembali ke Pending.']);
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        $canAdjustFinance = $user->hasRole('manager-finance') || $user->hasRole('head-finance') || $user->hasRole('super-admin');

        $baseTotalAmount = 0;
        foreach ($customer->items as $item) {
            $baseTotalAmount += ($item->quantity * $item->price);
        }

        $data = $customer->load(['sales.user', 'files', 'bankGaransis', 'bgRecommendations', 'creditLimits', 'items'])->toArray();

        $data['can_adjust_finance'] = $canAdjustFinance;
        $data['base_total_amount']  = $baseTotalAmount;

        return response()->json($data);
    }

    public function store(CustomerRequest $request)
    {
        $category = 'Customer';
        $subCategory = ($request->term_of_payment === 'CBD') ? 'CBD' : null;

        $pathExists = ApprovalPath::where('category', $category)
            ->where(function ($q) use ($subCategory) {
                if (!empty($subCategory)) {
                    $q->where('sub_category', $subCategory);
                } else {
                    $q->whereNull('sub_category')->orWhere('sub_category', '');
                }
            })->exists();

        if (!$pathExists) {
            $pathName = $subCategory ? "Customer - $subCategory" : "Customer - General (Non-CBD)";
            return response()->json([
                'success' => false,
                'message' => "GAGAL: Alur Approval untuk '$pathName' tidak ditemukan. Hubungi IT/Admin."
            ], 422);
        }

        try {
            $customer = $this->customerService->createCustomer($request->all(), $request);
            $this->dispatchEmailJob($customer);
            try {
                $admins = User::role('super-admin')->get();
                if ($admins->count() > 0) {
                    Notification::send($admins, new SystemNotification(
                        'New Customer Created',
                        "Customer <b>{$customer->name}</b> telah dibuat (Monitoring).",
                        route('customers.index'),
                        'ph-eye',
                        'info'
                    ));
                }
            } catch (\Exception $e) {
                Log::error("Gagal kirim notif sistem pembuatan customer: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully!',
                'data' => $customer
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error create customer: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    private function dispatchEmailJob($customer)
    {
        try {
            $firstLog = ApprovalLog::where('category', 'Customer')
                ->where('related_id', $customer->id)
                ->where('level', 1)
                ->first();

            if ($firstLog) {
                $approverUser = User::where('nik', $firstLog->approver_nik)->first();
                if ($approverUser && $approverUser->email) {
                    $recipients = [[
                        'nik' => $approverUser->nik,
                        'email' => $approverUser->email,
                        'name' => $approverUser->name,
                        'level' => 1,
                        'is_first' => true,
                        'is_it' => $approverUser->hasRole('it')
                    ]];

                    CustomerJob::dispatch($customer->id, $recipients, $firstLog->token, 'approval');
                    Log::info("Job Email Approval dikirim ke antrian untuk: " . $approverUser->email);
                }
            }
        } catch (\Exception $e) {
            Log::error('Gagal dispatch email job: ' . $e->getMessage());
        }
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

        $customer = Customer::with(['user', 'accountGroup', 'customerClass', 'files'])
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
            'notes' => 'nullable|string',
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
            ->where('status', 'Pending')
            ->first();

        if (!$currentLog) {
            return view('page.customer.links.approval-invalid');
        }

        $actor = User::where('nik', $currentLog->approver_nik)->first();

        $isFinanceAdjuster = $actor && ($actor->hasRole('manager-finance') || $actor->hasRole('head-finance') || $actor->hasRole('super-admin'));
        $isIT = $actor && $actor->hasRole('it');
        $cleanNotes = trim($notes);

        if ($isFinanceAdjuster && ($action === 'review' || $action === 'approve')) {
            $isTopChanged = request()->has('update_top') && request('update_top') != $customer->term_of_payment;


            if ($isTopChanged) {
                if (empty($cleanNotes)) {
                    return back()->withInput()->withErrors(['notes' => 'Notes wajib diisi karena Anda mengubah Term of Payment (TOP).']);
                }
            }
        } else {
            if ($action === 'review' || $action === 'reject') {

                if (!$isIT) {
                    if (empty($cleanNotes)) {
                        return back()->withInput()->withErrors(['notes' => 'Notes wajib diisi untuk keputusan Review/Reject.']);
                    }

                    if (!preg_match('/[a-zA-Z]{2,}/', $cleanNotes)) {
                        return back()->withInput()->withErrors(['notes' => 'Notes harus berisi kalimat yang jelas.']);
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            if (($action === 'review' || $action === 'approve') && $isFinanceAdjuster) {
                $updateData = [];
                $changesLog = [];


                if (request()->has('update_top') && request('update_top') != $customer->term_of_payment) {
                    $updateData['term_of_payment'] = request('update_top');
                    $changesLog[] = "TOP changed to " . request('update_top');
                }

                if (request()->has('update_lead_time') && request('update_lead_time') != $customer->lead_time) {
                    $updateData['lead_time'] = request('update_lead_time');
                    $changesLog[] = "Lead Time changed to " . request('update_lead_time');
                }

                if (request()->has('update_credit_limit_value') && request('update_credit_limit_value') != $customer->credit_limit) {
                    $updateData['credit_limit'] = request('update_credit_limit_value');
                }

                if (request()->filled('update_npwp') && request('update_npwp') != $customer->npwp) {
                    $updateData['npwp'] = request('update_npwp');
                    $changesLog[] = "NPWP corrected by Finance";
                }

                if (request()->has('update_va')) {
                    $updateData['virtual_account'] = request('update_va');
                }

                if (request()->has('update_payment_days')) {
                    $updateData['payment_days'] = request('update_payment_days');
                }

                if (request()->has('update_payment_date')) {
                    $updateData['payment_date'] = request('update_payment_date');
                }

                if (request()->has('update_faktur_days')) {
                    $updateData['faktur_days'] = request('update_faktur_days');
                }

                if (request()->has('update_faktur_date')) {
                    $updateData['faktur_date'] = request('update_faktur_date');
                }

                if (!empty($updateData)) {
                    $customer->update($updateData);
                    if (isset($updateData['virtual_account'])) $changesLog[] = "VA Updated";
                    if (isset($updateData['payment_days'])) $changesLog[] = "Payment Days Updated";

                    if (!empty($changesLog)) {
                        $prefix = !empty($notes) ? "\n" : "";
                        $notes .= $prefix . "[System - Finance]: Data Finance (VA/Payment/Faktur) diperbarui.";
                    }
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
            }

            // --- 1. Tentukan Status & Notes ---
            $dbStatus = '';
            $dbNotes = '';

            if ($action === 'approve') {
                $dbStatus = 'Approved';
                $dbNotes = empty($notes) ? 'Approve tanpa notes' : $notes;
            } elseif ($action === 'review') {
                $dbStatus = 'Approved';
                $dbNotes = $notes; // Gunakan notes dari inputan
            } elseif ($action === 'reject') {
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

            $admins = User::role('super-admin')
                ->where('id', '!=', $actor->id)
                ->get();

            $adminTitle = "Monitoring Approval";
            $adminMessage = "";
            $adminIcon = "ph-info";
            $adminColor = "info";

            if ($action === 'approve' || $action === 'review') {
                $adminTitle = "Approval Customer";
                $adminMessage = "Customer <b>{$customer->name}</b> telah disetujui (Level {$currentLog->level}) oleh <b>{$actor->name}</b>. Status: " . ($dbStatus == 'Approved' ? 'Lanjut ke Level Berikutnya/Selesai' : 'Review');
                $adminIcon = "ph-check-circle";
                $adminColor = "success";
            } elseif ($action === 'reject') {
                $adminTitle = "Rejection Customer";
                $adminMessage = "Customer <b>{$customer->name}</b> DITOLAK oleh <b>{$actor->name}</b> di Level {$currentLog->level}. Alasan: {$notes}";
                $adminIcon = "ph-x-circle";
                $adminColor = "danger";
            }

            if ($admins->count() > 0) {
                Notification::send($admins, new SystemNotification(
                    $adminTitle,
                    $adminMessage,
                    route('customers.index'),
                    $adminIcon,
                    $adminColor
                ));
            }

            $currentLog->update([
                'status' => $dbStatus,
                'notes' => $dbNotes,
                'updated_at' => now(),
                'token' => null,
            ]);

            if ($dbStatus === 'Approved') {
                $nextLevel = $currentLog->level + 1;
                $nextLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('level', $nextLevel)
                    ->where('status', 'Pending')
                    ->latest('id')
                    ->first();

                if ($nextLog) {
                    if (empty($nextLog->token)) {
                        $nextLog->update(['token' => Str::uuid()->toString()]);
                        $nextLog->refresh();
                    }

                    $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                    $nextApproverName = $nextApproverUser ? $nextApproverUser->name : $nextLog->approver_nik;

                    $customer->update([
                        'status_approval' => 'Processing',
                        'route_to' => $nextApproverName
                    ]);

                    if ($nextApproverUser) {
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

                        if ($nextApproverUser->email) {
                            $recipients = [[
                                'nik' => $nextApproverUser->nik,
                                'email' => $nextApproverUser->email,
                                'name' => $nextApproverUser->name,
                                'level' => $nextLog->level,
                                'is_first' => false,
                                'is_it' => $nextApproverUser->hasRole('it')
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

                    if ($customer->email) {
                        try {
                            Mail::to($customer->email)
                                ->queue(new CustomerWelcomeMail($customer));

                            $notes .= "\n[System]: Welcome Email sent to Customer ($customer->email).";
                        } catch (\Exception $e) {
                            Log::error("Gagal kirim Welcome Email ke Customer: " . $e->getMessage());
                            $notes .= "\n[System Error]: Gagal kirim email ke customer.";
                        }
                    }

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
            } elseif ($dbStatus === 'Rejected') {
                ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customer->id)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Rejected', 'token' => null]);

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

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Action processed successfully.']);
            }

            return view('page.customer.links.approval-success', [
                'action' => $action,
                'customerName' => $customer->name,
                'routeTo' => $customer->route_to,
                'statusApproval' => $customer->status_approval
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval Process Error: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
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
            'sales',
            'top',
            'accountgroup',
            'customerClass',
            'pendingCount',
            'approvedCount',
            'activeCount',
            'inactiveCount',
            'approvalStatuses',
            'accountStatuses'
        ));
    }

    public function getApprovalData(Request $request)
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

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'Active') {
                $query->where('customers.bank_garansi', 'YA');
            } elseif ($request->status === 'Inactive') {
                $query->where(function ($q) {
                    $q->where('customers.bank_garansi', '!=', 'YA')
                        ->orWhereNull('customers.bank_garansi');
                });
            }
        }

        if ($request->has('approval_status') && $request->approval_status !== 'all') {
            $query->where('customers.status_approval', $request->approval_status);
        }

        $query->where('approval_logs.status', 'Pending');
        $query->whereIn('customers.status_approval', ['Pending', 'Processing']);

        if (!$currentUser->hasRole('super-admin')) {
            $query->where('approval_logs.approver_nik', $currentUser->nik);
        }

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
                $baseClass = match ($status) {
                    'Approved', 'Completed' => 'bg-success',
                    'Rejected' => 'bg-danger',
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
            ->addColumn('action', function ($row) use ($currentUser) {
                $token = $row->token;
                $customerName = e($row->customer_name);
                $customerId = $row->customer_id;

                $canAction = true;
                if ($row->level > 1) {
                    $prevLog = ApprovalLog::where('category', 'Customer')
                        ->where('related_id', $row->customer_id)
                        ->where('level', $row->level - 1)
                        ->latest('id')
                        ->first();

                    if (!$prevLog || $prevLog->status !== 'Approved') {
                        $canAction = false;
                    }
                }

                if (!$canAction) {
                    return '<button type="button" class="btn btn-sm btn-secondary" onclick="Swal.fire(\'Locked\', \'Menunggu approval level sebelumnya.\', \'warning\')">
                            <i class="ph-bold ph-lock-key text-white"></i> Locked</button>';
                }

                $btnResend = '';
                if ($row->status === 'Pending') {
                    $approverName = $row->approver ? $row->approver->name : $row->approver_nik;
                    $btnResend = '<button type="button" class="btn btn-sm btn-warning btn-resend-email"
                                    data-token="' . $token . '"
                                    data-approver-name="' . e($approverName) . '"
                                    data-bs-toggle="tooltip" title="Resend Email Notification">
                                    <i class="ph-bold ph-paper-plane-tilt text-white"></i>
                                </button>';
                }

                $btnInputCode = '<button type="button" class="btn btn-sm btn-primary action-btn-modal"
                                data-id="' . $customerId . '"
                                data-token="' . $token . '"
                                data-action="review"
                                data-name="' . $customerName . '"
                                data-bs-toggle="tooltip" title="Input Customer Code & Join Date">
                                <i class="ph-bold ph-pencil-simple text-white me-1"></i>
                            </button>';

                // 4. Tombol Standard (Approve/Review/Reject)
                $btnApprove = '<button type="button" class="btn btn-sm btn-success action-btn"
                                data-id="' . $customerId . '"
                                data-token="' . $token . '"
                                data-action="approve"
                                data-name="' . $customerName . '"
                                data-bs-toggle="tooltip" title="Quick Approve">
                                <i class="ph-bold ph-thumbs-up text-white"></i></button>';

                $btnReview = '<button type="button" class="btn btn-sm btn-primary action-btn-modal"
                                data-id="' . $customerId . '"
                                data-token="' . $token . '"
                                data-action="review"
                                data-name="' . $customerName . '"
                                data-bs-toggle="tooltip" title="Review with Notes">
                                <i class="ph-bold ph-note-pencil text-white"></i></button>';

                $btnReject = '<button type="button" class="btn btn-sm btn-danger action-btn-modal"
                                data-id="' . $customerId . '"
                                data-token="' . $token . '"
                                data-action="reject"
                                data-name="' . $customerName . '"
                                data-bs-toggle="tooltip" title="Reject">
                                <i class="ph-bold ph-thumbs-down text-white"></i></button>';

                if ($currentUser->hasRole(['it', 'IT'])) {
                    return "<div class='d-flex gap-1 justify-content-center'>{$btnInputCode}</div>";
                }

                if ($currentUser->hasRole('super-admin')) {
                    $targetIsIT = false;
                    if ($row->approver && $row->approver->hasRole(['it', 'IT'])) {
                        $targetIsIT = true;
                    }

                    if ($targetIsIT) {
                        return "<div class='d-flex gap-1 justify-content-center'>{$btnInputCode} {$btnResend}</div>";
                    } else {
                        return "<div class='d-flex gap-1 justify-content-center'>{$btnApprove} {$btnReview} {$btnReject} {$btnResend}</div>";
                    }
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

    public function logPage()
    {
        return view('page.customer.log.index');
    }

    public function getLogData()
    {
        $query = Activity::with('causer')
            ->where(function ($q) {
                $q->where('log_name', 'like', '%customer%')
                    ->orWhere('log_name', 'like', 'sample%')
                    ->orWhere('log_name', 'path%'); // Menangkap 'path - customer' dll
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('log_name', function ($log) {
                $logName = $log->log_name;
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

                $displayText = ucwords(str_replace('-', ' ', $logName));

                return '<span class="badge ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . e($displayText) . '</span>';
            })
            ->addColumn('properties', function ($log) {
                $props = $log->properties ?? [];

                if (empty($props) || (is_object($props) && method_exists($props, 'isEmpty') && $props->isEmpty())) {
                    return '<span class="text-muted small">-</span>';
                }

                $output = '<div class="small">';

                foreach ($props as $key => $value) {
                    if ($key === 'old') continue;
                    $label = ucfirst(str_replace(['_', '-'], ' ', $key));

                    if ($key === 'attributes') {
                        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $escaped = e($json);
                        $output .= '<div class="mb-1"><span class="text-muted">' . e($label) . ':</span> '
                            . '<button class="btn btn-xs btn-outline-secondary btn-view-json ms-2" data-json="' . $escaped . '">View details</button></div>';
                        continue;
                    }

                    if (is_array($value) || $value instanceof \Illuminate\Support\Collection) {
                        if (str_contains(strtolower($key), 'approver') || str_contains(strtolower($key), 'approvers')) {
                            $output .= '<div class="mb-1"><span class=" text-muted text-dark">' . e($label) . ':</span>';
                            $output .= '<ul class="m-0 ps-3" style="font-size:0.9rem;">';
                            foreach ((array) $value as $v) {
                                if (is_array($v)) {
                                    $name = $v['name'] ?? reset($v);
                                } else {
                                    $name = $v;
                                }
                                $output .= '<li class="text-dark">' . e($name) . '</li>';
                            }
                            $output .= '</ul></div>';
                            continue;
                        }

                        $preview = implode(', ', array_map(function ($i) {
                            if (is_array($i)) return json_encode($i, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            return (string) $i;
                        }, (array) $value));

                        $output .= '<div class="mb-1"><span class="">' . e($label) . ':</span> <span class="fw-bold text-dark">' . e(Str::limit($preview, 80)) . '</span></div>';
                        continue;
                    }

                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                            if (str_contains(strtolower($key), 'approver') || str_contains(strtolower($key), 'approvers')) {
                                $output .= '<div class="mb-1"><span class="fw-bold text-dark">' . e($label) . ':</span>';
                                $output .= '<ul class="m-0 ps-3" style="font-size:0.9rem;">';
                                foreach ((array) $decoded as $v) {
                                    if (is_array($v)) {
                                        $name = $v['name'] ?? reset($v);
                                    } else {
                                        $name = $v;
                                    }
                                    $output .= '<li class="text-dark">' . e($name) . '</li>';
                                }
                                $output .= '</ul></div>';
                                continue;
                            }

                            $preview = is_array($decoded) ? implode(', ', array_map(function ($i) {
                                return is_array($i) ? json_encode($i) : (string) $i;
                            }, $decoded)) : json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                            $output .= '<div class="mb-1"><span class="">' . e($label) . ':</span> <span class="fw-bold text-dark">' . e(Str::limit($preview, 80)) . '</span></div>';
                            continue;
                        }
                    }

                    $display = is_null($value) ? '-' : (string) $value;
                    $output .= '<div class="mb-1"><span class="text-muted">' . e($label) . ':</span> <span class="fw-bold text-dark">' . e(Str::limit($display, 80)) . '</span></div>';
                }

                $output .= '</div>';

                return $output;
            })
            ->addColumn('subject_info', function ($log) {

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
                    case 'create':
                        $badgeClass = 'bg-success';
                        $icon = 'ph-plus-circle';
                        break;
                    case 'update':
                        $badgeClass = 'bg-warning text-dark';
                        $icon = 'ph-pencil-simple';
                        break;
                    case 'delete':
                        $badgeClass = 'bg-danger';
                        $icon = 'ph-trash';
                        break;
                    case 'approve':
                        $badgeClass = 'bg-success';
                        $icon = 'ph-check-circle';
                        break;
                    case 'reject':
                        $badgeClass = 'bg-danger';
                        $icon = 'ph-x-circle';
                        break;
                    case 'review':
                        $badgeClass = 'bg-info';
                        $icon = 'ph-eye';
                        break;
                    case 'resend':
                        $badgeClass = 'bg-primary';
                        $icon = 'ph-paper-plane-tilt';
                        break;
                }
                return '<span class="badge ' . $badgeClass . '"><i class="ph-bold ' . $icon . ' me-1"></i>' . ucfirst($event) . '</span>';
            })
            ->editColumn('created_at', function ($log) {
                return Carbon::parse($log->created_at)->format('d M Y, H:i');
            })
            ->rawColumns(['log_name', 'event', 'subject_info', 'causer_info', 'subject_id', 'properties'])
            ->make(true);
    }
}
