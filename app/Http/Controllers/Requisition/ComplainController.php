<?php

namespace App\Http\Controllers\Requisition;

use App\Http\Controllers\Controller;
use App\Http\Requests\paymentProofRequest;
use App\Http\Requests\StoreComplainRequest;
use App\Jobs\sendComplain;
use App\Jobs\sendMailComplain;
use App\Jobs\sendPaymentProofer;
use App\Jobs\sendPrintBatchMail;
use App\Jobs\sendRejectionNotification;
use App\Jobs\sendWarehouseCompletion;
use App\Models\Master\Customer;
use App\Models\Master\ItemMaster;
use App\Models\Requisition\ApprovalLog;
use App\Models\Requisition\ApprovalPath;
use App\Models\Requisition\ComplainImage;
use App\Models\Requisition\Payment;
use App\Models\Requisition\Requisition;
use App\Models\Requisition\RequisitionItem;
use App\Models\Requisition\Tracking;
use App\Models\User;
use App\Notifications\RequisitionNotification;
use App\Traits\approvalTrait;
use App\Traits\traitRequisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Master\Revision;
use function Pest\Laravel\json;

class ComplainController extends Controller
{
    use traitRequisition;

    /**
     * Helper method to format datetime to Indonesian timezone
     */
    private function formatToIndonesianTime($datetime, $format = 'd M Y, H:i:s')
    {
        if (!$datetime) {
            return 'Unknown';
        }

        return Carbon::parse($datetime)->setTimezone('Asia/Jakarta')->format($format);
    }

    /**
     * Generate warehouse tracking berdasarkan print_batch
     */
    private function generateWarehouseTracking($requisitionId, $printBatch)
    {
        if ($printBatch === true) {
            // Jika print_batch true: WH Supervisor First -> Material Supervisor -> WH Supervisor Final

            // 1. WH Supervisor First
            Tracking::create([
                'requisition_id' => $requisitionId,
                'current_position' => 'WH Supervisor First',
                'last_updated' => null,
                'notes' => null,
                'token' => bin2hex(random_bytes(16)),
            ]);

            // 2. Material Supervisor
            Tracking::create([
                'requisition_id' => $requisitionId,
                'current_position' => 'Material Supervisor',
                'last_updated' => null,
                'notes' => null,
                'token' => bin2hex(random_bytes(16)),
            ]);

            // 3. WH Supervisor Final
            Tracking::create([
                'requisition_id' => $requisitionId,
                'current_position' => 'WH Supervisor Final',
                'last_updated' => null,
                'notes' => null,
                'token' => bin2hex(random_bytes(16)),
            ]);

            Log::info("Generated warehouse tracking for requisition {$requisitionId}", [
                'print_batch' => true,
                'tracking_count' => 3,
                'sequence' => 'WH Supervisor First -> Material Supervisor -> WH Supervisor Final'
            ]);
        } else {
            // Jika print_batch false: hanya WH Supervisor Final
            Tracking::create([
                'requisition_id' => $requisitionId,
                'current_position' => 'WH Supervisor Final',
                'last_updated' => null,
                'notes' => 'Waiting for WH Supervisor final approval',
                'token' => bin2hex(random_bytes(16)),
            ]);

            Log::info("Generated warehouse tracking for requisition {$requisitionId}", [
                'print_batch' => false,
                'tracking_count' => 1,
                'sequence' => 'WH Supervisor Final only'
            ]);
        }

        return true;
    }

