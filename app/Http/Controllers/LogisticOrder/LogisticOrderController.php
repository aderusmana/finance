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
use App\Models\Customer\DeliveryOrderNote; // Tambahkan ini
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\LogisticOrderDistributorMail; // Mailer baru
use App\Notifications\SystemNotification; // Notifikasi ke Sales
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class LogisticOrderController extends Controller
{
    public function getCustomerDependencies($customerId)
    {
        $distributors = DistributorCustomer::with('distributor')
            ->where('customer_id', $customerId)
            ->get()
            ->map(function($item) { return $item->distributor; })
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

            $data = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note'])
                ->whereHas('note', function($q) use ($tab) {
                    if ($tab === 'downloaded') {
                        $q->where('status', 'Downloaded');
                    } else {
                        $q->where('status', 'Pending Download');
                    }
                })
                ->select('logistic_orders.*');

            // Urutkan data berdasarkan status
            if ($tab === 'downloaded') {
                $data->orderBy('updated_at', 'desc'); // Selesai terbaru
            } else {
                $data->orderBy('created_at', 'desc'); // Pending order terbaru
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('logistic_order_no', function($row) {
                    return 'LO-' . str_pad($row->id, 4, '0', STR_PAD_LEFT);
                })
                ->addColumn('do_no', function($row) {
                    return $row->note->delivery_order_no ?? '-';
                })
                ->addColumn('distributor_name', function($row) { return $row->distributor->name ?? '-'; })
                ->addColumn('customer_name', function($row) { return $row->customer->name ?? '-'; })
                ->addColumn('ship_to', function($row) { return $row->customerShipTo->ship_to_name ?? '-'; })
                ->addColumn('status_badge', function($row) use ($tab) {
                    if ($tab === 'downloaded') {
                        $count = $row->note->download_count ?? 0;
                        return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-check-circle me-1"></i> Downloaded ('.$count.'x)</span>';
                    }
                    return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-clock me-1"></i> Pending</span>';
                })
                ->addColumn('action', function($row) use ($tab) {
                    if ($tab === 'downloaded') {
                        return '<button class="btn btn-sm btn-info text-white btn-detail shadow-sm px-3 rounded-pill" data-id="'.$row->id.'"><i class="ph-bold ph-eye"></i> Lihat DN</button>';
                    }
                    return '<button class="btn btn-sm btn-primary text-white btn-detail shadow-sm px-3 rounded-pill" data-id="'.$row->id.'"><i class="ph-bold ph-eye"></i> Tinjau Order</button>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $customers = Customer::orderBy('name', 'asc')->get();
        return view('page.logistic_order.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'         => 'required',
            'distributor_id'      => 'required',
            'customer_ship_to_id' => 'required',
            'delivery_date'       => 'required|date',
            'items'               => 'required|array|min:1',
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
                if(empty($item['qty']) || $item['qty'] <= 0) continue;

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

            if($distributor && $distributor->email) {
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

        return response()->json($order);
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
        // Pastikan relasi 'items' ikut ditarik agar bisa ditampilkan di PDF
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
                    "DN Telah Di-download",
                    "Distributor <b>{$order->distributor->name}</b> telah mencetak DN untuk Customer {$order->customer->name}.",
                    "#",
                    "ph-printer",
                    "info"
                ));
            }
        }

        $note->increment('download_count');

        $pdf = Pdf::loadView('pdf.delivery_order', compact('order'))
                  ->setPaper('a5', 'landscape');

        return $pdf->stream($note->delivery_order_no . '.pdf');
    }
}
