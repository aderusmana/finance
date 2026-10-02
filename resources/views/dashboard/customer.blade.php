<x-app-layout>
@section('title', 'Customer Dashboard')

<style>
    /* Premium Gradient Cards */
    .premium-card { border: none; border-radius: 16px; position: relative; overflow: hidden; color: #fff; transition: transform 0.3s ease, box-shadow 0.3s ease; z-index: 1; }
    .premium-card:hover { transform: translateY(-5px); }
    
    .bg-grad-primary { background: linear-gradient(135deg, #485ede 0%, #293892 100%); box-shadow: 0 10px 20px -5px rgba(72, 94, 222, 0.5); }
    .bg-grad-success { background: linear-gradient(135deg, #1aac6e 0%, #0f6c44 100%); box-shadow: 0 10px 20px -5px rgba(26, 172, 110, 0.5); }
    .bg-grad-danger  { background: linear-gradient(135deg, #ef476f 0%, #a82746 100%); box-shadow: 0 10px 20px -5px rgba(239, 71, 111, 0.5); }
    .bg-grad-warning { background: linear-gradient(135deg, #f7b84b 0%, #b88225 100%); box-shadow: 0 10px 20px -5px rgba(247, 184, 75, 0.5); }
    
    .premium-card .card-body { padding: 1.8rem 1.5rem; position: relative; z-index: 3; }
    .premium-card .metric-title { font-size: 0.85rem; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; opacity: 0.85; margin-bottom: 0.5rem; }
    .premium-card h3, .premium-card h4 { font-weight: 800; color: #fff; font-size: 1.8rem; margin-bottom: 0; }
    .premium-card small { font-size: 0.8rem; opacity: 0.9; background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; display: inline-block; margin-top: 8px;}
    
    /* Watermark Icon */
    .watermark-icon { position: absolute; right: -15px; bottom: -20px; font-size: 7rem; opacity: 0.15; z-index: 2; transform: rotate(-15deg); }

    /* Chart Cards Elegant */
    .chart-card { border-radius: 16px; border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.04); background: #fff; margin-bottom: 24px; }
    .chart-header { border-bottom: 1px solid #f0f4f8; padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1.05rem; border-radius: 16px 16px 0 0; display: flex; align-items: center; }
    .header-blue { background-color: #f4f7fa; border-top: 4px solid #485ede; color: #2c3e50; }
    .header-gold { background-color: #fff9f0; border-top: 4px solid #f7b84b; color: #5c4113; }

    /* List Hover */
    .list-hover-elegant:hover { background-color: #f8fafd; cursor: default; }
    .avatar-initial { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; }

    /* Premium Step Badges */
    .step-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 5px 14px; border-radius: 20px; box-shadow: 0 3px 8px rgba(0,0,0,0.05); display: inline-flex; align-items: center; }
    .step-normal { background: linear-gradient(135deg, #fffcf5 0%, #fff4d6 100%); color: #9a6b22; border: 1px solid #fde047; }
    .step-optional { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #475569; border: 1px solid #e2e8f0; }
    .step-finish { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: #166534; border: 1px solid #bbf7d0; }
</style>

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bolder mb-1" style="color: #1e293b;">Customer Dashboard</h3>
            <p class="text-muted mb-0">Overview metrics, statistics, and customer analytics.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-primary h-100">
                <i class="iconoir-group watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Total Customers</p>
                    <h3 id="m-total-cust"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Overall Partners</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-success h-100">
                <i class="iconoir-star watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Top Customer Class</p>
                    <h3 id="m-top-class" class="text-truncate" style="max-width: 100%;"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small id="m-top-class-count">Loading...</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-danger h-100">
                <i class="iconoir-layers watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Total Classes Used</p>
                    <h3 id="m-total-classes"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Distinct Customer Classes</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-warning h-100">
                <i class="iconoir-trophy watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Highest Credit Limit</p>
                    <h4 class="text-truncate" style="max-width: 100%;" id="m-top-limit-val">
                        <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                    </h4>
                    <small class="text-truncate d-block" style="max-width: 100%; border:none; padding:0; background:none; font-weight:600; font-size:0.9rem" id="m-top-limit-name">Loading...</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 col-lg-7">
            <div class="card chart-card h-100">
                <div class="chart-header header-blue d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="iconoir-graph-up text-primary me-2 f-s-22"></i> Registration Status Trends
                    </div>
                    <div class="d-flex gap-2">
                        <select id="growthFilterView" class="form-select form-select-sm border-0 shadow-sm text-secondary" style="border-radius: 8px; width: 140px; font-size: 0.75rem;">
                            <option value="all">All (Combined)</option>
                            <option value="registration">Total Registration</option>
                            <option value="status">Per Status</option>
                        </select>
                        <select id="growthFilterMonth" class="form-select form-select-sm border-0 shadow-sm text-secondary" style="border-radius: 8px; width: 110px; font-size: 0.75rem;">
                            <option value="all">All Months</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <select id="growthFilterYear" class="form-select form-select-sm border-0 shadow-sm text-secondary" style="border-radius: 8px; width: 95px; font-size: 0.75rem;">
                            <option value="all">All Years</option>
                            @if(isset($availableYears) && count($availableYears) > 0)
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            @else
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="card-body p-4" style="position: relative;">
                    <div id="growthChartEmptyState" class="d-flex flex-column justify-content-center align-items-center" style="display: none !important; height: 300px; width: 100%; position: absolute; top: 0; left: 0; background: rgba(255,255,255,0.85); z-index: 10;">
                        <i class="iconoir-emoji-sad text-muted mb-2" style="font-size: 3rem;"></i>
                        <h6 class="text-muted fw-bold">No new customer registrations</h6>
                        <p class="text-muted small text-center px-4">There are no customers registered in the selected time period.</p>
                    </div>
                    <div style="height: 300px; width: 100%;"><canvas id="customerGrowthChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-5">
            <div class="card chart-card h-100">
                <div class="chart-header header-gold d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <i class="iconoir-pie-chart text-warning me-2 f-s-22"></i> Customer Class Distribution
                    </div>
                    <div class="d-flex gap-2">
                        <select id="classFilterMonth" class="form-select form-select-sm border-0 shadow-sm text-secondary" style="border-radius: 8px; width: 110px; font-size: 0.75rem;">
                            <option value="all">All Months</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <select id="classFilterYear" class="form-select form-select-sm border-0 shadow-sm text-secondary" style="border-radius: 8px; width: 95px; font-size: 0.75rem;">
                            <option value="all">All Years</option>
                            @if(isset($availableYears) && count($availableYears) > 0)
                                @foreach($availableYears as $y)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            @else
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center" style="height: 300px; width: 100%; position: relative;">
                        <div style="width: 55%; height: 100%; position: relative;">
                            <canvas id="customerClassChart"></canvas>
                        </div>
                        <div id="customerClassLegend" style="width: 45%; max-height: 100%; overflow-y: auto; padding-left: 15px;">
                            <!-- Legend generated here -->
                        </div>
                        <div id="classChartLoading" style="position: absolute; top:50%; left:50%; transform:translate(-50%,-50%); display:none;">
                            <span class="spinner-border text-primary"></span>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 0.8rem;"><i class="ti ti-info-circle"></i> Tip: You can click a category on the legend above to show or hide data.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Alur BG YES --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card h-100" style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="card-header bg-white pt-4 pb-0" style="border-bottom: 0; border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background: #f0fdf4; color: #16a34a;">
                            <i class="ti ti-shield-check fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold" style="color: #2c3e50;">Customer Workflow (BG = Yes)</h5>
                            <p class="text-muted small mb-0">Registration using Bank Guarantee</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div style="position: relative; padding-left: 10px;">
                        <div style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;"></div>

                        {{-- Step 1 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-keyboard fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SALES TEAM</span>
                                    <span class="step-badge step-normal">Step 1</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input Data & BG</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Filling identity form and inputting <b class="text-dark">Bank Guarantee</b> data.</p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-user-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SUPERVISOR</span>
                                    <span class="step-badge step-normal">Step 2</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Data Validation</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Checking completeness and validity of the Bank Guarantee document.</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7; border: 3px solid #fff; box-shadow: 0 0 0 1px #7dd3fc; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-building fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">HEAD SALES</span>
                                    <span class="step-badge step-normal">Step 3</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Business Review</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Verification of business needs, area coverage, and customer prospects.</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-calculator fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">FINANCE MGR</span>
                                    <span class="step-badge step-normal">Step 4</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Financial Calc</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">If TOP = CBD, Auto Limit 0. If not, adjusted with BG nominal.</p>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #f8f9fa; color: #374151; border: 3px solid #fff; box-shadow: 0 0 0 1px #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-shield-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f8f9fa; color: #374151; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">HEAD FINANCE</span>
                                    <span class="step-badge step-optional">Optional</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Final Approval</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Head Finance level approval (if special limit policy is required).</p>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div style="position: relative; display: flex;">
                            <div style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-database fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SYSTEM</span>
                                    <span class="step-badge step-finish"><i class="ti ti-flag-checkered me-1" style="font-size: 0.8rem;"></i> Finish</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Registered</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Data saved along with active Bank Guarantee relations.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Alur BG NO --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card h-100" style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <div class="card-header bg-white pt-4 pb-0" style="border-bottom: 0; border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-2 me-3" style="background: #fff4d6; color: #b88225;">
                            <i class="ti ti-box fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0 fw-bold" style="color: #2c3e50;">Customer Workflow (BG = No) & (CBD)</h5>
                            <p class="text-muted small mb-0">Registration without Bank Guarantee</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div style="position: relative; padding-left: 10px;">
                        <div style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;"></div>

                        {{-- Step 1 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-keyboard fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SALES TEAM</span>
                                    <span class="step-badge step-normal">Step 1</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input Data & Product</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Filling identity form and inputting <b class="text-dark">Product, Qty, Price</b> for Credit Limit.</p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-user-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SUPERVISOR</span>
                                    <span class="step-badge step-normal">Step 2</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Data Validation</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Checking the eligibility of credit application without Bank Guarantee.</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #e0f2fe; color: #0284c7; border: 3px solid #fff; box-shadow: 0 0 0 1px #7dd3fc; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-building fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">HEAD SALES</span>
                                    <span class="step-badge step-normal">Step 3</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Business Review</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Verification of business needs, area coverage, and product purchase potential.</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-calculator fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">FINANCE MGR</span>
                                    <span class="step-badge step-normal">Step 4</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Financial Calc</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Automatic Credit Limit calculation based on Qty & Price multiplication.</p>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div style="position: relative; display: flex;">
                            <div style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-database fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SYSTEM</span>
                                    <span class="step-badge step-finish"><i class="ti ti-flag-checkered me-1" style="font-size: 0.8rem;"></i> Finish</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Registered</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Data saved to Master Data along with customer items.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
    let classChartInstance = null;
    let growthChartInstance = null;
    
    function fetchMetrics() {
        fetch("{{ route('dashboard.data.customer-metrics') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('m-total-cust').innerText = data.total;
                document.getElementById('m-top-class').innerText = data.top_class;
                document.getElementById('m-top-class-count').innerText = data.top_class_count + " Customers";
                document.getElementById('m-total-classes').innerText = data.total_classes;
                
                document.getElementById('m-top-limit-val').innerText = formatter.format(data.highest_limit_amount);
                document.getElementById('m-top-limit-name').innerText = data.highest_limit_name;
            });
    }

    function fetchGrowthChart() {
        const month = document.getElementById('growthFilterMonth').value;
        const year = document.getElementById('growthFilterYear').value;
        const view = document.getElementById('growthFilterView').value;

        fetch(`{{ route('dashboard.data.monthly-stats') }}?type=customer&month=${month}&year=${year}&growth_view=${view}`)
            .then(res => res.json())
            .then(data => {
                const totalCustomers = data.created.reduce((a, b) => a + b, 0);
                const emptyState = document.getElementById('growthChartEmptyState');
                const canvasEl = document.getElementById('customerGrowthChart');
                
                if (totalCustomers === 0) {
                    emptyState.style.setProperty('display', 'flex', 'important');
                    canvasEl.style.display = 'none';
                    if (growthChartInstance) {
                        growthChartInstance.destroy();
                        growthChartInstance = null;
                    }
                    return;
                } else {
                    emptyState.style.setProperty('display', 'none', 'important');
                    canvasEl.style.display = 'block';
                }

                const ctx = canvasEl.getContext('2d');
                
                if (growthChartInstance) {
                    growthChartInstance.destroy();
                }

                growthChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { 
                                display: view !== 'registration',
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    font: { family: "'Inter', sans-serif", size: 11 }
                                }
                            } 
                        }, 
                        scales: { 
                            y: { beginAtZero: true, grid: { borderDash: [5,5] } }, 
                            x: { grid: { display: false } } 
                        } 
                    }
                });
            });
    }
    
    function fetchClassChart() {
        const month = document.getElementById('classFilterMonth').value;
        const year = document.getElementById('classFilterYear').value;
        const loadingEl = document.getElementById('classChartLoading');
        loadingEl.style.display = 'block';

        fetch(`{{ route('dashboard.data.class-stats-chart') }}?month=${month}&year=${year}`)
            .then(res => res.json())
            .then(data => {
                loadingEl.style.display = 'none';
                const ctx = document.getElementById('customerClassChart').getContext('2d');
                
                if (classChartInstance) {
                    classChartInstance.destroy();
                }

                classChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#485ede', '#1aac6e', '#f7b84b', '#ef476f', '#38bdf8', '#8b5cf6', '#f43f5e', '#a8a29e'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Generate HTML Legend
                const legendContainer = document.getElementById('customerClassLegend');
                let legendHtml = '<ul style="list-style: none; padding: 0; margin: 0;">';
                const bgColors = classChartInstance.data.datasets[0].backgroundColor;
                data.labels.forEach((label, index) => {
                    const color = bgColors[index % bgColors.length];
                    legendHtml += `
                        <li class="custom-legend-item" data-index="${index}" style="display: flex; align-items: center; cursor: pointer; margin-bottom: 8px; transition: all 0.3s ease; padding: 6px 10px; border-radius: 8px;">
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: ${color}; border-radius: 50%; margin-right: 12px;"></span>
                            <span style="font-family: 'Inter', sans-serif; font-size: 13px; color: #4b5563; transition: all 0.2s ease;">${label}</span>
                        </li>
                    `;
                });
                legendHtml += '</ul>';
                legendContainer.innerHTML = legendHtml;

                // Add event listeners to legend items for golden hover effect and chart interaction
                const legendItems = legendContainer.querySelectorAll('.custom-legend-item');
                legendItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#fffbeb'; // Light golden background
                        this.querySelector('span:nth-child(2)').style.color = '#b45309'; // Dark golden text
                        this.querySelector('span:nth-child(2)').style.fontWeight = 'bold';
                        
                        // Highlight corresponding slice on chart
                        const idx = parseInt(this.getAttribute('data-index'));
                        classChartInstance.setActiveElements([{datasetIndex: 0, index: idx}]);
                        classChartInstance.update();
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = 'transparent';
                        this.querySelector('span:nth-child(2)').style.color = '#4b5563';
                        this.querySelector('span:nth-child(2)').style.fontWeight = 'normal';
                        
                        // Remove highlight from chart slice
                        classChartInstance.setActiveElements([]);
                        classChartInstance.update();
                    });
                    
                    item.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'));
                        const meta = classChartInstance.getDatasetMeta(0);
                        const alreadyHidden = meta.data[idx].hidden;
                        
                        if (alreadyHidden) {
                            meta.data[idx].hidden = false;
                            this.style.opacity = '1';
                        } else {
                            meta.data[idx].hidden = true;
                            this.style.opacity = '0.5';
                        }
                        classChartInstance.update();
                    });
                });
            });
    }

    // Initialize
    fetchMetrics();
    fetchGrowthChart();
    fetchClassChart();

    // Event Listeners for Filters
    ['growthFilterMonth', 'growthFilterYear', 'growthFilterView'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchGrowthChart);
    });

    ['classFilterMonth', 'classFilterYear'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchClassChart);
    });
});
</script>
@endpush
</x-app-layout>