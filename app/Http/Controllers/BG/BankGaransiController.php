<?php

namespace App\Http\Controllers\BG;

use App\Http\Controllers\Controller;
use App\Models\BG\BankGaransi;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BgExistingMail;
use App\Mail\BgExtensionMail;
use App\Models\BG\BgRecommendation;
use Illuminate\Support\Str;

class BankGaransiController extends Controller
{
    public function generateNumber(Request $request)
    {
        $customerId = $request->query('customer_id');

        if (!$customerId) {
            return response()->json(['number' => '']);
        }

        $currentYear = date('Y');

        $count = BankGaransi::where('customer_id', $customerId)
                            ->whereYear('created_at', $currentYear)
                            ->count();

        $nextSequence = $count + 1;
        $sequenceStr = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        $generatedNumber = "BG-{$currentYear}-{$sequenceStr}";

        return response()->json([
            'status' => 'success',
            'number' => $generatedNumber,
            'sequence' => $nextSequence,
            'prefix' => "BG-{$currentYear}-"
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BankGaransi::leftJoin('customers', 'bank_garansi.customer_id', '=', 'customers.id')
                ->with(['details'])
                ->select([
                    'bank_garansi.*',
                    'customers.name as customer_name_real'
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
                ->addColumn('menu', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<button class="btn btn-outline-success btn-sm btn-extension" data-id="'.$row->id.'" title="Ajukan Extension (Tambah BG)">';
                    $btn .= '<i class="ph-bold ph-plus-square me-1"></i> Ext';
                    $btn .= '</button>';

                    $btn .= '<button class="btn btn-outline-primary btn-sm btn-existing" data-id="'.$row->id.'" title="Update Existing (Ubah Nominal)">';
                    $btn .= '<i class="ph-bold ph-arrows-clockwise me-1"></i> Exist';
                    $btn .= '</button>';
                    $btn .= '</div>';
                    return $btn;
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-2">';
                    $btn .= '<button class="btn btn-sm btn-outline-info btn-show" data-id="'.$row->id.'" title="Lihat Detail">';
                    $btn .= '<i class="ph-bold ph-eye"></i>';
                    $btn .= '</button>';
                    $btn .= '<button class="btn btn-sm btn-outline-warning btn-edit" data-id="'.$row->id.'" title="Edit Data">';
                    $btn .= '<i class="ph-bold ph-pencil-simple"></i>';
                    $btn .= '</button>';
                    $btn .= '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="'.$row->id.'" title="Hapus Data">';
                    $btn .= '<i class="ph-bold ph-trash"></i>';
                    $btn .= '</button>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'menu', 'bg_type', 'status'])
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
            $bg = BankGaransi::with(['details', 'customer', 'creator    '])->findOrFail($id);
            return response()->json($bg);
        }
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

                if (($item['bg_type'] ?? '') === 'extension') {
                    if ($bg->customer && $bg->customer->email) {
                        Mail::to($bg->customer->email)->queue(new BgExtensionMail($bg));
                    }
                }
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
        $items = $request->input('items');
        if (!$items) {
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
            $mainData = $items[0];
            $bg = BankGaransi::findOrFail($id);

            $bg->update([
                'customer_id' => $request->customer_id,
                'bg_number'   => $mainData['bg_number'],
                'bg_type'     => $mainData['bg_type'],
                'bg_nominal'  => $mainData['nominal'],
                'issued_date' => $mainData['issued_date'],
                'exp_date'    => $mainData['exp_date'],
            ]);

            $bg->details()->delete();
            $bg->details()->create([
                'bank_name'      => $mainData['bank_name'],
                'branch_name'    => $mainData['branch_name'] ?? null,
                'bank_address'   => $mainData['bank_address'] ?? null,
                'contact_person' => $mainData['contact_person'] ?? null,
                'nominal'        => $mainData['nominal'],
            ]);

            if (($mainData['bg_type'] ?? '') === 'existing') {
                if ($bg->customer && $bg->customer->email) {
                    Mail::to($bg->customer->email)->queue(new BgExistingMail($bg));
                }
            }

            if (count($items) > 1) {
                for ($i = 1; $i < count($items); $i++) {
                    $item = $items[$i];

                    if(BankGaransi::where('bg_number', $item['bg_number'])->exists()){
                        throw new \Exception("BG Number {$item['bg_number']} already exists.");
                    }

                    $latestBg = BankGaransi::where('customer_id', $request->customer_id)
                                        ->orderBy('id', 'desc')->first();
                    $baseBgId = $latestBg ? $latestBg->id : null;

                    $newBg = BankGaransi::create([
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
                        $newBg->update(['base_bg_id' => $newBg->id]);
                    }

                    $newBg->details()->create([
                        'bank_name'      => $item['bank_name'],
                        'branch_name'    => $item['branch_name'] ?? null,
                        'bank_address'   => $item['bank_address'] ?? null,
                        'contact_person' => $item['contact_person'] ?? null,
                        'nominal'        => $item['nominal'],
                    ]);
                }
            }

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

    public function requestExisting(Request $request, $id)
    {
        try {
            $bg = BankGaransi::with('customer')->findOrFail($id);

            $bg->update([
                'bg_type' => 'existing'
            ]);

            $metadata = json_encode([
                'action' => 'existing',
                'target_bg_id' => $bg->id
            ]);

            $rec = BgRecommendation::where('customer_id', $bg->customer_id)
                                   ->latest()
                                   ->first();

            if ($rec) {
                $rec->update([
                    'token'      => Str::uuid(),
                    'status'     => 'process',
                    'notes'      => $metadata,
                    'created_by' => auth()->id(),
                    'updated_at' => now()
                ]);
            } else {
                $rec = BgRecommendation::create([
                    'customer_id' => $bg->customer_id,
                    'token'       => Str::uuid(),
                    'status'      => 'process',
                    'created_by'  => auth()->id(),
                    'notes'       => $metadata
                ]);
            }

            Mail::to($bg->customer->email)->queue(new BgExistingMail($bg, $rec));

            return response()->json(['success' => true, 'message' => 'Tipe BG diubah menjadi EXISTING & Link update dikirim!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function requestExtension(Request $request)
    {
        try {
            $customerId = $request->input('customer_id');
            if($request->has('bg_id')){
                $bg = BankGaransi::find($request->bg_id);
                $customerId = $bg->customer_id;
            }

            $customer = Customer::findOrFail($customerId);

            $metadata = json_encode([
                'action' => 'extension'
            ]);

            $rec = BgRecommendation::where('customer_id', $customer->id)
                                   ->latest()
                                   ->first();

            if ($rec) {
                $rec->update([
                    'token'      => Str::uuid(),
                    'status'     => 'process',
                    'notes'      => $metadata,
                    'created_by' => auth()->id(),
                    'updated_at' => now()
                ]);
            } else {
                $rec = BgRecommendation::create([
                    'customer_id' => $customer->id,
                    'token'       => Str::uuid(),
                    'status'      => 'process',
                    'created_by'  => auth()->id(),
                    'notes'       => $metadata
                ]);
            }

            Mail::to($customer->email)->queue(new BgExtensionMail($rec));

            return response()->json(['success' => true, 'message' => 'Request Extension diproses. Link pembuatan BG Baru dikirim!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
