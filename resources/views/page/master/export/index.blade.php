<x-app-layout>
    @section('title', 'Master Data Export Center')

    <style>
        .export-hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #06b6d4 100%);
            border-radius: 16px;
            padding: 2rem 2.25rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.25);
            margin-bottom: 2rem;
        }
        .export-hero-banner::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .export-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .export-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.1);
            border-color: #cbd5e1;
        }
        .card-icon-wrapper {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }
        .icon-customer {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
        }
        .icon-fee {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff;
        }
        .icon-distributor {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
        }
        .icon-shipto {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: #ffffff;
        }
        .icon-more {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
        }
        .btn-export-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.7rem 1.25rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            width: 100%;
            border: none;
            text-decoration: none;
        }
        .btn-export-blue {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .btn-export-blue:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }
        .btn-export-emerald {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }
        .btn-export-emerald:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
        }
        .btn-export-amber {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }
        .btn-export-amber:hover {
            background: linear-gradient(135deg, #b45309 0%, #92400e 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(217, 119, 6, 0.35);
        }
        .btn-export-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }
        .btn-export-purple:hover {
            background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(124, 58, 237, 0.35);
        }
        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .stat-badge-blue {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .stat-badge-emerald {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .stat-badge-amber {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .stat-badge-purple {
            background-color: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }
    </style>

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Master Data Export Center</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('dashboard') }}">
                        <i class="ph-duotone ph-house f-s-16"></i> Home
                    </a>
                </li>
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-database f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="f-s-14 f-w-500">Export Data</li>
            </ul>
        </div>
    </div>

    {{-- HERO BANNER --}}
    <div class="row m-1">
        <div class="col-12">
            <div class="export-hero-banner">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-white bg-opacity-25 px-3 py-1 rounded-pill text-white fw-bold">
                                <i class="ph-bold ph-file-xls me-1"></i> Excel Spreadsheet Generator
                            </span>
                        </div>
                        <h3 class="fw-bold mb-2">Pusat Unduh & Export Data Master</h3>
                        <p class="mb-0 text-white-50" style="font-size: 0.95rem; line-height: 1.5;">
                            Unduh data master terkini (Customer, Logistic Fee, Distributor, dan Customer Ship To) ke dalam format Microsoft Excel (.xlsx) siap olah lengkap dengan relasi dan status persetujuan.
                        </p>
                    </div>
                    <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                        <div class="d-inline-flex align-items-center gap-3 bg-white bg-opacity-10 px-3 py-3 rounded-4 backdrop-blur">
                            <div class="text-center px-1">
                                <span class="d-block text-white fw-bolder fs-5">{{ $stats['customers']['total'] }}</span>
                                <small class="text-white-50" style="font-size: 0.72rem;">Customer</small>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                            <div class="text-center px-1">
                                <span class="d-block text-white fw-bolder fs-5">{{ $stats['logistic_fees']['total'] }}</span>
                                <small class="text-white-50" style="font-size: 0.72rem;">Logistic Fee</small>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                            <div class="text-center px-1">
                                <span class="d-block text-white fw-bolder fs-5">{{ $stats['distributors']['total'] }}</span>
                                <small class="text-white-50" style="font-size: 0.72rem;">Distributor</small>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(255,255,255,0.2);"></div>
                            <div class="text-center px-1">
                                <span class="d-block text-white fw-bolder fs-5">{{ $stats['ship_tos']['total'] }}</span>
                                <small class="text-white-50" style="font-size: 0.72rem;">Ship To</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARDS GRID --}}
    <div class="row m-1 g-4">

        {{-- 1. CARD CUSTOMER --}}
        <div class="col-md-6 col-xl-3">
            <div class="export-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="card-icon-wrapper icon-customer">
                        <i class="ph-fill ph-users-three"></i>
                    </div>
                    <span class="stat-badge stat-badge-blue">
                        <i class="ph-bold ph-check-circle"></i> {{ $stats['customers']['active'] }} Aktif
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-2">Data Customer</h5>
                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                    Data profil customer lengkap mencakup Kode, Nama, Sort Name, Class, Account Group / Region, TOP, Credit Limit, BG, dan Legalitas.
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Status Data:</label>
                    <select id="filter_customer_status" class="form-select form-select-sm">
                        <option value="all">Semua Status ({{ $stats['customers']['total'] }})</option>
                        <option value="Active" selected>Hanya Aktif ({{ $stats['customers']['active'] }})</option>
                        <option value="Inactive">Hanya Tidak Aktif ({{ $stats['customers']['inactive'] }})</option>
                    </select>
                </div>

                <button type="button" class="btn-export-action btn-export-blue" id="btnExportCustomer" onclick="downloadCustomer()">
                    <i class="ph-bold ph-download-simple fs-5"></i>
                    <span>Download Excel</span>
                </button>
            </div>
        </div>

        {{-- 2. CARD LOGISTIC FEE --}}
        <div class="col-md-6 col-xl-3">
            <div class="export-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="card-icon-wrapper icon-fee">
                        <i class="ph-fill ph-currency-circle-dollar"></i>
                    </div>
                    <span class="stat-badge stat-badge-emerald">
                        <i class="ph-bold ph-seal-check"></i> {{ $stats['logistic_fees']['approved'] }} Disetujui
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-2">Data Logistic Fee</h5>
                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                    Data relasi tarif pengiriman logistik per rute Distributor ke Customer, mencakup nilai tarif aktif, *proposed fee*, status approval, dan PIC.
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <label class="form-label small fw-bold text-secondary mb-1">Filter Status Tarif:</label>
                    <select id="filter_fee_status" class="form-select form-select-sm">
                        <option value="all">Semua Status ({{ $stats['logistic_fees']['total'] }})</option>
                        <option value="Approved" selected>Hanya Disetujui ({{ $stats['logistic_fees']['approved'] }})</option>
                        <option value="Pending">Hanya Menunggu ({{ $stats['logistic_fees']['pending'] }})</option>
                    </select>
                </div>

                <button type="button" class="btn-export-action btn-export-emerald" id="btnExportFee" onclick="downloadLogisticFee()">
                    <i class="ph-bold ph-download-simple fs-5"></i>
                    <span>Download Excel</span>
                </button>
            </div>
        </div>

        {{-- 3. CARD DISTRIBUTOR --}}
        <div class="col-md-6 col-xl-3">
            <div class="export-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="card-icon-wrapper icon-distributor">
                        <i class="ph-fill ph-buildings"></i>
                    </div>
                    <span class="stat-badge stat-badge-amber">
                        <i class="ph-bold ph-link-simple"></i> {{ $stats['distributors']['linked'] }} Terhubung
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-2">Data Distributor</h5>
                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                    Data seluruh entitas distributor resmi, status integrasi relasi Master Customer (`customer_id`), kontak email pengiriman order, dan jumlah outlet.
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center small py-1 border-bottom">
                        <span class="text-muted">Total Terdaftar:</span>
                        <span class="fw-bold text-dark">{{ $stats['distributors']['total'] }} Distributor</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small py-1">
                        <span class="text-muted">Input Manual:</span>
                        <span class="fw-bold text-dark">{{ $stats['distributors']['manual'] }} Distributor</span>
                    </div>
                </div>

                <a href="{{ route('master-export.distributor') }}" class="btn-export-action btn-export-amber" id="btnExportDistributor" onclick="showToastDownload()">
                    <i class="ph-bold ph-download-simple fs-5"></i>
                    <span>Download Excel</span>
                </a>
            </div>
        </div>

        {{-- 4. CARD CUSTOMER SHIP TO --}}
        <div class="col-md-6 col-xl-3">
            <div class="export-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="card-icon-wrapper icon-shipto">
                        <i class="ph-fill ph-map-pin-line"></i>
                    </div>
                    <span class="stat-badge stat-badge-purple">
                        <i class="ph-bold ph-map-pin"></i> {{ $stats['ship_tos']['total'] }} Lokasi
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-2">Customer Ship To</h5>
                <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                    Data alamat & lokasi pengiriman cabang/outlet per customer, mencakup Kode Ship To, Nama Lokasi, Alamat Lengkap 1-3, Kota, dan Sales PIC.
                </p>

                <div class="bg-light p-3 rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center small py-1 border-bottom">
                        <span class="text-muted">Total Alamat/Outlet:</span>
                        <span class="fw-bold text-dark">{{ $stats['ship_tos']['total'] }} Lokasi</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small py-1">
                        <span class="text-muted">Format File:</span>
                        <span class="fw-bold text-primary">MS Excel (.xlsx)</span>
                    </div>
                </div>

                <a href="{{ route('master-export.customer-ship-to') }}" class="btn-export-action btn-export-purple" id="btnExportShipTo" onclick="showToastDownload()">
                    <i class="ph-bold ph-download-simple fs-5"></i>
                    <span>Download Excel</span>
                </a>
            </div>
        </div>

    </div>

    {{-- FOOTER EXTENSIBLE BANNER --}}
    <div class="row m-1 mt-4">
        <div class="col-12">
            <div class="p-3 bg-white border rounded-3 d-flex flex-column flex-md-row align-items-center justify-content-between shadow-sm gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #475569;">
                        <i class="ph-bold ph-info fs-5"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block">Modul Export Siap Diperluas</span>
                        <small class="text-muted">Dapat ditambahkan modul export tambahan seperti Master Sales, Account Group, Region, atau Term of Payment (TOP).</small>
                    </div>
                </div>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                    <i class="ph-bold ph-check-circle me-1"></i> Sistem Export Terintegrasi
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function downloadCustomer() {
            let status = $('#filter_customer_status').val();
            let url = "{{ route('master-export.customer') }}?status=" + encodeURIComponent(status);
            showToastDownload();
            window.location.href = url;
        }

        function downloadLogisticFee() {
            let status = $('#filter_fee_status').val();
            let url = "{{ route('master-export.logistic-fee') }}?status=" + encodeURIComponent(status);
            showToastDownload();
            window.location.href = url;
        }

        function showToastDownload() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Memproses Unduhan...',
                    text: 'File Excel sedang di-generate dan akan otomatis diunduh oleh browser Anda.',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