    public function index()
    {
        $user = Auth::user();
        if(!$user->can('view requisition-form')){
            abort(403);
        }
        return view('page.complain.index');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function() use ($id){
                $data = Requisition::where('id', $id)->first();
                if($data){
                    $data->delete();
                }
            });
        }catch(\Exception $e){
            return response()->json(['message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreComplainRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User belum login.'], 401);
        }

        // Debug log untuk print_batch dengan null safety
        Log::info('validasi dari print_batch', [
            'value' => $validated['print_batch'] ?? 'null',
            'type' => gettype($validated['print_batch'] ?? null),
            'boolean_conversion' => isset($validated['print_batch']) ? (bool) $validated['print_batch'] : false
        ]);

        try{
            $approvalLogs = [];

            DB::transaction(function () use ($validated, $user, &$approvalLogs) {

            $requisition = Requisition::create([
                'requester_nik' => $user->nik,
                'customer_id' => $validated['customer_id'],
                'no_srs' => str_replace(' ', '', $validated['rs_number']),
                'account' => $validated['account'],
                'cost_center' => $validated['cost_center'],
                'request_date' => $validated['date'],
                'category' => 'Complain',
                'status' => 'Pending',
                'reason_for_replacement' => $validated['objectives'] ?? null,
                'route_to' => null,
                'print_batch' => isset($validated['print_batch']) ? (bool) $validated['print_batch'] : false,
            ]);

            // Generate approval logs menggunakan trait approvalTrait
            $generatedLogs = $this->generateApprovalLogs($user, $requisition->id, 'Complain');

            if ($generatedLogs->isEmpty()) {
                throw new \Exception('Tidak ada approval path yang ditemukan untuk kategori Complain.');
            }

            // Generate additional warehouse tracking berdasarkan print_batch
            $this->generateWarehouseTracking($requisition->id, $requisition->print_batch);

            // Convert generated logs ke format yang dibutuhkan untuk job dispatch
            foreach ($generatedLogs as $logData) {
                $approver = User::where('nik', $logData['approver_nik'])->first();
                if ($approver) {
                    $approvalLog = ApprovalLog::where('requisition_id', $requisition->id)
                        ->where('approver_nik', $logData['approver_nik'])
                        ->where('level', $logData['level'])
                        ->first();

                    if ($approvalLog) {
                        $approvalLogs[] = [
                            'approval_log' => $approvalLog,
                            'approver' => $approver,
                            'requisition' => $requisition
                        ];
                    }
                }
            }

            // Set route_to ke approver pertama (level 1)
            $firstApprover = $approvalLogs[0]['approver'] ?? null;
            if ($firstApprover) {
                $requisition->route_to = $firstApprover->name;
                $requisition->save();
            }

            $requisitionitems = [];
            $now = Carbon::now();

            foreach ($validated['items'] as $itemMasterId => $masterData) {
                foreach ($masterData['details'] as $itemDetailId => $detailData) {
                    $requisitionitems[] = [
                        'requisition_id'    => $requisition->id,
                        'item_master_id'    => $itemMasterId,
                        'item_detail_id'    => $itemDetailId,
                        'quantity_required' => $detailData['qty_required'] ?? 0,
                        'quantity_issued'   => $detailData['qty_issued'] ?? 0,
                        'batch_number'      => !empty($detailData['batch_number']) ? $detailData['batch_number'] : null,
                        'remarks'           => !empty($detailData['remarks']) ? $detailData['remarks'] : null,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }

            if (!empty($requisitionitems)) {
                RequisitionItem::insert($requisitionitems);
            } else {
                throw new \Exception('Tidak ada item yang valid untuk disimpan.');
            }

            // Handle image upload if any
            if (isset($validated['complain_images']) && is_array($validated['complain_images'])) {
                foreach ($validated['complain_images'] as $image) {
                    // Generate unique filename
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    // Store image in storage/app/public/complain_images
                    $imagePath = $image->storeAs('complain_images', $fileName, 'public');

                    // Save to database
                    ComplainImage::create([
                        'requisition_id' => $requisition->id,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            $casuer = User::where('nik', $user->nik)->first();

            activity()
                ->inLog('complain')
                ->causedBy($casuer)
                ->performedOn($requisition, $requisitionitems)
                ->event('created requisition complain')
                ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent(), 'requisition_no' => $requisition->no_srs, ])
                ->log('user ' . $casuer->name . ' Membuat Requisition Complain dengan ID: ' . $requisition->id . ' dan No SRS: ' . $requisition->no_srs);
            });

            // Dispatch email to the first approver (head QA)
            $firstApprover = $approvalLogs[0];
            sendMailComplain::dispatch(
                $firstApprover['approver'],
                $firstApprover['requisition'],
                $firstApprover['approval_log']
            );

            // Kirim notifikasi ke approver
            $approveWithReviewLink = route('complain.approval');

            $notificationData = [
                'requisition_id' => $firstApprover['requisition']->id,
                'srs_number' => $firstApprover['requisition']->no_srs,
                'message' => "Requisition {$firstApprover['requisition']->no_srs} menunggu approval Anda",
                'url' => $approveWithReviewLink
            ];

            $causer = User::where('nik', $firstApprover['requisition']->requester_nik)->first();
            if ($causer) {
                $firstApprover['approver']->notify(new RequisitionNotification($notificationData, $causer));
            }

            return response()->json(['message' => 'Form Requisition complain berhasil dibuat.'], 201);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            $statusCode = 500;
            if (str_contains($errorMessage, 'tidak ditemukan') ||
                str_contains($errorMessage, 'not found') ||
                str_contains($errorMessage, 'kosong')) {
                $statusCode = 400;
            }
            return response()->json(['message' => $errorMessage], $statusCode);
        }
    }

    public function getData(Request $request)
    {
        $draw = $request->input('draw');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir', 'asc');

        // Dapatkan nama kolom untuk sorting dari request berdasarkan indexnya
        $orderColumnName = $request->input("columns.{$orderColumnIndex}.name");

        // Hitung total data tanpa filter apa pun
        $totalData = Requisition::count();

        // Mulai query builder
        $query = Requisition::query();

        // 2. Terapkan filter pencarian jika ada input dari kotak search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('requester', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhereHas('customer', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%{$searchValue}%");
                })
                ->orWhere('cost_center', 'like', "%{$searchValue}%")
                ->orWhere('category', 'like', "%{$searchValue}%")
                ->orWhere('route_to', 'like', "%{$searchValue}%")
                ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }

        // Terapkan filter kategori khusus Complain sebelum menghitung filtered total
        $query->where('category', 'Complain');

        // Terapkan filter status jika diberikan (mis. 'Pending', 'Approved', atau 'all')
        $statusFilter = $request->input('status');
        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all') {
            // Bandingkan case-insensitive
            $query->whereRaw('LOWER(status) = ?', [strtolower($statusFilter)]);
        }

        $totalFiltered = $query->count();

        if (!empty($orderColumnName)) {
            $query->orderBy($orderColumnName, $orderDirection);
        }

        $data = $query->with(['customer', 'revision', 'requester', 'approvalLogs'])
            ->offset($start)
            ->limit($length)
            ->get();

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];

        return response()->json($response);
    }

    public function getCustomerList()
    {
        $customers = Customer::select('id', 'name', 'address')->distinct()->get();
        return response()->json($customers);
    }

    public function getSerial()
    {
        $prefix = 'S';
        $now = Carbon::now();

        $yearMonthPart = $now->format('y m');

        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        $lastRecordThisYear = Requisition::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->latest('id')
            ->first();

        $sequence = 1;

        if ($lastRecordThisYear) {
            $lastSeriesNumber = $lastRecordThisYear->no_srs;

            $lastSequence = (int) substr($lastSeriesNumber, -4);

            $sequence = $lastSequence + 1;
        }

        $sequencePadded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $seriesNumber = sprintf('%s %s %s', $prefix, $yearMonthPart, $sequencePadded);

        $accountNumber = 4914;

        return response()->json(['series_number' => $seriesNumber, 'account_number' => $accountNumber]);
    }

    public function getProductList(Request $request)
    {
        $items = ItemMaster::with('ItemDetails')->get();
        return response()->json(['items' => $items]);
    }

    public function getFormDetail($id)
    {
        try {
            $complain = Requisition::with([
                'customer',
                'requester',
                'requisitionItems.itemMaster.ItemDetails',
                'approvalLogs.approver',
                'payments',
                'complainImages',
                'trackings' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }
            ])->findOrFail($id);

            // Build history array similar to SampleController
            $history = [];

            // 1. Kejadian: Pembuatan Requisition
            $history[] = [
                'type' => 'created',
                'timestamp' => $complain->created_at,
                'title' => 'Requisition Created',
                'description' => 'Requisition complain was created by ' . ($complain->requester->name ?? 'Unknown'),
                'icon' => 'ph-file-plus',
                'color' => 'primary'
            ];

            // 2. Kejadian: Approval & Rejection
            foreach ($complain->approvalLogs as $log) {
                if ($log->status === 'Approved') {
                    $history[] = [
                        'type' => 'approved',
                        'timestamp' => $log->approved_at ?? $log->updated_at,
                        'title' => 'Approved by ' . ($log->approver->name ?? 'Unknown'),
                        'description' => 'Level ' . $log->level . ' approval completed' .
                                       ($log->notes ? '. Notes: ' . $log->notes : ''),
                        'icon' => 'ph-check-circle',
                        'color' => 'success'
                    ];
                } elseif ($log->status === 'Rejected') {
                    if($log->approver->hasRole('head-QA')){
                        $history[] = [
                            'type' => 'rejected_payment_proof',
                            'timestamp' => $log->approved_at ?? $log->updated_at,
                            'title' => 'Rejected by ' . ($log->approver->name ?? 'Unknown'),
                            'description' => 'Level ' . $log->level . ' approval rejected, payment proof required' . ($log->notes ? '. Reason: ' . $log->notes : ''),
                            'icon' => 'ph-x-circle',
                            'color' => 'danger'
                        ];
                    }else {
                        $history[] = [
                            'type' => 'rejected',
                            'timestamp' => $log->approved_at ?? $log->updated_at,
                            'title' => 'Rejected by ' . ($log->approver->name ?? 'Unknown'),
                            'description' => 'Level ' . $log->level . ' approval rejected' . ($log->notes ? '. Reason: ' . $log->notes : ''),
                            'icon' => 'ph-x-circle',
                            'color' => 'danger'
                        ];
                    }
                }
            }

            // 3. Kejadian: Proses Warehouse & Tracking
            foreach ($complain->trackings as $tracking) {
                if ($tracking->token === null) { // Sudah diproses (token null berarti sudah approve)
                    $history[] = [
                        'type' => 'warehouse_processed',
                        'timestamp' => $tracking->last_updated ?? $tracking->updated_at,
                        'title' => $tracking->current_position . ' Completed',
                        'description' => 'Warehouse process completed' .
                                       ($tracking->notes ? '. Notes: ' . $tracking->notes : ''),
                        'icon' => 'ph-package',
                        'color' => 'info'
                    ];
                }
            }

            // 4. Kejadian: Pembatalan
            if ($complain->status === 'Cancelled') {
                $history[] = [
                    'type' => 'cancelled',
                    'timestamp' => $complain->updated_at,
                    'title' => 'Requisition Cancelled',
                    'description' => 'Requisition was cancelled',
                    'icon' => 'ph-x-circle',
                    'color' => 'danger'
                ];
            }

            // Sort history by timestamp
            usort($history, function($a, $b) {
                return $a['timestamp'] <=> $b['timestamp'];
            });

            // Add history to response
            $responseData = $complain->toArray();
            $responseData['history'] = $history;

            return response()->json($responseData);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Complain data not found.'], 404);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'An error occurred on the server.'], 500);
        }
    }

    /**
     * Method untuk handle approval process (akan dipanggil dari approval controller)
     */
    public function processApproval(Request $request)
    {
        // Check if token is already used/expired first
        $tokenStatus = $this->checkTokenStatus($request);
        if ($tokenStatus !== null) {
            return $tokenStatus;
        }

        if ($request->isMethod('post')) {
            return $this->processApprovalWithValidation($request);
        } else {
            return $this->processDirectApproval($request);
        }
    }

    /**
     * Check if token is already used or expired
     */
    private function checkTokenStatus(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        $id = $request->query('id') ?? $request->input('id');

        // Jika ID atau token tidak ada dalam request
        if (!$token || !$id) {
            return view('page.complain.links.approval-invalid', [
                'message' => 'The approval link is missing required parameters.',
                'errorType' => 'missing_params'
            ]);
        }

        // Cek apakah requisition ID ada terlebih dahulu
        $requisitionExists = ApprovalLog::where('requisition_id', $id)->exists();

        // Jika requisition ID tidak ditemukan sama sekali
        if (!$requisitionExists) {
            return view('page.complain.links.approval-invalid', [
                'message' => 'Invalid approval link - requisition not found.',
                'errorType' => 'invalid_link'
            ]);
        }

        // Jika requisition ID ada, cek apakah token masih valid
        $approvalLog = ApprovalLog::where('requisition_id', $id)
            ->where('token', $token)
            ->first();

        // Jika token tidak ditemukan (sudah null/digunakan), berarti link expired
        if (!$approvalLog) {
            // Cari approval log berdasarkan requisition_id saja untuk mendapatkan updated_at terakhir
            $lastApprovalLog = ApprovalLog::where('requisition_id', $id)
                ->orderBy('updated_at', 'desc')
                ->first();

            $lastActionDate = $lastApprovalLog ? $this->formatToIndonesianTime($lastApprovalLog->updated_at) : 'Unknown';

            return view('page.complain.links.approval-expired', [
                'message' => 'This approval link has already been used and is no longer valid.',
                'errorType' => 'token_expired',
                'lastActionDate' => $lastActionDate,
                'requisition' => Requisition::with('customer')->find($id),
                'approvalLog' => $lastApprovalLog
            ]);
        }

        // Jika approval log ditemukan dan masih pending, lanjut ke proses normal
        if ($approvalLog->status === 'Pending') {
            return null;
        }

        // Jika approval log ditemukan tapi sudah diproses (bukan Pending)
        $requisition = Requisition::with('customer')->find($id);

        return view('page.complain.links.approval-expired', compact('requisition', 'approvalLog'));
    }

    /**
     * Process approval with validation (from review form)
     */
    private function processApprovalWithValidation(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'id' => 'required|integer',
            'status' => 'required|in:approve,approve_with_review,reject',
            'notes' => $request->input('status') === 'reject' || $request->input('status') === 'approve_with_review' ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'notes.required' => 'Notes/reason is required for rejection and approve with review.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ]);

        try {
            $token = $request->input('token');
            $id = $request->input('id');
            $status = $request->input('status');
            $notes = $request->input('notes');

            $requisition = null;

            DB::transaction(function () use ($token, $id, $status, $notes, &$requisition) {
                $approvalLog = ApprovalLog::where('requisition_id', $id)
                    ->where('token', $token)
                    ->where('status', 'Pending')
                    ->first();

                if (!$approvalLog) {
                    throw new \Exception('Invalid or expired approval link.');
                }

                // Update status approval log
                if ($status === 'approve' || $status === 'approve_with_review') {
                    $approvalLog->status = 'Approved';
                } else {
                    $approvalLog->status = 'Rejected';
                }
                $approvalLog->notes = $notes;
                $approvalLog->token = null;
                $approvalLog->save();

                $requisition = Requisition::with('customer')->find($approvalLog->requisition_id);

                // Jika diapprove (termasuk approve_with_review), cek apakah ada level berikutnya
                if ($status === 'approve' || $status === 'approve_with_review') {
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    // simpan perubahan status requisition karna diapprove
                    $requisition->status = 'In Progress';
                    $requisition->save();

                    // Kirim notifikasi approval ke requester
                    $approver = User::where('nik', $approvalLog->approver_nik)->first();
                    $requester = User::where('nik', $requisition->requester_nik)->first();
                    if ($approver && $requester) {
                        $notificationData = [
                            'requisition_id' => $requisition->id,
                            'srs_number' => $requisition->no_srs,
                            'message' => "Requisition {$requisition->no_srs} telah di-approve oleh {$approver->name}",
                            'url' => route('complain-form.index')
                        ];
                        $requester->notify(new RequisitionNotification($notificationData, $approver));
                    }

                    $this->mailOtherLevel($approvalLog->requisition_id, $approvalLog->level, $requisition->print_batch);
                } else {
                    // Jika direject, langsung set status requisition ke Rejected
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    $requisition->status = 'Rejected';
                    $requisition->save();

                    $rejectedBy = User::where('nik', $approvalLog->approver_nik)->first();
                    $requester = User::where('nik', $requisition->requester_nik)->first();

                    if ($approvalLog->approver->hasRole('head-QA')) {
                        $requisition->status = 'payment proof';
                        $requisition->save();
                        sendPaymentProofer::dispatch($requisition);

                        // Kirim notifikasi payment proof required ke requester
                        if ($rejectedBy && $requester) {
                            $notificationData = [
                                'requisition_id' => $requisition->id,
                                'srs_number' => $requisition->no_srs,
                                'message' => "Requisition {$requisition->no_srs} memerlukan bukti pembayaran untuk proses ulang",
                                'url' => route('complain-form.index')
                            ];
                            $requester->notify(new RequisitionNotification($notificationData, $rejectedBy));
                        }
                    } else {
                        // token approval log setelah user melakukan reject jadi null
                        $getApproverAfters = ApprovalLog::where('requisition_id', $requisition->id)
                            ->where('level', '>', $approvalLog->level)
                            ->where('status', 'Pending')
                            ->get();

                        foreach ($getApproverAfters as $approverAfter) {
                            $approverAfter->token = null;
                            $approverAfter->status = 'Rejected';
                            $approverAfter->save();
                        }

                        // Send rejection notification to requester
                        if ($rejectedBy) {
                            sendRejectionNotification::dispatch(
                                $requisition,
                                $rejectedBy,
                                $notes,
                                'approval',
                                now()
                            );

                            // Kirim notifikasi rejection ke requester
                            if ($requester) {
                                $notificationData = [
                                    'requisition_id' => $requisition->id,
                                    'srs_number' => $requisition->no_srs,
                                    'message' => "Requisition {$requisition->no_srs} telah di-reject oleh {$rejectedBy->name}",
                                    'url' => route('complain-form.index')
                                ];
                                $requester->notify(new RequisitionNotification($notificationData, $rejectedBy));
                            }
                        }
                    }
                }

                activity()
                    ->inLog('complain')
                    ->causedBy(User::where('nik', $approvalLog->approver_nik)->first())
                    ->performedOn($approvalLog)
                    ->event('processed approval')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent(), 'requisition_no' => $requisition->no_srs])
                    ->log('User ' . ($approvalLog->approver->name ?? 'Unknown') . ' has ' . $approvalLog->status . ' requisition ID: ' . $approvalLog->requisition_id . ' No: '. $requisition->no_srs . ' with notes: ' . ($notes ?? 'No notes provided'));
            });

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your decision has been recorded successfully.',
                    'status' => $status,
                    'requisition_id' => $requisition->id ?? null
                ]);
            }

            // Untuk non-AJAX approval with validation, tampilkan halaman hasil
            return view('page.complain.links.approval-result', compact('requisition', 'status'));

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Check if it's an AJAX request for error handling
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => 'approval_failed'
                ], 400);
            }

            // Untuk non-AJAX error, redirect ke halaman error dengan pesan
            if (str_contains($errorMessage, 'Invalid approval link') || str_contains($errorMessage, 'expired')) {
                return view('page.complain.links.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'token_expired'
                ]);
            } elseif (str_contains($errorMessage, 'not found')) {
                return view('page.complain.links.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'not_found'
                ]);
            }

            return view('page.complain.links.approval-invalid', [
                'message' => 'Terjadi kesalahan saat memproses approval.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Process direct approval (from email links)
     */
    private function processDirectApproval(Request $request)
    {
        try {
            $token = $request->query('token');
            $id = $request->query('id');
            $status = $request->query('status');

            if (!$token || !$id || !in_array($status, ['approve', 'reject'])) {
                return view('page.complain.links.approval-invalid', [
                    'message' => 'Invalid approval link - missing parameters.',
                    'errorType' => 'missing_params'
                ]);
            }

            $requisition = null;

            DB::transaction(function () use ($token, $id, $status, &$requisition) {
                $approvalLog = ApprovalLog::where('requisition_id', $id)
                    ->where('token', $token)
                    ->where('status', 'Pending')
                    ->first();

                if (!$approvalLog) {
                    throw new \Exception('Invalid or expired approval link.');
                }

                // Update status approval log
                $approvalLog->status = ($status === 'approve') ? 'Approved' : 'Rejected';
                $approvalLog->notes = null;
                $approvalLog->token = null;
                $approvalLog->save();

                $requisition = Requisition::with('customer')->find($approvalLog->requisition_id);

                // Jika diapprove, cek apakah ada level berikutnya
                if ($status === 'approve') {
                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    // simpan perubahan status requisition karna diapprove
                    $requisition->status = 'In Progress';
                    $requisition->save();

                    // Kirim notifikasi approval ke requester
                    $approver = User::where('nik', $approvalLog->approver_nik)->first();
                    $requester = User::where('nik', $requisition->requester_nik)->first();
                    if ($approver && $requester) {
                        $notificationData = [
                            'requisition_id' => $requisition->id,
                            'srs_number' => $requisition->no_srs,
                            'message' => "Requisition {$requisition->no_srs} telah di-approve oleh {$approver->name}",
                            'url' => route('complain-form.index')
                        ];
                        $requester->notify(new RequisitionNotification($notificationData, $approver));
                    }

                    $this->mailOtherLevel($approvalLog->requisition_id, $approvalLog->level, $requisition->print_batch);
                } else {

                    if (!$requisition) {
                        throw new \Exception('Requisition not found.');
                    }

                    // Jika direject, langsung set status requisition ke Rejected
                    $requisition->status = 'Rejected';
                    $requisition->save();

                    $rejectedBy = User::where('nik', $approvalLog->approver_nik)->first();
                    $requester = User::where('nik', $requisition->requester_nik)->first();

                    if ($approvalLog->approver->hasRole('head-QA')) {
                        $requisition->status = 'payment proof';
                        $requisition->save();
                        sendPaymentProofer::dispatch($requisition);

                        // Kirim notifikasi payment proof required ke requester
                        if ($rejectedBy && $requester) {
                            $notificationData = [
                                'requisition_id' => $requisition->id,
                                'srs_number' => $requisition->no_srs,
                                'message' => "Requisition {$requisition->no_srs} memerlukan bukti pembayaran untuk proses ulang",
                                'url' => route('complain-form.index')
                            ];
                            $requester->notify(new RequisitionNotification($notificationData, $rejectedBy));
                        }
                    } else {
                        // token approval log setelah user melakukan reject jadi null
                        $getApproverAfters = ApprovalLog::where('requisition_id', $requisition->id)
                            ->where('level', '>', $approvalLog->level)
                            ->where('status', 'Pending')
                            ->get();

                        foreach ($getApproverAfters as $approverAfter) {
                            // Set token null untuk membatalkan approval selanjutnya
                            $approverAfter->token = null;
                            $approverAfter->status = 'Rejected';
                            $approverAfter->save();
                        }

                        // Send rejection notification to requester
                        if ($rejectedBy) {
                            sendRejectionNotification::dispatch(
                                $requisition,
                                $rejectedBy,
                                null,
                                'approval',
                                now()
                            );

                            // Kirim notifikasi rejection ke requester
                            if ($requester) {
                                $notificationData = [
                                    'requisition_id' => $requisition->id,
                                    'srs_number' => $requisition->no_srs,
                                    'message' => "Requisition {$requisition->no_srs} telah di-reject oleh {$rejectedBy->name}",
                                    'url' => route('complain-form.index')
                                ];
                                $requester->notify(new RequisitionNotification($notificationData, $rejectedBy));
                            }
                        }
                    }
                }

                activity()
                    ->inLog('complain')
                    ->causedBy(User::where('nik', $approvalLog->approver_nik)->first())
                    ->performedOn($approvalLog)
                    ->event('processed approval')
                    ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent(), 'requisition_no' => $requisition->no_srs])
                    ->log('User ' . ($approvalLog->approver->name ?? 'Unknown') . ' has ' . $approvalLog->status . ' requisition ID: ' . $approvalLog->requisition_id . ' No: '. $requisition->no_srs . ' with notes: ' . ($notes ?? 'No notes provided'));
            });

            // tampilkan halaman hasil approval
            return view('page.complain.links.approval-result', compact('requisition', 'status'));

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            // Untuk semua error, tampilkan halaman error yang sesuai
            if (str_contains($errorMessage, 'Invalid approval link') || str_contains($errorMessage, 'expired')) {
                return view('page.complain.links.approval-expired', [
                    'message' => $errorMessage,
                    'errorType' => 'token_expired'
                ]);
            } elseif (str_contains($errorMessage, 'not found')) {
                return view('page.complain.links.approval-invalid', [
                    'message' => $errorMessage,
                    'errorType' => 'not_found'
                ]);
            }

            return view('page.complain.links.approval-invalid', [
                'message' => 'Terjadi kesalahan saat memproses approval.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Show review page for approval with review
     */
    public function showReviewPage(Request $request)
    {
        try {
            $token = $request->query('token');
            $id = $request->query('id');

            if (!$token || !$id) {
                return view('page.complain.links.approval-invalid', [
                    'message' => 'Invalid approval link - missing parameters.',
                    'errorType' => 'missing_params'
                ]);
            }

            // Check if token is already used/expired first
            $tokenStatus = $this->checkTokenStatus($request);
            if ($tokenStatus !== null) {
                return $tokenStatus;
            }

            // Verify approval log exists and is valid
            $approvalLog = ApprovalLog::where('requisition_id', $id)
                ->where('token', $token)
                ->where('status', 'Pending')
                ->first();

            if (!$approvalLog) {
                return view('page.complain.links.approval-invalid', [
                    'message' => 'Invalid or expired approval link.',
                    'errorType' => 'token_expired'
                ]);
            }

            // Get requisition with related data
            $requisition = Requisition::with(['customer', 'requisitionItems.itemMaster.ItemDetails'])
                ->find($id);

            if (!$requisition) {
                return view('page.complain.links.approval-invalid', [
                    'message' => 'Requisition not found.',
                    'errorType' => 'not_found'
                ]);
            }

            return view('page.complain.links.approval-review', compact('requisition', 'token'));
        } catch (\Exception $e) {
            Log::error('Error showing review page: ' . $e->getMessage());
            return view('page.complain.links.approval-invalid', [
                'message' => 'An error occurred while loading the review page.',
                'errorType' => 'server_error'
            ]);
        }
    }

    /**
     * Kirim email ke approver level berikutnya setelah current level approve
     */
    public function mailOtherLevel($requisitionId, $currentLevel, $whPhase)
    {
        try {
            // Cari approval log level berikutnya yang masih pending
            $nextApprovalLog = ApprovalLog::where('requisition_id', $requisitionId)
                ->where('level', $currentLevel + 1)
                ->where('status', 'Pending')
                ->whereNotNull('token')
                ->first();

            if ($nextApprovalLog) {
                $approver = User::where('nik', $nextApprovalLog->approver_nik)->first();
                if ($approver) {
                    $requisition = Requisition::find($requisitionId);
                    if ($requisition) {
                        $requisition->route_to = $approver->name;
                        $requisition->save();

                        sendMailComplain::dispatch($approver, $requisition, $nextApprovalLog);

                        // Kirim notifikasi ke approver level berikutnya
                        $approveWithReviewLink = route('complain.approval');

                        $notificationData = [
                            'requisition_id' => $requisition->id,
                            'srs_number' => $requisition->no_srs,
                            'message' => "Requisition {$requisition->no_srs} menunggu approval Anda",
                            'url' => $approveWithReviewLink
                        ];

                        $causer = User::where('nik', $requisition->requester_nik)->first();
                        if ($causer) {
                            $approver->notify(new RequisitionNotification($notificationData, $causer));
                        }

                        Log::info("Email approval dikirim ke level {$nextApprovalLog->level} - {$approver->name}");
                        return true;
                    }
                }
            } else {
                // Tidak ada approval level berikutnya - mulai warehouse phase dari tracking
                Log::info("No more approval levels found, checking warehouse tracking for requisition {$requisitionId}");
                return $this->processWarehouseTracking($requisitionId);
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error sending next level notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process warehouse tracking dan kirim email bertingkat sesuai urutan tracking
     */
    private function processWarehouseTracking($requisitionId)
    {
        try {
            $requisition = Requisition::find($requisitionId);
            if (!$requisition) {
                Log::error("Requisition not found for warehouse tracking: {$requisitionId}");
                return false;
            }

            // Ambil semua tracking untuk requisition ini, urutkan berdasarkan ID (urutan insert)
            $trackings = Tracking::where('requisition_id', $requisitionId)
                ->whereNotNull('token')
                ->orderBy('id', 'asc')
                ->get();

            if ($trackings->isEmpty()) {
                Log::warning("No warehouse tracking found for requisition {$requisitionId}");
                return false;
            }

            // Cari tracking pertama yang belum diproses
            $currentTracking = $trackings->whereNotNull('token')->first();

            if (!$currentTracking) {
                Log::info("All warehouse tracking completed for requisition {$requisitionId}");
                return true;
            }

            // Tentukan approver berdasarkan current_position
            $approver = $this->getApproverByPosition($currentTracking->current_position);

            if (!$approver) {
                Log::error("No approver found for position: {$currentTracking->current_position}");
                return false;
            }

            // Update route_to di requisition
            $requisition->route_to = $approver->name;
            $requisition->save();

            // Kirim email menggunakan sendPrintBatchMail
            sendPrintBatchMail::dispatch($approver, $requisition, $currentTracking);

            Log::info("Warehouse email sent for requisition {$requisitionId}", [
                'position' => $currentTracking->current_position,
                'approver' => $approver->name,
                'tracking_id' => $currentTracking->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error processing warehouse tracking: ' . $e->getMessage(), [
                'requisition_id' => $requisitionId,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Mendapatkan approver berdasarkan position di tracking
     */
    private function getApproverByPosition($position)
    {
        try {
            switch ($position) {
                case 'WH Supervisor First':
                case 'WH Supervisor Final':
                    // Ambil user dengan role wh-supervisor yang pertama
                    return User::role('wh-supervisor')->first();

                case 'Material Supervisor':
                    // Ambil user dengan role material-supervisor yang pertama
                    return User::role('material-supervisor')->first();

                default:
                    Log::warning("Unknown position in tracking: {$position}");
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error getting approver by position: " . $e->getMessage(), [
                'position' => $position
            ]);
            return null;
        }
    }

    /**
     * Process warehouse approval (untuk direct approval dari email)
     */
    public function processWarehouseApproval(Request $request)
    {
        try {
            $token = $request->query('token') ?? $request->input('token');
            $id = $request->query('id') ?? $request->input('id');

            if (!$token || !$id) {
                return response()->json(['message' => 'Invalid request parameters'], 400);
            }

            // Cek apakah tracking dengan token ini masih valid
            $tracking = Tracking::where('requisition_id', $id)
                ->where('token', $token)
                ->first();

            if (!$tracking) {
                $requisition = Requisition::with('customer')->find($id);
                return view('page.complain.links.warehouse-expired', compact('requisition'));
            }

            if ($request->isMethod('post')) {
                return $this->processWarehouseApprovalWithValidation($request, $tracking);
            } else {
                // Direct approval dari email - langsung approve
                return $this->processDirectWarehouseApproval($tracking);
            }

        } catch (\Exception $e) {
            Log::error('Error in warehouse approval process: ' . $e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    /**
     * Show warehouse review page
     */
    public function showWarehouseReviewPage(Request $request)
    {
        try {
            $token = $request->query('token');
            $id = $request->query('id');

            if (!$token || !$id) {
                return response()->json(['message' => 'Invalid request parameters'], 400);
            }

            $tracking = Tracking::where('requisition_id', $id)
                ->where('token', $token)
                ->whereNotNull('token')
                ->first();

            if (!$tracking) {
                $requisition = Requisition::with(['customer', 'requisitionItems'])->find($id);
                return view('page.complain.links.warehouse-expired', compact('requisition'));
            }

            $requisition = Requisition::with(['customer', 'requester', 'requisitionItems.itemMaster'])
                ->find($id);

            return view('page.complain.links.warehouse-review', compact('requisition', 'token', 'tracking'));

        } catch (\Exception $e) {
            Log::error('Error showing warehouse review page: ' . $e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    /**
     * Process direct warehouse approval (langsung approve dari email)
     */
    private function processDirectWarehouseApproval($tracking)
    {
        try {
            DB::beginTransaction();

            // Invalidate token untuk menandai tracking sudah selesai
            $tracking->token = null;
            $tracking->notes = 'Approved via direct email link';
            $tracking->save();

            $requisition = Requisition::find($tracking->requisition_id);

            $nextTracking = Tracking::where('requisition_id', $tracking->requisition_id)
                ->where('id', '>', $tracking->id)
                ->whereNotNull('token')
                ->orderBy('id', 'asc')
                ->first();

            if ($nextTracking) {
                // Masih ada tracking berikutnya, lanjutkan ke level berikutnya
                $this->processWarehouseTracking($tracking->requisition_id);

                Log::info("Warehouse tracking approved, proceeding to next level", [
                    'tracking_id' => $tracking->id,
                    'next_tracking_id' => $nextTracking->id,
                    'position' => $tracking->current_position
                ]);
            } else {
                $requisition->status = 'Completed';
                $requisition->route_to = '-';
                $requisition->save();

                // Send completion notification to requester
                $completedBy = $this->getApproverByPosition($tracking->current_position);
                if ($completedBy) {
                    sendWarehouseCompletion::dispatch(
                        $requisition,
                        $completedBy,
                        now()
                    );

                    // Kirim notifikasi completion ke requester
                    $requester = User::where('nik', $requisition->requester_nik)->first();
                    if ($requester) {
                        $notificationData = [
                            'requisition_id' => $requisition->id,
                            'srs_number' => $requisition->no_srs,
                            'message' => "Requisition {$requisition->no_srs} telah selesai diproses - Status: Completed",
                            'url' => route('complain-form.index')
                        ];
                        $requester->notify(new RequisitionNotification($notificationData, $completedBy));
                    }
                }

                Log::info("All warehouse tracking completed for requisition {$tracking->requisition_id}");
            }

            // Log activity untuk warehouse approval
            $approver = $this->getApproverByPosition($tracking->current_position);
            if ($approver) {
                activity()
                    ->inLog('complain')
                    ->causedBy($approver)
                    ->performedOn($requisition)
                    ->event('warehouse approval')
                    ->withProperties([
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'complain' => true,
                        'warehouse_position' => $tracking->current_position,
                        'requisition_no' => $requisition->no_srs
                    ])
                    ->log('User ' . $approver->name . ' approved warehouse tracking for requisition ID: ' . $requisition->id . ' No: ' . $requisition->no_srs . ' at position: ' . $tracking->current_position);
            }

            DB::commit();

            return view('page.complain.links.warehouse-success', compact('requisition', 'tracking'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in direct warehouse approval: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process warehouse approval dengan validasi form
     */
    private function processWarehouseApprovalWithValidation(Request $request, $tracking)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $notes = $request->input('notes');

            // Update tracking - selalu approve dalam validasi form
            $tracking->notes = $notes ?? 'Approved via review form';

            // Invalidate token untuk menandai tracking sudah selesai
            $tracking->token = null;
            $tracking->save();

            $requisition = Requisition::find($tracking->requisition_id);

            // Cek apakah ada tracking berikutnya
            $nextTracking = Tracking::where('requisition_id', $tracking->requisition_id)
                ->where('id', '>', $tracking->id)
                ->whereNotNull('token')
                ->orderBy('id', 'asc')
                ->first();

            if ($nextTracking) {
                $this->processWarehouseTracking($tracking->requisition_id);
            } else {
                // Semua selesai
                $requisition->status = 'Completed';
                $requisition->route_to = '-';
                $requisition->save();

                // Send completion notification to requester
                $completedBy = $this->getApproverByPosition($tracking->current_position);
                if ($completedBy) {
                    sendWarehouseCompletion::dispatch(
                        $requisition,
                        $completedBy,
                        now()
                    );

                    // Kirim notifikasi completion ke requester
                    $requester = User::where('nik', $requisition->requester_nik)->first();
                    if ($requester) {
                        $notificationData = [
                            'requisition_id' => $requisition->id,
                            'srs_number' => $requisition->no_srs,
                            'message' => "Requisition {$requisition->no_srs} telah selesai diproses - Status: Completed",
                            'url' => route('complain-form.index')
                        ];
                        $requester->notify(new RequisitionNotification($notificationData, $completedBy));
                    }
                }
            }

            // Log activity untuk warehouse approval
            $approver = $this->getApproverByPosition($tracking->current_position);
            if ($approver) {
                activity()
                    ->inLog('complain')
                    ->causedBy($approver)
                    ->performedOn($requisition)
                    ->event('warehouse approval')
                    ->withProperties([
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'warehouse_position' => $tracking->current_position,
                        'requisition_no' => $requisition->no_srs,
                    ])
                    ->log('User ' . $approver->name . ' approved warehouse tracking for requisition ID: ' . $requisition->id . ' No: ' . $requisition->no_srs . ' at position: ' . $tracking->current_position . ' with notes: ' . ($notes ?? 'No notes provided'));
            }

            DB::commit();

            return response()->json([
                'message' => 'Warehouse approval successful',
                'status' => 'approve'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in warehouse approval validation: ' . $e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    public function testData()
    {
        $headsQA = User::role('head-qa')->get();
        return response()->json($headsQA);
    }

    /**
     * Test function untuk warehouse tracking system
     */
    public function testWarehouseTracking($requisitionId)
    {
        try {
            Log::info("Testing warehouse tracking for requisition: {$requisitionId}");

            // Test apakah ada tracking untuk requisition ini
            $trackings = Tracking::where('requisition_id', $requisitionId)
                ->orderBy('id', 'asc')
                ->get();

            if ($trackings->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tracking found for this requisition',
                    'requisition_id' => $requisitionId
                ]);
            }

            // Test process warehouse tracking
            $result = $this->processWarehouseTracking($requisitionId);

            return response()->json([
                'status' => $result ? 'success' : 'error',
                'message' => $result ? 'Warehouse tracking processed successfully' : 'Failed to process warehouse tracking',
                'trackings' => $trackings,
                'requisition_id' => $requisitionId
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing warehouse tracking: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'requisition_id' => $requisitionId
            ]);
        }
    }

    public function uploadPaymentProof(paymentProofRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function() use ($validated) {
                // Check if requisition exists and has correct status
                $requisition = Requisition::findOrFail($validated['complain_id']);

                if ($requisition->status !== 'payment proof') {
                    throw new \Exception('This requisition does not require payment proof upload.');
                }

                // Handle file upload
                $file = $validated['payment_document'];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('payment_proofs', $fileName, 'public');

                // Create payment record
                $payment = Payment::create([
                    'requisition_id' => $validated['complain_id'],
                    'payment_date' => $validated['payment_date'],
                    'document_url' => $filePath,
                ]);

                // Update requisition status
                $requisition->status = 'In Progress';
                $requisition->save();

                // current level
                $currentLevel = $requisition->approvalLogs()
                    ->where('status', 'Pending')
                    ->orderBy('level', 'asc')
                    ->whereNotNull('token')
                    ->value('level');

                // Log activity
                $user = Auth::user();
                if ($user) {
                    activity()
                        ->inLog('complain')
                        ->causedBy(User::find($user->id))
                        ->performedOn($requisition)
                        ->event('uploaded payment proof')
                        ->withProperties(['ip' => request()->ip(), 'user_agent' => request()->userAgent(), 'requisition_no' => $requisition->no_srs])
                        ->log('User ' . $user->name . ' uploaded payment proof for requisition ID: ' . $requisition->id . ' No: '. $requisition->no_srs);
                }

                // Send payment confirmation email with attachment
                $this->mailOtherLevel($validated['complain_id'], $currentLevel, false);

                // Kirim notifikasi payment proof uploaded
                $requester = User::where('nik', $requisition->requester_nik)->first();
                if ($user && $requester) {
                    $notificationData = [
                        'requisition_id' => $requisition->id,
                        'srs_number' => $requisition->no_srs,
                        'message' => "Bukti pembayaran untuk requisition {$requisition->no_srs} telah berhasil diupload",
                        'url' => route('complain-form.index')
                    ];
                    $requester->notify(new RequisitionNotification($notificationData, $user));
                }
            });

            return response()->json([
                'message' => 'Payment proof uploaded successfully. Requisition status updated to Completed.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to upload payment proof: ' . $e->getMessage());

            $errorMessage = $e->getMessage();
            $statusCode = 500;

            if (str_contains($errorMessage, 'does not require payment proof') ||
                str_contains($errorMessage, 'Invalid complain ID')) {
                $statusCode = 400;
            }

            return response()->json(['message' => $errorMessage], $statusCode);
        }
    }

    public function printBulkReport(Request $request)
    {
        $request->validate([
            'selected_ids'   => 'required|array',
            'selected_ids.*' => 'integer|exists:requisitions,id' // Pastikan semua ID valid
        ]);

        $requisitions = Requisition::with([
            'customer',
            'requester.department',
            'requisitionItems.itemMaster',
            'requisitionItems.itemDetail',
            'requisitionSpecial',
            'approvalLogs' => fn($q) => $q->orderBy('level', 'asc'),
            'approvalLogs.approver.roles'
        ])->whereIn('id', $request->selected_ids)->get();

        if ($requisitions->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk dicetak.');
        }

        // Ambil data revision pertama (atau bisa disesuaikan dengan kebutuhan)
        $revision = Revision::first();

        $pdf = Pdf::loadView('page.complain.reports.report-template', [
            'requisitions' => $requisitions,
            'revision' => $revision
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Bulk-RS-Complain-' . now()->format('Y-m-d') . '.pdf');
    }

    public function reports()
    {
        $user = Auth::user();
        if(!$user->can('view report')){
            abort(403);
        }
        return view('page.complain.reports.report');
    }
}
