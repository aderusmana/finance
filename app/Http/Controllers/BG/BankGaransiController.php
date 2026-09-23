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
use Illuminate\Support\Facades\Auth;

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

    public function downloadTemplate()
    {
        $headers = [
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=template_import_master_bank_garansi.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'customer_code',
            'bg_number',
            'bank_name',
            'branch_name',
            'bg_nominal',
            'issued_date',
            'exp_date',
            'status',
            'contact_person',
            'bank_address'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            fputcsv($file, [
                'CUST-001',
                '0021/BG/BCA/2026',
                'Bank Central Asia',
                'KCU Sudirman',
                '500000000',
                '2026-01-15',
                '2027-01-15',
                'approved',
                'Bpk. Budi (08123456789)',
                'Jl. Jend Sudirman No 1 Jakarta'
            ], ';');

            fputcsv($file, [
                'PKD/002/2026',
                'BG-2026-0002',
                'Bank Mandiri',
                'Cabang Thamrin',
                '250000000',
                '2026-02-01',
                '2027-02-01',
                'approved',
                'Ibu Siti (08198765432)',
                'Jl. MH Thamrin No 2 Jakarta'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'file.required' => 'File import wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa CSV atau Excel (.xlsx, .xls).',
            'file.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($ext, ['xlsx', 'xls'])) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);
            } else {
                $content = file_get_contents($file->getPathname());
                $delimiter = strpos(substr($content, 0, 500), ';') !== false ? ';' : ',';
                $handle = fopen($file->getPathname(), "r");
                while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membaca file: ' . $e->getMessage()], 422);
        }

        if (empty($rows) || count($rows) < 2) {
            return response()->json(['success' => false, 'message' => 'File kosong atau hanya berisi baris header.'], 422);
        }

        $rawHeader = array_shift($rows);
        $header = [];
        foreach ($rawHeader as $h) {
            $hClean = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h);
            $header[] = strtolower(trim($hClean));
        }

        $importedCount = 0;
        $updatedCount = 0;
        $skippedRows = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $idx => $row) {
                $rowNumber = $idx + 2;
                if (empty(array_filter($row, fn($v) => !is_null($v) && trim((string)$v) !== ''))) {
                    continue;
                }

                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), null);
                }
                $data = array_combine($header, array_slice($row, 0, count($header)));

                $clean = function ($key, $default = null) use ($data) {
                    $val = isset($data[$key]) ? trim((string)$data[$key]) : null;
                    if ($val === '' || strtoupper($val) === 'NULL') {
                        return $default;
                    }
                    return $val;
                };

                $custIdentifier = $clean('customer_code') ?? $clean('customer') ?? $clean('code');
                $bgNumber = $clean('bg_number');
                $bankName = $clean('bank_name');
                $branchName = $clean('branch_name');
                $rawNominal = $clean('bg_nominal') ?? $clean('nominal', 0);
                $rawIssuedDate = $clean('issued_date');
                $rawExpDate = $clean('exp_date');
                $status = strtolower($clean('status', 'approved'));
                $contactPerson = $clean('contact_person');
                $bankAddress = $clean('bank_address');

                if (!$custIdentifier || !$bgNumber || !$bankName) {
                    $skippedRows[] = "Baris {$rowNumber}: Kolom customer_code, bg_number, atau bank_name kosong.";
                    continue;
                }

                $customer = Customer::where('code', $custIdentifier)
                    ->orWhere('no_pkd', $custIdentifier)
                    ->orWhere('name', 'like', "%{$custIdentifier}%")
                    ->first();

                if (!$customer && is_numeric($custIdentifier)) {
                    $customer = Customer::find((int)$custIdentifier);
                }

                if (!$customer) {
                    $skippedRows[] = "Baris {$rowNumber}: Customer '{$custIdentifier}' tidak ditemukan di database.";
                    continue;
                }

                $nominal = (float) preg_replace('/[^0-9]/', '', (string)$rawNominal);

                $parseDate = function ($dateVal) {
                    if (!$dateVal) return null;
                    if (is_numeric($dateVal) && (float)$dateVal > 20000 && (float)$dateVal < 70000) {
                        try {
                            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateVal)->format('Y-m-d');
                        } catch (\Exception $e) {}
                    }
                    try {
                        return \Carbon\Carbon::parse($dateVal)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                $issuedDate = $parseDate($rawIssuedDate) ?? now()->toDateString();
                $expDate = $parseDate($rawExpDate);

                $validStatus = in_array($status, ['approved', 'active', 'draft', 'expired']) 
                    ? ($status === 'active' ? 'approved' : $status) 
                    : 'approved';

                $existingBg = BankGaransi::where('bg_number', $bgNumber)->first();

                if ($existingBg) {
                    $existingBg->update([
                        'customer_id' => $customer->id,
                        'bg_nominal'  => $nominal,
                        'issued_date' => $issuedDate,
                        'exp_date'    => $expDate ?? $existingBg->exp_date,
                        'status'      => $validStatus,
                        'bg_type'     => 'new',
                    ]);

                    $detail = $existingBg->details()->first();
                    if ($detail) {
                        $detail->update([
                            'bank_name'      => $bankName,
                            'branch_name'    => $branchName ?? $detail->branch_name,
                            'nominal'        => $nominal,
                            'contact_person' => $contactPerson ?? $detail->contact_person,
                            'bank_address'   => $bankAddress ?? $detail->bank_address,
                        ]);
                    } else {
                        $existingBg->details()->create([
                            'bank_name'      => $bankName,
                            'branch_name'    => $branchName,
                            'nominal'        => $nominal,
                            'contact_person' => $contactPerson,
                            'bank_address'   => $bankAddress,
                        ]);
                    }
                    $updatedCount++;
                } else {
                    $newBg = BankGaransi::create([
                        'customer_id' => $customer->id,
                        'bg_number'   => $bgNumber,
                        'bg_type'     => 'new',
                        'bg_nominal'  => $nominal,
                        'base_bg_id'  => null,
                        'issued_date' => $issuedDate,
                        'exp_date'    => $expDate,
                        'status'      => $validStatus,
                        'created_by'  => auth()->id(),
                    ]);
                    $newBg->update(['base_bg_id' => $newBg->id]);

                    $newBg->details()->create([
                        'bank_name'      => $bankName,
                        'branch_name'    => $branchName,
                        'nominal'        => $nominal,
                        'contact_person' => $contactPerson,
                        'bank_address'   => $bankAddress,
                    ]);
                    $importedCount++;
                }

                if (strtoupper($customer->bank_garansi ?? '') !== 'YA') {
                    $customer->update(['bank_garansi' => 'YA']);
                }
            }

            activity()
                ->causedBy(auth()->user())
                ->log("Imported Master Bank Garansi: {$importedCount} created, {$updatedCount} updated.");

            DB::commit();

            $msg = "Import selesai! {$importedCount} BG baru berhasil ditambahkan";
            if ($updatedCount > 0) $msg .= ", {$updatedCount} BG diperbarui";
            if (!empty($skippedRows)) {
                $msg .= ". Catatan (" . count($skippedRows) . " baris dilewati): " . implode(' | ', array_slice($skippedRows, 0, 3));
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $importedCount,
                'updated'  => $updatedCount,
                'skipped'  => $skippedRows
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses import: ' . $e->getMessage()], 500);
        }
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

            // Tab filter: active vs expired
            if ($request->has('tab') && $request->tab === 'expired') {
                $query->where(function($q) {
                    $q->where('bank_garansi.status', 'expired')
                      ->orWhere('bank_garansi.exp_date', '<', now()->toDateString());
                });
            } else if (!$request->has('tab') || $request->tab === 'active') {
                if (!$request->has('status') || $request->status === 'all') {
                    $query->where(function($q) {
                        $q->where('bank_garansi.status', '!=', 'expired')
                          ->where(function($sub) {
                              $sub->whereNull('bank_garansi.exp_date')
                                  ->orWhere('bank_garansi.exp_date', '>=', now()->toDateString());
                          });
                    });
                }
            }

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
                    $btn = '<div class="action-btn-group">';
                    $btn .= '<button class="btn btn-info action-btn-hover btn-show" data-id="'.$row->id.'" data-tooltip="View Details">';
                    $btn .= '<i class="ph-bold ph-eye text-white"></i>';
                    $btn .= '</button>';
                    $btn .= '<button class="btn btn-warning text-white action-btn-hover btn-edit" data-id="'.$row->id.'" data-tooltip="Edit Data">';
                    $btn .= '<i class="ph-bold ph-pencil"></i>';
                    $btn .= '</button>';
                    $btn .= '<button class="btn btn-danger action-btn-hover btn-delete" data-id="'.$row->id.'" data-tooltip="Delete Data">';
                    $btn .= '<i class="ph-bold ph-trash"></i>';
                    $btn .= '</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'bg_type', 'status'])
                ->make(true);
        }

        $customers = Customer::select('id', 'name', 'code')
                        ->where('bank_garansi', 'YA')
                        ->orderBy('name')
                        ->get();

        $stats = [
            'total'    => BankGaransi::count(),
            'active'   => BankGaransi::where('status', '!=', 'expired')->where(function($q){ $q->whereNull('exp_date')->orWhere('exp_date', '>=', now()->toDateString()); })->count(),
            'expired'  => BankGaransi::where('status', 'expired')->orWhere('exp_date', '<', now()->toDateString())->count(),
            'draft'    => BankGaransi::where('status', 'draft')->count(),
            'expiring' => BankGaransi::where('exp_date', '<', now()->addMonth())->where('status', 'approved')->count(),
        ];

        return view('page.bg.bg_list.index', compact('customers', 'stats'));
    }

    public function show($id)
    {
        if (request()->ajax() || request()->wantsJson()) {
            $bg = BankGaransi::with(['details', 'customer', 'creator'])->findOrFail($id);
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
                    $custEmail = $bg->customer->email ?? null;
                    if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($custEmail)->queue(new BgExtensionMail($bg));
                    }
                }

                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($bg)
                    ->log('Generated New Bank Garansi');
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
                $custEmail = $bg->customer->email ?? null;
                if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($custEmail)->queue(new BgExistingMail($bg));
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
            $currentNominal = $bg->bg_nominal;

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

            $custEmail = $bg->customer->email ?? null;
            if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($custEmail)->queue(new BgExistingMail($bg, $rec));
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($bg)
                ->useLog('bg_transaction')
                ->event('trigger_existing')
                ->withProperties([
                    'bg_number' => $bg->bg_number,
                    'current_nominal' => $currentNominal,
                    'customer' => $bg->customer->name
                ])
                ->log("Admin started EXISTING process for BG {$bg->bg_number}");

            return response()->json(['success' => true, 'message' => 'BG type changed to EXISTING & update link sent!']);

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

            $custEmail = $customer->email ?? null;
            if ($custEmail && filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($custEmail)->queue(new BgExtensionMail($rec));
            }

            $parentBgNumber = '-';
            if($request->has('bg_id')) {
                $parentBg = BankGaransi::find($request->bg_id);
                $parentBgNumber = $parentBg ? $parentBg->bg_number : '-';
            }

            activity()
                ->causedBy(Auth::user())
                ->performedOn($rec)
                ->useLog('bg_transaction')
                ->event('trigger_extension')
                ->withProperties([
                    'customer' => $customer->name,
                    'parent_bg' => $parentBgNumber
                ])
                ->log("Admin started EXTENSION process for Customer {$customer->name}");

            return response()->json(['success' => true, 'message' => 'Extension request processed. New BG creation link sent!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
