<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use Yajra\DataTables\Facades\DataTables;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Distributor::with('customer')->select('distributors.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function($row) {
                    $badge = '';
                    if ($row->customer_id && $row->customer) {
                        $badge = ' <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 ms-1" style="font-size: 0.72rem;" title="Terhubung dengan Master Customer"><i class="ph-bold ph-link-simple"></i> Customer</span>';
                    }
                    return '<span>' . e($row->name) . '</span>' . $badge;
                })
                ->editColumn('email', function($row) {
                    $emails = $row->email_list;
                    if (empty($emails)) {
                        return '<span class="text-muted fst-italic">Belum diisi</span>';
                    }
                    $badges = array_map(function($e) {
                        return '<span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.78rem; font-weight: normal;"><i class="ph-bold ph-envelope me-1 text-primary"></i>' . e($e) . '</span>';
                    }, $emails);
                    return '<div class="d-flex flex-wrap gap-1">' . implode('', $badges) . '</div>';
                })
                ->addColumn('action', function($row){
                    return '
                        <button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'"><i class="ph-bold ph-trash"></i> Hapus</button>
                    ';
                })
                ->rawColumns(['name', 'email', 'action'])
                ->make(true);
        }

        $customers = Customer::whereNotNull('code')
            ->where('code', '!=', '')
            ->orderByRaw("CASE WHEN code LIKE 'ID%' OR code LIKE 'IE%' THEN 0 ELSE 1 END")
            ->orderBy('code', 'asc')
            ->get(['id', 'code', 'name', 'email', 'purchasing_manager_email', 'finance_manager_email']);

        return view('page.master.distributor.index', compact('customers'));
    }

    protected function parseEmails($input): array
    {
        if (is_array($input)) {
            $raw = $input;
        } elseif (is_string($input)) {
            $raw = preg_split('/[;,]+/', $input);
        } else {
            $raw = [];
        }

        $emails = [];
        foreach ($raw as $e) {
            $trimmed = trim((string)$e);
            if ($trimmed !== '' && !in_array($trimmed, $emails)) {
                $emails[] = $trimmed;
            }
        }
        return $emails;
    }

    public function store(Request $request)
    {
        $emails = $this->parseEmails($request->input('email') ?? $request->input('emails'));

        $request->merge([
            'email_items' => $emails,
        ]);

        $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'code'          => 'required|string|max:100|unique:distributors,code',
            'name'          => 'required|string|max:255',
            'email_items'   => 'required|array|min:1',
            'email_items.*' => 'required|email|max:255',
        ], [
            'email_items.required' => 'Email distributor wajib diisi minimal 1 email.',
            'email_items.min'      => 'Email distributor wajib diisi minimal 1 email.',
            'email_items.*.email'  => 'Format email :input tidak valid.',
        ]);

        Distributor::create([
            'customer_id' => $request->customer_id,
            'code'        => $request->code,
            'name'        => $request->name,
            'email'       => implode(', ', $emails),
        ]);

        return response()->json(['success' => true, 'message' => 'Distributor berhasil ditambahkan!']);
    }

    public function show($id)
    {
        $distributor = Distributor::with('customer')->findOrFail($id);
        return response()->json($distributor);
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $emails = $this->parseEmails($request->input('email') ?? $request->input('emails'));

        $request->merge([
            'email_items' => $emails,
        ]);

        $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'code'          => 'required|string|max:100|unique:distributors,code,'.$id,
            'name'          => 'required|string|max:255',
            'email_items'   => 'required|array|min:1',
            'email_items.*' => 'required|email|max:255',
        ], [
            'email_items.required' => 'Email distributor wajib diisi minimal 1 email.',
            'email_items.min'      => 'Email distributor wajib diisi minimal 1 email.',
            'email_items.*.email'  => 'Format email :input tidak valid.',
        ]);

        $distributor->update([
            'customer_id' => $request->customer_id,
            'code'        => $request->code,
            'name'        => $request->name,
            'email'       => implode(', ', $emails),
        ]);

        return response()->json(['success' => true, 'message' => 'Distributor berhasil diubah!']);
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();

        return response()->json(['success' => true, 'message' => 'Distributor berhasil dihapus!']);
    }

    public function getCustomersByDistributor($distributor_id)
    {
        // Mengambil customer yang sudah punya baris di tabel pivot distributor_customers
        $distributor = Distributor::with('customers')->findOrFail($distributor_id);

        return response()->json($distributor->customers);
    }
}
