<?php

namespace App\Http\Controllers\LogisticOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Distributor;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerShipTo;
use App\Models\Customer\DistributorCustomer;
use App\Models\Customer\LogisticOrder;
use App\Models\Customer\LogisticOrderItem;
use App\Models\Customer\DeliveryOrderNote;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\LogisticOrderDistributorMail;
use App\Models\Customer\DeliveryOrderDownloadLog;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Exports\DeliveryNoteItemExport;
use Maatwebsite\Excel\Facades\Excel;

class LogisticOrderController extends Controller
{
    public function getCustomerDependencies($customerId)
    {
        $distributors = DistributorCustomer::with('distributor')
            ->where('customer_id', $customerId)
            ->get()
            ->map(function ($item) {
                return $item->distributor;
            })
            ->filter()
            ->unique('id');

        $shipToLocations = CustomerShipTo::with('user')->where('customer_id', $customerId)->get();

        // Jika model Customer kamu punya relasi ke items barang, tarik di sini
        $customer = Customer::with('items')->find($customerId);

        return response()->json([
            'distributors' => $distributors->values(),
            'ship_to_list' => $shipToLocations,
            'items'        => $customer ? $customer->items : []
        ]);
    }

    /**
     * API 2: Saat Distributor dipilih -> Tarik Harga Logistic Fee
     */
    public function getLogisticFee($distributorId, $customerId)
    {
        $logisticFee = DistributorCustomer::where('distributor_id', $distributorId)
            ->where('customer_id', $customerId)
            ->first();

        // Cek apakah sedang ada pengajuan harga pending. Jika ya, pakai harga yang diajukan
        $fee = 0;
        if ($logisticFee) {
            $fee = ($logisticFee->status === 'Pending') ? $logisticFee->proposed_fee : $logisticFee->logistic_fee;
        }

        return response()->json([
            'logistic_fee' => $fee,
        ]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil parameter tab dari AJAX, default 'pending'
            $tab = $request->get('tab', 'pending');

            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $data = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note'])
                ->whereHas('note', function ($q) use ($tab) {
                    if ($tab === 'downloaded') {
                        $q->where('status', 'Downloaded');
                    } else {
                        $q->where('status', 'Pending Download');
                    }
                })
                ->select('logistic_orders.*');

            if ($tab === 'downloaded' && !empty($dateFrom) && !empty($dateTo)) {
                $data->whereBetween('delivery_date', [$dateFrom, $dateTo]);
            }

            // Urutkan data berdasarkan status
            if ($tab === 'downloaded') {
                $data->orderBy('updated_at', 'desc'); // Selesai terbaru
            } else {
                $data->orderBy('created_at', 'desc'); // Pending order terbaru
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('logistic_order_no', function ($row) {
                    $loNo = 'LO-' . str_pad($row->id, 4, '0', STR_PAD_LEFT);
                    $createdAt = $row->created_at->format('d M Y, H:i');

                    // Format HTML Cantik untuk Kolom Order Info (Pending)
                    return '
                        <div class="d-flex flex-column gap-1">
                            <span class="fw-bolder text-primary" style="font-size: 0.95rem;">' . $loNo . '</span>
                            <span class="text-secondary" style="font-size: 0.75rem;"><i class="ph-fill ph-calendar-blank text-primary opacity-75"></i> Dibuat: ' . $createdAt . '</span>
                        </div>
                    ';
                })
                ->addColumn('do_no', function ($row) {
                    $doNo = $row->note->delivery_order_no ?? '-';
                    $createdAt = $row->created_at->format('d M Y, H:i');

                    // Format HTML Cantik untuk Kolom DN Info (Hanya Dibuat)
                    return '
                        <div class="d-flex flex-column gap-1">
                            <span class="fw-bolder text-success" style="font-size: 0.95rem;">' . $doNo . '</span>
                            <span class="text-secondary" style="font-size: 0.75rem;"><i class="ph-fill ph-calendar-plus text-primary opacity-50"></i> Dibuat: ' . $createdAt . '</span>
                        </div>
                    ';
                })
                ->addColumn('distributor_name', function ($row) {
                    return $row->distributor->name ?? '-';
                })
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? '-';
                })
                ->addColumn('ship_to', function ($row) {
                    return $row->customerShipTo->ship_to_name ?? '-';
                })
                ->addColumn('status_badge', function ($row) use ($tab) {
                    if ($tab === 'downloaded') {
                        $count = $row->note->download_count ?? 0;
                        $lastDownloadAt = DeliveryOrderDownloadLog::where('delivery_order_note_id', $row->note->id)
                            ->latest('created_at')
                            ->value('created_at');
                        $updatedAt = $lastDownloadAt
                            ? Carbon::parse($lastDownloadAt)->format('d M Y, H:i')
                            : $row->updated_at->format('d M Y, H:i');

                        return '
                            <div class="d-flex flex-column align-items-start">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill">
                                    <i class="ph-bold ph-check-circle me-1"></i> Download (' . $count . 'x)
                                </span>
                                <span class="text-secondary" style="font-size: 0.72rem; margin-top: 4px; padding-left: 4px;">
                                    <i class="ph-fill ph-eye text-success opacity-75"></i> Terakhir: ' . $updatedAt . '
                                </span>
                            </div>
                        ';
                    }
                    return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-clock me-1"></i> Pending</span>';
                })
                ->addColumn('action', function ($row) use ($tab) {
                    if ($tab === 'downloaded') {
                        $btnDetail = '<button type="button" class="btn btn-md btn-primary text-white btn-detail shadow-sm px-3 rounded-pill flex-fill" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail DN" aria-label="Detail DN"><i class="ph-bold ph-eye"></i></button>';
                        $btnDownload = '<a href="' . URL::signedRoute('public.lo.download', ['id' => $row->id, 'fromEmail' => 0]) . '" target="_blank" class="btn btn-sm btn-success text-white shadow-sm px-3 rounded-pill flex-fill" data-bs-toggle="tooltip" data-bs-placement="top" title="Download DN" aria-label="Download DN"><i class="ph-bold ph-printer"></i></a>';

                        return '<div class="d-flex flex-row gap-2 align-items-center w-100">' . $btnDetail . $btnDownload . '</div>';
                    }

                    return '<button 
                                class="btn btn-sm btn-primary text-white btn-detail shadow-sm px-3 rounded-pill" 
                                data-id="' . $row->id . '" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Detail">
                                <i class="ph-bold ph-eye"></i>
                            </button>';
                })
                ->rawColumns(['logistic_order_no', 'do_no', 'status_badge', 'action'])
                ->make(true);
        }

