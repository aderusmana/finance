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
            <p class="text-muted mb-0" style="font-size: 0.95rem;"><i class="iconoir-community text-primary"></i> Ringkasan Eksekutif Data Pelanggan & Profil Kredit</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-primary h-100">
                <i class="iconoir-group watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Total Customers</p>
                    <h3 id="m-total-cust"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Keseluruhan Mitra</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-success h-100">
                <i class="iconoir-shield-check watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Using Bank Garansi</p>
                    <h3 id="m-bg-users"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small id="m-non-bg-users">Loading...</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-danger h-100">
                <i class="iconoir-warning-triangle watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Limit Exceeded</p>
                    <h3 id="m-overlimit"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Needs Attention!</small>
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
        <div class="col-xl-8 col-lg-7">
            <div class="card chart-card h-100">
                <div class="chart-header header-blue">
                    <i class="iconoir-graph-up text-primary me-2 f-s-22"></i> Customer Registration Growth
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; width: 100%;"><canvas id="customerGrowthChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card chart-card h-100">
                <div class="chart-header header-gold">
                    <i class="iconoir-star text-warning me-2 f-s-22"></i> Top Customers by BG
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="top-customers-list">
                        <li class="list-group-item text-center p-5 text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Memuat Data...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card h-100" style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SPV SALES</span>
                                    <span class="step-badge step-normal">Step 2</span>
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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">HEAD SALES</span>
                                    <span class="step-badge step-normal">Step 3</span>
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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">FINANCE MGR</span>
                                    <span class="step-badge step-normal">Step 4</span>
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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f8f9fa; color: #374151; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">HEAD FINANCE</span>
                                    <span class="step-badge step-optional">Optional</span>
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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SYSTEM</span>
                                    <span class="step-badge step-finish"><i class="ti ti-flag-checkered me-1" style="font-size: 0.8rem;"></i> Finish</span>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

    // 1. Fetch Metrics
    fetch("{{ route('dashboard.data.customer-metrics') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('m-total-cust').innerText = data.total;
            document.getElementById('m-bg-users').innerText = data.with_bg;
            document.getElementById('m-non-bg-users').innerText = data.without_bg + " Without BG";
            document.getElementById('m-overlimit').innerText = data.credit_exceeded;
            
            document.getElementById('m-top-limit-val').innerText = formatter.format(data.highest_limit_amount);
            document.getElementById('m-top-limit-name').innerText = data.highest_limit_name;
        });

    // 2. Fetch Top Customers
    fetch("{{ route('dashboard.data.top-customers-bg') }}?metric=bg_count")
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('top-customers-list');
            list.innerHTML = '';
            const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
            data.forEach((item, index) => {
                let initial = item.name.charAt(0).toUpperCase();
                let colorClass = colors[index % colors.length];
                list.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center p-3 list-hover-elegant border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar-initial ${colorClass} text-white me-3 shadow-sm">${initial}</div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color:#2c3e50;">${item.name}</h6>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm">${item.bg_count} BG</span>
                </li>`;
            });
        });

    // 3. Fetch & Render Chart
    fetch("{{ route('dashboard.data.monthly-stats') }}?type=customer")
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('customerGrowthChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(72, 94, 222, 0.4)');
            gradient.addColorStop(1, 'rgba(72, 94, 222, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'New Customers', data: data.created, borderColor: '#485ede', backgroundColor: gradient, borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#485ede', pointRadius: 5, fill: true, tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5,5] } }, x: { grid: { display: false } } } }
            });
        });
});
</script>
@endpush
</x-app-layout>