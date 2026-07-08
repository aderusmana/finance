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
use App\Jobs\SendLogisticOrderEmailJob;

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
        $customer = Customer::with('items')->find($customerId);

        return response()->json([
            'distributors' => $distributors->values(),
            'ship_to_list' => $shipToLocations,
            'items'        => $customer ? $customer->items : []
        ]);
    }

    public function getLogisticFee($distributorId, $customerId)
    {
        $logisticFee = DistributorCustomer::where('distributor_id', $distributorId)
            ->where('customer_id', $customerId)
            ->first();

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
            $tab = $request->get('tab', 'pending');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $filterDistributors = $request->get('distributors');

            $data = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note'])
                ->whereHas('note', function ($q) use ($tab) {
                    if ($tab === 'downloaded') {
                        $q->where('status', 'Downloaded');
                    } elseif ($tab === 'canceled') {
                        $q->where('status', 'Canceled');
                    } else {
                        $q->where('status', 'Pending Download');
                    }
                })
                ->select('logistic_orders.*');

            $user = Auth::user();
            if (!$user->hasRole(['super-admin', 'sales-ka-approver'])) {
                $data->where('created_by', $user->id);
            }

            if (!empty($dateFrom) && !empty($dateTo)) {
                $data->whereBetween('delivery_date', [$dateFrom, $dateTo]);
            }

            if (!empty($filterDistributors)) {
                $data->whereIn('distributor_id', $filterDistributors);
            }

            if ($tab === 'downloaded' || $tab === 'canceled') {
                $data->orderBy('updated_at', 'desc');
            } else {
                $data->orderBy('created_at', 'desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('logistic_order_no', function ($row) {
                    $loNo = 'LO-' . str_pad($row->id, 4, '0', STR_PAD_LEFT);
                    $createdAt = $row->created_at->format('d M Y, H:i');
                    return '
                        <div class="d-flex flex-column gap-1">
                            <span class="fw-bolder text-primary" style="font-size: 0.95rem;">' . $loNo . '</span>
                            <span class="text-secondary" style="font-size: 0.75rem;"><i class="ph-fill ph-calendar-blank text-primary opacity-75"></i> Created: ' . $createdAt . '</span>
                        </div>
                    ';
                })
                ->addColumn('do_no', function ($row) {
                    $doNo = $row->note->delivery_order_no ?? '-';
                    $createdAt = $row->created_at->format('d M Y, H:i');
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
                    if ($tab === 'canceled') {
                        $cancelTime = $row->canceled_at ? Carbon::parse($row->canceled_at)->format('d M Y, H:i') : '-';
                        return '
                            <div class="d-flex flex-column align-items-start">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill">
                                    <i class="ph-bold ph-x-circle me-1"></i> Canceled
                                </span>
                                <span class="text-secondary" style="font-size: 0.72rem; margin-top: 4px; padding-left: 4px;">
                                    <i class="ph-fill ph-clock text-danger opacity-75"></i> ' . $cancelTime . '
                                </span>
                            </div>
                        ';
                    }

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
                    $btnDetail = '<button type="button" class="btn btn-sm btn-primary text-white btn-detail shadow-sm px-2 rounded-pill flex-fill" data-id="' . $row->id . '" title="Detail Document"><i class="ph-bold ph-eye"></i></button>';

                    // TAB DOWNLOADED (Delivery Notes)
                    if ($tab === 'downloaded') {
                        $btnDownload = '<a href="' . URL::signedRoute('public.lo.download', ['id' => $row->id, 'fromEmail' => 0]) . '" target="_blank" class="btn btn-sm btn-success text-white shadow-sm px-2 rounded-pill flex-fill" title="Download DN & PO"><i class="ph-bold ph-printer"></i></a>';
                        $btnCancel = '<button type="button" class="btn btn-sm btn-danger text-white btn-cancel shadow-sm px-2 rounded-pill flex-fill" data-id="' . $row->id . '" title="Cancel Order"><i class="ph-bold ph-x-circle"></i></button>';
                        
                        return '<div class="d-flex flex-row gap-1 align-items-center w-100">' . $btnDetail . $btnDownload . $btnCancel . '</div>';
                    } 
                    
                    // TAB CANCELED
                    if ($tab === 'canceled') {
                        $btnEdit = '<button type="button" class="btn btn-sm btn-warning text-dark btn-edit shadow-sm px-2 rounded-pill flex-fill" data-id="' . $row->id . '" title="Revise/Resubmit Order"><i class="ph-bold ph-pencil-simple"></i></button>';
                        
                        return '<div class="d-flex flex-row gap-1 align-items-center w-100">' . $btnDetail . $btnEdit . '</div>';
                    }

                    // TAB PENDING (Logistic Orders)
                    $btnCancel = '<button type="button" class="btn btn-sm btn-danger text-white btn-cancel shadow-sm px-2 rounded-pill flex-fill" data-id="' . $row->id . '" title="Cancel Order"><i class="ph-bold ph-x-circle"></i></button>';
                    
                    return '<div class="d-flex flex-row gap-1 align-items-center w-100">' . $btnDetail . $btnCancel . '</div>';
                })
                ->rawColumns(['logistic_order_no', 'do_no', 'status_badge', 'action'])
                ->make(true);
        }

        $customers = Customer::orderBy('name', 'asc')->get();
        $distributors = Distributor::orderBy('name', 'asc')->get();
        return view('page.logistic_order.index', compact('customers', 'distributors'));
    }

    public function exportDeliveryNotes(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $distributors = $request->query('distributors');
        $apNumber = $request->query('ap_number', '-');

        if ((!empty($dateFrom) && empty($dateTo)) || (empty($dateFrom) && !empty($dateTo))) {
            return response()->json([
                'message' => 'The date filter must be filled in completely (From and To).',
            ], 422);
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            try {
                $from = Carbon::parse($dateFrom)->format('Y-m-d');
                $to = Carbon::parse($dateTo)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Invalid date format. Please use a valid date format (e.g., YYYY-MM-DD).',
                ], 422);
            }

            if ($from > $to) {
                return response()->json([
                    'message' => 'The From date cannot be later than the To date.',
                ], 422);
            }

            $export = new DeliveryNoteItemExport($from, $to, $distributors, $apNumber);
            $suffix = $from . '_to_' . $to;
        } else {
            $export = new DeliveryNoteItemExport(null, null, $distributors, $apNumber);
            $suffix = now()->format('Ymd_His');
        }

        return Excel::download($export, 'delivery_no_export_' . $suffix . '.xlsx');
    }

    public function exportDeliveryNotesPdf(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $distributors = $request->query('distributors');
        $query = LogisticOrderItem::with(['logisticOrder.distributor', 'logisticOrder.customer', 'logisticOrder.note']);
        $apNumber = $request->query('ap_number', '-');

        $statusTab = $request->query('tab', 'downloaded');
        $query->whereHas('logisticOrder.note', function ($q) use ($statusTab) {
            if ($statusTab === 'downloaded') {
                $q->where('status', 'Downloaded');
            } else {
                $q->where('status', 'Pending Download');
            }
        });

        $user = Auth::user();
        if (!$user->hasRole(['super-admin', 'sales-ka-approver'])) {
            $query->whereHas('logisticOrder', function($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereHas('logisticOrder', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('delivery_date', [$dateFrom, $dateTo]);
            });
        }

        if (!empty($distributors)) {
            $distArray = explode(',', $distributors);
            $query->whereHas('logisticOrder', function($q) use ($distArray) {
                $q->whereIn('distributor_id', $distArray);
            });
        }

        $searchCustomer = $request->query('search_customer');
        if (!empty($searchCustomer)) {
            $query->whereHas('logisticOrder.customer', function($q) use ($searchCustomer) {
                $q->where('name', 'LIKE', '%' . $searchCustomer . '%');
            });
        }

        $items = $query->get();

        $pdf = Pdf::loadView('pdf.logistic_export_pdf', compact('items', 'apNumber'))->setPaper('a4', 'landscape');
        return $pdf->download('logistic_order_report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function store(Request $request)
    {
        if ($request->has('items') && is_array($request->items)) {
            $cleanedItems = [];
            foreach ($request->items as $key => $item) {
                $cleanedItems[$key] = $item;

                if (isset($item['price_list'])) {
                    $cleanedItems[$key]['price_list'] = str_replace(['Rp', '.', ' '], '', $item['price_list']);
                }
            }
            $request->merge(['items' => $cleanedItems]);
        }

        $request->validate([
            'customer_id'         => 'required',
            'distributor_id'      => 'required',
            'customer_ship_to_id' => 'required',
            'delivery_date'       => 'required|date',
            'attention'           => 'nullable|string',
            'date_of_po'          => 'nullable|date',
            'items'               => 'required|array|min:1',
            'items.*.item_code'   => 'required|string',
            'items.*.item_name'   => 'required|string',
            'items.*.qty'         => 'required|numeric|min:1',
            'items.*.price_list'  => 'required|numeric|min:0',
            'items.*.pack_size'   => 'required|string',
        ], [
            'items.*.item_code.required'  => 'All item codes are required.',
            'items.*.item_name.required'  => 'All item names are required.',
            'items.*.qty.required'        => 'Quantity is required and cannot be 0.',
            'items.*.qty.min'             => 'Quantity must be at least 1.',
            'items.*.price_list.required' => 'Pricelist is required.',
        ]);

        try {
            DB::beginTransaction();
            $order = LogisticOrder::create([
                'distributor_id'      => $request->distributor_id,
                'customer_id'         => $request->customer_id,
                'customer_ship_to_id' => $request->customer_ship_to_id,
                'logistic_order_no'   => 0,
                'attention'           => $request->attention,
                'date_of_po'          => $request->date_of_po,
                'no_po'               => $request->no_po,
                'delivery_date'       => $request->delivery_date,
                'created_by'          => Auth::id(),
            ]);

            $order->update(['logistic_order_no' => $order->id]);
            $loNo = 'LO-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);

            foreach ($request->items as $item) {
                if (empty($item['qty']) || $item['qty'] <= 0) continue;
                LogisticOrderItem::create([
                    'logistic_order_id' => $order->id,
                    'ship_to_code'      => $request->ship_to_code_header,
                    'order_item_code'   => $item['item_code'] ?? '-',
                    'order_item_name'   => $item['item_name'],
                    'pack_size'         => $item['pack_size'] ?? null,
                    'order_quantity'    => $item['qty'],
                    'price_list'        => $item['price_list'] ?? 0,
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
                $orderEmail = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note', 'items'])->find($order->id);
                dispatch(new SendLogisticOrderEmailJob($orderEmail, $distributor->email, 'distributor'));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "Order ($loNo) & Note ($doNo) successfully created! Email sent to Distributor."], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to save: ' . $e->getMessage()], 500);
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

    public function publicDetail($id)
    {
        $order = LogisticOrder::with(['distributor', 'customer', 'customerShipTo.user', 'items', 'note'])->findOrFail($id);
        return view('page.logistic_order.links.public_detail', compact('order'));
    }

    public function publicDownload($id, $fromEmail = false)
    {
        $order = LogisticOrder::with(['note', 'customerShipTo.user', 'customer', 'distributor', 'items'])->findOrFail($id);
        $note = $order->note;

        if ($fromEmail && $note->status === 'Pending Download') {
            return redirect(URL::signedRoute('public.lo.detail', ['id' => $id]))
                ->with('warning', 'Please check the order details before downloading the DN document for the first time.');
        }

        if ($note->status === 'Pending Download' && $note->download_count == 0) {
            $note->update(['status' => 'Downloaded']);

            $salesUser = $order->customerShipTo->user ?? null;
            if ($salesUser) {
                Notification::send($salesUser, new SystemNotification(
                    "DN Document Has Been Downloaded",
                    "The delivery note document for order {$order->logistic_order_no} has been downloaded. Please review the order details and ensure timely delivery to the Customer {$order->customer->name}.",
                    "#",
                    "ph-printer",
                    "info"
                ));
                if (!empty($salesUser->email)) {
                    try {
                        Mail::to($salesUser->email)->queue(new LogisticOrderDistributorMail($order, 'sales'));
                    } catch (\Exception $e) {
                        Log::error('Failed to send email to Sales: ' . $e->getMessage());
                    }
                }
            }
        }

        $note->increment('download_count');

        $downloadedBy = Auth::check() ? Auth::user()->name . ' (Admin)' : 'Distributor (Public Link)';

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

        // return response($pdfContent, 200, [
        //     'Content-Type' => 'application/pdf',
        //     'Content-Disposition' => 'inline; filename="' . $pdfFileName . '"',
        // ]);
    }

    public function cancel(Request $request, $id)
        {
            $request->validate(['reason' => 'required|string']);
            
            $order = LogisticOrder::with(['note', 'distributor', 'customer'])->findOrFail($id);
            
            $order->update([
                'cancel_reason' => $request->reason,
                'canceled_at' => now(),
            ]);

            if ($order->note) {
                $order->note->update(['status' => 'Canceled']);
            }

            $distributorMail = $order->distributor->email ?? null;
            if ($distributorMail) {
                dispatch(new SendLogisticOrderEmailJob($order, $distributorMail, 'cancel'));
            }

            $atasanMail = Auth::user()->atasan->email ?? [EMAIL_ADDRESS]; 
            if ($atasanMail) {
                dispatch(new SendLogisticOrderEmailJob($order, $atasanMail, 'cancel'));
            }

            return response()->json(['success' => true, 'message' => 'Order canceled successfully!']);
        }

        public function update(Request $request, $id)
        {            
            $order = LogisticOrder::findOrFail($id);

            $order->update([
                'distributor_id'      => $request->distributor_id,
                'customer_id'         => $request->customer_id,
                'customer_ship_to_id' => $request->customer_ship_to_id,
                'attention'           => $request->attention,
                'date_of_po'          => $request->date_of_po,
                'no_po'               => $request->no_po,
                'delivery_date'       => $request->delivery_date,
                'cancel_reason'       => $request->reason,
                'canceled_at'         => $order->canceled_at,
            ]);

            $order->items()->delete();
            foreach ($request->items as $item) {
                if (empty($item['qty']) || $item['qty'] <= 0) continue;
                LogisticOrderItem::create([
                    'logistic_order_id' => $order->id,
                    'ship_to_code'      => $request->ship_to_code_header,
                    'order_item_code'   => $item['item_code'] ?? '-',
                    'order_item_name'   => $item['item_name'],
                    'pack_size'         => $item['pack_size'] ?? null,
                    'order_quantity'    => $item['qty'],
                    'price_list'        => str_replace(['Rp', '.', ' '], '', $item['price_list'] ?? 0),
                    'order_amount'      => str_replace(['Rp', '.', ' '], '', $item['amount']),
                ]);
            }

            if ($order->note) {
                $order->note->update([
                    'status' => 'Pending Download',
                    'download_count' => 0
                ]);
            }

            $distributor = Distributor::find($request->distributor_id);
            if ($distributor && $distributor->email) {
                $orderEmail = LogisticOrder::with(['distributor', 'customer', 'customerShipTo', 'note', 'items'])->find($order->id);
                dispatch(new SendLogisticOrderEmailJob($orderEmail, $distributor->email, 'distributor'));
            }

            return response()->json(['success' => true, 'message' => "Order data successfully revised! Email sent to Distributor."]);
        }
}
