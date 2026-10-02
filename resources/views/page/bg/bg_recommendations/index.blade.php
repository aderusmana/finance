<x-app-layout>
    @section('title', 'BG Recommendations')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Recommendations</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-recommendations.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Recommendations</a></li>
            </ul>
        </div>
    </div>
    {{-- 1. HEADER SECTION (COMPACT HERO CARD) --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-3 d-flex align-items-center justify-content-between"
                     style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); position: relative; min-height: 80px;">

                    {{-- Background Pattern --}}
                    <div style="position: absolute; right: 0; top: 0; bottom: 0; width: 40%; background: linear-gradient(to left, rgba(255,255,255,0.1), transparent); pointer-events: none;"></div>

                    {{-- Bagian Kiri: Judul & Ikon (Compact) --}}
                    <div class="d-flex align-items-center position-relative z-1">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm"
                             style="width: 48px; height: 48px; backdrop-filter: blur(5px);">
                            <i class="ph-fill ph-trend-up text-white fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white tracking-wide">
                                Credit Limit Recommendations
                            </h5>
                            <span class="text-white-50 small" style="font-size: 0.85rem;">
                                Monitor Bank Guarantee status & limit history.
                            </span>
                        </div>
                    </div>

                    {{-- Bagian Kanan: Quick Stats (Compact Row) --}}
                    <div class="d-none d-md-flex align-items-center gap-2 position-relative z-1 text-white">
                        {{-- Stat: Action --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-end line-height-sm">
                                <span class="d-block text-uppercase text-white-50 fw-bold" style="font-size: 0.7rem;">Action Needed</span>
                                <span class="fs-5 fw-bold text-warning" id="expiringCountBadge">0</span>
                            </div>
                            <i class="ph-fill ph-warning-circle fs-4 text-warning"></i>
                        </div>

                        <div class="vr bg-white opacity-25 mx-2" style="height: 30px;"></div>

                        {{-- Stat: History --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="text-end line-height-sm">
                                <span class="d-block text-uppercase text-white-50 fw-bold" style="font-size: 0.7rem;">History Data</span>
                                <span class="fs-5 fw-bold" id="historyCountBadge">0</span>
                            </div>
                            <i class="ph-fill ph-clock-counter-clockwise fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="alert alert-light-warning border-danger border-opacity-25 d-flex align-items-center">
                        <i class="ph-fill ph-warning-circle text-danger me-2 fs-5"></i>
                        <small class="text-danger fw-bold">List of customers whose Bank Guarantees are expiring soon or require immediate action.</small>
                    </div>
                    <style>
                        .custom-pill-tabs {
                            background-color: #f8fafc;
                            padding: 6px;
                            border-radius: 50rem;
                            display: inline-flex;
                            border: 1px solid #e2e8f0;
                            gap: 5px;
                        }
                        .custom-pill-tabs .nav-link {
                            color: #64748b;
                            border-radius: 50rem;
                            font-weight: 600;
                            font-size: 1rem;
                            padding: 12px 48px;
                            min-width: 240px;
                            justify-content: center;
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            border: none;
                            display: flex;
                            align-items: center;
                            background: transparent;
                        }
                        .custom-pill-tabs .nav-link i {
                            color: #94a3b8;
                            transition: all 0.3s ease;
                        }
                        .custom-pill-tabs .nav-link:hover {
                            color: #1e3a8a;
                            background-color: #f1f5f9;
                        }
                        .custom-pill-tabs .nav-link:hover i {
                            color: #1e3a8a;
                        }
                        .custom-pill-tabs .nav-link.active {
                            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                            color: white !important;
                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
                        }
                        .custom-pill-tabs .nav-link.active i {
                            color: white !important;
                        }
                    </style>
                    <ul class="nav nav-pills custom-pill-tabs mb-4" id="recommendationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                    id="expiring-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#expiring-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="expiring-pane"
                                    aria-selected="true">
                                <i class="ph-fill ph-warning-circle me-2 fs-5"></i> Expiring (Action Needed)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                    id="history-tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#history-pane"
                                    type="button"
                                    role="tab"
                                    aria-controls="history-pane"
                                    aria-selected="false">
                                <i class="ph-bold ph-clock-counter-clockwise me-2 fs-5"></i> History
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="recommendationTabContent">
                        <div class="tab-pane fade show active" id="expiring-pane" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 align-middle" id="sampleTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%" class="text-center rounded-start">No</th>
                                            <th width="35%">BG Number</th>
                                            <th width="35%">Customer Info</th>
                                            <th width="25%">Current BG (Rp.)</th>
                                            <th width="15%" class="text-center rounded-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data diisi oleh DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="history-pane" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover w-100 align-middle" id="historyTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%" class="text-center rounded-start">No</th>
                                            <th width="25%">BG Number</th>
                                            <th width="25%">Customer</th>
                                            <th width="15%">Avg Sales</th>
                                            <th width="15%">Rec. Limit</th>
                                            <th width="15%">Set BG (Final)</th>
                                            <th width="15%" class="text-center">Status</th>
                                            <th width="10%" class="text-center rounded-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data diisi oleh DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: MAIN PROCESS RECOMMENDATION --}}
    <div class="modal fade" id="recModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="ph-bold ph-calculator f-s-24 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">Credit Analysis & Recommendation</h5>
                            <small class="text-white">System-assisted credit limit calculation</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="recForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="recId">

                    <input type="hidden" id="raw_current_bg" value="0">
                    <input type="hidden" name="credit_limit_updated" id="input_limit_updated">

                    <div class="modal-body p-0 bg-light">
                        {{-- SECTION 1: CUSTOMER INFO --}}
                        <div class="bg-white px-4 py-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="d-inline-block text-start bg-soft-primary px-4 py-2 rounded-3 border border-primary border-opacity-10 shadow-sm">
                                        <small class="d-block text-primary fw-bold f-s-11 mb-1 opacity-75">CUSTOMER NAME</small>
                                        <h3 class="fw-bold text-primary mb-0 f-s-24" id="disp_customer">-</h3>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <div class="d-inline-block text-start bg-soft-primary px-4 py-2 rounded-3 border border-primary border-opacity-10 shadow-sm">
                                        <small class="d-block text-primary fw-bold f-s-11 mb-1 opacity-75">CURRENT EXISTING BG</small>
                                        <h3 class="fw-bold text-primary mb-0 f-s-24" id="disp_current_bg">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mt-3">
                                <div class="col-6 col-md"><div class="p-2 border rounded bg-light text-center h-100"><small class="text-muted d-block f-s-10 mb-1">TOP (Days)</small><span class="fw-bold text-dark" id="disp_top">-</span><input type="hidden" id="top"></div></div>
                                <div class="col-6 col-md"><div class="p-2 border rounded bg-light text-center h-100"><small class="text-muted d-block f-s-10 mb-1">Lead Time</small><span class="fw-bold text-dark" id="disp_lead">-</span><input type="hidden" id="lead_time"></div></div>
                                <div class="col-6 col-md"><div class="p-2 border rounded bg-light text-center h-100"><small class="text-muted d-block f-s-10 mb-1">Inflation</small><span class="fw-bold text-dark" id="disp_inflation">-</span><input type="hidden" id="inflation"></div></div>
                                <div class="col-6 col-md"><div class="p-2 border rounded bg-light text-center h-100"><small class="text-muted d-block f-s-10 mb-1">Tax (PPN)</small><span class="fw-bold text-dark" id="disp_tax">-</span><input type="hidden" id="tax_val"></div></div>
                                <div class="col-12 col-md-3"><div class="p-2 border border-primary rounded bg-primary bg-opacity-10 text-center h-100 d-flex flex-column justify-content-center"><small class="text-primary fw-bold d-block f-s-10 mb-1 text-uppercase">Limit Rule</small><strong class="text-primary f-s-16"><span id="disp_rule">-</span></strong><input type="hidden" id="rule_percent"></div></div>
                            </div>
                        </div>

                        <div class="row g-0">
                            {{-- SECTION 2: CALCULATION --}}
                            <div class="col-lg-7 border-end bg-white">
                                <div class="p-4">
                                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="ph-bold ph-chart-bar me-2 text-primary"></i>Input & Calculation
                                    </h6>

                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark f-s-12">AVERAGE SALES (Monthly)</label>
                                        <div class="d-flex gap-2">
                                            <div class="input-group input-group-lg shadow-sm flex-grow-1" style="cursor: pointer;">
                                                <span class="input-group-text bg-light text-muted fw-bold border-end-0">Rp</span>
                                                <input type="text" id="average_display" class="form-control border-start-0 fw-bold text-dark bg-white" placeholder="0" readonly style="cursor: pointer;">
                                                <input type="hidden" name="average" id="average">
                                            </div>
                                            <button type="button" class="btn btn-warning shadow-sm fw-bold px-3" id="btnOpenPeriodModal">
                                                <i class="ph-bold ph-calendar-plus me-1"></i> Manage Details
                                            </button>
                                        </div>
                                    </div>

                                    <hr class="border-secondary border-opacity-10 my-4">

                                    <div class="bg-light border rounded-3 overflow-hidden">
                                        <table class="table table-borderless table-sm mb-0 align-middle">
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted w-50">Est. PPN <small class="fw-bold text-dark" id="lbl_tax_calc"></small></td>
                                                <td class="pe-3 py-2 text-end fw-bold text-dark f-s-14" id="calc_avg_ppn">-</td>
                                            </tr>
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted">Multiplier Factors <small class="text-primary">(TOP & Inflation)</small></td>
                                                <td class="pe-3 py-2 text-end"><span class="badge bg-primary bg-opacity-10 text-primary f-s-12 px-3" id="calc_factor_val">-</span></td>
                                            </tr>
                                            <tr class="border-bottom border-white bg-white">
                                                <td class="ps-3 py-2 fw-bold text-dark">Recommendation Limit</td>
                                                <td class="pe-3 py-2 text-end fw-bold text-dark f-s-15" id="calc_rec_limit">-</td>
                                            </tr>
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted"><i class="ph-arrow-elbow-down-right me-2"></i>FK Limit Rule <small class="text-dark fw-bold" id="lbl_rule_calc"></small></td>
                                                <td class="pe-3 py-2 text-end fw-bold text-secondary" id="calc_fk_limit">-</td>
                                            </tr>
                                            <tr class="bg-success bg-opacity-10">
                                                <td class="ps-3 py-3 fw-bold text-success">ROUNDED (Millions)</td>
                                                <td class="pe-3 py-3 text-end fw-bold text-success f-s-18" id="calc_rounded">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: DECISION --}}
                            <div class="col-lg-5 border-end bg-white">
                                <div class="p-4 h-100 d-flex flex-column">
                                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="ph-bold ph-check-circle me-2 text-success"></i>Final Decision
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark f-s-12">SET BG (APPROVED NOMINAL) <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg shadow-sm">
                                            <span class="input-group-text bg-success text-white border-success fw-bold">Rp</span>
                                            {{-- REVISI 2: Perubahan ID ke average mentrigger Set BG auto --}}
                                            <input type="number" name="set_bg" id="set_bg" class="form-control border-success text-success fw-bold" placeholder="0">
                                        </div>
                                        <small class="text-muted f-s-10">Automatically select the highest value (Rounded vs Current BG)</small>
                                    </div>

                                    <div class="p-4 border border-primary border-opacity-25 rounded-3 bg-primary bg-opacity-10 text-center mb-4">
                                        <small class="text-uppercase text-primary fw-bold f-s-11 mb-2 d-block letter-spacing-1">CREDIT LIMIT UPDATED</small>
                                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                            <h2 class="fw-bold text-primary mb-0 f-s-28" id="calc_limit_updated">Rp 0</h2>
                                            <button type="button" class="btn btn-sm btn-outline-primary bg-white shadow-sm rounded-circle p-1" id="btnRoundLimit" title="Round to the Nearest Million">
                                                <i class="ph-bold ph-arrows-in-line-vertical f-s-16"></i>
                                            </button>
                                        </div>
                                        <div class="d-inline-block bg-white text-primary px-3 py-1 rounded-pill border border-primary border-opacity-10 shadow-sm">
                                            <small class="f-s-10 fw-bold">Set BG / (Rule %)</small>
                                        </div>
                                    </div>

                                    <div class="mb-4 flex-grow-1">
                                        <label class="form-label fw-bold text-muted f-s-12">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control bg-white" rows="3" placeholder="Leave empty for auto-generated date"></textarea>
                                    </div>

                                    <div class="row g-2 mt-auto">
                                        <div class="col-8">
                                            <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold shadow-sm">Save Recommendation</button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-white border w-100 btn-lg fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: PERIOD INPUT --}}
    <div class="modal fade" id="periodModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold"><i class="ph-bold ph-calendar-blank me-2"></i>Sales Details Per Period</h5>
                    <button type="button" class="btn-close" id="btnClosePeriodModal"></button>
                </div>
                <div class="modal-body bg-light">

                    {{-- Kontrol Generator (Versi Dinamis JS) --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row align-items-end g-3">
                                {{-- Periode Mulai --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Start Period</label>
                                    <div class="input-group">
                                        {{-- Kosongkan option, nanti diisi JS --}}
                                        <select id="start_month" class="form-select bg-white"></select>
                                        <select id="start_year" class="form-select bg-white fw-bold"></select>
                                    </div>
                                </div>

                                {{-- Periode Selesai --}}
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">End Period</label>
                                    <div class="input-group">
                                        {{-- Kosongkan option, nanti diisi JS --}}
                                        <select id="end_month" class="form-select bg-white"></select>
                                        <select id="end_year" class="form-select bg-white fw-bold"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- List Input Dinamis --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-muted">Monthly Nominal Input</h6>
                                <span class="badge bg-info text-dark" id="period-counter">0 Months</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="period-inputs-wrapper" class="p-3" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center text-muted py-5">
                                    <i class="ph-duotone ph-calendar-slash f-s-32 mb-2"></i>
                                    <p class="mb-0">Please select the period and click the "Generate" button</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-end py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block text-start">Total Sales:</small>
                                    <h5 class="fw-bold text-success mb-0" id="live-total-period">Rp 0</h5>
                                </div>
                                <button type="button" class="btn btn-primary px-4 fw-bold" id="btnSavePeriod">
                                    <i class="ph-bold ph-check me-2"></i> Save & Use
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 3: SALES REVIEW & APPROVAL (PAK RONAL - dep-SNM) --}}
    <div class="modal fade" id="salesReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white"><i class="ph-bold ph-stamp me-2"></i>Sales Review & Approval (Pak Ronal - dep-SNM)</h5>
                        <small class="text-white text-opacity-75">Review Credit Limit & SET BG recommendation before sending to customer</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" id="sales_review_id">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted text-uppercase fw-bold f-s-11">Customer</small>
                                    <h5 class="fw-bold text-dark mb-0" id="sales_disp_customer">-</h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <small class="text-muted text-uppercase fw-bold f-s-11">Workflow Status</small>
                                    <div><span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1 rounded-pill fw-bold"><i class="ph-bold ph-hourglass me-1"></i>Waiting Sales Approval</span></div>
                                </div>
                            </div>
                            <hr class="my-3 border-secondary border-opacity-25">
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <small class="text-muted d-block f-s-11">Avg Sales</small>
                                        <span class="fw-bold text-dark" id="sales_disp_avg">-</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <small class="text-muted d-block f-s-11">TOP</small>
                                        <span class="fw-bold text-dark" id="sales_disp_top">-</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <small class="text-muted d-block f-s-11">Lead Time</small>
                                        <span class="fw-bold text-dark" id="sales_disp_lead">-</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-center">
                                        <small class="text-muted d-block f-s-11">Inflation</small>
                                        <span class="fw-bold text-dark" id="sales_disp_inflation">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="p-3 border border-success rounded bg-success bg-opacity-10">
                                        <small class="text-success fw-bold text-uppercase d-block f-s-11">SET BG (Approved Nominal)</small>
                                        <h4 class="fw-bold text-success mb-0" id="sales_disp_set_bg">Rp 0</h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border border-primary rounded bg-primary bg-opacity-10">
                                        <small class="text-primary fw-bold text-uppercase d-block f-s-11">Credit Limit Updated</small>
                                        <h4 class="fw-bold text-primary mb-0" id="sales_disp_limit_updated">Rp 0</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted text-uppercase fw-bold f-s-11">Admin Notes</small>
                                <div class="p-2 rounded bg-light border small text-dark" id="sales_disp_notes">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Rejection reason box (hidden by default) --}}
                    <div id="salesRejectBox" class="card border-danger border-opacity-50 shadow-sm mb-3 d-none" style="background-color: #fef2f2;">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold text-danger f-s-13"><i class="ph-bold ph-warning-circle me-1"></i>Rejection Reason (Wajib Diisi)</label>
                            <textarea id="sales_rejection_reason" class="form-control bg-white" rows="2" placeholder="Tuliskan alasan penolakan agar dapat disesuaikan oleh Admin-RTM..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-danger px-4 fw-bold" id="btnSalesToggleReject">
                        <i class="ph-bold ph-x-circle me-1"></i> Reject
                    </button>
                    <button type="button" class="btn btn-danger px-4 fw-bold d-none" id="btnSalesConfirmReject">
                        <i class="ph-bold ph-check me-1"></i> Confirm Reject
                    </button>
                    <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" id="btnSalesApprove">
                        <i class="ph-bold ph-check-circle me-1"></i> Approve & Send to Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update Badge Header saat tabel selesai load/draw
        $('#sampleTable').on('draw.dt', function () {
            let info = $('#sampleTable').DataTable().page.info();
            $('#expiringCountBadge').text(info.recordsTotal);
        });

        $('#historyTable').on('draw.dt', function () {
            let info = $('#historyTable').DataTable().page.info();
            $('#historyCountBadge').text(info.recordsTotal);
        });

        $(document).ready(function() {
            const fmt = (num) => new Intl.NumberFormat('id-ID', {
                style:'currency',
                currency:'IDR',
                maximumFractionDigits:0
            }).format(num);

            const fmtDecimal = (num) => new Intl.NumberFormat('id-ID', {
                style:'currency',
                currency:'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);

            let rawLimitUpdatedValue = 0;

            function initDateDropdowns() {
                const months = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                let monthOptions = '';
                months.forEach((name, index) => {
                    let val = String(index + 1).padStart(2, '0');
                    monthOptions += `<option value="${val}">${name}</option>`;
                });

                $('#start_month').html(monthOptions);
                $('#end_month').html(monthOptions);

                let currentYear = new Date().getFullYear();
                let startYear = currentYear - 5;
                let endYear = currentYear + 5;

                let yearOptions = '';
                for(let i = startYear; i <= endYear; i++) {
                    let selected = (i === currentYear) ? 'selected' : '';
                    yearOptions += `<option value="${i}" ${selected}>${i}</option>`;
                }

                $('#start_year').html(yearOptions);
                $('#end_year').html(yearOptions);
            }

            initDateDropdowns();

            let tableExpiring = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-recommendations.index') }}", data: { type: 'expiring' } },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'bg_number', className: 'fw-bold' },
                    { data: 'customer_name', className: 'fw-bold' },
                    { data: 'current_bg' },
                    { data: 'action', className: 'text-center' }
                ]
            });

            let tableHistory = $('#historyTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-recommendations.index') }}", data: { type: 'history' } },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'bg_number', className: 'fw-bold' },
                    { data: 'customer_name' },
                    { data: 'average' },
                    { data: 'recommended_credit_limit' },
                    { data: 'set_bg' },
                    { data: 'status' },
                    { data: 'action' }
                ],
                order: [[1, 'asc']]
            });

            $(document).on('click', '.btn-process, .btn-edit-rec', function() {
                let id = $(this).data('id');

                $('#recForm')[0].reset();
                $('#recId').val(id);
                $('#hidden-period-data-container').empty();
                $('#period-inputs-wrapper').empty();

                $('#disp_customer').text('Loading...');
                $('#disp_current_bg').text('-');
                $('#disp_top').text('-'); $('#disp_lead').text('-'); $('#disp_inflation').text('-');
                $('#disp_tax').text('-'); $('#disp_rule').text('-');

                $('#average_display').val('');
                $('#lbl_tax_calc').text(''); $('#calc_avg_ppn').text('-'); $('#calc_factor_val').text('-');
                $('#calc_rec_limit').text('-'); $('#lbl_rule_calc').text(''); $('#calc_fk_limit').text('-');
                $('#calc_rounded').text('-');

                $('#set_bg').val('');
                $('#calc_limit_updated').text('Rp 0');

                $('#top').val(0); $('#lead_time').val(0); $('#inflation').val(0);
                $('#tax_val').val(0); $('#rule_percent').val(0); $('#average').val(0); $('#raw_current_bg').val(0);

                $.get("{{ url('bg/bg-recommendations') }}/" + id, function(data) {
                    $('#disp_customer').text(data.customer ? data.customer.name : '-');
                    $('#disp_current_bg').text(fmt(data.current_bg));
                    $('#raw_current_bg').val(data.raw_current_bg || 0);

                    let inflRaw = parseFloat(data.inflation); $('#disp_inflation').text(inflRaw + '%'); $('#inflation').val(inflRaw);
                    let taxRaw = parseFloat(data.tax_value); let taxPercent = Math.round(taxRaw * 100);
                    $('#disp_tax').text(taxPercent + '%'); $('#tax_val').val(taxRaw); $('#lbl_tax_calc').text('(Avg x ' + taxPercent + '%)');

                    $('#disp_top').text(data.top); $('#top').val(data.top);
                    $('#disp_lead').text(data.lead_time); $('#lead_time').val(data.lead_time);

                    let ruleRaw = parseFloat(data.calculated_rule_percent); $('#disp_rule').text(ruleRaw + '%'); $('#rule_percent').val(ruleRaw); $('#lbl_rule_calc').text('(x ' + ruleRaw + '%)');

                    if(data.average > 0) {
                        $('#average').val(data.average);
                        $('#average_display').val(fmt(data.average));
                    }
                    if(data.set_bg > 0) {
                        $('#set_bg').val(data.set_bg);
                    }

                    $('#notes').val(data.notes || '');

                    loadExistingPeriods(data.periods);

                    calculateAll();
                    $('#recModal').modal('show');
                });
            });

            // 4. LOGIC PERIODE (Load Existing & Generate New)
            function loadExistingPeriods(periods) {
                // Default ke bulan ini jika kosong
                if(!periods || periods.length === 0) {
                    let now = new Date();
                    let m = String(now.getMonth() + 1).padStart(2, '0');
                    let y = now.getFullYear();
                    $('#start_month').val(m); $('#start_year').val(y);
                    $('#end_month').val(m); $('#end_year').val(y);
                    generatePeriods();
                    return;
                }

                // Urutkan tanggal untuk ambil range
                let dates = periods.map(p => p.period_date.substring(0, 7)); // YYYY-MM
                dates.sort();

                // Set Dropdown Start & End berdasarkan data
                let minDate = dates[0].split('-'); // [YYYY, MM]
                let maxDate = dates[dates.length - 1].split('-');

                $('#start_year').val(minDate[0]); $('#start_month').val(minDate[1]);
                $('#end_year').val(maxDate[0]); $('#end_month').val(maxDate[1]);

                // Render List
                let wrapper = $('#period-inputs-wrapper');
                wrapper.empty();

                let total = 0;
                periods.forEach(p => {
                    let d = new Date(p.period_date);
                    let monthName = d.toLocaleString('id-ID', { month: 'long', year: 'numeric' });
                    let valFmt = new Intl.NumberFormat('id-ID').format(p.amount);
                    total += parseFloat(p.amount);

                    let datePrefix = p.period_date.substring(0, 7);
                    let monthValue = datePrefix + '-01';
                    let html = `
                        <div class="row mb-2 align-items-center period-row border-bottom pb-2">
                            <label class="col-sm-4 col-form-label text-end small fw-bold">${monthName}</label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control period-amount text-end fw-bold" value="${valFmt}" onkeyup="formatRupiah(this)">
                                    <input type="hidden" class="period-date-val" value="${monthValue}">
                                    <input type="hidden" class="period-amount-real" value="${p.amount}">
                                </div>
                            </div>
                        </div>`;
                    wrapper.append(html);
                });
                $('#period-counter').text(periods.length + " Months");
                $('#live-total-period').text(fmt(total));
            }

            $('#btnOpenPeriodModal').click(function() { $('#periodModal').modal('show'); });
            $('#btnClosePeriodModal').click(function() { $('#periodModal').modal('hide'); });

            $('#average_display').on('click', function() {
                Swal.fire({
                    icon: 'warning', title: 'Input Locked',
                    html: 'Please fill in the period by clicking the button <br><b>"Manage Details"</b>.',
                    confirmButtonColor: '#ffc107', confirmButtonText: 'OK'
                });
            });

            // AUTO GENERATE PERIODS ON DROPDOWN CHANGE
            function generatePeriods() {
                let startY = $('#start_year').val();
                let startM = $('#start_month').val();
                let endY = $('#end_year').val();
                let endM = $('#end_month').val();

                let startVal = startY + '-' + startM;
                let endVal = endY + '-' + endM;

                if (startVal > endVal) {
                    $('#period-inputs-wrapper').html('<div class="text-center text-danger py-4 fw-bold"><i class="ph-fill ph-warning-circle fs-1 mb-2 d-block"></i>Start period cannot be greater than end period</div>');
                    $('#period-counter').text("0 Months");
                    $('#live-total-period').text("Rp 0");
                    return;
                }

                let startDate = new Date(startVal + "-01");
                let endDate = new Date(endVal + "-01");
                let wrapper = $('#period-inputs-wrapper');
                
                // Simpan value inputan yang sudah ada agar tidak keriset
                let savedValues = {};
                $('.period-amount-real').each(function() {
                    let date = $(this).siblings('.period-date-val').val();
                    let val = $(this).val();
                    let displayVal = $(this).siblings('.period-amount').val();
                    savedValues[date] = { real: val, display: displayVal };
                });

                wrapper.empty();

                let currentDate = startDate;
                let countMonth = 0;
                let newTotal = 0;

                while (currentDate <= endDate) {
                    let monthName = currentDate.toLocaleString('id-ID', { month: 'long', year: 'numeric' });
                    let year = currentDate.getFullYear();
                    let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
                    let monthValue = `${year}-${month}-01`;

                    let realVal = "0";
                    let displayVal = "";
                    
                    if (savedValues[monthValue]) {
                        realVal = savedValues[monthValue].real;
                        displayVal = savedValues[monthValue].display;
                        newTotal += parseFloat(realVal) || 0;
                    }

                    let html = `
                        <div class="row mb-2 align-items-center period-row border-bottom pb-2">
                            <label class="col-sm-4 col-form-label text-end small fw-bold">${monthName}</label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control period-amount text-end fw-bold" placeholder="0" onkeyup="formatRupiah(this)" value="${displayVal}">
                                    <input type="hidden" class="period-date-val" value="${monthValue}">
                                    <input type="hidden" class="period-amount-real" value="${realVal}">
                                </div>
                            </div>
                        </div>`;

                    wrapper.append(html);
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    countMonth++;
                }
                
                $('#period-counter').text(countMonth + " Months");
                $('#live-total-period').text(fmt(newTotal));
            }

            $('#start_month, #start_year, #end_month, #end_year').on('change', generatePeriods);

            $(document).on('keyup', '.period-amount', function() {
                let val = $(this).val().replace(/[^,\d]/g, '').toString();
                $(this).siblings('.period-amount-real').val(val);
                let total = 0;
                $('.period-amount-real').each(function() { total += parseFloat($(this).val()) || 0; });
                $('#live-total-period').text(fmt(total));
            });

            // 5. AJAX SIMPAN PERIODE
            $('#btnSavePeriod').click(function() {
                let id = $('#recId').val();
                let periodData = [];
                let total = 0;

                $('.period-amount-real').each(function() {
                    let amount = parseFloat($(this).val()) || 0;
                    let date = $(this).siblings('.period-date-val').val();
                    if(amount > 0) {
                        periodData.push({ date: date, amount: amount });
                        total += amount;
                    }
                });

                if(periodData.length === 0 && total === 0) {
                    Swal.fire('Warning', 'No amount has been filled in', 'warning');
                    return;
                }

                let btn = $(this);
                btn.prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ url('bg/bg-recommendations') }}/" + id + "/periods",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        periods: periodData
                    },
                    success: function(res) {
                        // Update Average di Form Utama
                        $('#average').val(res.total_average);
                        $('#average_display').val(fmt(res.total_average));

                        calculateAll(); // Trigger kalkulasi

                        $('#periodModal').modal('hide');
                        Swal.fire({
                            icon: 'success', title: 'Stored',
                            text: 'Period details stored & Average updated.',
                            timer: 1500, showConfirmButton: false
                        });
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Failed to store period', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<i class="ph-bold ph-check me-2"></i> Save & Use');
                    }
                });
            });

            // 6. GLOBAL CALCULATION
            function calculateAll() {
                let avg = parseFloat($('#average').val()) || 0;
                let top = parseFloat($('#top').val()) || 0;
                let lead = parseFloat($('#lead_time').val()) || 0;
                let inflation = parseFloat($('#inflation').val()) || 130;
                let rule = parseFloat($('#rule_percent').val()) || 0;
                let taxRaw = parseFloat($('#tax_val').val()) || 0.11;

                // 1. Est PPN
                let valAvgPpn = avg * taxRaw;
                $('#calc_avg_ppn').text(fmt(valAvgPpn));

                // 2. Factor
                let timeFactor = top > 0 ? (top + lead) / top : 1;
                let inflFactor = inflation / 100;
                let totalFactor = timeFactor * inflFactor;
                $('#calc_factor_val').text(totalFactor.toFixed(3));

                // 3. Rec Limit
                let recLimit = valAvgPpn * totalFactor;
                $('#calc_rec_limit').text(fmt(recLimit));

                // 4. FK Limit Rule
                let activeRule = rule > 0 ? rule : 100;
                let fkLimit = recLimit * (activeRule / 100);
                $('#calc_fk_limit').text(fmt(fkLimit));

                // 5. Rounded
                let rounded = Math.round(fkLimit / 1000000) * 1000000;
                $('#calc_rounded').text(fmt(rounded));

                // Logic Set BG (Max of Rounded vs Current)
                let currentBg = parseFloat($('#raw_current_bg').val()) || 0;
                let recommendedSetBg = (rounded > currentBg) ? rounded : currentBg;

                // Auto-fill jika user tidak sedang mengetik
                if (!$('#set_bg').is(':focus')) {
                    $('#set_bg').val(recommendedSetBg);
                }

                // 6. Updated Limit Display
                let setBgUser = parseFloat($('#set_bg').val()) || 0;
                rawLimitUpdatedValue = setBgUser / (activeRule / 100);

                // Tampilkan TANPA desimal (receh)
                $('#calc_limit_updated').text(fmt(rawLimitUpdatedValue));
                $('#input_limit_updated').val(rawLimitUpdatedValue);
            }

            $('#set_bg').on('input', calculateAll);

            $('#btnRoundLimit').click(function() {
                if(rawLimitUpdatedValue > 0) {
                    let roundedValue = Math.round(rawLimitUpdatedValue / 1000000) * 1000000;
                    rawLimitUpdatedValue = roundedValue;

                    $('#calc_limit_updated').text(fmt(roundedValue));
                    $('#calc_limit_updated').fadeOut(100).fadeIn(100);
                    $('#input_limit_updated').val(roundedValue);
                }
            });

            // Helper Format Rupiah
            window.formatRupiah = function(element) {
                let value = element.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                element.value = rupiah;
            }

            // 7. SUBMIT FORM UTAMA (DENGAN DETAIL KONFIRMASI)
            $('#recForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#recId').val();
                let formData = $(this).serialize();

                // --- 1. AMBIL DATA UNTUK PREVIEW ---
                let custName = $('#disp_customer').text();
                let avgSales = $('#average_display').val() || 'Rp 0';

                // Format Set BG Manual agar rapi
                let setBgVal = parseFloat($('#set_bg').val()) || 0;
                let setBgFmt = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
                }).format(setBgVal);

                let limitUpdated = $('#calc_limit_updated').text();
                let notes = $('#notes').val() || '-';

                // --- 2. HTML CONTENT UNTUK SWEETALERT ---
                let htmlContent = `
                    <div class="text-start bg-light p-3 rounded border">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-secondary small fw-bold">Customer</td>
                                <td class="text-end fw-bold text-dark text-wrap" style="max-width: 200px;">${custName}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary small fw-bold">Avg. Sales</td>
                                <td class="text-end fw-bold text-dark">${avgSales}</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="my-1 border-secondary border-opacity-25"></td>
                            </tr>
                            <tr>
                                <td class="text-secondary small fw-bold align-middle">Set BG (Final)</td>
                                <td class="text-end fw-bold text-success fs-5">${setBgFmt}</td>
                            </tr>
                            <tr>
                                <td class="text-secondary small fw-bold">Limit Updated</td>
                                <td class="text-end fw-bold text-primary">${limitUpdated}</td>
                            </tr>
                             <tr>
                                <td colspan="2"><hr class="my-1 border-secondary border-opacity-25"></td>
                            </tr>
                            <tr>
                                <td class="text-secondary small fw-bold">Notes</td>
                                <td class="text-end small fst-italic text-muted text-wrap" style="max-width: 200px;">${notes}</td>
                            </tr>
                        </table>
                    </div>
                    <p class="text-center text-muted mt-3 mb-0 small">Please ensure the data above is correct before saving.</p>
                `;

                // --- 3. TAMPILKAN SWEETALERT ---
                Swal.fire({
                    title: 'Recommendation Confirmation',
                    html: htmlContent, // Gunakan HTML custom di atas
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ph-bold ph-check me-1"></i> Yes, Save',
                    confirmButtonColor: '#3085d6',
                    cancelButtonText: 'Check Again',
                    cancelButtonColor: '#d33',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan Loading state
                        Swal.fire({
                            title: 'Saving...',
                            text: 'Please wait a moment',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: "{{ url('bg/bg-recommendations') }}/" + id,
                            method: "PUT",
                            data: formData,
                            success: function(res) {
                                $('#recModal').modal('hide');
                                tableExpiring.ajax.reload(); // Reload tabel
                                tableHistory.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // --- RESET FORM SETELAH SUKSES ---
                                $('#recForm')[0].reset();

                                // Reset Tampilan Angka / Label
                                $('#live-total-period').text(fmt(0));
                                $('#period-counter').text('0 Bulan');
                                $('#period-inputs-wrapper').empty();
                                $('#hidden-period-data-container').empty();

                                // Reset Kalkulasi
                                $('#average_display').val('');
                                $('#calc_limit_updated').text('Rp 0');
                                $('#calc_rec_limit').text('-');
                                $('#calc_rounded').text('-');

                                // Reset Variabel Global
                                if(typeof rawLimitUpdatedValue !== 'undefined') {
                                    rawLimitUpdatedValue = 0;
                                }
                            },
                            error: function(err) {
                                let msg = err.responseJSON ? err.responseJSON.message : 'System error occurred';
                                Swal.fire('Failed!', msg, 'error');
                            }
                        });
                    }
                });
            });
            // --- SALES REVIEW & APPROVAL HANDLERS (PAK RONAL - dep-SNM) ---
            $(document).on('click', '.btn-sales-review', function() {
                let id = $(this).data('id');
                $('#sales_review_id').val(id);
                $('#salesRejectBox').addClass('d-none');
                $('#sales_rejection_reason').val('');
                $('#btnSalesConfirmReject').addClass('d-none');
                $('#btnSalesToggleReject').removeClass('d-none').html('<i class="ph-bold ph-x-circle me-1"></i> Reject');
                $('#btnSalesApprove').removeClass('d-none');

                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching recommendation details',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ url('bg/bg-recommendations') }}/" + id,
                    method: 'GET',
                    success: function(res) {
                        Swal.close();
                        $('#sales_disp_customer').text(res.customer ? res.customer.name : '-');
                        $('#sales_disp_avg').text(fmt(res.average || 0));
                        $('#sales_disp_top').text((res.top || 0) + ' Days');
                        $('#sales_disp_lead').text((res.lead_time || 0) + ' Days');
                        $('#sales_disp_inflation').text((res.inflation || 0) + '%');
                        $('#sales_disp_set_bg').text(fmt(res.set_bg || 0));
                        $('#sales_disp_limit_updated').text(fmt(res.credit_limit_updated || 0));
                        $('#sales_disp_notes').text(res.notes || '-');
                        $('#salesReviewModal').modal('show');
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Failed to fetch recommendation details.', 'error');
                    }
                });
            });

            // Sales Approve Handler
            $('#btnSalesApprove').on('click', function() {
                let id = $('#sales_review_id').val();
                let cust = $('#sales_disp_customer').text();
                let setBg = $('#sales_disp_set_bg').text();

                Swal.fire({
                    title: 'Approve Recommendation?',
                    html: `Approve credit limit recommendation for <b>${cust}</b> (SET BG: <b>${setBg}</b>) and send secure portal link to Customer?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ph-bold ph-check-circle me-1"></i> Yes, Approve & Send',
                    confirmButtonColor: '#10b981',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Approving and sending to customer',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: "{{ url('bg/bg-recommendations') }}/" + id + "/sales-approve",
                            method: "POST",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                $('#salesReviewModal').modal('hide');
                                tableExpiring.ajax.reload();
                                tableHistory.ajax.reload();
                                Swal.fire('Approved!', res.message, 'success');
                            },
                            error: function(err) {
                                let msg = err.responseJSON ? err.responseJSON.message : 'System error occurred';
                                Swal.fire('Failed!', msg, 'error');
                            }
                        });
                    }
                });
            });

            // Sales Reject Toggle
            $('#btnSalesToggleReject').on('click', function() {
                $('#salesRejectBox').removeClass('d-none');
                $('#sales_rejection_reason').focus();
                $(this).addClass('d-none');
                $('#btnSalesConfirmReject').removeClass('d-none');
            });

            // Sales Confirm Reject
            $('#btnSalesConfirmReject').on('click', function() {
                let id = $('#sales_review_id').val();
                let reason = $('#sales_rejection_reason').val().trim();

                if (!reason) {
                    Swal.fire('Warning', 'Please enter a rejection reason before confirming.', 'warning');
                    $('#sales_rejection_reason').focus();
                    return;
                }

                Swal.fire({
                    title: 'Confirm Rejection?',
                    text: 'Recommendation will be rejected and Admin-RTM will be notified to revise.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Reject',
                    confirmButtonColor: '#ef4444',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Rejecting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: "{{ url('bg/bg-recommendations') }}/" + id + "/sales-reject",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                rejection_reason: reason
                            },
                            success: function(res) {
                                $('#salesReviewModal').modal('hide');
                                tableExpiring.ajax.reload();
                                tableHistory.ajax.reload();
                                Swal.fire('Rejected', res.message, 'info');
                            },
                            error: function(err) {
                                let msg = err.responseJSON ? err.responseJSON.message : 'System error occurred';
                                Swal.fire('Failed!', msg, 'error');
                            }
                        });
                    }
                });
            });

            // --- RESUBMIT (DUPLICATE) HANDLER (ADMIN-RTM) ---
            $(document).on('click', '.btn-resubmit-duplicate', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Resubmit Recommendation?',
                    text: 'This will duplicate the rejected recommendation into a new draft so you can adjust values and resubmit to Sales.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ph-bold ph-copy me-1"></i> Yes, Duplicate & Edit',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Duplicating...',
                            text: 'Creating draft recommendation',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        $.ajax({
                            url: "{{ url('bg/bg-recommendations') }}/" + id + "/resubmit-duplicate",
                            method: "POST",
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                tableExpiring.ajax.reload();
                                tableHistory.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Draft Created!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Automatically open the process modal for the new duplicated recommendation!
                                setTimeout(() => {
                                    $('.btn-process[data-id="' + res.new_id + '"]').trigger('click');
                                }, 500);
                            },
                            error: function(err) {
                                let msg = err.responseJSON ? err.responseJSON.message : 'Failed to duplicate recommendation';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // View Rejection Reason Handler
            $(document).on('click', '.btn-view-reject-reason', function() {
                let reason = $(this).data('reason') || 'No reason specified.';
                Swal.fire({
                    icon: 'info',
                    title: 'Rejection Reason (Sales)',
                    html: `<div class="p-3 bg-light rounded text-start border border-danger border-opacity-25"><i class="ph-bold ph-quotes text-danger fs-5 me-1"></i> ${reason}</div>`,
                    confirmButtonText: 'Understood'
                });
            });
        });
    </script>
    @endpush
</x-app-layout>