        $customers = Customer::orderBy('name', 'asc')->get();
        return view('page.logistic_order.index', compact('customers'));
    }

    public function exportDeliveryNotes(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ((!empty($dateFrom) && empty($dateTo)) || (empty($dateFrom) && !empty($dateTo))) {
            return response()->json([
                'message' => 'Filter tanggal harus diisi lengkap (From dan To).',
            ], 422);
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            try {
                $from = Carbon::parse($dateFrom)->format('Y-m-d');
                $to = Carbon::parse($dateTo)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Format tanggal tidak valid.',
                ], 422);
            }

            if ($from > $to) {
                return response()->json([
                    'message' => 'Tanggal From tidak boleh melebihi To.',
                ], 422);
            }

            $export = new DeliveryNoteItemExport($from, $to);
            $suffix = $from . '_to_' . $to;
        } else {
            $export = new DeliveryNoteItemExport();
            $suffix = now()->format('Ymd_His');
        }

        return Excel::download($export, 'delivery_no_export_' . $suffix . '.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'         => 'required',
            'distributor_id'      => 'required',
            'customer_ship_to_id' => 'required',
            'delivery_date'       => 'required|date',
            'items'               => 'required|array|min:1',
            'items.*.item_code'   => 'required|string',
            'items.*.item_name'   => 'required|string',
        ], [
            'items.*.item_code.required' => 'Semua kode item wajib diisi.',
            'items.*.item_name.required' => 'Semua nama item wajib diisi.'
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Header (Hapus period & delivery_to & route_to & status)
            $order = LogisticOrder::create([
                'distributor_id'      => $request->distributor_id,
                'customer_id'         => $request->customer_id,
                'customer_ship_to_id' => $request->customer_ship_to_id,
                'logistic_order_no'   => 0,
                'delivery_date'       => $request->delivery_date,
            ]);

            $order->update(['logistic_order_no' => $order->id]);
            $loNo = 'LO-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);

            // 2. Simpan Item
            foreach ($request->items as $item) {
                if (empty($item['qty']) || $item['qty'] <= 0) continue;

                LogisticOrderItem::create([
                    'logistic_order_id' => $order->id,
                    'ship_to_code'      => $request->ship_to_code_header,
                    'order_item_code'   => $item['item_code'] ?? '-',
                    'order_item_name'   => $item['item_name'],
                    'order_quantity'    => $item['qty'],
                    'order_amount'      => str_replace(['Rp', '.', ' '], '', $item['amount']),
                ]);
            }

            $distributor = Distributor::find($request->distributor_id);
            $distCode = $distributor ? $distributor->code : 'XXX';

            $year = date('Y');
            $month = date('m');
            $increment = str_pad($order->id, 4, '0', STR_PAD_LEFT);

            $doNo = "{$distCode}-{$year}-{$month}-{$increment}";

            DeliveryOrderNote::create([
                'logistic_order_id' => $order->id,
                'delivery_order_no' => $doNo,
                'status'            => 'Pending Download',
                'download_count'    => 0,
            ]);

            if ($distributor && $distributor->email) {
                // Tarik ulang order dengan relasi lengkap untuk email
                $orderEmail = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note', 'items'])->find($order->id);
                Mail::to($distributor->email)->queue(new LogisticOrderDistributorMail($orderEmail));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Order ($loNo) & Note ($doNo) berhasil dibuat! Email dikirim ke Distributor."]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = LogisticOrder::with(['distributor', 'customer', 'customerShipTo.user', 'items', 'note'])->findOrFail($id);

        $downloadLogs = [];
        if ($order->note) {
            $downloadLogs = DeliveryOrderDownloadLog::where('delivery_order_note_id', $order->note->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $responseData = $order->toArray();
        $responseData['download_logs'] = $downloadLogs;
        $responseData['download_url'] = URL::signedRoute('public.lo.download', ['id' => $id, 'fromEmail' => 0]);

        return response()->json($responseData);
    }

    // ==========================================
    // FUNGSI UNTUK PORTAL PUBLIK DISTRIBUTOR
    // ==========================================

    /**
     * Halaman Detail Publik untuk Distributor
     */
    public function publicDetail($id)
    {
        $order = LogisticOrder::with(['distributor', 'customer', 'customerShipTo.user', 'items', 'note'])->findOrFail($id);
        return view('page.logistic_order.links.public_detail', compact('order'));
    }

    /**
     * Fungsi Smart Download (Direct Link Email / Tombol Detail)
     */
    public function publicDownload($id, $fromEmail = false)
    {
        $order = LogisticOrder::with(['note', 'customerShipTo.user', 'customer', 'distributor', 'items'])->findOrFail($id);
        $note = $order->note;

        if ($fromEmail && $note->status === 'Pending Download') {
            return redirect(URL::signedRoute('public.lo.detail', ['id' => $id]))
                ->with('warning', 'Harap periksa detail pesanan terlebih dahulu sebelum mengunduh Dokumen DN untuk pertama kali.');
        }

        if ($note->status === 'Pending Download' && $note->download_count == 0) {
            $note->update(['status' => 'Downloaded']);

            $salesUser = $order->customerShipTo->user ?? null;
            if ($salesUser) {
                Notification::send($salesUser, new SystemNotification(
                    "Dokumen DN Telah Di Download",
                    "Distributor <b>{$order->distributor->name}</b> telah mencetak DN untuk Customer {$order->customer->name}.",
                    "#",
                    "ph-printer",
                    "info"
                ));
                if (!empty($salesUser->email)) {
                    try {
                        Mail::to($salesUser->email)->queue(new LogisticOrderDistributorMail($order, 'sales'));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim email ke Sales: ' . $e->getMessage());
                    }
                }
            }
        }

        $note->increment('download_count');

        $downloadedBy = 'Distributor (Public Link)';
        if (Auth::check()) {
            $downloadedBy = Auth::user()->name . ' (Admin)';
        }

        DeliveryOrderDownloadLog::create([
            'delivery_order_note_id' => $note->id,
            'downloaded_by' => $downloadedBy
        ]);

        $pdfFileName = $note->delivery_order_no . '.pdf';
        $cacheKey    = 'delivery_order_pdf_' . $order->id;

        $pdfBase64 = Cache::remember($cacheKey, now()->addHours(24), function () use ($order) {
            $pdf = Pdf::loadView('pdf.delivery_order', compact('order'))
                ->setPaper('a5', 'landscape')
                ->output();

            return base64_encode($pdf);
        });

        $pdfContent = base64_decode($pdfBase64);

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $pdfFileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
