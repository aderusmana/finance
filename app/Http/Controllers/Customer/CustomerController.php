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
use App\Models\Requisition\ApprovalLog;
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

            $term = strtoupper(trim((string) $request->input('term_of_payment', '')));
            $subCategory = ($term === 'CBD') ? $term : null;

            $logs = $this->generateApprovalLogs($user, $createdCustomer->id, 'CUSTOMER', $subCategory);

            $firstLog = ApprovalLog::where('category', 'CUSTOMER')
                ->where('related_id', $createdCustomer->id)
                ->orderBy('level', 'asc')
                ->first();

            if ($firstLog) {
                $firstApprover = User::where('nik', $firstLog->approver_nik)->first();
                if ($firstApprover) {
                    $createdCustomer->update(['route_to' => $firstApprover->name, 'status' => 'Pending']);
                } else {
                    $createdCustomer->update(['status_approval' => 'Error', 'route_to' => 'Error: First Approver Not Found']);
                    Log::error("Approver pertama dengan NIK {$firstLog->approver_nik} tidak ditemukan.");
                }
            } else {
                $createdCustomer->update(['status' => 'Completed', 'route_to' => 'Finished (No Path)']);
                Log::warning("Tidak ada alur approval untuk kategori CUSTOMER yang cocok. Auto-complete Customer ID {$createdCustomer->id}.");
            }
        });

        // Ensure customer was created before proceeding
        if (! $createdCustomer) {
            Log::error('Customer creation failed; aborting notification dispatch');
            return response()->json(['success' => false, 'message' => 'Failed to create customer'], 500);
        }

        // After commit, prepare recipients and dispatch queued job to send mails
        try {
            if ($logs && $logs->isNotEmpty()) {
                $niks = collect($logs)->pluck('approver_nik')->unique()->filter()->values()->all();
                $users = User::whereIn('nik', $niks)->get()->keyBy('nik');

                $firstNik = $firstLog->approver_nik ?? null;

                $recipients = [];
                foreach ($logs as $log) {
                    $nik = $log['approver_nik'] ?? null;
                    $u = $users->get($nik);
                    $recipients[] = [
                        'nik' => $nik,
                        'email' => $u->email ?? null,
                        'name' => $u->name ?? null,
                        'level' => $log['level'] ?? null,
                        'is_first' => ($nik === $firstNik),
                    ];
                }

                $adminEmail = config('mail.from.address');
                if ($adminEmail) {
                    $recipients[] = ['nik' => null, 'email' => $adminEmail, 'name' => config('mail.from.name'), 'level' => null, 'is_first' => false];
                }

                $token = $firstLog->token ?? null;

                CustomerJob::dispatch($createdCustomer->id, $recipients, $token, 'approval');
            } else {
                $adminEmail = config('mail.from.address');
                if ($adminEmail) {
                    CustomerJob::dispatch($createdCustomer->id, [['nik' => null, 'email' => $adminEmail, 'name' => config('mail.from.name'), 'level' => null, 'is_first' => false]], null, 'notification');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error dispatching CustomerJob', ['customer_id' => $createdCustomer->id ?? null, 'error' => $e->getMessage()]);
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
}
