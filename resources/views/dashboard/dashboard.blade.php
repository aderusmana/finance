<x-app-layout>
    @section('title', 'Executive Dashboard')

    {{-- LOAD LIBRARIES --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

    {{-- HEADER & ACTIONS --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-0" style="color: #2c3e50; font-size: 1.75rem;">Executive Dashboard | Customers & Bank
                Garansi</h3>
            <p class="text-muted small mb-0">Ringkasan Eksekutif Real-Time: Monitoring Bank Garansi, Data Pelanggan, dan
                Alur Kerja Terintegrasi</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- DATE PICKER --}}
            <div class="bg-white rounded-pill shadow-sm border d-flex align-items-center p-1 ps-3">
                <i class="ti ti-calendar text-primary me-2"></i>
                <input type="text" id="dashboardDateFilter" class="border-0 bg-transparent fw-bold text-dark"
                    style="outline: none; width: 220px; cursor: pointer; font-size: 0.85rem;"
                    placeholder="Filter Tanggal...">
            </div>

            <div class="dropdown">
                <button
                    class="btn btn-primary rounded-pill py-2 px-3 px-md-4 shadow-sm fw-bold dropdown-toggle d-flex align-items-center"
                    style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border:none;"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bolt me-2"></i>
                    <span class="d-none d-sm-inline">Quick Action</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('bg-list.index') }}"><i
                                class="ti ti-file-plus me-2 text-primary"></i>Input BG Baru</a></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('customers.index') }}"><i
                                class="ti ti-user-plus me-2 text-success"></i>Customer Baru</a></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('logistic-fees.index') }}"><i
                                class="ti ti-currency-dollar me-2 text-warning"></i>Logistic Fee</a></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('logistic-orders.index') }}"><i
                                class="ti ti-truck me-2 text-info"></i>Logistic Orders</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ROW 1: COMPACT METRICS --}}
    <div class="row g-3 mb-4">
        {{-- Total Active BG Value --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm text-white"
                style="background: linear-gradient(135deg, #007adf 0%, #00ecbc 100%); border-radius: 20px; position: relative; overflow: hidden; min-height: 180px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative"
                    style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="px-3 py-1 rounded-pill fw-bold"
                            style="background: rgba(0,0,0,0.2); font-size: 0.75rem; backdrop-filter: blur(5px);">
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
                <i class="ti ti-wallet"
                    style="position: absolute; right: -20px; bottom: -30px; font-size: 9rem; opacity: 0.15; transform: rotate(-20deg); z-index: 1;"></i>
            </div>
        </div>

        {{-- Expiring & Customers --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="d-flex flex-column gap-3 h-100">
                <div class="card border-0 shadow-sm p-3 flex-grow-1 d-flex flex-row align-items-center"
                    style="border-radius: 20px; border-left: 6px solid #ffc107 !important; background: white;">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px;">
                        <i class="ti ti-clock-exclamation fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase"
                            style="letter-spacing: 1px; font-size: 0.65rem;">Expiring Soon</small>
                        <h3 class="fw-bold text-dark mb-0" id="metric_bg_expiring" style="font-size: 1.8rem;">-</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Dalam 60 Hari</small>
                    </div>
                </div>
                <div class="card border-0 shadow-sm p-3 flex-grow-1 d-flex flex-row align-items-center"
                    style="border-radius: 20px; border-left: 6px solid #dc3545 !important; background: white;">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px;">
                        <i class="ti ti-alert-circle fs-3"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase"
                            style="letter-spacing: 1px; font-size: 0.65rem;">Credit Exceeded</small>
                        <h3 class="fw-bold text-danger mb-0" id="metric_customers_credit_exceeded"
                            style="font-size: 1.8rem;">-</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">Over Limit</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Base --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm text-white"
                style="background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%); border-radius: 20px; position: relative; overflow: hidden;">
                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative"
                    style="z-index: 2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="px-3 py-1 rounded-pill fw-bold"
                            style="background: rgba(0,0,0,0.2); font-size: 0.75rem; backdrop-filter: blur(5px);">
                            CUSTOMER BASE
                        </div>
                        <i class="ti ti-users-group fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="mt-auto text-center py-2">
                        <h3 class="fw-bold mb-0 display-5" id="metric_customers_total">-</h3>
                        <small class="text-white opacity-75 d-block">Total Registered Partners</small>
                    </div>
                    <div
                        class="mt-auto bg-white bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-between border border-white border-opacity-10">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-user-plus me-2"></i>
                            <span class="small fw-bold">New (Filtered)</span>
                        </div>
                        <span class="badge bg-white text-primary fw-bold fs-6 shadow-sm">+<span
                                id="adv_cust_growth">0</span></span>
                    </div>
                </div>
                <i class="ti ti-world"
                    style="position: absolute; left: -30px; top: -30px; font-size: 10rem; opacity: 0.1; z-index: 1;"></i>
            </div>
        </div>

        {{-- My Action --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 p-0 border-0 shadow-sm text-white"
                style="background: linear-gradient(135deg, #232526 0%, #414345 100%); border-radius: 20px;">
                <div
                    class="card-header border-0 bg-transparent pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-0 fw-bold">My Actions</h6>
                        <small class="text-white opacity-50" style="font-size: 0.75rem;">Need your attention</small>
                    </div>
                    <span
                        class="badge bg-danger rounded-circle d-flex align-items-center justify-content-center shadow"
                        style="width: 25px; height: 25px;" id="myActionsCount">0</span>
                </div>
                <div class="card-body px-1 pb-0 pt-0 d-flex flex-column">
                    <div class="custom-scroll pe-1 flex-grow-1" style="overflow-y: auto; height: 0; min-height: 140px;">
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 pb-2" id="myActionsList">
                            <li class="text-white-50 small fst-italic text-center mt-4">Loading actions...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: CHART & HIGHLIGHTS --}}
    <div class="row g-3 mb-4">
        {{-- Chart Section (White - BORDER TEGAS) --}}
        <div class="col-lg-8">
            <div class="card p-4 h-100"
                style="background: white; border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="text-dark mb-1" style="font-weight: 800;"><i
                                class="ti ti-chart-bar text-primary me-2"></i>Statistik Transaksi</h5>
                        <p class="text-muted small mb-0">Monitoring data bulanan (Jan - Des)</p>
                    </div>
                    <select class="form-select form-select-sm bg-light border-0 fw-bold text-primary"
                        id="dataTypeSelect" style="width: auto; cursor: pointer;">
                        <option value="bg">Bank Garansi</option>
                        <option value="customer">Customer</option>
                        <option value="logistic_order">Logistic Order</option>
                    </select>
                </div>

                {{-- Chart Wrapper --}}
                <div style="min-height: 280px;">
                    <div id="monthlyBgChart"></div>
                </div>

                {{-- Legend / Counts --}}
                <div class="row mt-3 g-3 text-center">
                    <div class="col-4">
                        <div
                            class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10">
                            <span class="d-block small fw-bold text-uppercase mb-1">Created</span>
                            <span class="fs-4" id="summaryCreated" style="font-weight: 800;">-</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div
                            class="p-3 rounded-3 bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                            <span class="d-block small fw-bold text-uppercase mb-1">Approved</span>
                            <span class="fs-4" id="summaryApproved" style="font-weight: 800;">-</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div
                            class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10">
                            <span class="d-block small fw-bold text-uppercase mb-1">Pending</span>
                            <span class="fs-4" id="summaryPending" style="font-weight: 800;">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side Stats (Breakdown & Extremes) --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-1 h-100">
                {{-- Donut (White - BORDER TEGAS) --}}
                <div class="card bg-white flex-grow-1"
                    style="border: 1px solid #d1d5db; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
                    {{-- Header dengan Icon & Judul --}}
                    <div class="p-3 border-bottom" style="border-color: #f1f5f9;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px; background: #eef2ff; color: #4f46e5;">
                                    <i class="ti ti-chart-donut fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" id="bgBreakdownTitle"
                                        style="font-size: 0.95rem;">BG Type Breakdown</h6>
                                    <small class="text-muted"
                                        style="font-size: 0.7rem; display: block; line-height: 1.2;"
                                        id="bgBreakdownSubtitle">Komposisi Pengajuan</small>
                                </div>
                            </div>

                            {{-- Dropdown Menu --}}
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb;">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow p-2"
                                    style="border-radius: 12px;">
                                    <li>
                                        <h6 class="dropdown-header text-uppercase small fw-bold">Filter Type</h6>
                                    </li>
                                    <li><a class="dropdown-item rounded-2 cursor-pointer"
                                            onclick="filterBgChart('all')"><i
                                                class="ti ti-chart-pie me-2 text-primary"></i> Show All</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item rounded-2 cursor-pointer"
                                            onclick="filterBgChart('new')"><i class="ti ti-circle-filled me-2"
                                                style="color: #0d6efd;"></i> New Only</a></li>
                                    <li><a class="dropdown-item rounded-2 cursor-pointer"
                                            onclick="filterBgChart('extension')"><i class="ti ti-circle-filled me-2"
                                                style="color: #fd7e14;"></i> Extension</a></li>
                                    <li><a class="dropdown-item rounded-2 cursor-pointer"
                                            onclick="filterBgChart('existing')"><i class="ti ti-circle-filled me-2"
                                                style="color: #6f42c1;"></i> Existing</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Chart Area --}}
                    <div class="card-body p-0 position-relative d-flex align-items-center justify-content-center"
                        style="height: 220px;">
                        <div id="bgTypeDonutChart"></div>

                        {{-- Custom Center Label --}}
                        <div id="chartCenterLabel"
                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <h4 class="fw-bold mb-0 text-dark" id="chartCenterValue" style="font-size: 1.5rem;">-
                            </h4>
                            <small class="text-muted fw-bold"
                                style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;"
                                id="chartCenterText">TOTAL</small>
                        </div>
                    </div>

                    {{-- Footer Info Kecil (Agar tidak kosong) --}}
                    <div class="px-3 py-2 bg-light border-top d-flex align-items-center justify-content-between"
                        style="border-color: #f1f5f9;">
                        <small class="text-muted" style="font-size: 0.65rem;"><i
                                class="ti ti-info-circle me-1"></i>Data Realtime</small>

                        {{-- UPDATE DI SINI: MENGGUNAKAN TAG <a> --}}
                        <a href="{{ route('bg-list.index') }}" class="text-decoration-none small text-primary fw-bold"
                            style="font-size: 0.65rem;">
                            View Details <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                {{-- Largest BG (Orange Gradient) --}}
                <div class="card p-3 border-0 shadow-sm"
                    style="background: linear-gradient(to right, #ff9966, #ff5e62); color: white; border-radius: 16px; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-5px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center position-relative">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3">
                            <i class="ti ti-crown fs-1 text-white"></i>
                        </div>
                        <div style="overflow: hidden; width: 100%;">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-uppercase fw-bold opacity-75" style="font-size:0.65rem;">Largest
                                    Active BG</small>
                                <span class="badge bg-white text-warning fw-bold" style="font-size:0.6rem;">TOP
                                    1</span>
                            </div>
                            <h5 class="text-white mb-0 text-truncate" id="adv_largest_bg_nominal"
                                style="font-weight: 800;">Loading...</h5>
                            <div class="small text-white text-truncate opacity-90" id="adv_largest_bg_cust">-</div>
                        </div>
                    </div>
                </div>

                {{-- Longest Customer (White - BORDER TEGAS) --}}
                <div class="card bg-white p-3"
                    style="border: 1px solid #1449e9; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-5px)'"
                    onmouseout="this.style.transform='translateY(0)'">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary">
                            <i class="ti ti-award fs-1"></i>
                        </div>
                        <div style="overflow: hidden; width: 100%;">
                            <small class="text-uppercase fw-bold text-muted" style="font-size:0.65rem;">Longest
                                Loyalty</small>
                            <h6 class="text-dark mb-0 text-truncate" id="adv_longest_cust_name"
                                style="font-weight: 800;">-</h6>
                            <div class="small text-muted">Member since <span class="fw-bold text-primary"
                                    id="adv_longest_cust_year">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: LISTS (Top Cust & Activities) --}}
    <div class="row g-3 mb-5">
        {{-- Top Customer (White - BORDER TEGAS) --}}
        <div class="col-lg-6">
            <div class="card bg-white h-100"
                style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary"><i class="ti ti-trophy me-2"></i> Top Customers (Value)</h6>
                </div>
                <div class="p-2" style="max-height: 350px; overflow-y: auto;">
                    <ul class="list-group list-group-flush" id="topCustomersList">
                        {{-- JS Injected --}}
                    </ul>
                </div>
            </div>
        </div>

        {{-- Recent Activity (White - BORDER TEGAS) --}}
        <div class="col-lg-6">
            <div class="card bg-white h-100"
                style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="px-4 py-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-info"><i class="ti ti-activity me-2"></i> Recent Activities</h6>
                </div>
                <div style="max-height: 350px; overflow-y: auto;">
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

    {{-- ROW 4: WORKFLOW SPLIT COLUMNS (White - BORDER TEGAS) --}}
    <div class="row g-3 mb-4">
        {{-- KOLOM KIRI: ALUR BG --}}
        <div class="col-lg-6">
            <div class="card h-100"
                style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="card-header bg-white pt-4 pb-0" style="border-bottom: 0; border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background: #eef2ff; color: #4f46e5;">
                            <i class="ti ti-file-certificate fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold" style="color: #2c3e50;">Alur Bank Garansi</h5>
                            <p class="text-muted small mb-0">Tracking proses pengajuan BG (H-60)</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Container Timeline --}}
                    <div style="position: relative; padding-left: 10px;">
                        {{-- Garis Konektor Vertikal --}}
                        <div
                            style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;">
                        </div>

                        {{-- Step 1 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #f3f4f6; color: #4b5563; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-bell fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #f3f4f6; color: #374151; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SYSTEM</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Step 1</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Notifikasi
                                    Reminder</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sistem mengirim notifikasi
                                    otomatis H-60 sebelum expired date.</p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-file-plus fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SALES
                                        </span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Step 2</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Draft Rekomendasi
                                </h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sales  membuat draft
                                    rekomendasi (+11%) dan mengirim link.</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #fff7ed; color: #ea580c; border: 3px solid #fff; box-shadow: 0 0 0 1px #fed7aa; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-pencil fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #fff7ed; color: #c2410c; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">CUSTOMER</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Step 3</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input & Upload
                                </h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Customer melengkapi form
                                    bank, cetak, ttd, dan upload scan Lampiran D.</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #fefce8; color: #ca8a04; border: 3px solid #fff; box-shadow: 0 0 0 1px #fde047; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-eye-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #fefce8; color: #854d0e; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SALES
                                        </span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Step 4</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Review Dokumen
                                </h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sales mengecek kelengkapan.
                                    Jika OK lanjut Finance, jika tidak Return.</p>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-signature fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">FINANCE
                                        MGR</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Step 5</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Final Approval
                                </h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Approval limit final.
                                    Sistem menyimpan versi Lampiran D (Versioning).</p>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div style="position: relative; display: flex;">
                            <div
                                style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge"
                                        style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SYSTEM</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Finish</small>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Completed</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Update/Create BG,
                                    Notifikasi Email, dan Arsip Data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: ALUR CUSTOMER --}}
        <div class="col-lg-6">
            <div class="card h-100"
                style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="card-header bg-white pt-4 pb-0" style="border-bottom: 0; border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background: #f0fdf4; color: #16a34a;">
                            <i class="ti ti-user-plus fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold" style="color: #2c3e50;">Alur Customer Baru</h5>
                            <p class="text-muted small mb-0">Tracking registrasi & limit kredit</p>
                        </div>
                    </div>
                    <ul class="nav nav-pills mb-2" id="customer-workflow-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active py-1 px-3 fw-bold rounded-pill" id="bg-workflow-tab" data-bs-toggle="pill" data-bs-target="#bg-workflow" type="button" role="tab" aria-controls="bg-workflow" aria-selected="true" style="font-size: 0.75rem;">Dengan BG</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-1 px-3 fw-bold rounded-pill ms-2" id="cbd-workflow-tab" data-bs-toggle="pill" data-bs-target="#cbd-workflow" type="button" role="tab" aria-controls="cbd-workflow" aria-selected="false" style="font-size: 0.75rem;">Tanpa BG (CBD)</button>
                        </li>
                    </ul>
                </div>

                <div class="card-body pt-3">
                    <div class="tab-content" id="customer-workflow-content">
                        {{-- TAB 1: DENGAN BG --}}
                        <div class="tab-pane fade show active" id="bg-workflow" role="tabpanel" aria-labelledby="bg-workflow-tab">
                            {{-- Container Timeline --}}
                            <div style="position: relative; padding-left: 10px;">
                                {{-- Garis Konektor --}}
                                <div style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;"></div>

                                {{-- Step 1 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-keyboard fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SALES TEAM</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 1</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input Data</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Mengisi form lengkap customer baru (Identitas, Bank, Kontak, Limit Awal).</p>
                                    </div>
                                </div>

                                {{-- Step 2 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-user-check fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SPV SALES</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 2</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Validasi Data</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Pengecekan kelengkapan dan validitas data awal dari tim sales.</p>
                                    </div>
                                </div>

                                {{-- Step 3 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7; border: 3px solid #fff; box-shadow: 0 0 0 1px #7dd3fc; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-building fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">HEAD SALES</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 3</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Business Review</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Verifikasi kebutuhan bisnis, area coverage, dan prospek customer.</p>
                                    </div>
                                </div>

                                {{-- Step 4 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-calculator fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">FINANCE MGR</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 4</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Financial Calc</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Perhitungan final credit limit. Dapat menyesuaikan angka sebelum approve.</p>
                                    </div>
                                </div>

                                {{-- Step 5 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #f8f9fa; color: #374151; border: 3px solid #fff; box-shadow: 0 0 0 1px #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-shield-check fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #f8f9fa; color: #374151; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">HEAD FINANCE</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Optional</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Approval Akhir</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Persetujuan level Head Finance (jika diperlukan kebijakan).</p>
                                    </div>
                                </div>

                                {{-- Step 6 --}}
                                <div style="position: relative; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-database fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SYSTEM</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Finish</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Registered</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Data tersimpan di Master Data Customer dan log aktivitas tercatat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: TANPA BG (CBD) --}}
                        <div class="tab-pane fade" id="cbd-workflow" role="tabpanel" aria-labelledby="cbd-workflow-tab">
                            {{-- Container Timeline --}}
                            <div style="position: relative; padding-left: 10px;">
                                {{-- Garis Konektor --}}
                                <div style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;"></div>

                                {{-- Step 1 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-keyboard fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SALES TEAM</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 1</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input Data</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Mengisi form lengkap customer baru (Identitas, Bank, Kontak, Limit Awal).</p>
                                    </div>
                                </div>

                                {{-- Step 2 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-user-check fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SPV SALES</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 2</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Validasi Data</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Pengecekan kelengkapan dan validitas data awal dari tim sales.</p>
                                    </div>
                                </div>

                                {{-- Step 3 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7; border: 3px solid #fff; box-shadow: 0 0 0 1px #7dd3fc; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-building fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">HEAD SALES</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 3</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Business Review</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Verifikasi kebutuhan bisnis, area coverage, dan prospek customer.</p>
                                    </div>
                                </div>

                                {{-- Step 4 --}}
                                <div style="position: relative; padding-bottom: 30px; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-calculator fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">FINANCE MGR</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Step 4</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Financial Calc</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Perhitungan final credit limit. Dapat menyesuaikan angka sebelum approve.</p>
                                    </div>
                                </div>

                                {{-- Step 5 (Finish, Head Finance skipped) --}}
                                <div style="position: relative; display: flex;">
                                    <div style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                        <i class="ti ti-database fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div style="margin-left: 15px; flex-grow: 1;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 4px 8px;">SYSTEM</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">Finish</small>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Registered</h6>
                                        <p class="text-muted mb-0 small" style="line-height: 1.3;">Data tersimpan di Master Data Customer dan log aktivitas tercatat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            // --- UTILITAS ---
            const fmtIDR = (n) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(n);
            const fmtNum = (n) => new Intl.NumberFormat('id-ID').format(n);

            async function fetchData(url) {
                try {
                    const r = await fetch(url);
                    return r.ok ? await r.json() : null;
                } catch (e) {
                    console.error(e);
                    return null;
                }
            }

            // --- 1. SETUP TANGGAL OTOMATIS ---
            const defaultStart = new Date(new Date().getFullYear(), 0, 1); // 1 Jan Tahun Ini
            const defaultEnd = new Date(); // Hari ini

            // Siapkan format string untuk dikirim ke Controller (YYYY-MM-DD to YYYY-MM-DD)
            const startStr = defaultStart.toISOString().split('T')[0];
            const endStr = defaultEnd.toISOString().split('T')[0];
            let currentDateRange = `${startStr} to ${endStr}`; // Variable Global untuk filter

            // Init Flatpickr (UI Kalender)
            flatpickr("#dashboardDateFilter", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [defaultStart, defaultEnd],
                onChange: function(selectedDates, dateStr) {
                    currentDateRange = dateStr;
                    reloadAllData(); // Reload semua data saat tanggal ganti
                }
            });

            function reloadAllData() {
                loadAdvanced();
                loadChart();
                loadMetrics();
                loadTopCust();
            }

            // --- 2. LOAD METRICS (ANGKA KARTU ATAS) ---
            async function loadMetrics() {
                const bg = await fetchData(
                    `{{ route('dashboard.data.bg-metrics') }}?date_range=${currentDateRange}`);
                if (bg) {
                    document.getElementById('metric_bg_total_value').textContent = fmtIDR(bg.total_value);
                    document.getElementById('metric_bg_expiring').textContent = fmtNum(bg.expiring);
                }
                const cust = await fetchData("{{ route('dashboard.data.customer-metrics') }}");
                if (cust) {
                    document.getElementById('metric_customers_total').textContent = fmtNum(cust.total);
                    document.getElementById('metric_customers_credit_exceeded').textContent = fmtNum(cust
                        .credit_exceeded);
                }
            }

            // --- VARIABEL GLOBAL UNTUK MENYIMPAN DATA CHART ---
            let globalBgData = null;
            let bgChartInstance = null;

            // --- FUNGSI LOAD DATA (Update function loadAdvanced yang lama) ---
            async function loadAdvanced() {
                const d = await fetchData(
                    `{{ route('dashboard.data.advanced-stats') }}?date_range=${currentDateRange}`);
                if (!d) return;

                // Simpan data ke variabel global agar bisa dipakai filtering tanpa fetch ulang
                globalBgData = d.bg_composition;

                // Update Text Card Bawah (Extremes)
                document.getElementById('adv_largest_bg_nominal').textContent = fmtIDR(d.largest_bg
                .nominal);
                document.getElementById('adv_largest_bg_cust').textContent = d.largest_bg.customer + ' (' +
                    d.largest_bg.number + ')';
                document.getElementById('adv_longest_cust_name').textContent = d.longest_customer.name;
                document.getElementById('adv_longest_cust_year').textContent = d.longest_customer.year;
                // document.getElementById('adv_cust_growth').textContent = d.cust_growth > 0 ? '+' + d.cust_growth : d.cust_growth;

                // Render Chart Default (All)
                filterBgChart('all');
            }

            // --- FUNGSI FILTERING CHART (BARU) ---
            window.filterBgChart = function(type) {
                if (!globalBgData) return;

                const data = globalBgData;
                const total = (parseInt(data.new) || 0) + (parseInt(data.extension) || 0) + (parseInt(data
                    .existing) || 0);

                let seriesData = [];
                let labelsData = [];
                let colorsData = [];
                let centerValue = 0;
                let centerLabel = "";
                let subtitle = "";

                // Konfigurasi berdasarkan tipe filter
                if (type === 'all') {
                    seriesData = [data.new, data.extension, data.existing];
                    labelsData = ['New', 'Extension', 'Existing'];
                    colorsData = ['#0d6efd', '#fd7e14', '#6f42c1']; // Blue, Orange, Purple
                    centerValue = total;
                    centerLabel = "TOTAL BG";
                    subtitle = "All Categories";
                } else {
                    // Logika untuk Single View (Selected vs Others)
                    let selectedValue = 0;
                    let selectedColor = '';
                    let selectedLabel = '';

                    if (type === 'new') {
                        selectedValue = data.new;
                        selectedColor = '#0d6efd';
                        selectedLabel = 'New BG';
                    } else if (type === 'extension') {
                        selectedValue = data.extension;
                        selectedColor = '#fd7e14';
                        selectedLabel = 'Extension';
                    } else if (type === 'existing') {
                        selectedValue = data.existing;
                        selectedColor = '#6f42c1';
                        selectedLabel = 'Existing';
                    }

                    const othersValue = total - selectedValue;

                    // Series: [Nilai Pilihan, Sisanya]
                    seriesData = [selectedValue, othersValue];
                    labelsData = [selectedLabel, 'Others'];
                    // Warna: [Warna Pilihan, Abu-abu pudar]
                    colorsData = [selectedColor, '#f3f4f6'];

                    // Hitung Persentase
                    const pct = total > 0 ? Math.round((selectedValue / total) * 100) : 0;

                    centerValue = pct + "%";
                    centerLabel = selectedLabel;
                    subtitle = "Showing " + selectedLabel + " Only";
                }

                // Update Text UI
                document.getElementById('bgBreakdownSubtitle').textContent = subtitle;
                document.getElementById('chartCenterValue').textContent = centerValue;
                document.getElementById('chartCenterText').textContent = centerLabel;

                // Config ApexCharts
                const options = {
                    series: seriesData,
                    labels: labelsData,
                    chart: {
                        type: 'donut',
                        height: 220,
                        fontFamily: 'inherit',
                        events: {
                            // Disable klik chart agar tidak mengganggu custom filter
                            dataPointSelection: (e, chart, opts) => {
                                return;
                            }
                        }
                    },
                    colors: colorsData,
                    legend: {
                        show: false
                    }, // Kita sembunyikan legend default agar lebih bersih
                    dataLabels: {
                        enabled: false
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%', // Donut lebih tipis agar terlihat modern
                                labels: {
                                    show: false
                                } // Matikan label bawaan, kita pakai custom HTML overlay
                            }
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#ffffff']
                    }, // Garis putih pemisah
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val, {
                                seriesIndex,
                                w
                            }) {
                                // Jika mode filter single, jangan tampilkan tooltip untuk "Others"
                                if (type !== 'all' && seriesIndex === 1) return val + " (Others)";
                                return val + " Transaksi";
                            }
                        }
                    }
                };

                // Render atau Update Chart
                if (bgChartInstance) {
                    bgChartInstance.updateOptions(options);
                } else {
                    bgChartInstance = new ApexCharts(document.querySelector("#bgTypeDonutChart"), options);
                    bgChartInstance.render();
                }
            };

            // --- 4. LOAD MAIN CHART (BULANAN) ---
            let chartInstance;
            async function loadChart() {
                const type = document.getElementById('dataTypeSelect').value;
                const d = await fetchData(
                    `{{ route('dashboard.data.monthly-stats') }}?date_range=${currentDateRange}&type=${type}`
                    );
                if (!d) return;

                // Update Box Summary di bawah chart
                const sum = a => a.reduce((x, y) => x + y, 0);
                document.getElementById('summaryCreated').textContent = sum(d.created);
                document.getElementById('summaryApproved').textContent = sum(d.approved);
                document.getElementById('summaryPending').textContent = sum(d.pending);

                const options = {
                    series: [{
                            name: 'Created',
                            data: d.created,
                            color: '#0d6efd'
                        },
                        {
                            name: 'Approved',
                            data: d.approved,
                            color: '#198754'
                        },
                        {
                            name: 'Pending',
                            data: d.pending,
                            color: '#ffc107'
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            columnWidth: '55%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    // Force X-Axis Jan-Des agar tampilan konsisten
                    xaxis: {
                        type: 'category',
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep',
                            'Okt', 'Nov', 'Des'
                        ],
                        labels: {
                            style: {
                                fontSize: '11px',
                                colors: '#6c757d'
                            }
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        show: false
                    },
                    grid: {
                        strokeDashArray: 4,
                        borderColor: '#f1f2f6',
                        xaxis: {
                            lines: {
                                show: false
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    legend: {
                        position: 'top',
                        fontSize: '12px'
                    }
                };

                if (chartInstance) chartInstance.destroy();
                chartInstance = new ApexCharts(document.querySelector("#monthlyBgChart"), options);
                chartInstance.render();
            }

            document.getElementById('dataTypeSelect').addEventListener('change', loadChart);

            // --- 5. LOAD LISTS (TANPA FILTER TANGGAL AGAR TETAP ADA DATA) ---
            async function loadTopCust() {
                const d = await fetchData(`{{ route('dashboard.data.top-customers-bg') }}?metric=value`);
                const l = document.getElementById('topCustomersList');
                l.innerHTML = '';
                if (!d || !d.length) {
                    l.innerHTML =
                        '<li class="list-group-item text-center small text-muted border-0">No data available</li>';
                    return;
                }

                const max = Math.max(...d.map(i => i.bg_value || 0));
                d.forEach((i, idx) => {
                    const val = i.bg_value || 0;
                    const pct = max > 0 ? (val / max) * 100 : 0;
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
                if (!d || !d.length) {
                    t.innerHTML =
                        '<tr><td colspan="4" class="text-center small text-muted border-0 py-4">No recent activities</td></tr>';
                    return;
                }
                d.forEach(r => {
                    let badge = 'bg-secondary';
                    if (r.status === 'approved') badge = 'bg-success';
                    else if (r.status === 'process') badge = 'bg-info';
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
                if (!d || !d.notifications.length) {
                    l.innerHTML =
                        '<div class="text-white-50 text-center small mt-4 opacity-50">No actions pending.</div>';
                    return;
                }
                d.notifications.forEach(n => {
                    l.innerHTML += `
                    <li>
                        <a href="${n.url}" class="d-flex text-white text-decoration-none p-2 rounded-3" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="me-2">
                                <div class="rounded-circle bg-warning bg-opacity-25 text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="ti ti-bell"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="text-truncate fw-bold mb-1" style="font-size: 0.8rem;">${n.message}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-white-50" style="font-size: 0.65rem;">Check Now</small>
                                    <small class="text-white-50" style="font-size: 0.65rem;">${n.timestamp}</small>
                                </div>
                            </div>
                        </a>
                    </li>`;
                });
            }

            // === EKSEKUSI OTOMATIS SAAT PAGE LOAD ===
            // Fungsi dipanggil langsung tanpa menunggu event listener kalender
            reloadAllData();
            loadTopCust();
            loadRecents();
            loadActions();
        });
    </script>
</x-app-layout>
