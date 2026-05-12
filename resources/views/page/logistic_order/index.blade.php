<x-app-layout>
    @section('title', 'Logistic Center')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .toggle-card {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
            border: 1px solid #eef2ff;
            box-shadow: 0 4px 12px rgba(16, 24, 40, 0.06);
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .toggle-card .icon-box {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #eef2ff, #e0f2ff);
            color: #0f172a;
            font-size: 1.35rem;
            box-shadow: inset 0 -2px 6px rgba(255, 255, 255, 0.6);
        }

        .toggle-card .title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .toggle-card .subtitle {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .toggle-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .toggle-card.active {
            background: linear-gradient(90deg, #eef2ff, #e6f2ff);
            border: 1px solid #c7e0ff;
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.12);
        }

        .icon-box i {
            font-size: 1.3rem;
            color: #0ea5e9;
        }

        /* Distinct colors per card */
        #btn-tab-pending.active {
            background: linear-gradient(90deg, #fff7ed, #fff1d6);
            border: 1px solid #ffdba4;
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.10);
        }

        #btn-tab-pending .icon-box {
            background: linear-gradient(135deg, #fff5eb, #ffedd5);
            color: #92400e;
        }

        #btn-tab-pending .icon-box i {
            color: #d97706;
        }

        #btn-tab-downloaded.active {
            background: linear-gradient(90deg, #ecfdf5, #d1fae5);
            border: 1px solid #86efac;
            box-shadow: 0 12px 30px rgba(34, 197, 94, 0.08);
        }

        #btn-tab-downloaded .icon-box {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #065f46;
        }

        #btn-tab-downloaded .icon-box i {
            color: #059669;
        }

        @media (max-width: 767px) {
            .toggle-card {
                padding: 0.75rem;
            }

            .icon-box {
                width: 44px;
                height: 44px;
            }

            .title {
                font-size: 0.98rem;
            }

            .subtitle {
                font-size: 0.82rem;
            }
        }
    </style>

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Center</h4>
            {{-- <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph ph-shopping-cart f-s-16"></i>
                        Transaction</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Logistic Center</a></li>
            </ul> --}}
        </div>
    </div>

    {{-- 1. INFO BANNER --}}
    <div class="row m-1">
        <div class="col-12 ">
            <div class="info-banner bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 p-3 mb-4">
                <div class="info-banner-icon">
                    <i class="ph-bold ph-printer"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Pusat Kelola Dokumen Logistik</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola pesanan logistik baru dan pantau arsip
                        Surat Jalan (Delivery Order) yang telah diunduh oleh Distributor melalui panel ini.</p>
                </div>
            </div>
        </div>
    </div>



    {{-- 2. TOGGLE MENU CARDS --}}
    <div class="row m-1 g-3 mb-4">
        {{-- Card: Logistic Orders (Pending) --}}
        <div class="col-md-6">
            <div class="toggle-card active" id="btn-tab-pending" onclick="switchTab('pending')">
                <div class="icon-box"><i class="ph-bold ph-file-text"></i></div>
                <div>
                    <h6 class="title text-black">Logistic Orders</h6>
                    <p class="subtitle">Pesanan baru yang menunggu proses cetak DN.</p>
                </div>
            </div>
        </div>

        {{-- Card: Delivery Notes (Selesai) --}}
        <div class="col-md-6">
            <div class="toggle-card" id="btn-tab-downloaded" onclick="switchTab('downloaded')">
                <div class="icon-box"><i class="ph-bold ph-check-circle"></i></div>
                <div>
                    <h6 class="title text-black">Delivery No</h6>
                    <p class="subtitle">Arsip Surat Jalan yang telah diunduh (Selesai).</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. AREA TABEL --}}
    <div class="row m-1">
        <div class="col-12">
            <div class="table-panel">

                {{-- Bagian Header Tabel (Dinamis) --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div id="table-title-area">
                        <h5 class="fw-bold text-dark mb-1" id="dynamic-table-title"><i
                                class="ph-fill ph-stack text-primary me-2"></i> Daftar Logistic Order</h5>
                        <p class="text-muted mb-0 small" id="dynamic-table-subtitle">Menampilkan data pesanan dengan
                            status Pending.</p>
                    </div>

                    {{-- Tombol Buat Order (Hanya tampil di tab Pending) --}}
                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm" id="btn-create-order"
                        onclick="openModal()">
                        <i class="ph-bold ph-plus me-1"></i> Buat Order Baru
                    </button>

                    {{-- Filter + Export (Hanya tampil di tab Delivery No) --}}
                    <div id="dn-export-area" class="d-none">
                        <div class="d-flex flex-wrap justify-content-end align-items-end gap-2">
                            <div>
                                <label class="form-label small mb-1 text-muted">From</label>
                                <input type="date" class="form-control form-control-sm" id="dn_date_from">
                            </div>
                            <div>
                                <label class="form-label small mb-1 text-muted">To</label>
                                <input type="date" class="form-control form-control-sm" id="dn_date_to">
                            </div>
                            <button class="btn btn-success px-2 py-2 rounded-pill fw-semibold shadow-sm"
                                id="btn-export-dn">
                                <i class="ph-bold ph-file-xls me-1"></i> Export Excel
                            </button>
                            <button type="button" class="btn btn-danger px-2 py-2 rounded-pill fw-semibold shadow-sm"
                                id="btn-clear-dn-date">
                                <i class="ph-bold ph-x me-1"></i> Clear
                            </button>
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
                        <small class="text-white-50">Silakan pilih Customer terlebih dahulu.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                            class="ph-bold ph-info"></i> Informasi Order</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Customer <span
                                                class="text-danger">*</span></label>
                                        <select name="customer_id" id="customer_id"
                                            class="form-select select2-custom" style="width: 100%;" required>
                                            <option value="">-- Pilih Customer --</option>
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
                                            <option value="">-- Pilih Distributor --</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Delivery Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="delivery_date" id="delivery_date"
                                            class="form-control" required>
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
                                            <option value="">-- Pilih Alamat Pengiriman --</option>
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
                                            <div class="col-12"><span class="text-muted small fw-bold d-block">Alamat
                                                    Lengkap:</span><span class="text-dark small d-block"
                                                    id="txt_address_1">-</span><span class="text-dark small d-block"
                                                    id="txt_address_2">-</span><span class="text-dark small d-block"
                                                    id="txt_address_3">-</span></div>
                                        </div>
                                        <div class="row border-top pt-2">
                                            <div class="col-5"><span
                                                    class="text-muted small fw-bold d-block">Kota:</span><span
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
                                            onclick="addRow()"><i class="ph-bold ph-plus"></i> Tambah Manual</button>
                                    </div>
                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-bordered table-hover align-middle mb-0"
                                            id="itemTable">
                                            <thead class="table-light position-sticky top-0 shadow-sm"
                                                style="z-index: 10;">
                                                <tr>
                                                    <th width="20%">Item Code <span class="text-danger">*</span>
                                                    </th>
                                                    <th width="35%">Item Name <span class="text-danger">*</span>
                                                    </th>
                                                    <th width="15%">Qty <span class="text-danger">*</span></th>
                                                    <th width="20%">Amount</th>
                                                    <th width="10%" class="text-center"><i
                                                            class="ph-bold ph-gear"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="emptyRow">
                                                    <td colspan="5" class="text-center text-muted py-5">Pilih
                                                        Customer untuk memuat item otomatis...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 pt-0 pb-3 pe-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">Submit Order</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL DETAIL PESANAN (WIDE LAYOUT - SIDE BY SIDE) --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        {{-- Gunakan modal-xl untuk memberikan ruang lebar ke samping --}}
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">

                {{-- Header: Blue Gradient Theme --}}
                <div class="modal-header border-bottom-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 text-white border border-white border-opacity-25 d-flex align-items-center justify-content-center rounded-3 shadow-sm"
                            style="width: 50px; height: 50px; backdrop-filter: blur(4px);">
                            <i class="ph-bold ph-receipt fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bolder text-white mb-1">Detail Dokumen Logistik</h5>
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

                {{-- Body: Background soft gray --}}
                <div class="modal-body p-4" style="background-color: #f8fafc;">

                    {{-- SEKSI ATAS: Info Dokumen & Tujuan (Tetap Berdampingan) --}}
                    <div class="row g-3 mb-4">
                        {{-- Kiri: Info Utama --}}
                        <div class="col-md-5">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-info me-2"></i>Informasi Dokumen
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%" class="text-muted pb-2">Delivery No</td>
                                        <td class="fw-bold text-dark pb-2">: <span id="detail_do_no">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Tgl. Kirim</td>
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

                        {{-- Kanan: Ship To --}}
                        <div class="col-md-7">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm"
                                style="border-top: 4px solid #3b82f6 !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-primary text-uppercase mb-0"
                                        style="font-size: 0.8rem; letter-spacing: 1px;">
                                        <i class="ph-bold ph-map-pin me-2"></i>Tujuan Pengiriman
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
                                            style="font-size: 0.7rem;">KOTA</span>
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

                    {{-- SEKSI BAWAH: Rincian Barang & Tracking (BERDAMPINGAN) --}}
                    <div class="row g-3">
                        {{-- Samping Kiri: Rincian Barang (7/12 area) --}}
                        <div class="col-lg-7">
                            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-package me-2"></i>Daftar Item Barang
                                </h6>
                                <div class="table-responsive border rounded-3 overflow-hidden">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="10%" class="text-center text-muted py-3">NO</th>
                                                <th width="70%" class="text-muted py-3">NAMA BARANG</th>
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

                        {{-- Samping Kanan: Riwayat Tracking (5/12 area) --}}
                        <div class="col-lg-5">
                            <div class="bg-white border rounded-4 p-4 shadow-sm h-100">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3"
                                    style="font-size: 0.8rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-clock-counter-clockwise me-2"></i>Tracking Aktivitas Unduhan
                                </h6>
                                <div class="table-responsive border rounded-3 overflow-hidden">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50%" class="text-muted py-3 px-3">WAKTU</th>
                                                <th width="50%" class="text-muted py-3">EKSEKUTOR</th>
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
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            let sampleTable, historyTable;
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

            document.addEventListener("DOMContentLoaded", function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            $(document).ready(function() {
                const exportDnBaseUrl = "{{ route('logistic-orders.export-dn') }}";

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Inisialisasi DataTable 1: PENDING
                sampleTable = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('logistic-orders.index') }}",
                        data: {
                            tab: 'pending'
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

                // Inisialisasi DataTable 2: DOWNLOADED
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

                $('#historyTable').on('draw.dt', function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });

                // Reload tabel downloaded saat tanggal berubah (hanya jika tab downloaded aktif)
                $('#dn_date_from, #dn_date_to').on('change', function() {
                    if (activeTab === 'downloaded') {
                        historyTable.ajax.reload(null, false);
                    }
                });

                // Clear date filter
                $('#btn-clear-dn-date').on('click', function() {
                    $('#dn_date_from').val('');
                    $('#dn_date_to').val('');

                    if (activeTab === 'downloaded') {
                        historyTable.ajax.reload(null, false);
                    }
                });

                // Export Excel DN
                $('#btn-export-dn').on('click', function() {
                    const from = $('#dn_date_from').val();
                    const to = $('#dn_date_to').val();

                    // If user doesn't use date filter, export all dates
                    if (!from && !to) {
                        window.location.href = exportDnBaseUrl;
                        return;
                    }

                    // Prevent partial range
                    if (!from || !to) {
                        Swal.fire('Peringatan', 'Filter tanggal harus diisi lengkap (From dan To).', 'warning');
                        return;
                    }

                    if (from > to) {
                        Swal.fire('Peringatan', 'Tanggal From tidak boleh melebihi To.', 'warning');
                        return;
                    }

                    const url = exportDnBaseUrl + '?date_from=' + encodeURIComponent(from) + '&date_to=' +
                        encodeURIComponent(to);
                    window.location.href = url;
                });

                // Init Select2
                $('.select2-custom').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    allowClear: true
                });

                // LOGIC FORM CREATE (Sama persis seperti sebelumnya)
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
                                '<option value="">-- Pilih Distributor --</option>').prop(
                                'disabled', false);
                            $.each(res.distributors, function(key, d) {
                                distSelect.append('<option value="' + d.id + '">' + d.code +
                                    ' - ' + d.name + '</option>');
                            });
                            distSelect.trigger('change');

                            cachedShipTos = res.ship_to_list;
                            shipSelect.empty().append(
                                '<option value="">-- Pilih Alamat Pengiriman --</option>').prop(
                                'disabled', false);
                            $.each(res.ship_to_list, function(key, s) {
                                shipSelect.append('<option value="' + s.id + '" data-code="' + s
                                    .ship_to_code + '">' + s.ship_to_code + ' - ' + s
                                    .ship_to_name + '</option>');
                            });
                            shipSelect.trigger('change');

                            if (res.items && res.items.length > 0) {
                                $.each(res.items, function(key, item) {
                                    addRow(item.item_code || '', item.item_name, item
                                        .quantity || 0);
                                });
                            } else {
                                addRow();
                            }
                        });
                    } else {
                        $('#itemTable tbody').html(
                            '<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer untuk memuat item otomatis...</td></tr>'
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
                    $('.qty-input').each(function() {
                        if ($(this).val() > 0) totalItems++;
                    });
                    if (totalItems === 0) {
                        Swal.fire('Peringatan', 'Minimal 1 item diisi.', 'warning');
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Pengajuan Order',
                        html: `<div class="text-start">Pesanan ini akan men-generate nomor dokumen dan mengirim email notifikasi.</div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Submit Sekarang',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menyimpan Order...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            $.ajax({
                                url: "{{ route('logistic-orders.store') }}",
                                method: "POST",
                                data: $(this).serialize(),
                                success: function(res) {
                                    $('#modalForm').modal('hide');
                                    sampleTable.ajax.reload(); // Refresh tabel pending
                                    Swal.fire('Berhasil!', res.message, 'success');
                                },
                                error: function() {
                                    Swal.fire('Gagal', 'Terjadi kesalahan sistem.',
                                        'error');
                                }
                            });
                        }
                    });
                });

                // TOMBOL DETAIL DI-KLIK
                $(document).on('click', '.btn-detail', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Memuat...',
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
                            let statusHtml = data.note.status === 'Downloaded' ?
                                `<span class="badge bg-success text-white border border-success px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-check-circle me-1"></i> Download</span>` :
                                `<span class="badge bg-warning text-dark border border-warning px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-clock me-1"></i> Pending</span>`;
                            $('#detail_status').html(statusHtml);
                        }

                        $('#detail_delivery_date').text(data.delivery_date || '-');
                        $('#detail_customer').text(data.customer ? data.customer.name : '-');
                        $('#detail_distributor').text(data.distributor ? data.distributor.name : '-');

                        if (data.customer_ship_to) {
                            let st = data.customer_ship_to;
                            $('#detail_ship_to_code').text(st.ship_to_code || '-');
                            $('#detail_ship_to_name').text(st.ship_to_name || '-');
                            $('#detail_ship_to_address').text(st.ship_to_address_1 + ' ' + (st
                                .ship_to_address_2 || ''));
                            $('#detail_ship_to_city').text(st.ship_to_city || '-');
                            $('#detail_sales_pic').text(st.user ? st.user.name : '-');
                        }

                        // --- RENDER TABEL ITEM BARANG ---
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
                                    <td class="text-center fw-bold text-primary py-2">${item.order_quantity}</td>
                                </tr>
                            `);
                            });
                        } else {
                            tbody.append(
                                '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada rincian barang.</td></tr>'
                            );
                        }

                        // --- RENDER TABEL RIWAYAT UNDUHAN (TRACKING) ---
                        let logTbody = $('#detail_download_logs_table');
                        logTbody.empty();
                        if (data.download_logs && data.download_logs.length > 0) {
                            $.each(data.download_logs, function(index, log) {
                                // Format Tanggal
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
                                '<tr><td colspan="2" class="text-center text-muted py-4 small italic">Belum ada aktivitas download dokumen.</td></tr>'
                            );
                        }

                        let downloadUrl = "{{ url('/public/lo/download') }}/" + data.id +
                            "/0"; // Sesuaikan base URL nya
                        if (data.note && data.note.status === 'Downloaded') {
                            $('#admin-download-wrapper').html(`
                            <a href="${data.download_url}" target="_blank" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="ph-bold ph-printer me-2"></i> Download DN (Admin)
                            </a>
                        `);
                        } else {
                            $('#admin-download-wrapper').empty();
                        }

                        $('#modalDetail').modal('show');
                    });
                });
            });

            // --- FUNGSI GANTI TAB CARD ---
            function switchTab(tabName) {
                activeTab = tabName;
                // Ubah Warna Card
                $('.toggle-card').removeClass('active');
                $('#btn-tab-' + tabName).addClass('active');

                if (tabName === 'pending') {
                    // Tampilkan Tabel Pending
                    $('#wrapper-downloaded').addClass('d-none');
                    $('#wrapper-pending').removeClass('d-none');

                    // Ubah Judul & Tombol
                    $('#dynamic-table-title').html('<i class="ph-fill ph-stack text-primary me-2"></i> Daftar Logistic Order');
                    $('#dynamic-table-subtitle').text('Menampilkan data pesanan dengan status Pending.');
                    $('#btn-create-order').removeClass('d-none');
                    $('#dn-export-area').addClass('d-none');

                    // Refresh Data
                    sampleTable.ajax.reload(null, false);
                } else {
                    // Tampilkan Tabel Downloaded
                    $('#wrapper-pending').addClass('d-none');
                    $('#wrapper-downloaded').removeClass('d-none');

                    // Ubah Judul & Sembunyikan Tombol Create
                    $('#dynamic-table-title').html(
                        '<i class="ph-fill ph-check-circle text-success me-2"></i> Arsip Delivery No');
                    $('#dynamic-table-subtitle').text('Menampilkan Surat Jalan yang telah diunduh (Selesai).');
                    $('#btn-create-order').addClass('d-none');
                    $('#dn-export-area').removeClass('d-none');

                    // Refresh Data
                    historyTable.ajax.reload(null, false);
                }
            }

            function addRow(code = '', name = '', qty = '') {
                $('#emptyRow').remove();
                let index = Date.now() + Math.floor(Math.random() * 1000);
                let initialTotal = (qty > 0) ? formatRupiah(qty * activeLogisticFee) : 'Rp 0';
                let row = `
                <tr>
                    <td><input type="text" name="items[${index}][item_code]" class="form-control form-control-sm" value="${code}" placeholder="Kode Item" required></td>
                    <td><input type="text" name="items[${index}][item_name]" class="form-control form-control-sm" value="${name}" placeholder="Nama Item" required></td>
                    <td><input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input" value="${qty}" placeholder="0" min="0" oninput="calculateRow(this)"></td>
                    <td><input type="text" name="items[${index}][amount]" class="form-control form-control-sm bg-light amount-display fw-bold text-primary" readonly value="${initialTotal}"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="ph-bold ph-trash"></i></button></td>
                </tr>
            `;
                $('#itemTable tbody').append(row);
            }

            function removeRow(btn) {
                $(btn).closest('tr').remove();
                if ($('#itemTable tbody tr').length === 0) $('#itemTable tbody').append(
                    '<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Tabel kosong.</td></tr>');
            }

            function calculateRow(input) {
                let qty = parseFloat($(input).val()) || 0;
                $(input).closest('tr').find('.amount-display').val(formatRupiah(qty * activeLogisticFee));
            }

            function openModal() {
                $('#mainForm')[0].reset();
                let today = new Date().toISOString().split('T')[0];
                let currentMonth = new Date().toLocaleString('id-ID', {
                    month: 'long',
                    year: 'numeric'
                });
                $('#delivery_date').val(today);
                $('#hidden_delivery_to').val(today);
                $('#hidden_period').val(currentMonth);
                $('#customer_id').val('').trigger('change');
                $('#distributor_id').empty().append('<option value="">-- Pilih Distributor --</option>').prop('disabled', true)
                    .trigger('change');
                $('#customer_ship_to_id').empty().append('<option value="">-- Pilih Alamat --</option>').prop('disabled', true)
                    .trigger('change');
                $('#shipToDetailBox').hide();
                activeLogisticFee = 0;
                $('#active_fee_display').text('Rp 0 / ctn');
                $('#itemTable tbody').html(
                    '<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer terlebih dahulu.</td></tr>'
                );
                $('#modalForm').modal('show');
            }
        </script>
    @endpush
</x-app-layout>
