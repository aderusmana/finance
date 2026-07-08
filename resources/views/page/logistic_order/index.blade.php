<x-app-layout>
    @section('title', 'Logistic Center')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .toggle-card {
            display: flex; gap: 1rem; align-items: center; padding: 1rem; border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #fbfdff); border: 1px solid #eef2ff;
            box-shadow: 0 4px 12px rgba(16, 24, 40, 0.06); cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .toggle-card .icon-box {
            width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; background: linear-gradient(135deg, #eef2ff, #e0f2ff);
            color: #0f172a; font-size: 1.35rem; box-shadow: inset 0 -2px 6px rgba(255, 255, 255, 0.6);
        }
        .toggle-card .title { margin: 0; font-size: 1.05rem; font-weight: 700; }
        .toggle-card .subtitle { margin: 0; color: #6b7280; font-size: 0.9rem; }
        .toggle-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); }
        .toggle-card.active { background: linear-gradient(90deg, #eef2ff, #e6f2ff); border: 1px solid #c7e0ff; box-shadow: 0 12px 30px rgba(14, 165, 233, 0.12); }
        .icon-box i { font-size: 1.3rem; color: #0ea5e9; }
        #btn-tab-pending.active { background: linear-gradient(90deg, #fff7ed, #fff1d6); border: 1px solid #ffdba4; box-shadow: 0 12px 30px rgba(245, 158, 11, 0.10); }
        #btn-tab-pending .icon-box { background: linear-gradient(135deg, #fff5eb, #ffedd5); color: #92400e; }
        #btn-tab-pending .icon-box i { color: #d97706; }
        #btn-tab-downloaded.active { background: linear-gradient(90deg, #ecfdf5, #d1fae5); border: 1px solid #86efac; box-shadow: 0 12px 30px rgba(34, 197, 94, 0.08); }
        #btn-tab-downloaded .icon-box { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #065f46; }
        #btn-tab-downloaded .icon-box i { color: #059669; }

        /* FIX LAYOUT SELECT2 MULTIPLE AGAR TIDAK AMBURADUL */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 38px;
            max-height: 85px; /* Membatasi tinggi agar tidak mendorong tombol */
            overflow-y: auto; /* Bisa discroll kalau isinya terlalu banyak */
        }

        @media (max-width: 767px) {
            .toggle-card { padding: 0.75rem; }
            .icon-box { width: 44px; height: 44px; }
            .title { font-size: 0.98rem; }
            .subtitle { font-size: 0.82rem; }
        }

        #btn-tab-canceled.active { background: linear-gradient(90deg, #fef2f2, #fee2e2); border: 1px solid #fca5a5; box-shadow: 0 12px 30px rgba(239, 68, 68, 0.08); }
        #btn-tab-canceled .icon-box { background: linear-gradient(135deg, #fef2f2, #fecaca); color: #991b1b; }
        #btn-tab-canceled .icon-box i { color: #dc2626; }
    </style>

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Center</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="/">
                        <i class="ph-duotone ph-monitor f-s-16"></i> Logistic Orders
                    </a>
                </li>
                <li class="f-s-14 f-w-500">Logistic Center</li>
            </ul>
        </div>
    </div>

    <div class="row m-1">
        <div class="col-12 ">
            <div class="info-banner bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 p-3 mb-4">
                <div class="info-banner-icon">
                    <i class="ph-bold ph-printer"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Logistics Document Management Center</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage new logistics orders and monitor Delivery Order archives that have been downloaded by Distributors through this panel.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TOGGLE MENU CARDS --}}
    <div class="row m-1 mb-3">
        <div class="col-md-4 mb-2 mb-md-0">
            <div class="toggle-card active" id="btn-tab-pending" onclick="switchTab('pending')">
                <div class="icon-box"><i class="ph-bold ph-file-text"></i></div>
                <div>
                    <h6 class="title text-black">Logistic Orders</h6>
                    <p class="subtitle">New orders waiting for DN process.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2 mb-md-0">
            <div class="toggle-card" id="btn-tab-downloaded" onclick="switchTab('downloaded')">
                <div class="icon-box"><i class="ph-bold ph-check-circle"></i></div>
                <div>
                    <h6 class="title text-black">Delivery Notes</h6>
                    <p class="subtitle">Downloaded and completed notes.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="toggle-card" id="btn-tab-canceled" onclick="switchTab('canceled')">
                <div class="icon-box"><i class="ph-bold ph-x-circle"></i></div>
                <div>
                    <h6 class="title text-black">Canceled Orders</h6>
                    <p class="subtitle">Orders that have been canceled.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. AREA TABEL --}}
    <div class="row m-1">
        <div class="col-12">
            <div class="table-panel">

                {{-- Bagian Header Tabel & Action Button (Dipisah dari Filter) --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                    <div id="table-title-area" class="mb-3 mb-md-0">
                        <h5 class="fw-bold text-dark mb-1" id="dynamic-table-title"><i
                                class="ph-fill ph-stack text-primary me-2"></i> Logistics Order List</h5>
                        <p class="text-muted mb-0 small" id="dynamic-table-subtitle">Showing data of orders with
                            Pending status.</p>
                    </div>

                    {{-- Tombol Buat Order (Hanya tampil di tab Pending) --}}
                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm" id="btn-create-order"
                        onclick="openModal()">
                        <i class="ph-bold ph-plus me-1"></i> New Order
                    </button>
                </div>

                {{-- AREA FILTER + EXPORT --}}
                <div id="dn-export-area" class="bg-light bg-opacity-50 border rounded-3 p-3 mb-4 shadow-sm">
                    <div class="row g-3 align-items-end">

                        {{-- FILTER DISTRIBUTOR (MULTIPLE) --}}
                        <div class="col-md-12 col-lg-5">
                            <label class="form-label small mb-1 text-dark fw-bold">Filter Distributor</label>
                            <select id="filter_distributor" class="form-select select2-filter" multiple="multiple" data-placeholder="Select distributors..." style="width: 100%;">
                                @if(isset($distributors))
                                    @foreach ($distributors as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- TANGGAL FROM --}}
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label small mb-1 text-dark fw-bold">From Date</label>
                            <input type="date" class="form-control" style="height: 38px;" id="dn_date_from">
                        </div>

                        {{-- TANGGAL TO --}}
                        <div class="col-6 col-md-3 col-lg-2">
                            <label class="form-label small mb-1 text-dark fw-bold">To Date</label>
                            <input type="date" class="form-control" style="height: 38px;" id="dn_date_to">
                        </div>

                        {{-- TOMBOL ACTION --}}
                        <div class="col-md-12 col-lg-3 d-flex gap-2 mt-3 mt-lg-0 justify-content-md-end flex-fill">
                            <button type="button" class="btn btn-light border px-3 rounded-3 fw-semibold shadow-sm" style="height: 38px;" id="btn-clear-dn-date" data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="ph-bold ph-arrows-clockwise text-secondary"></i>
                            </button>
                            
                            {{-- Wrapper untuk Export yang bisa di hide/show (Default: Sembunyi) --}}
                            <div id="export-action-buttons" class="d-none d-flex gap-2 flex-fill">
                                <button class="btn btn-danger px-3 rounded-3 fw-semibold shadow-sm flex-fill" style="height: 38px;" id="btn-export-pdf" data-bs-toggle="tooltip" title="Export PDF Report">
                                    <i class="ph-bold ph-file-pdf"></i> PDF
                                </button>
                                <button class="btn btn-success px-3 rounded-3 fw-semibold shadow-sm flex-fill" style="height: 38px;" id="btn-export-dn" data-bs-toggle="tooltip" title="Export Excel Data">
                                    <i class="ph-bold ph-file-xls"></i> Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Pending --}}
                <div id="wrapper-pending" class="table-responsive">
                    <table class="table table-hover w-100 align-middle" id="sampleTable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="18%">Order Info</th>
                                <th width="20%">Customer</th>
                                <th width="20%">Distributor</th>
                                <th width="17%">Ship To</th>
                                <th width="10%">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tabel Downloaded (Sembunyikan via d-none default) --}}
                <div id="wrapper-downloaded" class="table-responsive d-none">
                    <table class="table table-hover w-100 align-middle" id="historyTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="15%">DN Info</th>
                                <th width="20%">Customer</th>
                                <th width="20%">Distributor</th>
                                <th width="15%">Ship To</th>
                                <th width="15%">Status</th>
                                <th width="12%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tabel Canceled --}}
                <div id="wrapper-canceled" class="table-responsive d-none">
                    <table class="table table-hover w-100 align-middle" id="canceledTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th width="15%">DN Info</th>
                                <th width="20%">Customer</th>
                                <th width="20%">Distributor</th>
                                <th width="15%">Ship To</th>
                                <th width="15%">Status</th>
                                <th width="12%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL CREATE LOGISTIC ORDER --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="mainForm" class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header bg-primary text-white pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle">Create Logistic Order</h5>
                        <small class="text-white-50">Please select a Customer first.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div id="cancelInfoBanner" class="alert alert-danger m-3 d-none">
                    <strong><i class="ph-bold ph-warning-circle"></i> Canceled on:</strong> <span
                        id="canceledDateText"></span><br>
                    <strong>Reason:</strong> <span id="cancelReasonText"></span>
                </div>
                @csrf
                <input type="hidden" name="period" id="hidden_period">
                <input type="hidden" name="delivery_to" id="hidden_delivery_to">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i
                                            class="ph-bold ph-info"></i> Order Information</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Customer <span
                                                class="text-danger">*</span></label>
                                        <select name="customer_id" id="customer_id"
                                            class="form-select select2-custom" style="width: 100%;" required>
                                            <option value="">-- Select Customer --</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}">
                                                    {{ $c->customer_code ?? ($c->code ?? '-') }} - {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Distributor <span
                                                class="text-danger">*</span></label>
                                        <select name="distributor_id" id="distributor_id"
                                            class="form-select select2-custom" style="width: 100%;" disabled required>
                                            <option value="">-- Select Distributor --</option>
                                        </select>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Delivery Date <span class="text-danger">*</span></label>
                                            <input type="date" id="delivery_date" name="delivery_date" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">No. PO (Purchase Order)</label>
                                            <input type="text" id="no_po" name="no_po" class="form-control" placeholder="e.g: PO-2026-001">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Attention</label>
                                            <input type="text" id="attention" name="attention" class="form-control" placeholder="">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Date of PO</label>
                                            <input type="date" id="date_of_po" name="date_of_po" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i
                                            class="ph-bold ph-map-pin"></i> Customer Ship To</h6>
                                    <div class="mb-3">
                                        <select name="customer_ship_to_id" id="customer_ship_to_id"
                                            class="form-select select2-custom" style="width: 100%;" disabled required>
                                            <option value="">-- Select Shipping Address --</option>
                                        </select>
                                        <input type="hidden" name="ship_to_code_header" id="ship_to_code_header">
                                    </div>
                                    <div id="shipToDetailBox" class="bg-white border rounded-3 p-3 shadow-sm"
                                        style="display: none; border-left: 4px solid #3b82f6 !important;">
                                        <div class="row mb-2">
                                            <div class="col-5"><span class="text-muted small fw-bold d-block">Ship To
                                                    Code:</span><span class="text-dark fw-bold"
                                                    id="txt_ship_code">-</span></div>
                                            <div class="col-7"><span class="text-muted small fw-bold d-block">Ship To
                                                    Name:</span><span class="text-dark fw-bold"
                                                    id="txt_ship_name">-</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-12"><span class="text-muted small fw-bold d-block">Complete
                                                    Address:</span><span class="text-dark small d-block"
                                                    id="txt_address_1">-</span><span class="text-dark small d-block"
                                                    id="txt_address_2">-</span><span class="text-dark small d-block"
                                                    id="txt_address_3">-</span></div>
                                        </div>
                                        <div class="row border-top pt-2">
                                            <div class="col-5"><span
                                                    class="text-muted small fw-bold d-block">City:</span><span
                                                    class="text-dark small fw-semibold" id="txt_city">-</span></div>
                                            <div class="col-7"><span class="text-muted small fw-bold d-block">Sales
                                                    PIC:</span><span class="text-dark small"
                                                    id="txt_sales_name">-</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <h6 class="fw-bold text-primary mb-0"><i class="ph-bold ph-package"></i>
                                                Order Items</h6>
                                            <span class="bg-success text-white py-2 px-3 border rounded-3">Logistic
                                                Fee: <b id="active_fee_display">Rp 0 / ctn</b></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="addRow()"><i class="ph-bold ph-plus"></i> Add Item</button>
                                    </div>
                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-bordered table-hover align-middle mb-0"
                                            id="itemTable">
                                            <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 10;">
                                                <tr>
                                                    <th width="12%">Item Code <span class="text-danger">*</span></th>
                                                    <th width="23%">Item Name <span class="text-danger">*</span></th>
                                                    <th width="15%">Pack Size</th>
                                                    <th width="15%">Price List <span class="text-danger">*</span></th>
                                                    <th width="10%">Qty <span class="text-danger">*</span></th>
                                                    <th width="15%">Amount</th>
                                                    <th width="10%" class="text-center"><i class="ph-bold ph-gear"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="emptyRow">
                                                    <td colspan="6" class="text-center text-muted py-5">Select
                                                        Customer to load items automatically...</td>
                                                </tr>
                                            </tbody>
                                            <tfoot id="tableFooter" style="display: none;">
                                                <tr class="table-light">
                                                    <td colspan="4" class="text-end fw-bold align-middle">Total Amount Claim :</td>
                                                    <td>
                                                        <input type="text" id="grand_total_display" class="form-control form-control-sm bg-success bg-opacity-10 text-success fw-bolder" readonly value="Rp 0">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 pt-0 pb-3 pe-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">Submit Order</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL DETAIL PESANAN (WIDE LAYOUT - SIDE BY SIDE) --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-bottom-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 text-white border border-white border-opacity-25 d-flex align-items-center justify-content-center rounded-3 shadow-sm"
                            style="width: 50px; height: 50px; backdrop-filter: blur(4px);">
                            <i class="ph-bold ph-receipt fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bolder text-white mb-1">Logistics Document Details</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold shadow-sm"
                                    id="detail_lo_no">LO-0000</span>
                                <span id="detail_status"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div id="detail_cancel_banner" class="alert alert-danger mb-4 d-none border-0 shadow-sm" style="border-left: 4px solid #dc2626 !important;">
                        <div class="d-flex align-items-center mb-1">
                            <i class="ph-bold ph-warning-circle fs-5 me-2 text-danger"></i>
                            <strong class="text-danger">This order was canceled on <span id="detail_canceled_date"></span></strong>
                        </div>
                        <div class="ms-4 text-dark" style="font-size: 0.9rem;">
                            <strong>Reason:</strong> <span id="detail_cancel_reason">-</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-info me-2"></i>Document Information
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%" class="text-muted pb-2">Delivery No</td>
                                        <td class="fw-bold text-dark pb-2">: <span id="detail_do_no">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Purchase Order No.</td>
                                        <td class="fw-bold text-dark pb-2">: <span id="detail_no_po">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Attention</td>
                                        <td class="fw-bold text-dark pb-2">: <span id="detail_attention">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Date of PO</td>
                                        <td class="fw-semibold text-dark pb-2">: <span id="detail_date_of_po">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Delivery Date</td>
                                        <td class="fw-semibold text-dark pb-2">: <span
                                                id="detail_delivery_date">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Customer</td>
                                        <td class="fw-bold text-primary pb-2">: <span id="detail_customer">-</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-0">Distributor</td>
                                        <td class="fw-semibold text-dark pb-0">: <span
                                                id="detail_distributor">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm"
                                style="border-top: 4px solid #3b82f6 !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-primary text-uppercase mb-0"
                                        style="font-size: 0.8rem; letter-spacing: 1px;">
                                        <i class="ph-bold ph-map-pin me-2"></i>Delivery Destination
                                    </h6>
                                    <span
                                        class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-1 rounded"
                                        id="detail_ship_to_code">-</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 mt-2 fs-6" id="detail_ship_to_name">-</h6>
                                <p class="text-secondary mb-3 small text-truncate" id="detail_ship_to_address">-</p>

                                <div class="d-flex align-items-center gap-4 mt-auto border-top pt-3">
                                    <div>
                                        <span class="text-muted d-block fw-semibold"
                                            style="font-size: 0.7rem;">CITY</span>
                                        <span class="fw-bold text-dark" id="detail_ship_to_city">-</span>
                                    </div>
                                    <div class="border-start ps-4">
                                        <span class="text-muted d-block fw-semibold" style="font-size: 0.7rem;">SALES
                                            PIC</span>
                                        <span class="fw-bold text-dark" id="detail_sales_pic">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-7">
                            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-package me-2"></i>Item List
                                </h6>
                                <div class="table-responsive border rounded-3 overflow-hidden">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="10%" class="text-center text-muted py-3">NO</th>
                                                <th width="50%" class="text-muted py-3">ITEM NAME</th>
                                                <th width="20%" class="text-center text-muted py-3">PACK SIZE</th>
                                                <th width="20%" class="text-center text-muted py-3">QTY</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail_items_table">
                                            {{-- Data di-inject via JS --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-clock-counter-clockwise me-2"></i>Tracking Activity
                                </h6>
                                <div class="table-responsive border rounded-3 overflow-hidden">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50%" class="text-muted py-3 px-3">TIME</th>
                                                <th width="50%" class="text-muted py-3">EXECUTOR</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail_download_logs_table" style="font-size: 0.85rem;">
                                            {{-- Data di-inject via JS --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-top p-3 bg-white rounded-bottom-4 d-flex justify-content-between">
                    {{-- Tombol Download Admin yang akan muncul di modal detail --}}
                    <div id="admin-download-wrapper">
                        {{-- Akan diisi via Javascript --}}
                    </div>
                    <button type="button" class="btn btn-secondary px-5 py-2 rounded-pill fw-semibold shadow-sm"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            let sampleTable, historyTable, canceledTable;
            let activeLogisticFee = 0;
            let cachedShipTos = [];
            let activeTab = 'pending';

            function formatRupiah(angka) {
                let number_string = angka.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah ? 'Rp ' + rupiah : 'Rp 0';
            }

            function handlePriceInput(input) {
                let val = input.value.replace(/[^0-9]/g, '');
                input.value = val ? formatRupiah(val) : '';
                calculateRow(input);
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.amount-display').each(function() {
                    let val = $(this).val().replace(/[^0-9]/g, '');
                    if (val) {
                        grandTotal += parseInt(val);
                    }
                });
                $('#grand_total_display').val(formatRupiah(grandTotal));

                if ($('#itemTable tbody tr#emptyRow').length > 0) {
                    $('#tableFooter').hide();
                } else {
                    $('#tableFooter').show();
                }
            }

            // document.addEventListener("DOMContentLoaded", function() {
            //     var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            //     tooltipTriggerList.map(function(tooltipTriggerEl) {
            //         return new bootstrap.Tooltip(tooltipTriggerEl);
            //     });
            // });

            $(document).ready(function() {
                const exportDnBaseUrl = "{{ route('logistic-orders.export-dn') }}";
                const exportPdfBaseUrl = "{{ route('logistic-orders.export-pdf') ?? '#' }}";

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.select2-filter').select2({
                    theme: 'bootstrap-5',
                    allowClear: true
                });

                sampleTable = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('logistic-orders.index') }}",
                        data: function(d) {
                            d.tab = 'pending';
                            d.date_from = $('#dn_date_from').val();
                            d.date_to = $('#dn_date_to').val();
                            d.distributors = $('#filter_distributor').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'logistic_order_no',
                            name: 'logistic_order_no',
                            className: 'fw-bold text-primary'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer.name',
                            className: 'fw-semibold'
                        },
                        {
                            data: 'distributor_name',
                            name: 'distributor.name'
                        },
                        {
                            data: 'ship_to',
                            name: 'customerShipTo.ship_to_name'
                        },
                        {
                            data: 'status_badge',
                            name: 'note.status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ]
                });

                historyTable = $('#historyTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('logistic-orders.index') }}",
                        data: function(d) {
                            d.tab = 'downloaded';
                            d.date_from = $('#dn_date_from').val();
                            d.date_to = $('#dn_date_to').val();
                            d.distributors = $('#filter_distributor').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'do_no',
                            name: 'note.delivery_order_no',
                            className: 'fw-bold text-success'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer.name',
                            className: 'fw-semibold'
                        },
                        {
                            data: 'distributor_name',
                            name: 'distributor.name'
                        },
                        {
                            data: 'ship_to',
                            name: 'customerShipTo.ship_to_name'
                        },
                        {
                            data: 'status_badge',
                            name: 'note.status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ]
                });

                canceledTable = $('#canceledTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('logistic-orders.index') }}",
                        data: function(d) {
                            d.tab = 'canceled';
                            d.date_from = $('#dn_date_from').val();
                            d.date_to = $('#dn_date_to').val();
                            d.distributors = $('#filter_distributor').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'do_no', name: 'note.delivery_order_no', className: 'fw-bold text-danger' },
                        { data: 'customer_name', name: 'customer.name', className: 'fw-semibold' },
                        { data: 'distributor_name', name: 'distributor.name' },
                        { data: 'ship_to', name: 'customerShipTo.ship_to_name' },
                        { data: 'status_badge', name: 'note.status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ]
                });

                // $('#historyTable').on('draw.dt', function() {
                //     $('[data-bs-toggle="tooltip"]').tooltip();
                // });

                function initTooltips() {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        var existingInstance = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                        if (existingInstance) existingInstance.dispose();
                        
                        return new bootstrap.Tooltip(tooltipTriggerEl, {
                            trigger: 'hover'
                        });
                    });
                }

                initTooltips();

                $('#sampleTable, #historyTable').on('preDraw.dt', function() {
                    $('.tooltip').remove(); 
                });

                $('#sampleTable, #historyTable').on('draw.dt', function() {
                    initTooltips();
                });

                $(document).on('click', '[data-bs-toggle="tooltip"]', function() {
                    var tooltipInstance = bootstrap.Tooltip.getInstance(this);
                    if (tooltipInstance) {
                        tooltipInstance.hide();
                    }
                    $('.tooltip').remove();
                });

                $('#dn_date_from, #dn_date_to, #filter_distributor').on('change', function() {
                    if (activeTab === 'downloaded') historyTable.ajax.reload(null, false);
                    else if (activeTab === 'canceled') canceledTable.ajax.reload(null, false);
                    else sampleTable.ajax.reload(null, false);
                });

                $('#btn-clear-dn-date').on('click', function() {
                    $('#dn_date_from').val('');
                    $('#dn_date_to').val('');
                    $('#filter_distributor').val(null).trigger('change');
                    if (activeTab === 'downloaded') historyTable.ajax.reload(null, false);
                    else if (activeTab === 'canceled') canceledTable.ajax.reload(null, false);
                    else sampleTable.ajax.reload(null, false);
                });

                function executeExport(baseUrl) {
                    const from = $('#dn_date_from').val();
                    const to = $('#dn_date_to').val();
                    const dists = $('#filter_distributor').val();
                    const currentTab = activeTab;

                    let url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'tab=' + currentTab;

                    if (from && to) {
                        if (from > to) {
                            Swal.fire('Warning', 'From date cannot be later than To date.', 'warning');
                            return;
                        }
                        url += '&date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
                    }

                    if (dists && dists.length > 0) {
                        url += '&distributors=' + encodeURIComponent(dists.join(','));
                    }

                    Swal.fire({
                        title: 'Export Report',
                        text: 'Please enter the AP Number for this export:',
                        input: 'text',
                        inputPlaceholder: 'e.g: KA-SMII-2026-001',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ph-bold ph-download-simple me-1"></i> Export',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary rounded-pill px-4 fw-bold',
                            cancelButton: 'btn btn-light rounded-pill px-4 fw-bold border'
                        },
                        buttonsStyling: false,
                        inputValidator: (value) => {
                            if (!value) {
                                return 'AP Number is required to proceed with the export.';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const apNumber = result.value;
                            Swal.fire({
                                title: 'Processing Export',
                                html: '<span style="color: #64748b; font-size: 0.95rem;">Please wait while we process your export request.</span>',
                                allowOutsideClick: false, 
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            let url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'tab=' + currentTab + '&ap_number=' + encodeURIComponent(apNumber);

                            if (from && to) {
                                url += '&date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to);
                            }
                            
                            if (dists && dists.length > 0) {
                                url += '&distributors=' + encodeURIComponent(dists.join(','));
                            }

                            window.location.href = url;

                            setTimeout(() => {
                                Swal.close();
                            }, 3000); 
                        }
                    });
                }

                $('#btn-export-dn').on('click', function() { executeExport(exportDnBaseUrl); });
                $('#btn-export-pdf').on('click', function() { executeExport(exportPdfBaseUrl); });

                $('.select2-custom').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    allowClear: true
                });

                $('#customer_id').on('change', function() {
                    let custId = $(this).val();
                    let distSelect = $('#distributor_id'),
                        shipSelect = $('#customer_ship_to_id');

                    distSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled',
                        true).trigger('change');
                    shipSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled',
                        true).trigger('change');
                    $('#itemTable tbody').empty();
                    $('#shipToDetailBox').hide();
                    activeLogisticFee = 0;
                    $('#active_fee_display').text('Rp 0 / ctn');

                    if (custId) {
                        $.get("{{ url('/logistic-orders/customer-dependencies') }}/" + custId, function(res) {
                            distSelect.empty().append(
                                '<option value="">-- Select Distributor --</option>').prop(
                                'disabled', false);
                            $.each(res.distributors, function(key, d) {
                                distSelect.append('<option value="' + d.id + '">' + d.code +
                                    ' - ' + d.name + '</option>');
                            });
                            distSelect.trigger('change');

                            cachedShipTos = res.ship_to_list;
                            shipSelect.empty().append(
                                '<option value="">-- Select Shipping Address --</option>').prop(
                                'disabled', false);
                            $.each(res.ship_to_list, function(key, s) {
                                shipSelect.append('<option value="' + s.id + '" data-code="' + s
                                    .ship_to_code + '">' + s.ship_to_code + ' - ' + s
                                    .ship_to_name + '</option>');
                            });
                            shipSelect.trigger('change');

                            if (editDataBuffer && editDataBuffer.customer_id == custId) {
                            $('#distributor_id').val(editDataBuffer.distributor_id).trigger('change');
                            $('#customer_ship_to_id').val(editDataBuffer.customer_ship_to_id).trigger('change');
                            
                            $('#itemTable tbody').empty();
                            $.each(editDataBuffer.items, function(key, item) {
                                addRow(item.order_item_code, item.order_item_name, item.price_list, item.order_quantity, item.pack_size);
                            });
                            editDataBuffer = null;
                        } 
                        else if (res.items && res.items.length > 0) {
                            $.each(res.items, function(key, item) {
                                addRow(item.item_code || '', item.item_name, item.price_list || 0, item.quantity || 1, '');
                            });
                        } else {
                            addRow();
                        }
                        });
                    } else {
                        $('#itemTable tbody').html(
                            '<tr id="emptyRow"><td colspan="6" class="text-center text-muted py-5">Select Customer to load items automatically...</td></tr>'
                        );
                    }
                });

                $('#distributor_id').on('change', function() {
                    let distId = $(this).val(),
                        custId = $('#customer_id').val();
                    if (distId && custId) {
                        $.get("{{ url('/logistic-orders/fee') }}/" + distId + "/" + custId, function(res) {
                            activeLogisticFee = res.logistic_fee;
                            $('#active_fee_display').text(formatRupiah(activeLogisticFee) + ' / ctn');
                            $('.qty-input').trigger('input');
                        });
                    }
                });

                $('#customer_ship_to_id').on('change', function() {
                    let id = $(this).val(),
                        code = $(this).find(':selected').data('code');
                    $('#ship_to_code_header').val(code);
                    if (id) {
                        let st = cachedShipTos.find(x => x.id == id);
                        if (st) {
                            $('#txt_ship_code').text(st.ship_to_code || '-');
                            $('#txt_ship_name').text(st.ship_to_name || '-');
                            $('#txt_address_1').text(st.ship_to_address_1 || '-');
                            if (st.ship_to_address_2) {
                                $('#txt_address_2').text(st.ship_to_address_2).show();
                            } else {
                                $('#txt_address_2').hide();
                            }
                            if (st.ship_to_address_3) {
                                $('#txt_address_3').text(st.ship_to_address_3).show();
                            } else {
                                $('#txt_address_3').hide();
                            }
                            $('#txt_city').text(st.ship_to_city || '-');
                            $('#txt_sales_name').text((st.user ? st.user.nik : '-') + ' - ' + (st.user ? st.user
                                .name : '-'));
                            $('#shipToDetailBox').slideDown();
                        }
                    } else {
                        $('#shipToDetailBox').slideUp();
                    }
                });

                $('#mainForm').on('submit', function(e) {
                    e.preventDefault();

                    let totalItems = 0;
                    let invalidQty = false;

                    $('.qty-input').each(function() {
                        let val = parseFloat($(this).val());
                        if (isNaN(val) || val < 1) {
                            invalidQty = true;
                        }
                        totalItems++;
                    });

                    if (totalItems === 0) {
                        Swal.fire('Warning', 'At least 1 item must be filled.', 'warning');
                        return;
                    }

                    if (invalidQty) {
                        Swal.fire('Warning', 'Item quantity must be at least 1.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Confirm Order Submission',
                        html: `<div class="text-start">This order will generate a document number and send a notification email.</div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Submit Now',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('.price-input').each(function() {
                                let raw = $(this).val().replace(/[^0-9]/g, '');
                                $(this).data('formatted', $(this).val());
                                $(this).val(raw);
                            });

                            let formData = $(this).serialize();

                            $('.price-input').each(function() {
                                $(this).val($(this).data('formatted'));
                            });

                            Swal.fire({
                                title: 'Saving Order...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            let orderId = $('#edit_order_id').val();
                            let submitUrl = orderId ? "{{ url('logistic-orders') }}/" + orderId : "{{ route('logistic-orders.store') }}";

                            $.ajax({
                                url: submitUrl,
                                method: "POST",
                                data: formData,
                                success: function(res) {
                                    $('#modalForm').modal('hide');
                                    sampleTable.ajax.reload();
                                    Swal.fire('Success!', res.message, 'success');
                                },
                                error: function() {
                                    Swal.fire('Failed', 'An error occurred or data is incomplete.',
                                        'error');
                                }
                            });
                        }
                    });
                });

                $(document).on('click', '.btn-detail', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.get("{{ url('/logistic-orders') }}/" + id, function(data) {
                        Swal.close();
                        $('#detail_lo_no').text('LO-' + String(data.id).padStart(4, '0'));

                        if (data.note) {
                            $('#detail_do_no').text(data.note.delivery_order_no || '-');
                            
                            let statusHtml = '';
                            if (data.note.status === 'Canceled') {
                                statusHtml = `<span class="badge bg-danger text-white border border-danger px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-x-circle me-1"></i> Canceled</span>`;
                            } else if (data.note.status === 'Downloaded') {
                                statusHtml = `<span class="badge bg-success text-white border border-success px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-check-circle me-1"></i> Download</span>`;
                            } else {
                                statusHtml = `<span class="badge bg-warning text-dark border border-warning px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-clock me-1"></i> Pending</span>`;
                            }
                            $('#detail_status').html(statusHtml);

                            if (data.note.status === 'Canceled') {
                                let cancelDate = data.canceled_at ? new Date(data.canceled_at).toLocaleString('id-ID') : '-';
                                $('#detail_canceled_date').text(cancelDate);
                                $('#detail_cancel_reason').text(data.cancel_reason || '-');
                                $('#detail_cancel_banner').removeClass('d-none');
                            } else {
                                $('#detail_cancel_banner').addClass('d-none');
                            }
                        }

                        $('#detail_delivery_date').text(data.delivery_date || '-');
                        $('#detail_customer').text(data.customer ? data.customer.name : '-');
                        $('#detail_distributor').text(data.distributor ? data.distributor.name : '-');
                        $('#detail_no_po').text(data.no_po || '-');
                        $('#detail_attention').text(data.attention || '-');
                        $('#detail_date_of_po').text(data.date_of_po || '-');

                        if (data.customer_ship_to) {
                            let st = data.customer_ship_to;
                            $('#detail_ship_to_code').text(st.ship_to_code || '-');
                            $('#detail_ship_to_name').text(st.ship_to_name || '-');
                            $('#detail_ship_to_address').text(st.ship_to_address_1 + ' ' + (st
                                .ship_to_address_2 || ''));
                            $('#detail_ship_to_city').text(st.ship_to_city || '-');
                            $('#detail_sales_pic').text(st.user ? st.user.name : '-');
                        }

                        let tbody = $('#detail_items_table');
                        tbody.empty();
                        if (data.items && data.items.length > 0) {
                            $.each(data.items, function(index, item) {
                                tbody.append(`
                                <tr>
                                    <td class="text-center text-muted py-2">${index + 1}</td>
                                    <td class="py-2">
                                        <div class="fw-bold text-dark">${item.order_item_name}</div>
                                        <small class="text-muted">${item.order_item_code || '-'}</small>
                                    </td>
                                    <td class="text-center fw-bold text-primary py-2">${item.pack_size}</td>
                                    <td class="text-center fw-bold text-primary py-2">${item.order_quantity}</td>
                                </tr>
                            `);
                            });
                        } else {
                            tbody.append(
                                '<tr><td colspan="4" class="text-center text-muted py-4">No item details available.</td></tr>'
                            );
                        }

                        let logTbody = $('#detail_download_logs_table');
                        logTbody.empty();
                        if (data.download_logs && data.download_logs.length > 0) {
                            $.each(data.download_logs, function(index, log) {
                                let d = new Date(log.created_at);
                                let dateStr = d.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                                let timeStr = d.toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });

                                let badgeClass = log.downloaded_by.includes('Admin') ?
                                    'bg-primary' : 'bg-success';

                                logTbody.append(`
                                <tr>
                                    <td class="py-2 px-3">
                                        <div class="fw-bold text-dark">${dateStr}</div>
                                        <div class="text-muted small">${timeStr}</div>
                                    </td>
                                    <td class="py-2">
                                        <span class="badge ${badgeClass} bg-opacity-10 ${badgeClass.replace('bg-', 'text-')} border-0 px-2 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                            <i class="ph-fill ph-user-circle me-1"></i> ${log.downloaded_by}
                                        </span>
                                    </td>
                                </tr>
                            `);
                            });
                        } else {
                            logTbody.append(
                                '<tr><td colspan="2" class="text-center text-muted py-4 small italic">No document download activities available.</td></tr>'
                            );
                        }

                        let downloadUrl = "{{ url('/public/lo/download') }}/" + data.id +
                            "/0";
                        if (data.note && data.note.status === 'Downloaded') {
                            $('#admin-download-wrapper').html(`
                            <a href="${data.download_url}" target="_blank" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="ph-bold ph-printer me-2"></i> Download DN & PO (Admin)
                            </a>
                        `);
                        } else {
                            $('#admin-download-wrapper').empty();
                        }

                        setTimeout(function() {
                            Swal.close();
                            $('#modalDetail').modal('show');
                        }, 300);
                    });
                });

                let editDataBuffer = null;

                $(document).on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    openEditModal(id);
                });

                $(document).on('click', '.btn-cancel', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Cancel Order?',
                        text: "Please provide a reason for cancellation:",
                        input: 'textarea',
                        inputPlaceholder: 'Type your reason here...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="ph-bold ph-paper-plane-tilt"></i> Submit Cancel',
                        inputValidator: (value) => {
                            if (!value) return 'Reason is required!';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            $.ajax({
                                url: "{{ url('/logistic-orders') }}/" + id + "/cancel",
                                method: "POST",
                                data: { reason: result.value },
                                success: function(res) {
                                    if (typeof historyTable !== 'undefined') historyTable.ajax.reload(null, false);
                                    if (typeof sampleTable !== 'undefined') sampleTable.ajax.reload(null, false);
                                    if (typeof canceledTable !== 'undefined') canceledTable.ajax.reload(null, false);

                                    Swal.fire({
                                        title: 'Success Cancelled!',
                                        text: 'Data has been cancelled. You can revise the data by clicking the "Revise Data" button.',
                                        icon: 'success',
                                        showCancelButton: true,
                                        confirmButtonText: '<i class="ph-bold ph-pencil-simple text-white me-1"></i> Revise Data',
                                        cancelButtonText: 'Close',
                                        reverseButtons: true,
                                        customClass: { 
                                            confirmButton: 'btn btn-primary px-4 ms-2 shadow-sm', 
                                            cancelButton: 'btn btn-secondary px-4 me-2 shadow-sm' 
                                        },
                                        buttonsStyling: false
                                    }).then((res2) => {
                                        if (res2.isConfirmed) {
                                            openEditModal(id);
                                        }
                                    });
                                }
                            });
                        }
                    });
                });

                function openEditModal(id) {
                    Swal.fire({ title: 'Loading Data...', didOpen: () => Swal.showLoading() });
                    $.get("{{ url('/logistic-orders') }}/" + id, function(data) {
                        Swal.close();
                        $('#mainForm')[0].reset();
                        $('#formMethod').val('PUT');
                        $('#edit_order_id').val(data.id);
                        $('#modalTitle').text('Revise Logistic Order');
                        $('#btnSubmit').text('Submit Revision');
                        
                        $('#cancelInfoBanner').removeClass('d-none');
                        $('#canceledDateText').text(new Date(data.canceled_at).toLocaleString('id-ID'));
                        $('#cancelReasonText').text(data.cancel_reason);

                        $('#delivery_date').val(data.delivery_date);
                        $('#no_po').val(data.no_po);
                        $('#attention').val(data.attention);
                        $('#date_of_po').val(data.date_of_po);

                        editDataBuffer = data; 
                        $('#customer_id').val(data.customer_id).trigger('change');

                        $('#modalForm').modal('show');
                    });
                }

                $('#modalDetail').on('hidden.bs.modal', function () {
                    if (sampleTable) {
                        sampleTable.columns.adjust().draw();
                    }
                    if (historyTable) {
                        historyTable.columns.adjust().draw();
                    }
                });
            });

            function switchTab(tabName) {
                activeTab = tabName;
                $('.toggle-card').removeClass('active');
                $('#btn-tab-' + tabName).addClass('active');

                $('#wrapper-pending, #wrapper-downloaded, #wrapper-canceled').addClass('d-none');
                $('#wrapper-' + tabName).removeClass('d-none');

                if (tabName === 'pending') {
                    $('#dynamic-table-title').html('<i class="ph-fill ph-stack text-primary me-2"></i> List Logistic Order');
                    $('#dynamic-table-subtitle').text('Displaying orders with Pending status.');
                    $('#btn-create-order').removeClass('d-none');
                    $('#export-action-buttons').addClass('d-none');
                    sampleTable.ajax.reload(null, false);
                } else if (tabName === 'downloaded') {
                    $('#dynamic-table-title').html('<i class="ph-fill ph-check-circle text-success me-2"></i> Archive Delivery No');
                    $('#dynamic-table-subtitle').text('Displaying Delivery Notes that have been downloaded (Completed).');
                    $('#btn-create-order').addClass('d-none');
                    $('#export-action-buttons').removeClass('d-none');
                    historyTable.ajax.reload(null, false);
                } else {
                    $('#dynamic-table-title').html('<i class="ph-fill ph-x-circle text-danger me-2"></i> Canceled Orders');
                    $('#dynamic-table-subtitle').text('Displaying orders that have been canceled.');
                    $('#btn-create-order').addClass('d-none');
                    $('#export-action-buttons').addClass('d-none');
                    canceledTable.ajax.reload(null, false);
                }
            }

            function addRow(code = '', name = '', price = 0, qty = 1, packSize = '') {
                $('#emptyRow').remove();
                let index = Date.now() + Math.floor(Math.random() * 1000);
                let initialTotal = (qty > 0 && price > 0) ? formatRupiah(qty * price) : 'Rp 0';
                let displayPrice = (price > 0) ? formatRupiah(price) : '';
                let row = `
                <tr>
                    <td><input type="text" name="items[${index}][item_code]" class="form-control form-control-sm" value="${code}" placeholder="Item Code" required></td>
                    <td><input type="text" name="items[${index}][item_name]" class="form-control form-control-sm" value="${name}" placeholder="Item Name" required></td>
                    <td><input type="text" name="items[${index}][pack_size]" class="form-control form-control-sm" value="${packSize}" placeholder="e.g. 10 x 10's"></td>
                    <td><input type="text" name="items[${index}][price_list]" class="form-control form-control-sm price-input" value="${displayPrice}" placeholder="Rp 0" oninput="handlePriceInput(this)" required></td>
                    <td><input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input" value="${qty}" placeholder="1" min="1" oninput="calculateRow(this)" required></td>
                    <td><input type="text" name="items[${index}][amount]" class="form-control form-control-sm bg-light amount-display fw-bold text-primary" readonly value="${initialTotal}"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="ph-bold ph-trash"></i></button></td>
                </tr>
            `;
                $('#itemTable tbody').append(row);
                calculateGrandTotal();
            }

            function removeRow(btn) {
                $(btn).closest('tr').remove();
                if ($('#itemTable tbody tr').length === 0) $('#itemTable tbody').append(
                    '<tr id="emptyRow"><td colspan="6" class="text-center text-muted py-5">Table is empty.</td></tr>');

                calculateGrandTotal();
            }

            function calculateRow(input) {
                let row = $(input).closest('tr');
                let qty = parseFloat(row.find('.qty-input').val()) || 0;
                let priceStr = row.find('.price-input').val().replace(/[^0-9]/g, '');
                let price = parseFloat(priceStr) || 0;
                
                row.find('.amount-display').val(formatRupiah(qty * price));

                calculateGrandTotal();
            }

            function openModal() {
                $('#formMethod').val('POST');
                $('#edit_order_id').val('');
                $('#modalTitle').text('Create Logistic Order');
                $('#btnSubmit').text('Submit Order');
                $('#cancelInfoBanner').addClass('d-none');
                $('#mainForm')[0].reset();
                let today = new Date().toISOString().split('T')[0];
                let currentMonth = new Date().toLocaleString('id-ID', {
                    month: 'long',
                    year: 'numeric'
                });
                $('#delivery_date').val(today);
                $('#no_po').val('');
                $('#attention').val('');
                $('#date_of_po').val(today);
                $('#hidden_delivery_to').val(today);
                $('#hidden_period').val(currentMonth);
                $('#customer_id').val('').trigger('change');
                $('#distributor_id').empty().append('<option value="">-- Select Distributor --</option>').prop('disabled', true)
                    .trigger('change');
                $('#customer_ship_to_id').empty().append('<option value="">-- Select Address --</option>').prop('disabled', true)
                    .trigger('change');
                $('#shipToDetailBox').hide();
                activeLogisticFee = 0;
                $('#active_fee_display').text('Rp 0 / ctn');
                $('#itemTable tbody').html(
                    '<tr id="emptyRow"><td colspan="6" class="text-center text-muted py-5">Please select a customer first.</td></tr>'
                );

                calculateGrandTotal();
                $('#modalForm').modal('show');
            }
        </script>
    @endpush
</x-app-layout>
