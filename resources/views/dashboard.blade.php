<x-app-layout>
    {{-- Set Title --}}
    @section('title')
        Dashboard Bank Garansi & Customer
    @endsection

    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="mb-0 fw-bold">Bank Garansi & Customer Dashboard</h3>
            <small class="text-muted">Selamat datang kembali! Ringkasan data Bank Garansi & Customer.</small>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="ti ti-plus me-1"></i> Buat Bank Garansi Baru
                </button>
                {{-- <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('sample-form.index') }}">Sample</a></li>
                    <li><a class="dropdown-item" href="{{ route('complain-form.index') }}">Complain Packaging</a></li>
                    <li><a class="dropdown-item" href="{{ route('freegoods-form.index') }}">FreeGoods</a></li>
                </ul> --}}
            </div>
        </div>
    </div>

    {{-- Quick BG & Customer Metrics --}}
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-info animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-info text-info"><i class="ti ti-shield-check"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Total BG Open</div>
                        <div class="metric-value" id="quick_metric_bg_open">-</div>
                        <div class="metric-change text-muted small">BG yang masih aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-warning animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-warning text-warning"><i class="ti ti-clock"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">BG Expiring Soon</div>
                        <div class="metric-value" id="quick_metric_bg_expiring">-</div>
                        <div class="metric-change text-muted small">Expiring dalam 60 hari</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-primary animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-primary text-primary"><i class="ti ti-cash"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Total BG Value</div>
                        <div class="metric-value" id="quick_metric_bg_total_value">-</div>
                        <div class="metric-change text-muted small">Nilai seluruh BG (IDR)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-success animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-success text-success"><i class="ti ti-users"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Customers With Active BG</div>
                        <div class="metric-value" id="quick_metric_customers_with_bg">-</div>
                        <div class="metric-change text-muted small">Pelanggan memiliki BG</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 hover-effect b-t-4-danger animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon bg-light-danger text-danger"><i class="ti ti-alert-circle"></i></div>
                    <div class="ms-3 flex-grow-1">
                        <div class="text-muted small mb-1">Customers Credit Exceeded</div>
                        <div class="metric-value" id="quick_metric_customers_credit_exceeded">-</div>
                        <div class="metric-change text-muted small">Pelanggan melebihi limit kredit</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Chart dan Ringkasan Status --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Bank Garansi Statistics per Month</h5>
                    <div class="d-flex align-items-center">
                        {{-- Year filter - menggunakan data tahun dari database Bank Garansi --}}
                        <select class="form-select form-select-sm me-2" id="yearFilterSelect" style="width: auto;">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                            @endforeach
                        </select>
                        {{-- Data type selector: BG or Customer Create --}}
                        <select class="form-select form-select-sm" id="dataTypeSelect" style="width: auto;">
                            <option value="bg" selected>Bank Garansi</option>
                            <option value="customer">Customer Create</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="monthlyBgChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            {{-- [MODIFIKASI] Struktur wadah diubah total --}}
            <div id="summaryCardsContainer" class="row gx-3">

                {{-- [BARU] Kartu Total Created yang Lebar --}}
                <div class="col-12 mb-1">
                     <div class="card h-100 ticket-card bg-primary text-white shadow-lg">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-files fs-3 text-primary"></i>
                            </div>
                            <p class="fs-6 mb-0">Total Created</p>
                            <h3 class="mb-0" id="summaryCreated">0</h3>
                        </div>
                    </div>
                </div>

                {{-- Kartu Status Lainnya (6 kartu) --}}
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-success">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-check fs-3 text-success"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Approved</p>
                            <h3 class="text-success-dark mb-0" id="summaryApproved">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-dark">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-package-export fs-3 text-dark"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Completed</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Selesai proses warehouse/QA.</small>
                            <h3 class="text-dark mb-0" id="summaryCompleted">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-info">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-loader-2 fs-3 text-info"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">In Progress</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Dalam proses approval.</small>
                            <h3 class="text-info-dark mb-0" id="summaryInProgress">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-warning">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-clock fs-3 text-warning"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Pending</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Menunggu approval pertama.</small>
                            <h3 class="text-warning-dark mb-0" id="summaryPending">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-danger">
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-ban fs-3 text-danger"></i>
                            </div>
                            {{-- [MODIFIKASI] Judul, deskripsi, dan ID diubah --}}
                            <p class="fs-6 text-muted mb-0">Rejected</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Ditolak oleh approver.</small>
                            <h3 class="text-danger-dark mb-0" id="summaryRejected">0</h3>
                        </div>
                    </div>
                </div>
                {{-- [BARU] Kartu untuk Recalled --}}
                <div class="col-sm-6 mb-1">
                    <div class="card h-100 ticket-card bg-light-primary"> {{-- Style dari kartu 'created' lama --}}
                        <div class="card-body">
                            <div class="d-flex-center bg-white mb-2" style="width: 45px; height: 45px; border-radius: 12px;">
                                <i class="ti ti-file-plus fs-3 text-primary"></i>
                            </div>
                            <p class="fs-6 text-muted mb-0">Recalled</p>
                            <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Ditarik kembali oleh requester.</small>
                            <h3 class="text-primary-dark mb-0" id="summaryRecalled">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- BG & Customer Overview (Recent BGs, Top Customers by BG) --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Bank Garansi (BG) Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total BG Open</small>
                                        <div class="fw-bold fs-4" id="metric_bg_open">-</div>
                                    </div>
                                    <div>
                                        <small class="text-muted">BG Expiring Soon</small>
                                        <div class="fw-bold fs-4 text-warning" id="metric_bg_expiring">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Total BG Value</small>
                                <div class="fw-bold fs-5" id="metric_bg_total_value">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">Customer Metrics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Customers</small>
                                        <div class="fw-bold fs-4" id="metric_customers_total">-</div>
                                    </div>
                                    <div>
                                        <small class="text-muted">Credit Exceeded</small>
                                        <div class="fw-bold fs-4 text-danger" id="metric_customers_credit_exceeded">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Customers With Active BG</small>
                                <div class="fw-bold fs-5" id="metric_customers_with_bg">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Bank Garansi</h5>
                        <small class="text-muted">Latest</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>BG No</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recentBgTableBody">
                                    <tr><td colspan="3" class="text-center text-muted">No data.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-2 card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="mb-2">Top Customers by BG</h6>
                        <ul class="list-group list-group-flush" id="topCustomersByBgList">
                            <li class="list-group-item text-center text-muted">No data.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Item dan Customer --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Top 5 Customers by BG (Count)</h5>
                        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 0.8rem;">
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemCategoryFilter">
                                <option value="all">Semua Kategori</option>
                                <option value="sample">Sample</option>
                                <option value="complain">Complain</option>
                                <option value="freegoods">Free Goods</option>
                            </select>
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemMonthFilter">
                                 <option value="all">Semua Bulan</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                                @endforeach
                            </select>
                            {{-- Year filter - menggunakan data tahun dari database Bank Garansi --}}
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topItemYearFilter">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 simplebar-scroll" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topItemsList">
                        {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <h5 class="card-title mb-0">Top 5 Customers by BG (Value)</h5>
                        <div class="d-flex align-items-center flex-wrap gap-2" style="font-size: 0.8rem;">
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerCategoryFilter">
                                <option value="all">Semua Kategori</option>
                                <option value="sample">Sample</option>
                                <option value="complain">Complain</option>
                                <option value="freegoods">Free Goods</option>
                            </select>
                             <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerMonthFilter">
                                 <option value="all">Semua Bulan</option>
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                                @endforeach
                            </select>
                            {{-- Year filter - menggunakan data tahun dari database Bank Garansi --}}
                            <select class="form-select form-select-sm top-filter" style="width: auto;" id="topCustomerYearFilter">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0 simplebar-scroll" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topCustomersList">
                        {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Aktivitas Terbaru dan Tindakan Saya --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Aktivitas BG Terbaru</h5>
                    <a href="{{ route('sample-form.log') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Request ID</th>
                                <th scope="col">Requester</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivitiesTableBody">
                           {{-- Data diisi oleh JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 card-title">Tindakan Saya (BG) (<span id="myActionsCount">0</span>)</h5>
                </div>
                <div class="card-body p-0 simplebar-scroll" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0" id="myActionsList">
                       {{-- Data diisi oleh JavaScript --}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card workflow-guide shadow-sm border-0">
                <div class="card-header border-bottom-0 pb-0 pt-3 bg-white">
                    <h5 class="card-title mb-0">Panduan Alur Kerja</h5>
                    <p class="text-muted small">Pilih tab untuk melihat Alur BG atau Alur Customer Create.</p>
                    <ul class="nav nav-tabs mt-3" id="workflowTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="alur-bg-tab" data-bs-toggle="tab" data-bs-target="#alur-bg" type="button" role="tab">Alur BG</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alur-cust-tab" data-bs-toggle="tab" data-bs-target="#alur-cust" type="button" role="tab">Alur Customer Create</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content" id="workflowTabsContent">
                    <div class="tab-pane fade show active" id="alur-bg" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-4">
                                <h6>Aktor</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-users"></i></div>
                                    <div class="step-content"><strong>Firas (Sales)</strong><small class="text-muted">Membuat rekomendasi & kirim ke customer.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user"></i></div>
                                    <div class="step-content"><strong>Customer</strong><small class="text-muted">Mengisi form, print & upload lampiran.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Iren / Rainita (Finance)</strong><small class="text-muted">Approve Lampiran D / finalisasi credit limit.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-shield-check"></i></div>
                                    <div class="step-content"><strong>Rita (Finance)</strong><small class="text-muted">Penerima notifikasi final.</small></div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <h6>Alur Proses BG (ringkasan langkah)</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">1</span></div>
                                    <div class="step-content"><strong>Notifikasi 60 hari:</strong><small class="text-muted">Sistem kirim reminder sebelum <code>exp_date</code>.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">2</span></div>
                                    <div class="step-content"><strong>Rekomendasi:</strong><small class="text-muted">Buat <code>bg_recommendations</code> (rata-rata + 11%).</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">3</span></div>
                                    <div class="step-content"><strong>Firas edit & kirim:</strong><small class="text-muted">Sales sesuaikan nominal lalu kirim ke customer.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">4</span></div>
                                    <div class="step-content"><strong>Customer submit:</strong><small class="text-muted">Isi form multi-bank, upload scan Lampiran D.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">5</span></div>
                                    <div class="step-content"><strong>Review Firas:</strong><small class="text-muted">Sales review submission, minta revisi atau teruskan.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">6</span></div>
                                    <div class="step-content"><strong>Approve Finance:</strong><small class="text-muted">Manager Finance approve; Lampiran D versi disimpan (<code>lampiran_d_versions</code>).</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">7</span></div>
                                    <div class="step-content"><strong>Versioning:</strong><small class="text-muted">Simpan v1/v2... di <code>lampiran_d_versions</code>, set active pada <code>lampiran_d.active_version_id</code>.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">8</span></div>
                                    <div class="step-content"><strong>Finalize:</strong><small class="text-muted">Simpan ke <code>credit_limits</code> dan notifikasi pihak terkait.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">9</span></div>
                                    <div class="step-content"><strong>Audit:</strong><small class="text-muted">Semua perubahan tercatat di <code>bg_histories</code> / <code>activity_log</code>.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">10</span></div>
                                    <div class="step-content"><strong>Tambah BG (sum):</strong><small class="text-muted">Penambahan nominal pada BG yang sama sesuai aturan.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">11</span></div>
                                    <div class="step-content"><strong>Tambah BG baru:</strong><small class="text-muted">Jika bank berbeda, tambahkan line BG baru dan kirim notifikasi.</small></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="alur-cust" role="tabpanel">
                        <div class="row gy-4">
                            <div class="col-lg-4">
                                <h6>Aktor</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-users"></i></div>
                                    <div class="step-content"><strong>Sales (Firas / tim Sales)</strong><small class="text-muted">Input data customer baru.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-user-check"></i></div>
                                    <div class="step-content"><strong>Atasan Sales</strong><small class="text-muted">Approval pertama untuk validasi data.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-building-community"></i></div>
                                    <div class="step-content"><strong>Dept Head Sales</strong><small class="text-muted">Approval kedua untuk kebutuhan bisnis dan coverage.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-calculator"></i></div>
                                    <div class="step-content"><strong>Manager Finance</strong><small class="text-muted">Approval final finansial; dapat menyesuaikan perhitungan (wajib).</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><i class="ti ti-shield-check"></i></div>
                                    <div class="step-content"><strong>Dept Head Finance</strong><small class="text-muted">Approval opsional; dapat menyesuaikan nominal jika perlu.</small></div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <h6>Alur Proses</h6>
                                <div class="workflow-step">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">1</span></div>
                                    <div class="step-content"><strong>Sales input:</strong><small class="text-muted">Sales mengisi form customer baru (nama, kode, alamat, bank, kontak, limit kredit awal dsb.).</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">2</span></div>
                                    <div class="step-content"><strong>Approval Atasan Sales:</strong><small class="text-muted">Atasan Sales mengecek dan approve/return.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">3</span></div>
                                    <div class="step-content"><strong>Approval Dept Head Sales:</strong><small class="text-muted">Verifikasi kebutuhan bisnis dan coverage.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">4</span></div>
                                    <div class="step-content"><strong>Manager Finance (final):</strong><small class="text-muted">Dapat menyesuaikan perhitungan credit limit sebelum approval final.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">5</span></div>
                                    <div class="step-content"><strong>Penyimpanan:</strong><small class="text-muted">Simpan ke <code>customers</code> dan catat di <code>activity_log</code>.</small></div>
                                </div>
                                <div class="workflow-step mt-2">
                                    <div class="workflow-connector"></div>
                                    <div class="step-icon"><span class="fw-bold">6</span></div>
                                    <div class="step-content"><strong>Notifikasi:</strong><small class="text-muted">Kirim notifikasi ke Sales dan pihak terkait (opsional: welcome email).</small></div>
                                </div>
                                <div class="mt-3">
                                    <strong>Catatan:</strong>
                                    <div class="text-muted small">Manager Finance dapat menyesuaikan perhitungan; semua perubahan dicatat.</div>
                                    <div class="text-muted small">Dept Head Finance bersifat opsional sesuai kebijakan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Push scripts to layout stack --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // Animated counting function
            function animateCount(el, target, duration = 1200) {
                let start = 0;
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const currentVal = Math.floor(progress * (target - start) + start);
                    el.textContent = currentVal.toLocaleString('id-ID'); // Format dengan pemisah ribuan
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        el.textContent = target.toLocaleString('id-ID'); // Pastikan nilai akhir tepat
                    }
                };
                window.requestAnimationFrame(step);
            }

            document.addEventListener("DOMContentLoaded", async function() {

                // Helper untuk fetch data dengan error handling
                async function fetchData(url) {
                    try {
                        const response = await fetch(url);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return await response.json();
                    } catch (error) {
                        console.error(`Could not fetch data from ${url}:`, error);
                        return null;
                    }
                }

                // === 1. METRIC CARDS INITIALIZATION ===
                async function loadMetricCounts() {
                    const data = await fetchData("{{ route('dashboard.data.metric-counts') }}");
                    if (!data) return;

                    animateCount(document.getElementById('metric_sample_fg'), data.sample_fg || 0);
                    animateCount(document.getElementById('metric_sample_pkg'), data.sample_pkg || 0);
                    animateCount(document.getElementById('metric_sample_so'), data.sample_so || 0);
                    animateCount(document.getElementById('metric_complain'), data.complain || 0);
                    animateCount(document.getElementById('metric_free_goods'), data.free_goods || 0);
                }

                // === YEAR OPTIONS DYNAMIC LOADING ===
                async function loadAvailableYears() {
                    try {
                        const years = await fetchData("{{ route('dashboard.data.available-years') }}");
                        if (!years || !Array.isArray(years)) return;

                        const currentYear = new Date().getFullYear();

                        // Update semua year filter selects
                        const yearSelects = ['yearFilterSelect', 'topItemYearFilter', 'topCustomerYearFilter'];

                        yearSelects.forEach(selectId => {
                            const selectElement = document.getElementById(selectId);
                            if (selectElement) {
                                const currentValue = selectElement.value;
                                selectElement.innerHTML = '';

                                years.forEach(year => {
                                    const option = document.createElement('option');
                                    option.value = year;
                                    option.textContent = `Tahun ${year}`;
                                    option.selected = (year == currentYear || year == currentValue);
                                    selectElement.appendChild(option);
                                });
                            }
                        });
                    } catch (error) {
                        console.error('Failed to load available years:', error);
                    }
                }

                // === 2. CHART INITIALIZATION ===
                const getChartOptions = (data) => ({
                    series: [
                        { name: 'Created',     type: 'line', data: data.created,     color: '#0d6efd' },
                        { name: 'Approved',    type: 'line', data: data.approved,    color: '#198754' },
                        { name: 'In Progress', type: 'line', data: data.in_progress, color: '#0dcaf0' },
                        { name: 'Pending',     type: 'line', data: data.pending,     color: '#ffc107' },
                        { name: 'Rejected',    type: 'line', data: data.rejected,    color: '#dc3545' },
                        { name: 'Recalled',    type: 'line', data: data.recalled,    color: '#6f42c1' },
                        { name: 'Completed',   type: 'line', data: data.completed,   color: '#212529' }
                    ],
                    chart: { type: 'line', stacked: false, toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }}},
                    stroke: { width: [3, 3, 3, 3, 3, 3, 3], curve: 'smooth', dashArray: [0, 0, 5, 5, 0, 0, 0] }, // [MODIFIKASI] Disesuaikan jadi 7
                    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] },
                    yaxis: { title: { text: 'Jumlah Bank Garansi', style: { fontWeight: 500 }}},
                    tooltip: { shared: true, intersect: false },
                    legend: { position: 'top', horizontalAlign: 'center' },
                    dataLabels: { enabled: false }
                });

                let currentChart;
                const chartElement = document.querySelector("#monthlyBgChart");
                const summaryCardsContainer = document.getElementById('summaryCardsContainer');
                const dataTypeSelect = document.getElementById('dataTypeSelect');

                function adjustChartHeight() {
                    const chartCard = chartElement.closest('.card');
                    const chartCardHeader = chartCard.querySelector('.card-header');
                    const chartCardBody = chartCard.querySelector('.card-body');

                    if (chartElement && summaryCardsContainer && chartCardHeader && chartCardBody) {
                        const summaryHeight = summaryCardsContainer.offsetHeight;
                        const headerHeight = chartCardHeader.offsetHeight;
                        const bodyStyles = window.getComputedStyle(chartCardBody);
                        const bodyPaddingY = parseFloat(bodyStyles.paddingTop) + parseFloat(bodyStyles.paddingBottom);
                        const newChartHeight = summaryHeight - headerHeight - bodyPaddingY;

                        if (newChartHeight > 100) {
                            chartElement.style.height = `${newChartHeight}px`;
                            if (currentChart) {
                                currentChart.updateOptions({ chart: { height: newChartHeight } });
                            }
                        }
                    }
                }

                async function updateDashboardChart(year, type = 'bg') {
                    const data = await fetchData(`{{ route('dashboard.data.monthly-stats') }}?year=${year}&type=${type}`);
                    if (!data) return;

                    if (currentChart) {
                        currentChart.updateOptions(getChartOptions(data));
                    } else if (chartElement) {
                        currentChart = new ApexCharts(chartElement, getChartOptions(data));
                        currentChart.render().then(() => {
                            setTimeout(adjustChartHeight, 100);
                        });
                    }

                    const sum = arr => Array.isArray(arr) ? arr.reduce((acc, val) => acc + val, 0) : 0;

                    // [MODIFIKASI] Logika update kartu disesuaikan dengan ID baru
                    document.getElementById('summaryCreated').textContent = sum(data.created).toLocaleString('id-ID');
                    document.getElementById('summaryApproved').textContent = sum(data.approved).toLocaleString('id-ID');
                    document.getElementById('summaryInProgress').textContent = sum(data.in_progress).toLocaleString('id-ID');
                    document.getElementById('summaryPending').textContent = sum(data.pending).toLocaleString('id-ID');
                    document.getElementById('summaryCompleted').textContent = sum(data.completed).toLocaleString('id-ID');
                    document.getElementById('summaryRejected').textContent = sum(data.rejected).toLocaleString('id-ID');
                    document.getElementById('summaryRecalled').textContent = sum(data.recalled).toLocaleString('id-ID'); // [BARU]
                };

                const yearFilterElement = document.getElementById('yearFilterSelect');
                yearFilterElement.addEventListener('change', function() {
                    const selectedType = (dataTypeSelect && dataTypeSelect.value) ? dataTypeSelect.value : 'bg';
                    updateDashboardChart(this.value, selectedType);
                });

                if (dataTypeSelect) {
                    dataTypeSelect.addEventListener('change', function() {
                        const selectedYear = yearFilterElement ? yearFilterElement.value : new Date().getFullYear();
                        updateDashboardChart(selectedYear, this.value);
                    });
                }

                window.addEventListener('resize', adjustChartHeight);

                // === 3. TOP 5 LISTS INITIALIZATION ===
                async function updateTop5List(metric, filters) {
                    // metric: 'count' or 'value'
                    const listElement = metric === 'count' ? document.getElementById('topItemsList') : document.getElementById('topCustomersList');
                    const url = new URL("{{ route('dashboard.data.top-customers-bg') }}");
                    const params = Object.assign({}, filters, { metric: metric });
                    url.search = new URLSearchParams(params).toString();

                    const data = await fetchData(url);
                    listElement.innerHTML = ''; // Clear existing list

                    if (!data || data.length === 0) {
                        listElement.innerHTML = '<li class="list-group-item text-center text-muted">No data available.</li>';
                        return;
                    }

                    const maxTotal = Math.max(...data.map(item => item.total || 0), 0);

                    data.forEach(item => {
                        const value = metric === 'count' ? (item.bg_count ?? item.total ?? 0) : (item.bg_value ?? item.total ?? 0);
                        const progress = maxTotal > 0 ? (value / maxTotal) * 100 : 0;
                        const progressBarColor = metric === 'count' ? 'bg-primary' : 'bg-success';

                        const listItem = `
                            <li class="list-group-item px-0 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${item.name}</div>
                                    <small class="text-muted">ID: ${item.code || item.id || ''}</small>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar ${progressBarColor}" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="ms-3 text-end">
                                    <span class="fw-bold fs-5">${value}</span>
                                    <div class="small text-muted">${metric === 'count' ? 'BGs' : 'IDR'}</div>
                                </div>
                            </li>`;
                        listElement.insertAdjacentHTML('beforeend', listItem);
                    });
                }

                function getTopFilters(prefix) {
                    return {
                        category: document.getElementById(`${prefix}CategoryFilter`).value,
                        month: document.getElementById(`${prefix}MonthFilter`).value,
                        year: document.getElementById(`${prefix}YearFilter`).value,
                    };
                }

                document.querySelectorAll('.top-filter').forEach(filter => {
                    filter.addEventListener('change', () => {
                        // left list shows count, right shows value
                        updateTop5List('count', getTopFilters('topItem'));
                        updateTop5List('value', getTopFilters('topCustomer'));
                    });
                });


                // === 4. RECENT ACTIVITIES & MY ACTIONS ===
                async function loadRecentActivities() {
                    const data = await fetchData("{{ route('dashboard.data.recent-activities') }}");
                    const tableBody = document.getElementById('recentActivitiesTableBody');
                    tableBody.innerHTML = '';

                    if (!data || data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent activities.</td></tr>';
                        return;
                    }

                    data.forEach(activity => {
                        const row = `
                            <tr>
                                <td><a href="#" class="fw-bold text-dark">#${activity.srs_number || 'N/A'}</a></td>
                                <td><span class="badge bg-dark text-light rounded-pill">${activity.requester_name}</span></td>
                                <td><span class="badge bg-primary text-light rounded-pill">${activity.category}</span></td>
                                <td><span class="badge bg-info text-light rounded-pill">${activity.status}</span></td>
                                <td>${activity.timestamp}</td>
                            </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                }

                async function loadMyActions() {
                    const data = await fetchData("{{ route('dashboard.data.my-actions') }}");
                    const listElement = document.getElementById('myActionsList');
                    document.getElementById('myActionsCount').textContent = data ? data.count : 0;
                    listElement.innerHTML = '';

                    if (!data || !data.notifications || data.notifications.length === 0) {
                        listElement.innerHTML = '<li class="p-3 text-center text-muted">No pending actions.</li>';
                        return;
                    }

                    data.notifications.forEach(notif => {
                        const item = `
                            <li class="d-flex align-items-center p-3 border-bottom">
                                <div class="me-3">
                                    <div class="avatar-sm bg-light-warning text-warning rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="ti ti-file-check fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <a href="${notif.url}" class="fw-bold text-dark">${notif.message}</a>
                                    <div class="text-muted small">From: ${notif.causer_name} | ${notif.timestamp}</div>
                                </div>
                            </li>`;
                        listElement.insertAdjacentHTML('beforeend', item);
                    });
                }


                // === 5. BG & CUSTOMER DATA LOADERS ===
                async function loadBgMetrics() {
                    const elOpen = document.getElementById('metric_bg_open');
                    const elExpiring = document.getElementById('metric_bg_expiring');
                    const elTotalValue = document.getElementById('metric_bg_total_value');

                    try {
                        const data = await fetchData("{{ route('dashboard.data.bg-metrics') }}");
                        if (!data) {
                            elOpen.textContent = '-';
                            elExpiring.textContent = '-';
                            elTotalValue.textContent = '-';
                            return;
                        }

                        elOpen.textContent = (data.open ?? 0).toLocaleString('id-ID');
                        elExpiring.textContent = (data.expiring ?? 0).toLocaleString('id-ID');
                        elTotalValue.textContent = data.total_value ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(data.total_value) : '-';

                        // update quick metric cards if present
                        const qOpen = document.getElementById('quick_metric_bg_open');
                        const qExp = document.getElementById('quick_metric_bg_expiring');
                        const qTotal = document.getElementById('quick_metric_bg_total_value');
                        if (qOpen) qOpen.textContent = (data.open ?? 0).toLocaleString('id-ID');
                        if (qExp) qExp.textContent = (data.expiring ?? 0).toLocaleString('id-ID');
                        if (qTotal) qTotal.textContent = data.total_value ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(data.total_value) : '-';
                    } catch (e) {
                        console.warn('BG metrics not available', e);
                        elOpen.textContent = '-';
                        elExpiring.textContent = '-';
                        elTotalValue.textContent = '-';
                    }
                }

                async function loadCustomerMetrics() {
                    const elTotal = document.getElementById('metric_customers_total');
                    const elCredit = document.getElementById('metric_customers_credit_exceeded');
                    const elWithBg = document.getElementById('metric_customers_with_bg');

                    try {
                        const data = await fetchData("{{ route('dashboard.data.customer-metrics') }}");
                        if (!data) {
                            elTotal.textContent = '-';
                            elCredit.textContent = '-';
                            elWithBg.textContent = '-';
                            return;
                        }

                        elTotal.textContent = (data.total ?? 0).toLocaleString('id-ID');
                        elCredit.textContent = (data.credit_exceeded ?? 0).toLocaleString('id-ID');
                        elWithBg.textContent = (data.with_bg ?? 0).toLocaleString('id-ID');

                        // update quick metric cards if present
                        const qWithBg = document.getElementById('quick_metric_customers_with_bg');
                        const qCredit = document.getElementById('quick_metric_customers_credit_exceeded');
                        if (qWithBg) qWithBg.textContent = (data.with_bg ?? 0).toLocaleString('id-ID');
                        if (qCredit) qCredit.textContent = (data.credit_exceeded ?? 0).toLocaleString('id-ID');
                    } catch (e) {
                        console.warn('Customer metrics not available', e);
                        elTotal.textContent = '-';
                        elCredit.textContent = '-';
                        elWithBg.textContent = '-';
                    }
                }

                async function loadRecentBg() {
                    const tbody = document.getElementById('recentBgTableBody');
                    try {
                        const data = await fetchData("{{ route('dashboard.data.recent-bgs') }}");
                        if (!Array.isArray(data) || data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No recent BGs.</td></tr>';
                            return;
                        }

                        tbody.innerHTML = '';
                        data.forEach(bg => {
                            const row = `
                                <tr>
                                    <td>${bg.bg_number || bg.id || 'N/A'}</td>
                                    <td>${bg.customer_name || bg.customer?.name || 'N/A'}</td>
                                    <td><span class="badge bg-${(bg.status === 'active' ? 'success' : (bg.status === 'expiring' ? 'warning' : 'secondary'))} text-white">${bg.status || 'N/A'}</span></td>
                                </tr>`;
                            tbody.insertAdjacentHTML('beforeend', row);
                        });
                    } catch (e) {
                        console.warn('Recent BGs endpoint missing or error', e);
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No recent BGs.</td></tr>';
                    }
                }

                async function loadTopCustomersByBg() {
                    const list = document.getElementById('topCustomersByBgList');
                    try {
                        const data = await fetchData("{{ route('dashboard.data.top-customers-bg') }}");
                        if (!Array.isArray(data) || data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-center text-muted">No data.</li>';
                            return;
                        }

                        list.innerHTML = '';
                        data.forEach(item => {
                            const li = `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">${item.name}</div>
                                        <small class="text-muted">${item.code || ''}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">${item.bg_count || 0}</span>
                                </li>`;
                            list.insertAdjacentHTML('beforeend', li);
                        });
                    } catch (e) {
                        console.warn('Top customers by BG endpoint missing or error', e);
                        list.innerHTML = '<li class="list-group-item text-center text-muted">No data.</li>';
                    }
                }


                // === INITIAL DATA LOAD ===
                await loadAvailableYears();

                // core metrics (BG/Customer)
                const initialType = (dataTypeSelect && dataTypeSelect.value) ? dataTypeSelect.value : 'bg';
                updateDashboardChart(yearFilterElement.value, initialType);
                loadRecentActivities();
                loadMyActions();

                // BG & Customer specific
                loadBgMetrics();
                loadCustomerMetrics();
                loadRecentBg();
                loadTopCustomersByBg();

                // populate top-5 lists (count and value)
                updateTop5List('count', getTopFilters('topItem'));
                updateTop5List('value', getTopFilters('topCustomer'));
            });
        </script>
    @endpush
</x-app-layout>
