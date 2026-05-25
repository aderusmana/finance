<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Logistic Notes | LO-{{ str_pad($order->logistic_order_no, 4, '0', STR_PAD_LEFT) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            color: #334155;
        }
        .brand-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            padding: 60px 0 80px 0;
            border-bottom: 5px solid #f59e0b;
        }
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-top: -60px;
            background: #ffffff;
        }
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0f172a;
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px 12px;
        }
        .table-custom td {
            vertical-align: middle;
            color: #334155;
            font-weight: 500;
            padding: 15px 12px;
        }
        .badge-custom {
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 50rem;
            letter-spacing: 0.5px;
        }
        .btn-download {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .card-info-box {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        .card-info-box:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>

    <div class="brand-header">
        <div class="container text-center">
            <h2 class="fw-bold mb-1 tracking-tight">PT Sinar Meadow International Indonesia</h2>
            <p class="text-white-50 mb-0 fs-5">Logistic & Delivery Portal</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-custom p-4 p-md-5">

                    @if(session('warning'))
                        <div class="alert alert-warning border-0 border-start border-4 border-warning shadow-sm mb-4 d-flex align-items-center">
                            <i class="ph-fill ph-warning-circle fs-4 me-3 text-warning"></i>
                            <div>{{ session('warning') }}</div>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success border-0 border-start border-4 border-success shadow-sm mb-4 d-flex align-items-center">
                            <i class="ph-fill ph-check-circle fs-4 me-3 text-success"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-4 mb-4 gap-3">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">Delivery Notes Details</h3>
                            <p class="text-muted mb-0 fs-6">
                                <i class="ph-bold ph-receipt text-primary"></i> LO-{{ str_pad($order->logistic_order_no, 4, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                        <div class="text-md-end">
                            @if($order->note->status == 'Downloaded')
                                <span class="badge badge-custom bg-success text-white">
                                    <i class="ph-bold ph-check-circle me-1"></i> Has been downloaded
                                    @if($order->note->download_count > 0)
                                        <span class="opacity-75 ms-1 fw-normal">({{ $order->note->download_count }}x)</span>
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-custom bg-warning text-dark">
                                    <i class="ph-bold ph-clock me-1"></i> Has not been downloaded
                                </span>
                            @endif
                            <p class="text-muted small mt-2 mb-0">Tgl. Kirim: <strong class="text-dark">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</strong></p>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="card-info-box p-4 rounded-4 h-100">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white p-2 rounded-3 shadow-sm me-3 text-primary border">
                                        <i class="ph-duotone ph-buildings fs-3"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Customer Info</h5>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Delivery Order No.</div>
                                    <div class="info-value">{{ $order->note->delivery_order_no ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Purchase Order No.</div>
                                    <div class="info-value">{{ $order->no_po ?? '-' }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Customer Name</div>
                                    <div class="info-value">{{ $order->customer->name }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Distributor Assigned</div>
                                    <div class="info-value">{{ $order->distributor->name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-info-box p-4 rounded-4 h-100">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white p-2 rounded-3 shadow-sm me-3 text-info border">
                                        <i class="ph-duotone ph-map-pin fs-3"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Destination (Ship To)</h5>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">{{ $order->customerShipTo->ship_to_code ?? '-' }}</div>
                                    <div class="info-value">{{ $order->customerShipTo->ship_to_name }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Complete Address</div>
                                    <div class="text-dark" style="font-size: 0.95rem; line-height: 1.5;">
                                        {{ $order->customerShipTo->ship_to_address_1 }}<br>
                                        @if($order->customerShipTo->ship_to_address_2) {{ $order->customerShipTo->ship_to_address_2 }}<br> @endif
                                        @if($order->customerShipTo->ship_to_address_3) {{ $order->customerShipTo->ship_to_address_3 }}<br> @endif
                                        <span class="fw-bold mt-2 d-inline-block text-primary">{{ $order->customerShipTo->ship_to_city }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 d-flex align-items-center text-dark">
                        <i class="ph-duotone ph-package me-2 text-primary fs-4"></i> List of Order Items
                    </h5>
                    <div class="table-responsive rounded-3 border mb-5 overflow-hidden">
                        <table class="table table-custom table-hover mb-0 border-0">
                            <thead>
                                <tr>
                                    <th width="8%" class="text-center border-0">No</th>
                                    <th width="20%" class="border-0">Item Code</th>
                                    <th width="52%" class="border-0">Item Name</th>
                                    <th width="20%" class="text-center border-0">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->items as $index => $item)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-light text-secondary border px-2 py-1">{{ $item->order_item_code }}</span></td>
                                    <td class="fw-bold">{{ $item->order_item_name }}</td>
                                    <td class="text-center fs-5 fw-bold text-primary">{{ $item->order_quantity }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">Item data not found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-slate-50 rounded-4 p-5 text-center border" style="background-color: #f8fafc;">
                        <div class="mb-3">
                            <i class="ph-duotone ph-printer text-success opacity-75" style="font-size: 3rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">Ready to process shipment?</h4>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">Download the official Delivery Notes (DO) document below to be taken by the driver to the Customer location.</p>

                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('public.lo.download', ['id' => $order->id, 'fromEmail' => 0]) }}" target="_blank" class="btn btn-download btn-lg text-white rounded-pill px-5 py-3 fw-bold shadow">
                            <i class="ph-bold ph-printer me-2"></i> Download DN
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
