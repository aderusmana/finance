<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class BankGaransiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankGaransi::leftJoin('customers', 'bank_garansi.customer_id', '=', 'customers.id')
                ->with(['details']) // Tetap load details jika perlu
                ->select([
                    'bank_garansi.*',
                    'customers.name as customer_name_real' // Alias nama dari tabel customers
                ]);

            if ($request->has('status') && $request->status != 'all') {
                $query->where('bank_garansi.status', $request->status);
            }
            if ($request->has('bg_type') && $request->bg_type != 'all') {
                $query->where('bank_garansi.bg_type', $request->bg_type);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('issued_date', function($row){
                    return $row->issued_date ? date('d M Y', strtotime($row->issued_date)) : '-';
                })
                ->editColumn('exp_date', function($row){
                    return $row->exp_date ? date('d M Y', strtotime($row->exp_date)) : '-';
                })
                ->editColumn('bg_nominal', function($row){
                    return 'Rp ' . number_format($row->bg_nominal, 0, ',', '.');
                })
                ->addColumn('customer_name', function($row){
                    return $row->customer_name_real ?? 'N/A';
                })
                ->filterColumn('customer_name', function($query, $keyword) {
                    $query->where('customers.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('customer_name', function ($query, $order) {
                    $query->orderBy('customers.name', $order);
                })
                ->addColumn('action', function ($row) {
                    $viewBtn = '<a href="' . route('bg-list.show', $row->id) . '" class="btn btn-sm btn-info text-white" title="View Detail"><i class="ph-bold ph-eye"></i></a>';
                    $editBtn = '<button type="button" class="btn btn-sm btn-warning btn-edit-bg text-white" data-id="' . $row->id . '" title="Edit"><i class="ph-bold ph-pencil-simple"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-bg text-white" data-id="' . $row->id . '" title="Delete"><i class="ph-bold ph-trash"></i></button>';
                    return '<div class="d-flex gap-2 justify-content-center">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $customers = Customer::select('id', 'name', 'code')
                        ->where('bank_garansi', 'YA')
                        ->orderBy('name')
                        ->get();

        $stats = [
            'total' => BankGaransi::count(),
            'active' => BankGaransi::where('status', 'approved')->count(),
            'draft' => BankGaransi::where('status', 'draft')->count(),
            'expiring' => BankGaransi::where('exp_date', '<', now()->addMonth())->where('status', 'approved')->count(),
        ];

        return view('page.bg.bg_list.index', compact('customers', 'stats'));
    }

    public function show($id)
    {
        if (request()->ajax() || request()->wantsJson()) {
            $bg = BankGaransi::with(['details', 'customer'])->findOrFail($id);
            return response()->json($bg);
        }

        $bg = BankGaransi::with(['customer', 'details', 'histories.user', 'creator'])->findOrFail($id);

        return view('page.bg.bg_list.show', compact('bg'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'items' => 'required|array',
            'items.*.bg_number' => 'required|distinct|unique:bank_garansi,bg_number',
            'items.*.nominal' => 'required|numeric|min:0',
            'items.*.bank_name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $latestBg = BankGaransi::where('customer_id', $request->customer_id)
                                       ->orderBy('id', 'desc')
                                       ->first();

                $baseBgId = $latestBg ? $latestBg->id : null;

                $bg = BankGaransi::create([
                    'customer_id' => $request->customer_id,
                    'bg_number'   => $item['bg_number'],
                    'bg_type'     => $item['bg_type'] ?? 'new',
                    'bg_nominal'  => $item['nominal'],
                    'base_bg_id'  => $baseBgId,
                    'issued_date' => $item['issued_date'] ?? null,
                    'exp_date'    => $item['exp_date'] ?? null,
                    'status'      => 'draft',
                    'created_by'  => auth()->id(),
                ]);

                if (!$baseBgId) {
                    $bg->update(['base_bg_id' => $bg->id]);
                }

                $bg->details()->create([
                    'bank_name'      => $item['bank_name'],
                    'branch_name'    => $item['branch_name'] ?? null,
                    'bank_address'   => $item['bank_address'] ?? null,
                    'contact_person' => $item['contact_person'] ?? null,
                    'nominal'        => $item['nominal'],
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bank Garansi created successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->input('items')[0] ?? null;

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Invalid data'], 422);
        }

        $request->validate([
            'customer_id' => 'required',
            'items.0.bg_number' => 'required|unique:bank_garansi,bg_number,' . $id,
            'items.0.nominal' => 'required|numeric|min:0',
            'items.0.bank_name' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $bg = BankGaransi::findOrFail($id);

            $bg->update([
                'customer_id' => $request->customer_id,
                'bg_number'   => $data['bg_number'],
                'bg_type'     => $data['bg_type'],
                'bg_nominal'  => $data['nominal'],
                'issued_date' => $data['issued_date'],
                'exp_date'    => $data['exp_date'],
            ]);

            $bg->details()->delete();
            $bg->details()->create([
                'bank_name'      => $data['bank_name'],
                'branch_name'    => $data['branch_name'] ?? null,
                'bank_address'   => $data['bank_address'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'nominal'        => $data['nominal'],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bank Garansi updated successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bg = BankGaransi::findOrFail($id);
            $bg->delete();
            return response()->json(['success' => true, 'message' => 'Bank Garansi deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
