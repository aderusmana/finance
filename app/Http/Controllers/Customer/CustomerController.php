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
use App\Models\Master\ApprovalLog;
use App\Jobs\CustomerJob;
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
                $dataAttrs .= ' data-tanggal_npwp="' . e($row->tanggal_npwp) . '"';
                $dataAttrs .= ' data-nppkp="' . e($row->nppkp) . '"';
                $dataAttrs .= ' data-tanggal_nppkp="' . e($row->tanggal_nppkp) . '"';
                $dataAttrs .= ' data-output_tax="' . e($row->output_tax) . '"';
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
            ->rawColumns(['action'])
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

    public function store(Request $request)
    {

        $createdCustomer = null;
        $logs = collect();
        $firstLog = null;

        // Wrap DB changes in a transaction to keep consistency
        DB::transaction(function () use ($request, &$createdCustomer, &$logs, &$firstLog) {
            $user = Auth::user();

            $createdCustomer = Customer::create($request->all());

            // $term = strtoupper(trim((string) $request->input('term_of_payment', '')));
            // $subCategory = ($term === 'CBD') ? $term : null;

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
                Log::warning("Tidak ada alur approval untuk kategori CUSTOMER yang cocok. Auto-complete Customer ID {$createdCustomer->id}.");
            }
        });

        // Ensure customer was created before proceeding
        if (! $createdCustomer) {
            Log::error('Customer creation failed; aborting notification dispatch');
            return response()->json(['success' => false, 'message' => 'Failed to create customer'], 500);
        }

        try {
            // 1. Ambil Log Level 1 Saja
            // Karena $logs adalah Collection of Arrays, kita akses pakai array key
            $firstLog = $logs->firstWhere('level', 1);

            if ($firstLog) {
                // PERBAIKAN DISINI: Akses pakai kurung siku ['approver_nik']
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
            $adminEmail = config('mail.from.address');
            if ($adminEmail) {
                // Uncomment jika ingin kirim notif ke admin juga
                // CustomerJob::dispatch($createdCustomer->id, [['nik' => null, 'email' => $adminEmail, 'name' => 'Admin', 'level' => null, 'is_first' => false]], null, 'notification');
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

    public function viewApprovalPage(Request $request, $token) // Tambahkan Request $request
    {
        $log = ApprovalLog::where('token', $token)->first();

        if (!$log) {
            abort(404, 'Token Approval Invalid atau Kadaluarsa.');
        }

        $customer = Customer::with(['user', 'accountGroup', 'customerClass'])
                    ->findOrFail($log->related_id);

        if ($log->status !== 'Pending') {
             // return view('page.customer.already-processed');
        }

        // Tangkap parameter 'pre_action' dari URL (default ke 'approve' jika tidak ada)
        $preSelectedAction = $request->query('pre_action', 'approve');

        return view('page.customer.links.approval-form', [
            'customer' => $customer,
            'token' => $token,
            'log' => $log,
            'preSelectedAction' => $preSelectedAction // Kirim ke view
        ]);
    }

    // Tambahkan method ini di dalam class CustomerController

    public function approvalAction(Request $request, $customerId)
    {
        // 1. Validasi Input
        $request->validate([
            'token' => 'required|string',
            'action' => 'required|in:approve,reject,review',
            'notes' => 'nullable|string',
        ]);

        $token = $request->input('token');
        $action = $request->input('action');
        $notes = $request->input('notes');

        // 2. Cek Token Valid & Status Masih Pending
        $currentLog = ApprovalLog::where('token', $token)
            ->where('related_id', $customerId)
            ->where('category', 'Customer')
            ->where('status', 'Pending') // Penting! Biar ga bisa diklik 2x
            ->first();

        if (!$currentLog) {
            // Jika tidak ketemu atau status bukan pending, tampilkan halaman Invalid
            return view('page.customer.links.approval-invalid');
        }

        $customer = Customer::findOrFail($customerId);

        DB::beginTransaction();
        try {
            // 3. Update Log Saat Ini
            $currentLog->update([
                'status' => ucfirst($action), // Approve / Reject / Review
                'notes' => $notes,
                'updated_at' => now(),
            ]);

            // 4. Logika Percabangan (Decision Making)
            if ($action === 'approve') {

                // --- Cek Apakah Ada Level Selanjutnya? ---
                $nextLevel = $currentLog->level + 1;

                // Cari log berikutnya yang levelnya +1 dari yang sekarang
                $nextLog = ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customerId)
                    ->where('level', $nextLevel)
                    ->first();

                if ($nextLog) {
                    // A. JIKA ADA NEXT APPROVER (Estafet)
                    $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                    $nextApproverName = $nextApproverUser ? $nextApproverUser->name : $nextLog->approver_nik;

                    // UPDATE ROUTE TO KE USER SELANJUTNYA
                    $customer->update([
                        'status_approval' => 'Processing',
                        'route_to' => $nextApproverName // <--- Update Route To
                    ]);

                    if ($nextApproverUser && $nextApproverUser->email) {
                        $recipients = [[
                            'nik' => $nextApproverUser->nik,
                            'email' => $nextApproverUser->email,
                            'name' => $nextApproverUser->name,
                            'level' => $nextLog->level,
                            'is_first' => false
                        ]];

                        CustomerJob::dispatch($customer->id, $recipients, $nextLog->token, 'approval');
                        Log::info("Estafet approval: Level {$currentLog->level} -> Level {$nextLog->level}");
                    }

                } else {
                    // B. JIKA INI APPROVER TERAKHIR (Finish)

                    $customer->update([
                        'status_approval' => 'Approved',
                        'route_to' => 'Finished',
                        'status' => 'Active' // Opsional: Aktifkan customer
                    ]);

                    Log::info("Approval Completed for Customer ID: {$customer->id}");
                }

            } elseif ($action === 'reject') {
                // C. JIKA REJECT

                // Batalkan semua log sisa (opsional, tapi rapi)
                ApprovalLog::where('category', 'Customer')
                    ->where('related_id', $customerId)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Canceled']);

                $customer->update([
                    'status_approval' => 'Rejected',
                    'route_to' => 'Rejected by ' . $currentLog->approver_nik
                ]);

                Log::info("Approval Rejected by Level {$currentLog->level}");
            }

            DB::commit();

            // 5. Tampilkan Halaman Success
            return view('page.customer.links.approval-success', [
                'action' => $action,
                'customerName' => $customer->name,
                'routeTo' => $customer->route_to
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval Action Error: ' . $e->getMessage());
            return abort(500, 'Terjadi kesalahan saat memproses approval.');
        }
    }
}
