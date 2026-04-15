<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\DistributorCustomer;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\ApprovalTrait;
use App\Mail\LogisticFeeMail;
use App\Jobs\SendLogisticFee;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\ApprovalLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LogisticFeeController extends Controller
{
    use ApprovalTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DistributorCustomer::with(['distributor', 'customer'])->select('distributor_customers.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('distributor_code', function($row) { return $row->distributor->code ?? '-'; })
                ->addColumn('distributor_name', function($row) { return $row->distributor->name ?? '-'; })
                ->addColumn('customer_code', function($row) { return $row->customer->customer_code ?? $row->customer->code ?? '-'; })
                ->addColumn('customer_name', function($row) { return $row->customer->name ?? '-'; })
                ->editColumn('logistic_fee', function($row) {
                    // Tampilkan badge kuning jika sedang pending
                    $fee = 'Rp ' . number_format($row->logistic_fee, 0, ',', '.');
                    if($row->status == 'Pending') {
                        return $fee . '<br><span class="badge bg-warning text-dark">Pending Update: Rp '.number_format($row->proposed_fee, 0, ',', '.').'</span>';
                    }
                    return $fee;
                })
                ->addColumn('action', function($row){
                    // Disable edit jika masih pending
                    if($row->status == 'Pending') {
                        return '<button class="btn btn-sm btn-secondary" disabled>Menunggu Approval</button>';
                    }
                    return '<button class="btn btn-sm btn-primary btn-edit" data-id="'.$row->id.'"><i class="ph-bold ph-pencil"></i> Edit</button>';
                })
                ->addColumn('route_to', function($row) {
                    if ($row->status == 'Pending') {
                        return '<span class="badge bg-info text-white"><i class="ph-bold ph-paper-plane-tilt"></i> ' . $row->route_to . '</span>';
                    } elseif ($row->status == 'Rejected') {
                        return '<span class="badge bg-danger text-white"><i class="ph-bold ph-x-circle"></i> Rejected</span>';
                    }
                    return '<span class="badge bg-success text-white"><i class="ph-bold ph-check-circle"></i> Selesai</span>';
                })
                ->rawColumns(['logistic_fee', 'route_to', 'action'])
                ->make(true);
        }

        $distributors = Distributor::orderBy('name', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();

        return view('page.master.logistic-fee.index', compact('distributors', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'customer_id'    => 'required|exists:customers,id',
            'logistic_fee'   => 'required|numeric|min:0',
        ]);

        // Cari data existing. Jika tidak ada, buat baru dengan logistic_fee = 0
        $record = DistributorCustomer::firstOrCreate(
            ['distributor_id' => $request->distributor_id, 'customer_id' => $request->customer_id],
            ['logistic_fee' => 0]
        );

        // Update status menjadi Pending dan isi proposed_fee
        $record->update([
            'status' => 'Pending',
            'proposed_fee' => $request->logistic_fee
        ]);

        // Generate Approval Logs
        $logs = $this->generateApprovalLogs(Auth::user(), $record->id, 'Customer', 'Logistic Fee');

        if ($logs->isNotEmpty()) {
            $firstApproverLog = ApprovalLog::where('related_id', $record->id)
                                        ->where('category', 'Customer')
                                        ->where('sub_category', 'Logistic Fee')
                                        ->where('level', 1)
                                        ->where('status', 'Pending')
                                        ->latest()
                                        ->first();

            if ($firstApproverLog) {
                $firstApproverUser = User::where('nik', $firstApproverLog->approver_nik)->first();
                $approverName = $firstApproverUser ? $firstApproverUser->name : 'Atasan';

                $record->update([
                    'route_to' => $approverName
                ]);

                dispatch(new SendLogisticFee($firstApproverLog, $record));

                // PERBAIKAN: Sertakan nama approver dalam response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan berhasil dikirim ke <b>' . $approverName . '</b>',
                    'approver' => $approverName
                ]);
            }
        } else {
            // Jika gagal generate (misal path belum disetting), kembalikan status
            $record->update(['status' => 'Approved', 'logistic_fee' => $request->logistic_fee, 'proposed_fee' => null]);
            return response()->json(['success' => true, 'message' => 'Path approval belum disetting, data langsung tersimpan.']);
        }

        return response()->json(['success' => true, 'message' => 'Pengajuan Logistic Fee berhasil dikirim ke Approver!']);
    }

    /**
     * Get specific resource for modal edit.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'logistic_fee' => 'required|numeric|min:0',
        ]);

        $record = DistributorCustomer::findOrFail($id);

        // Update data ke status Pending
        $record->update([
            'status' => 'Pending',
            'proposed_fee' => $request->logistic_fee
        ]);

        // Generate logs approval baru
        $logs = $this->generateApprovalLogs(Auth::user(), $record->id, 'Customer', 'Logistic Fee');

        if ($logs->isNotEmpty()) {
            $firstApproverLog = ApprovalLog::where('related_id', $record->id)
                                        ->where('category', 'Customer')
                                        ->where('sub_category', 'Logistic Fee')
                                        ->where('level', 1)
                                        ->where('status', 'Pending')
                                        ->latest()
                                        ->first();

            if ($firstApproverLog) {
                $firstApproverUser = User::where('nik', $firstApproverLog->approver_nik)->first();
                $approverName = $firstApproverUser ? $firstApproverUser->name : 'Atasan';

                $record->update([
                    'route_to' => $approverName
                ]);

                dispatch(new SendLogisticFee($firstApproverLog, $record));

                // PERBAIKAN: Kembalikan nama approver agar bisa muncul di dialog sukses
                return response()->json([
                    'success' => true,
                    'message' => 'Perubahan harga berhasil dikirim ke <b>' . $approverName . '</b>',
                    'approver' => $approverName
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Perubahan harga berhasil disimpan.']);
    }

    public function show($id)
    {
        $logisticFee = DistributorCustomer::with(['distributor', 'customer'])->findOrFail($id);

        return response()->json([
            'id' => $logisticFee->id,
            'distributor_info' => ($logisticFee->distributor->code ?? '-') . ' - ' . ($logisticFee->distributor->name ?? '-'),
            'customer_info' => ($logisticFee->customer->customer_code ?? $logisticFee->customer->code ?? '-') . ' - ' . ($logisticFee->customer->name ?? '-'),
            // Gunakan proposed fee jika ada, jika tidak gunakan logistic fee asli
            'logistic_fee' => $logisticFee->status === 'Pending' ? $logisticFee->proposed_fee : $logisticFee->logistic_fee,
        ]);
    }

    // === TAMBAHKAN METHOD INI ===
    /**
     * Menampilkan form untuk Approve with Review atau Reject
     */
    public function showApprovalForm($token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.master.logistic-fee.links.invalid-token');
        }

        $logisticData = DistributorCustomer::with(['distributor', 'customer'])->find($log->related_id);

        if (!$logisticData) {
            return response('Data Logistic Fee tidak ditemukan.', 404);
        }

        // Sesuaikan path view dengan lokasi file blade yang kamu buat
        return view('page.master.logistic-fee.links.form-approval', compact('log', 'logisticData', 'action'));
    }

    // === UBAH PARAMETER METHOD INI ===
    /**
     * Memproses aksi Approve/Reject (Dari Email & Form)
     */
    public function processApproval(Request $request, $token, $action)
    {
        $log = ApprovalLog::where('token', $token)
                          ->where('status', 'Pending')
                          ->first();

        if (!$log) {
            return view('page.master.logistic-fee.links.invalid-token');
        }

        $logisticData = DistributorCustomer::find($log->related_id);

        if (!$logisticData) {
            return response('Data Logistic Fee tidak ditemukan.', 404);
        }

        $notes = $request->input('notes');
        $isApprove = in_array($action, ['approve', 'approve_with_review']);

        if ($isApprove) {
            // 1. Update log saat ini menjadi Approved
            $log->update([
                'status' => 'Approved',
                'token' => null,
                'notes' => $notes
            ]);

            // 2. Cek apakah ada approver level selanjutnya
            $nextLevel = $log->level + 1;
            $nextLog = ApprovalLog::where('related_id', $log->related_id)
                                  ->where('category', 'Customer')
                                  ->where('sub_category', 'Logistic Fee')
                                  ->where('level', $nextLevel)
                                  ->first();

            if ($nextLog) {
                // JIKA ADA NEXT APPROVER
                $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();

                $logisticData->update([
                    'route_to' => $nextApproverUser ? $nextApproverUser->name : 'Unknown Approver'
                ]);

                // Dispatch Job Email (Berjalan di background)
                dispatch(new SendLogisticFee($nextLog, $logisticData));

                return view('page.master.logistic-fee.links.success-approval')
                    ->with('successMessage', 'Berhasil Disetujui. Pengajuan telah diteruskan ke Approver selanjutnya: ' . ($nextApproverUser->name ?? 'Unknown'));
            } else {
                // JIKA FINAL APPROVER (Selesai)

                // 1. Tangkap harga lama sebelum di-update untuk ditampilkan di email
                $oldFee = $logisticData->logistic_fee;
                $newFee = $logisticData->proposed_fee;

                // 2. Update Data (proposed_fee dan route_to TIDAK NULL)
                $logisticData->update([
                    'logistic_fee' => $newFee,
                    'proposed_fee' => $newFee,   // Tetap simpan history harga yang diajukan
                    'status'       => 'Approved',
                    'route_to'     => 'Selesai'  // Menandakan tidak ada approval lagi
                ]);

                // Ambil Email Requester
                $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

                // Ambil Email Para Approver
                $approverEmails = ApprovalLog::where('related_id', $logisticData->id)
                                    ->where('category', 'Customer')
                                    ->where('sub_category', 'Logistic Fee')
                                    ->get()
                                    ->map(function($item) {
                                        return User::where('nik', $item->approver_nik)->value('email');
                                    })
                                    ->filter()
                                    ->toArray();

                $allRecipients = array_filter(array_unique(array_merge([$requesterEmail], $approverEmails)));

                if (!empty($allRecipients)) {
                    try {
                        Mail::to($allRecipients)->queue(new LogisticFeeMail(
                            'completed',
                            $logisticData,
                            [
                                'oldFee' => $oldFee, // Kirim harga lama ke email
                                'newFee' => $newFee,
                                'notes' => $notes
                            ]
                        ));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim email Final Approval: ' . $e->getMessage());
                    }
                }

                return view('page.master.logistic-fee.links.success-approval')
                    ->with('successMessage', 'Final Approval Berhasil! Harga Logistic Fee telah diperbarui ke sistem.');
            }

        } elseif ($action === 'reject') {
            // 1. Update log dan data menjadi Rejected
            $log->update([
                'status' => 'Rejected',
                'token' => null,
                'notes' => $notes
            ]);

            // UPDATE DATA REJECT: route_to diubah menjadi Selesai, jangan null
            $logisticData->update([
                'status'   => 'Rejected',
                'route_to' => 'Selesai (Ditolak)'
            ]);

            $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

            if (!empty($requesterEmail)) {
                try {
                    Mail::to($requesterEmail)->queue(new LogisticFeeMail(
                        'rejected',
                        $logisticData,
                        [
                            'notes' => $notes
                        ]
                    ));
                } catch (\Exception $e) {
                    Log::error('Gagal kirim email Rejected: ' . $e->getMessage());
                }
            }

            return view('page.master.logistic-fee.links.success-approval')
                ->with('successMessage', 'Pengajuan perubahan harga telah berhasil Anda tolak dan notifikasi telah dikirim ke pemohon.');
        }

        abort(404);
    }
}
