<x-app-layout>
    @section('title', 'Executive Dashboard')

    {{-- LOAD FLATPICKR (DATEPICKER) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

    {{-- HEADER & QUICK ACTIONS --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.75rem;">Executive Dashboard | Customers & Bank Garansi</h3>
            <p class="text-muted small mb-0" style="font-size: 0.9rem;">Overview Real-time: Bank Garansi, Customer Portfolio & Workflow</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- FLEXIBLE DATE PICKER --}}
            <div class="bg-white rounded-pill shadow-sm border d-flex align-items-center p-1 ps-3">
                <i class="ti ti-calendar text-primary me-2"></i>
                <input type="text" id="dashboardDateFilter"
                       class="border-0 bg-transparent fw-bold text-dark fs-7"
                       style="outline: none; width: 210px; cursor: pointer;"
                       placeholder="Filter Tanggal...">
            </div>

            <div class="dropdown">
                <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold dropdown-toggle"
                        style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); border:none; padding-top: 10px; padding-bottom: 10px;"
                        type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-bolt me-1"></i> Quick Action
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('bg-list.index') }}"><i class="ti ti-file-certificate me-2 text-primary"></i>Input BG Baru</a></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('customers.index') }}"><i class="ti ti-user-plus me-2 text-success"></i>Customer Baru</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('bg-reports.index') }}"><i class="ti ti-download me-2 text-muted"></i>Download Report</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ROW 1: KEY METRICS --}}
    <div class="row g-3 mb-4">
        {{-- Total Active BG Value --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm text-white"
                 style="background: linear-gradient(135deg, #007adf 0%, #00ecbc 100%); border-radius: 20px; position: relative; overflow: hidden; min-height: 180px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative" style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="px-3 py-1 rounded-pill fw-bold" style="background: rgba(0,0,0,0.2); font-size: 0.75rem; backdrop-filter: blur(5px);">
                            FINANCIAL OVERVIEW
                        </div>
                        <i class="ti ti-chart-pie fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="mt-3">
                        <h3 class="fw-bold mb-0" id="metric_bg_total_value" style="font-size: 1.8rem;">Loading...</h3>
                        <div class="d-flex align-items-center mt-1">
                            <span class="opacity-75 small">Total Nilai BG (Filtered)</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1 opacity-75">
                            <span>Annual Target</span><span class="fw-bold">85%</span>
                        </div>
                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.3);">
                            <div class="progress-bar bg-white" role="progressbar" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
                <i class="ti ti-wallet" style="position: absolute; right: -20px; bottom: -30px; font-size: 9rem; opacity: 0.15; transform: rotate(-20deg); z-index: 1;"></i>
            </div>
        </div>

        {{-- Expiring & Customers --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="d-flex flex-column gap-3 h-100">
                <div class="card border-0 shadow-sm p-3 flex-grow-1 d-flex flex-row align-items-center" style="border-radius: 20px; border-left: 6px solid #ffc107 !important; background: white;">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="ti ti-clock-exclamation fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.65rem;">Expiring Soon</small>
                        <h3 class="fw-bold text-dark mb-0" id="metric_bg_expiring" style="font-size: 1.8rem;">-</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Dalam 60 Hari</small>
                    </div>
                </div>
                <div class="card border-0 shadow-sm p-3 flex-grow-1 d-flex flex-row align-items-center" style="border-radius: 20px; border-left: 6px solid #dc3545 !important; background: white;">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="ti ti-alert-circle fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.65rem;">Credit Exceeded</small>
                        <h3 class="fw-bold text-danger mb-0" id="metric_customers_credit_exceeded" style="font-size: 1.8rem;">-</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Over Limit</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Base --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm text-white"
                 style="background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%); border-radius: 20px; position: relative; overflow: hidden;">
                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative" style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="px-3 py-1 rounded-pill fw-bold" style="background: rgba(0,0,0,0.2); font-size: 0.75rem; backdrop-filter: blur(5px);">
                            CUSTOMER BASE
                        </div>
                        <i class="ti ti-users-group fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="mt-auto text-center py-2">
                        <h3 class="fw-bold mb-0 display-5" id="metric_customers_total">-</h3>
                        <small class="text-white opacity-75 d-block">Total Registered Partners</small>
                    </div>
                    <div class="mt-auto bg-white bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-between border border-white border-opacity-10">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-user-plus me-2"></i>
                            <span class="small fw-bold">New (Filtered)</span>
                        </div>
                        <span class="badge bg-white text-primary fw-bold fs-6 shadow-sm">+<span id="adv_cust_growth">0</span></span>
                    </div>
                </div>
                <i class="ti ti-world" style="position: absolute; left: -30px; top: -30px; font-size: 10rem; opacity: 0.1; z-index: 1;"></i>
            </div>
        </div>

        {{-- My Action --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 p-0 border-0 shadow-sm text-white"
                 style="background: linear-gradient(135deg, #232526 0%, #414345 100%); border-radius: 20px;">
                <div class="card-header border-0 bg-transparent pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-0 fw-bold">My Actions</h6>
                        <small class="text-white opacity-50" style="font-size: 0.75rem;">Need your attention</small>
                    </div>
                    <span class="badge bg-danger rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 25px; height: 25px;" id="myActionsCount">0</span>
                </div>
                <div class="card-body px-3 pb-3 pt-0">
                    <div class="custom-scroll pe-2" style="height: 140px; overflow-y: auto;">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" id="myActionsList">
                            <li class="text-white-50 small fst-italic text-center mt-4">Loading actions...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: CHART & EXTREMES (REJECTED REMOVED) --}}
    <div class="row g-3 mb-4 mt-4">
        {{-- Main Chart --}}
        <div class="col-lg-8">
            <div class="card bg-white p-4 h-100 shadow-sm" style="border-radius: 20px; border: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="ti ti-chart-bar text-primary me-2"></i>Statistik Transaksi</h5>
                        <small class="text-muted">Monitoring pengajuan (Realtime: Jan 1 - Today)</small>
                    </div>
                    <div class="d-flex gap-2 bg-light p-1 rounded-3">
                        <select class="form-select form-select-sm border-0 bg-transparent fw-bold text-primary focus-ring-none" id="dataTypeSelect" style="width: auto; cursor: pointer; outline: none; box-shadow: none;">
                            <option value="bg">Bank Garansi</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </div>

                <div class="position-relative">
                    <div id="monthlyBgChart" style="height: 320px;"></div>
                </div>

                {{-- STATUS BOXES (REJECTED REMOVED & COLUMNS ADJUSTED TO COL-MD-4) --}}
                <div class="row mt-2 g-3 text-center">
                    <div class="col-4">
                        <div class="p-3 rounded-4 bg-primary bg-opacity-10 text-primary h-100 d-flex flex-column justify-content-center">
                            <span class="d-block small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Created</span>
                            <span class="fs-4 fw-bold" id="summaryCreated">-</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 bg-success bg-opacity-10 text-success h-100 d-flex flex-column justify-content-center">
                            <span class="d-block small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Approved</span>
                            <span class="fs-4 fw-bold" id="summaryApproved">-</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded-4 bg-warning bg-opacity-10 text-warning h-100 d-flex flex-column justify-content-center">
                            <span class="d-block small fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Pending</span>
                            <span class="fs-4 fw-bold" id="summaryPending">-</span>
                        </div>
                    </div>
                    {{-- REJECTED BOX REMOVED --}}
                </div>
            </div>
        </div>

        {{-- Hall of Fame (Extremes & Breakdown) --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <div class="card bg-white p-3 shadow-sm flex-grow-1" style="border-radius: 20px; border: 1px solid rgba(0,0,0,0.05);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">BG Type Breakdown</h6>
                        <button class="btn btn-sm btn-icon btn-light rounded-circle"><i class="ti ti-dots"></i></button>
                    </div>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div id="bgTypeDonutChart"></div>
                    </div>
                </div>

                <div class="card p-3 border-0 shadow-sm text-white"
                     style="background: radial-gradient(circle at 10% 20%, rgb(255, 131, 61) 0%, rgb(249, 183, 23) 90%); border-radius: 20px;">
                    <div class="d-flex align-items-center position-relative overflow-hidden">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3 flex-shrink-0">
                            <i class="ti ti-crown fs-1 text-white"></i>
                        </div>
                        <div style="z-index: 2; width: 100%;">
                            <div class="d-flex justify-content-between align-items-start">
                                <small class="text-uppercase fw-bold text-white opacity-75" style="font-size:0.65rem; letter-spacing: 1px;">Largest Active BG</small>
                                <span class="badge bg-white text-warning fw-bold small">TOP 1</span>
                            </div>
                            <h5 class="fw-bold text-white mb-0 mt-1 text-truncate" id="adv_largest_bg_nominal">Loading...</h5>
                            <div class="small text-white text-truncate opacity-90" id="adv_largest_bg_cust">-</div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white p-3 shadow-sm" style="border-radius: 20px; border: 1px solid rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary flex-shrink-0">
                            <i class="ti ti-award fs-1"></i>
                        </div>
                        <div style="overflow: hidden; width: 100%;">
                            <small class="text-uppercase fw-bold text-muted" style="font-size:0.65rem; letter-spacing: 1px;">Longest Loyalty</small>
                            <h6 class="fw-bold text-dark mb-0 mt-1 text-truncate" id="adv_longest_cust_name">-</h6>
                            <div class="small text-muted">Since <span class="fw-bold text-primary" id="adv_longest_cust_year">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: LISTS (Top Customers & Activities) --}}
    <div class="row g-3 mb-5">
        {{-- Top Customers --}}
        <div class="col-lg-6">
            <div class="card bg-white h-100 p-0 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary" style="font-size: 1rem;"><i class="ti ti-trophy me-2 bg-primary text-white p-1 rounded-circle small"></i> Top Customers (Value)</h6>
                </div>
                <div class="p-2 custom-scroll" style="max-height: 350px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topCustomersList">
                        {{-- JS Injected --}}
                    </ul>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="col-lg-6">
            <div class="card bg-white h-100 p-0 border-0 shadow-sm" style="border-radius: 20px;">
                <div class="px-4 py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-info" style="font-size: 1rem;"><i class="ti ti-activity me-2 bg-info text-white p-1 rounded-circle small"></i> Recent Activities</h6>
                </div>
                <div class="custom-scroll" style="max-height: 350px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4 text-secondary py-3">Ref ID</th>
                                    <th class="text-secondary">Requester</th>
                                    <th class="text-secondary">Category</th>
                                    <th class="text-secondary">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivitiesTableBody">
                                {{-- JS Injected --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 4: WORKFLOW VISUALIZATION --}}
    <div class="card bg-white p-4 mb-5 border-0 shadow-sm" style="border-radius: 20px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h5 class="fw-bold text-dark mb-1">Panduan Alur Kerja</h5>
                <p class="text-muted small mb-0">Visualisasi tahapan proses beserta Aktornya.</p>
            </div>
            <ul class="nav nav-pills bg-light p-1 rounded-pill" id="workflowTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill active px-4 py-2 small fw-bold" id="alur-bg-tab" data-bs-toggle="tab" data-bs-target="#alur-bg" type="button">BG Workflow</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 small fw-bold" id="alur-cust-tab" data-bs-toggle="tab" data-bs-target="#alur-cust" type="button">Customer Reg</button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="workflowTabsContent">
            {{-- Tab 1: Alur BG --}}
            <div class="tab-pane fade show active" id="alur-bg" role="tabpanel">
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-6 g-3">
                    <div class="col">
                        <div class="card h-100 border-0 bg-light text-center p-3 position-relative" style="border-radius: 16px;">
                            <span class="badge bg-secondary text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">System</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-secondary shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-bell fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">1. Reminder</h6>
                            <p class="text-muted small lh-sm mb-0">Notifikasi H-60</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #eef2ff;">
                            <span class="badge bg-primary text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Sales</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-primary shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-file-text fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">2. Recommendation</h6>
                            <p class="text-muted small lh-sm mb-0">Buat draft & kirim</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #e0f2fe;">
                            <span class="badge bg-info text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Customer</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-info shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-user-circle fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">3. Input Data</h6>
                            <p class="text-muted small lh-sm mb-0">Upload dokumen</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #fef3c7;">
                            <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Sales</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-warning shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-eye fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">4. Review</h6>
                            <p class="text-muted small lh-sm mb-0">Cek kelengkapan</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #d1fae5;">
                            <span class="badge bg-success text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Finance</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-signature fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">5. Approval</h6>
                            <p class="text-muted small lh-sm mb-0">Approve Limit</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 bg-light text-center p-3 position-relative" style="border-radius: 16px;">
                            <span class="badge bg-dark text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">System</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-dark shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-check fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">6. Finish</h6>
                            <p class="text-muted small lh-sm mb-0">Arsip & Email</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Alur Customer --}}
            <div class="tab-pane fade" id="alur-cust" role="tabpanel">
                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3">
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #eef2ff;">
                            <span class="badge bg-primary position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Sales</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-primary shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-user-plus fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">1. Input</h6>
                            <p class="text-muted small lh-sm mb-0">Data customer baru</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #eef2ff;">
                            <span class="badge bg-primary position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">SPV Sales</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-primary shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-user-check fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">2. Validasi</h6>
                            <p class="text-muted small lh-sm mb-0">Cek data sales</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #e0f2fe;">
                            <span class="badge bg-info text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Head Sales</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-info shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-building fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">3. Biz Review</h6>
                            <p class="text-muted small lh-sm mb-0">Cek Coverage</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 text-center p-3 position-relative" style="border-radius: 16px; background-color: #d1fae5;">
                            <span class="badge bg-success text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">Finance</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-success shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-calculator fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">4. Calculation</h6>
                            <p class="text-muted small lh-sm mb-0">Hitung Limit</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card h-100 border-0 bg-light text-center p-3 position-relative" style="border-radius: 16px;">
                            <span class="badge bg-dark text-white position-absolute top-0 start-50 translate-middle-x mt-2 shadow-sm px-3">System</span>
                            <div class="mx-auto my-3 d-flex align-items-center justify-content-center rounded-circle bg-white text-dark shadow-sm" style="width: 60px; height: 60px;">
                                <i class="ti ti-database fs-2"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">5. Registered</h6>
                            <p class="text-muted small lh-sm mb-0">Selesai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS (Inline Style for safety) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- LOAD FLATPICKR JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            // Helpers
            const fmtIDR = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n);
            const fmtNum = (n) => new Intl.NumberFormat('id-ID').format(n);

            async function fetchData(url) {
                try { const r = await fetch(url); return r.ok ? await r.json() : null; }
                catch(e){ console.error(e); return null; }
            }

            // === 1. SETUP DATEPICKER & GET DEFAULT VALUE IMMEDIATELY ===
            // Kita inisialisasi Flatpickr, tapi juga ambil value defaultnya untuk query pertama
            const defaultStart = new Date(new Date().getFullYear(), 0, 1); // 1 Jan Tahun Ini
            const defaultEnd = new Date(); // Hari ini

            const datePicker = flatpickr("#dashboardDateFilter", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [defaultStart, defaultEnd],
                onChange: function(selectedDates, dateStr, instance) {
                    // Trigger reload saat user mengubah tanggal
                    reloadData(dateStr);
                }
            });

            // PENTING: Set variabel range untuk load pertama kali (agar tidak menunggu user input)
            // Format harus YYYY-MM-DD to YYYY-MM-DD
            const startStr = defaultStart.toISOString().split('T')[0];
            const endStr = defaultEnd.toISOString().split('T')[0];
            let currentDateRange = `${startStr} to ${endStr}`;

            function reloadData(dateRange) {
                currentDateRange = dateRange;
                loadAdvanced();
                loadChart();
                loadMetrics();
                // loadTopCust(); // Optional: Aktifkan jika top cust ingin kena filter tanggal juga
            }

            // 2. Metrics
            async function loadMetrics() {
                const bg = await fetchData(`{{ route('dashboard.data.bg-metrics') }}?date_range=${currentDateRange}`);
                if(bg) {
                    document.getElementById('metric_bg_total_value').textContent = fmtIDR(bg.total_value);
                    document.getElementById('metric_bg_expiring').textContent = fmtNum(bg.expiring);
                }
                const cust = await fetchData("{{ route('dashboard.data.customer-metrics') }}");
                if(cust) {
                    document.getElementById('metric_customers_total').textContent = fmtNum(cust.total);
                    document.getElementById('metric_customers_credit_exceeded').textContent = fmtNum(cust.credit_exceeded);
                }
            }

            // 3. Advanced Stats (Donut & Extremes)
            async function loadAdvanced() {
                const d = await fetchData(`{{ route('dashboard.data.advanced-stats') }}?date_range=${currentDateRange}`);
                if(!d) return;

                document.getElementById('adv_largest_bg_nominal').textContent = fmtIDR(d.largest_bg.nominal);
                document.getElementById('adv_largest_bg_cust').textContent = d.largest_bg.customer + ' (' + d.largest_bg.number + ')';
                document.getElementById('adv_longest_cust_name').textContent = d.longest_customer.name;
                document.getElementById('adv_longest_cust_year').textContent = d.longest_customer.year;
                document.getElementById('adv_cust_growth').textContent = d.cust_growth;

                const opts = {
                    series: [d.bg_composition.new, d.bg_composition.extension, d.bg_composition.existing],
                    labels: ['New', 'Extension', 'Existing'],
                    chart: { type: 'donut', height: 180 },
                    colors: ['#0d6efd', '#fd7e14', '#6f42c1'],
                    legend: { position: 'bottom', fontSize: '11px', markers: {width: 8, height: 8} },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    plotOptions: { pie: { donut: { size: '70%' } } }
                };
                document.querySelector("#bgTypeDonutChart").innerHTML = "";
                new ApexCharts(document.querySelector("#bgTypeDonutChart"), opts).render();
            }

            // 4. Main Chart (Chart Loaded Immediately with default range)
            let chartInstance;
            async function loadChart() {
                const type = document.getElementById('dataTypeSelect').value;
                const d = await fetchData(`{{ route('dashboard.data.monthly-stats') }}?date_range=${currentDateRange}&type=${type}`);
                if(!d) return;

                const sum = a => a.reduce((x,y)=>x+y,0);
                document.getElementById('summaryCreated').textContent = sum(d.created);
                document.getElementById('summaryApproved').textContent = sum(d.approved);
                document.getElementById('summaryPending').textContent = sum(d.pending);
                // Summary Rejected removed from HTML, so no need to update ID

                const options = {
                    series: [
                        { name: 'Created', data: d.created, color: '#0d6efd' },
                        { name: 'Approved', data: d.approved, color: '#198754' },
                        { name: 'Pending', data: d.pending, color: '#ffc107' }
                    ],
                    chart: { type: 'bar', height: 320, toolbar: {show:false}, fontFamily: 'inherit' },
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: {
                        type: 'category',
                        categories: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                        labels: { style: { fontSize: '11px', colors: '#6c757d' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: { show: false },
                    grid: { strokeDashArray: 4, borderColor: '#f1f2f6', xaxis: { lines: { show: false } } },
                    fill: { opacity: 1 },
                    legend: { position: 'top' }
                };

                if(chartInstance) chartInstance.destroy();
                chartInstance = new ApexCharts(document.querySelector("#monthlyBgChart"), options);
                chartInstance.render();
            }

            document.getElementById('dataTypeSelect').addEventListener('change', loadChart);

            // 5. Lists
            async function loadTopCust() {
                const d = await fetchData(`{{ route('dashboard.data.top-customers-bg') }}?metric=value`);
                const l = document.getElementById('topCustomersList');
                l.innerHTML = '';
                if(!d || !d.length) { l.innerHTML = '<li class="list-group-item text-center small text-muted border-0">No data available</li>'; return; }

                const max = Math.max(...d.map(i=>i.bg_value||0));
                d.forEach((i, idx) => {
                    const val = i.bg_value || 0;
                    const pct = max>0 ? (val/max)*100 : 0;
                    l.innerHTML += `
                    <li class="list-group-item px-0 py-3 border-0 border-bottom" style="background:transparent;">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-light text-primary me-2 rounded-circle" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">${idx+1}</span>
                            <span class="fw-bold small text-dark text-truncate" style="max-width:60%">${i.name}</span>
                            <span class="ms-auto fw-bold small text-primary">${fmtIDR(val)}</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: ${pct}%"></div>
                        </div>
                    </li>`;
                });
            }

            async function loadRecents() {
                const d = await fetchData("{{ route('dashboard.data.recent-activities') }}");
                const t = document.getElementById('recentActivitiesTableBody');
                t.innerHTML = '';
                if(!d || !d.length) { t.innerHTML = '<tr><td colspan="4" class="text-center small text-muted border-0 py-4">No recent activities</td></tr>'; return; }
                d.forEach(r => {
                    let badge = 'bg-secondary';
                    if(r.status==='approved') badge='bg-success';
                    else if(r.status==='process') badge='bg-info';
                    t.innerHTML += `
                    <tr>
                        <td class="ps-4 py-3 border-bottom-0"><span class="fw-bold text-primary bg-primary bg-opacity-10 px-2 py-1 rounded small">${r.srs_number}</span></td>
                        <td class="border-bottom-0 fw-bold text-dark small">${r.requester_name}</td>
                        <td class="border-bottom-0"><span class="badge bg-light text-dark border fw-normal">${r.category}</span></td>
                        <td class="border-bottom-0"><span class="badge ${badge} rounded-pill bg-opacity-75 text-white" style="font-size:10px">${r.status}</span></td>
                    </tr>`;
                });
            }

            async function loadActions() {
                const d = await fetchData("{{ route('dashboard.data.my-actions') }}");
                const l = document.getElementById('myActionsList');
                document.getElementById('myActionsCount').textContent = d ? d.count : 0;
                l.innerHTML = '';
                if(!d || !d.notifications.length) { l.innerHTML = '<div class="text-white-50 text-center small mt-5"><i class="ti ti-check fs-4 d-block mb-2"></i>All caught up!</div>'; return; }
                d.notifications.forEach(n => {
                    l.innerHTML += `
                    <li>
                        <a href="${n.url}" class="d-flex text-white text-decoration-none p-3 rounded-3" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s;">
                            <div class="me-3">
                                <div class="rounded-circle bg-warning bg-opacity-25 text-warning d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="ti ti-bell"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="text-truncate small fw-bold mb-1">${n.message}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-white-50" style="font-size: 0.7rem;">Action Needed</small>
                                    <small class="text-white-50" style="font-size: 0.7rem;">${n.timestamp}</small>
                                </div>
                            </div>
                        </a>
                    </li>`;
                });
            }

            // === CALL ALL LOAD FUNCTIONS IMMEDIATELY ===
            loadAdvanced();
            loadChart();
            loadMetrics();
            loadTopCust();
            loadRecents();
            loadActions();
        });
    </script>
</x-app-layout>
