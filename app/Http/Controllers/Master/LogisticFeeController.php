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
use App\Models\Master\LogisticFeeLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

        $oldFee = $record->logistic_fee;
        $newFee = $request->logistic_fee;

        $record->update(['status' => 'Pending', 'proposed_fee' => $newFee]);

        LogisticFeeLog::create([
            'distributor_customer_id' => $record->id,
            'old_fee'   => $oldFee,
            'new_fee'   => $newFee,
            'status'    => 'Requested',
            'action_by' => Auth::user()->nik,
            'notes'     => 'Pengajuan harga baru'
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
            $record->update(['status' => 'Approved', 'logistic_fee' => $newFee, 'route_to' => 'Selesai']);
            
            LogisticFeeLog::create([
                'distributor_customer_id' => $record->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Approved',
                'action_by' => 'System',
                'notes'     => 'Auto-approved (Approval path tidak ditemukan)'
            ]);

            return response()->json(['success' => true, 'message' => 'Path approval belum disetting, data otomatis disetujui.']);
        }

        return response()->json(['success' => true, 'message' => 'Pengajuan Logistic Fee berhasil dikirim ke Approver!']);
    }

    /**
     * Get specific resource for modal edit.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['logistic_fee' => 'required|numeric|min:0']);
        $record = DistributorCustomer::findOrFail($id);

        $oldFee = $record->logistic_fee;
        $newFee = $request->logistic_fee;

        if ($oldFee == $newFee) {
            return response()->json(['success' => true, 'message' => 'Tidak ada perubahan harga.']);
        }

        $record->update(['status' => 'Pending', 'proposed_fee' => $newFee]);

        // CATAT KE LOG HISTORY SEBAGAI "REQUESTED"
        LogisticFeeLog::create([
            'distributor_customer_id' => $record->id,
            'old_fee'   => $oldFee,
            'new_fee'   => $newFee,
            'status'    => 'Requested',
            'action_by' => Auth::user()->nik,
            'notes'     => 'Pengajuan perubahan harga'
        ]);

        $logs = $this->generateApprovalLogs(Auth::user(), $record->id, 'Customer', 'Logistic Fee');

        if ($logs->isNotEmpty()) {
            $firstApproverLog = ApprovalLog::where('related_id', $record->id)->where('category', 'Customer')->where('sub_category', 'Logistic Fee')->where('level', 1)->where('status', 'Pending')->latest()->first();
            if ($firstApproverLog) {
                $firstApproverUser = User::where('nik', $firstApproverLog->approver_nik)->first();
                $approverName = $firstApproverUser ? $firstApproverUser->name : 'Atasan';
                $record->update(['route_to' => $approverName]);
                dispatch(new SendLogisticFee($firstApproverLog, $record));

                return response()->json(['success' => true, 'message' => 'Perubahan harga berhasil dikirim ke <b>' . $approverName . '</b>']);
            }
        } else {
            // Auto Approve jika tidak ada rule
            $record->update(['status' => 'Approved', 'logistic_fee' => $newFee, 'route_to' => 'Selesai']);
            
            LogisticFeeLog::create([
                'distributor_customer_id' => $record->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Approved',
                'action_by' => 'System',
                'notes'     => 'Auto-approved (Approval path tidak ditemukan)'
            ]);

            return response()->json(['success' => true, 'message' => 'Path approval belum disetting, harga otomatis disetujui.']);
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

                LogisticFeeLog::create([
                    'distributor_customer_id' => $logisticData->id,
                    'old_fee' => $oldFee,
                    'new_fee' => $newFee,
                    'status' => 'Approved',
                    'action_by' => $log->approver_nik, // Diambil dari log (siapa yg approve)
                    'notes' => $notes
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

            $oldFee = $logisticData->logistic_fee;
            $newFee = $logisticData->proposed_fee;

            // UPDATE DATA REJECT: route_to diubah menjadi Selesai, jangan null
            $logisticData->update([
                'status'   => 'Rejected',
                'route_to' => 'Selesai (Ditolak)'
            ]);

            LogisticFeeLog::create([
                'distributor_customer_id' => $logisticData->id,
                'old_fee'   => $oldFee,
                'new_fee'   => $newFee,
                'status'    => 'Rejected',
                'action_by' => $log->approver_nik,
                'notes'     => $notes
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

    public function approvalList(Request $request)
    {
        if ($request->ajax()) {
            $data = ApprovalLog::where('category', 'Customer')
                               ->where('sub_category', 'Logistic Fee')
                               ->where('status', 'Pending')
                               ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) { return $row->created_at->format('d M Y H:i'); })
                ->addColumn('distributor', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    return $dc->distributor->name ?? '-';
                })
                ->addColumn('customer', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    return $dc->customer->name ?? '-';
                })
                ->addColumn('old_fee', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    return 'Rp ' . number_format($dc->logistic_fee ?? 0, 0, ',', '.');
                })
                ->addColumn('new_fee', function($row) {
                    $dc = DistributorCustomer::find($row->related_id);
                    return '<span class="text-primary fw-bold">Rp ' . number_format($dc->proposed_fee ?? 0, 0, ',', '.') . '</span>';
                })
                ->addColumn('action', function($row) {
                    $btn = '<button class="btn btn-sm btn-primary btn-detail me-1 shadow-sm" data-id="'.$row->id.'"><i class="ph-bold ph-eye"></i> Tinjau</button>';
                    $btn .= '<button class="btn btn-sm btn-outline-secondary btn-resend shadow-sm" data-id="'.$row->id.'"><i class="ph-bold ph-paper-plane-right"></i> Resend Email</button>';
                    return $btn;
                })
                ->rawColumns(['new_fee', 'action'])
                ->make(true);
        }
        return view('page.master.logistic-fee.approval');
    }

    public function approvalDetail($id)
    {
        $log = ApprovalLog::findOrFail($id);
        $data = DistributorCustomer::with(['distributor', 'customer'])->findOrFail($log->related_id);
        
        return response()->json([
            'log_id' => $log->id,
            'distributor' => $data->distributor->name ?? '-',
            'customer' => $data->customer->name ?? '-',
            'old_fee' => 'Rp ' . number_format($data->logistic_fee, 0, ',', '.'),
            'new_fee' => 'Rp ' . number_format($data->proposed_fee, 0, ',', '.'),
        ]);
    }

    public function systemProcessApproval(Request $request, $id)
    {
        // --- VALIDASI BACKEND ---
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes'  => 'required_if:action,reject|nullable|string'
        ], [
            'notes.required_if' => 'Catatan / Alasan wajib diisi apabila Anda menolak pengajuan.'
        ]);
        
        $log = ApprovalLog::findOrFail($id);
        $action = $request->action; // 'approve' atau 'reject'
        $logisticData = DistributorCustomer::find($log->related_id);

        try {
            DB::beginTransaction();

            if ($action === 'approve') {
                $log->update(['status' => 'Approved', 'token' => null, 'notes' => $request->notes]);

                $nextLevel = $log->level + 1;
                $nextLog = ApprovalLog::where('related_id', $log->related_id)->where('sub_category', 'Logistic Fee')->where('level', $nextLevel)->first();

                if ($nextLog) {
                    $nextApproverUser = User::where('nik', $nextLog->approver_nik)->first();
                    $logisticData->update(['route_to' => $nextApproverUser ? $nextApproverUser->name : 'Unknown Approver']);
                    dispatch(new SendLogisticFee($nextLog, $logisticData));
                    $message = 'Berhasil disetujui. Diteruskan ke: ' . ($nextApproverUser->name ?? '-');
                } else {
                    $oldFee = $logisticData->logistic_fee;
                    $newFee = $logisticData->proposed_fee;

                    $logisticData->update(['logistic_fee' => $newFee, 'status' => 'Approved', 'route_to' => 'Selesai']);

                    // Catat ke History Log
                    LogisticFeeLog::create([
                        'distributor_customer_id' => $logisticData->id,
                        'old_fee'   => $oldFee,
                        'new_fee'   => $newFee,
                        'status'    => 'Approved',
                        'action_by' => Auth::user()->nik,
                        'notes'     => $request->notes
                    ]);

                    // =========================================================
                    // TAMBAHAN: KIRIM EMAIL FINAL APPROVAL KE SEMUA PIHAK
                    // =========================================================
                    $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');
                    
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
                                    'oldFee' => $oldFee,
                                    'newFee' => $newFee,
                                    'notes' => $request->notes
                                ]
                            ));
                        } catch (\Exception $e) {
                            Log::error('Gagal kirim email Final Approval (System): ' . $e->getMessage());
                        }
                    }
                    // =========================================================

                    $message = 'Final Approval Berhasil! Harga telah diperbarui.';
                }
            } elseif ($action === 'reject') {
                $log->update(['status' => 'Rejected', 'token' => null, 'notes' => $request->notes]);
                
                $oldFee = $logisticData->logistic_fee;
                $newFee = $logisticData->proposed_fee;

                $logisticData->update(['status' => 'Rejected', 'route_to' => 'Selesai (Ditolak)']);

                // Catat ke History Log
                LogisticFeeLog::create([
                    'distributor_customer_id' => $logisticData->id,
                    'old_fee'   => $oldFee,
                    'new_fee'   => $newFee,
                    'status'    => 'Rejected',
                    'action_by' => Auth::user()->nik,
                    'notes'     => $request->notes
                ]);

                // =========================================================
                // TAMBAHAN: KIRIM EMAIL REJECTED KE REQUESTER
                // =========================================================
                $requesterEmail = User::where('nik', $logisticData->created_by)->value('email');

                if (!empty($requesterEmail)) {
                    try {
                        Mail::to($requesterEmail)->queue(new LogisticFeeMail(
                            'rejected',
                            $logisticData,
                            [
                                'notes' => $request->notes
                            ]
                        ));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim email Rejected (System): ' . $e->getMessage());
                    }
                }
                // =========================================================

                $message = 'Pengajuan berhasil ditolak.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function resendEmail($id)
    {
        $log = ApprovalLog::findOrFail($id);
        $logisticData = DistributorCustomer::find($log->related_id);
        
        if($log->status === 'Pending' && $logisticData) {
            dispatch(new SendLogisticFee($log, $logisticData));
            return response()->json(['success' => true, 'message' => 'Email Approval berhasil dikirim ulang ke Approver!']);
        }
        return response()->json(['success' => false, 'message' => 'Data tidak valid untuk dikirim email.'], 400);
    }


    // ==============================================================
    // 4. FITUR LOG HISTORY (HISTORI PERUBAHAN HARGA)
    // ==============================================================

    public function logList(Request $request)
    {
        if ($request->ajax()) {
            $data = LogisticFeeLog::with(['distributorCustomer.distributor', 'distributorCustomer.customer', 'user'])
                                  ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function($row) { return $row->created_at->format('d M Y H:i'); })
                ->addColumn('distributor', function($row) { return $row->distributorCustomer->distributor->name ?? '-'; })
                ->addColumn('customer', function($row) { return $row->distributorCustomer->customer->name ?? '-'; })
                ->addColumn('old_fee', function($row) { return 'Rp ' . number_format($row->old_fee, 0, ',', '.'); })
                ->addColumn('new_fee', function($row) { return '<span class="text-dark fw-bold">Rp ' . number_format($row->new_fee, 0, ',', '.') . '</span>'; })
                ->addColumn('status_badge', function($row) {
                    if($row->status == 'Requested') {
                        return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 shadow-none"><i class="ph-bold ph-paper-plane-tilt me-1"></i>Requested</span>';
                    } 
                    elseif ($row->status == 'Approved') {
                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 shadow-none"><i class="ph-bold ph-check-circle me-1"></i>Approved</span>';
                    } 
                    else {
                        return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 shadow-none"><i class="ph-bold ph-x-circle me-1"></i>Rejected</span>';
                    }
                })
                ->addColumn('action_by', function($row) { return $row->user->name ?? $row->action_by ?? 'System'; })
                ->rawColumns(['new_fee', 'status_badge'])
                ->make(true);
        }
        return view('page.master.logistic-fee.log');
    }
}
