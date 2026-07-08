<x-app-layout>
@section('title', 'Bank Garansi Dashboard')

<style>
    /* Premium Gradient Cards */
    .premium-card { border: none; border-radius: 16px; position: relative; overflow: hidden; color: #fff; transition: transform 0.3s ease, box-shadow 0.3s ease; z-index: 1; }
    .premium-card:hover { transform: translateY(-5px); }
    
    .bg-grad-success { background: linear-gradient(135deg, #1aac6e 0%, #0f6c44 100%); box-shadow: 0 10px 20px -5px rgba(26, 172, 110, 0.5); }
    .bg-grad-info    { background: linear-gradient(135deg, #17a2b8 0%, #0d6371 100%); box-shadow: 0 10px 20px -5px rgba(23, 162, 184, 0.5); }
    .bg-grad-warning { background: linear-gradient(135deg, #f7b84b 0%, #b88225 100%); box-shadow: 0 10px 20px -5px rgba(247, 184, 75, 0.5); }
    .bg-grad-primary { background: linear-gradient(135deg, #485ede 0%, #293892 100%); box-shadow: 0 10px 20px -5px rgba(72, 94, 222, 0.5); }
    
    .premium-card .card-body { padding: 1.8rem 1.5rem; position: relative; z-index: 3; }
    .premium-card .metric-title { font-size: 0.85rem; letter-spacing: 1px; font-weight: 600; text-transform: uppercase; opacity: 0.85; margin-bottom: 0.5rem; }
    .premium-card h3, .premium-card h4 { font-weight: 800; color: #fff; font-size: 1.8rem; margin-bottom: 0; }
    .premium-card small { font-size: 0.8rem; opacity: 0.9; background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; display: inline-block; margin-top: 8px;}
    
    /* Watermark Icon */
    .watermark-icon { position: absolute; right: -15px; bottom: -20px; font-size: 7rem; opacity: 0.15; z-index: 2; transform: rotate(-15deg); }

    /* Chart Cards Elegant */
    .chart-card { border-radius: 16px; border: none; box-shadow: 0 4px 25px rgba(0,0,0,0.04); background: #fff; margin-bottom: 24px; }
    .chart-header { border-bottom: 1px solid #f0f4f8; padding: 1.25rem 1.5rem; font-weight: 700; font-size: 1.05rem; border-radius: 16px 16px 0 0; display: flex; align-items: center; }
    .header-green { background-color: #f2fbf7; border-top: 4px solid #1aac6e; color: #0d5435; }
    .header-purple { background-color: #f8f6ff; border-top: 4px solid #8b3dff; color: #3b1773; }

    /* Premium Step Badges */
    .step-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 5px 14px; border-radius: 20px; box-shadow: 0 3px 8px rgba(0,0,0,0.05); display: inline-flex; align-items: center; }
    .step-normal { background: linear-gradient(135deg, #fffcf5 0%, #fff4d6 100%); color: #9a6b22; border: 1px solid #fde047; }
    .step-optional { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); color: #475569; border: 1px solid #e2e8f0; }
    .step-finish { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: #166534; border: 1px solid #bbf7d0; }
</style>

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bolder mb-1" style="color: #1e293b;">Bank Garansi Dashboard</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;"><i class="iconoir-bank text-success"></i> Real-time Monitoring Dokumen, Nilai Aktif, & Jatuh Tempo</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-success h-100">
                <i class="iconoir-coins watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Total Active Value</p>
                    <h4 class="text-truncate" style="max-width:100%" id="m-bg-value">
                        <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                    </h4>
                    <small>Nilai Berlaku (Rp)</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-info h-100">
                <i class="iconoir-page watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Active Documents</p>
                    <h3 id="m-bg-count"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Approved & Active</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-warning h-100">
                <i class="iconoir-timer watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Expiring (60 Days)</p>
                    <h3 id="m-bg-expiring"><span class="spinner-border spinner-border-sm text-white" role="status"></span></h3>
                    <small>Segera Diperbarui</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card premium-card bg-grad-primary h-100">
                <i class="iconoir-crown watermark-icon"></i>
                <div class="card-body">
                    <p class="metric-title">Largest Active BG</p>
                    <h4 class="text-truncate" style="max-width: 100%;" id="m-largest-bg-val">
                        <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                    </h4>
                    <small class="text-truncate d-block" style="max-width: 100%; border:none; padding:0; background:none; font-weight:600; font-size:0.9rem" id="m-largest-bg-cust">Loading...</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card chart-card h-100">
                <div class="chart-header header-green">
                    <i class="iconoir-bar-chart text-success me-2 f-s-22"></i> Tren Status Bank Garansi Bulanan
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px; width: 100%;"><canvas id="bgStatusChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            <div class="card chart-card h-100">
                <div class="chart-header header-purple">
                    <i class="iconoir-pie-chart text-primary me-2 f-s-22"></i> Komposisi Bank Garansi
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-4">
                    <div style="height: 250px; width: 100%;"><canvas id="bgCompositionChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card h-100" style="border: 1px solid #cbd5e1; border-radius: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
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
                    <div style="position: relative; padding-left: 10px;">
                        <div style="position: absolute; top: 15px; bottom: 30px; left: 28px; width: 2px; background: #e5e7eb; z-index: 0;"></div>

                        {{-- Step 1 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #f3f4f6; color: #4b5563; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-bell fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f3f4f6; color: #374151; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SYSTEM</span>
                                    <span class="step-badge step-normal">Step 1</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Notifikasi Reminder</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sistem mengirim notifikasi otomatis H-60 sebelum expired date.</p>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #eff6ff; color: #2563eb; border: 3px solid #fff; box-shadow: 0 0 0 1px #bfdbfe; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-file-plus fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SALES</span>
                                    <span class="step-badge step-normal">Step 2</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Draft Rekomendasi</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sales membuat draft rekomendasi (+11%) dan mengirim link.</p>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #fff7ed; color: #ea580c; border: 3px solid #fff; box-shadow: 0 0 0 1px #fed7aa; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-pencil fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #fff7ed; color: #c2410c; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">CUSTOMER</span>
                                    <span class="step-badge step-normal">Step 3</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Input & Upload</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Customer melengkapi form bank, cetak, ttd, dan upload scan Lampiran D.</p>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #fefce8; color: #ca8a04; border: 3px solid #fff; box-shadow: 0 0 0 1px #fde047; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-eye-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #fefce8; color: #854d0e; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SALES</span>
                                    <span class="step-badge step-normal">Step 4</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Review Dokumen</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Sales mengecek kelengkapan. Jika OK lanjut Finance, jika tidak Return.</p>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div style="position: relative; padding-bottom: 30px; display: flex;">
                            <div style="width: 38px; height: 38px; background: #f0fdf4; color: #16a34a; border: 3px solid #fff; box-shadow: 0 0 0 1px #86efac; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-signature fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #f0fdf4; color: #14532d; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">FINANCE MGR</span>
                                    <span class="step-badge step-normal">Step 5</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Final Approval</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Approval limit final. Sistem menyimpan versi Lampiran D (Versioning).</p>
                            </div>
                        </div>

                        {{-- Step 6 --}}
                        <div style="position: relative; display: flex;">
                            <div style="width: 38px; height: 38px; background: #212529; color: #fff; border: 3px solid #fff; box-shadow: 0 0 0 1px #374151; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 1; flex-shrink: 0;">
                                <i class="ti ti-check fw-bold" style="font-size: 0.9rem;"></i>
                            </div>
                            <div style="margin-left: 15px; flex-grow: 1;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge" style="background: #212529; color: #fff; font-weight: 700; font-size: 0.65rem; padding: 6px 10px;">SYSTEM</span>
                                    <span class="step-badge step-finish"><i class="ti ti-flag-checkered me-1" style="font-size: 0.8rem;"></i> Finish</span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem; color: #1f2937;">Completed</h6>
                                <p class="text-muted mb-0 small" style="line-height: 1.3;">Update/Create BG, Notifikasi Email, dan Arsip Data.</p>
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
    fetch("{{ route('dashboard.data.bg-metrics') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('m-bg-value').innerText = formatter.format(data.total_value);
            document.getElementById('m-bg-count').innerText = data.active_count;
            document.getElementById('m-bg-expiring').innerText = data.expiring;
            
            document.getElementById('m-largest-bg-val').innerText = formatter.format(data.largest_bg_nominal);
            document.getElementById('m-largest-bg-cust').innerText = data.largest_bg_customer;
        });

    // 2. Fetch & Render Bar Chart
    fetch("{{ route('dashboard.data.monthly-stats') }}?type=bg")
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById('bgStatusChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        { label: 'Approved', data: data.approved, backgroundColor: '#1aac6e', borderRadius: 4 },
                        { label: 'Pending', data: data.pending, backgroundColor: '#f7b84b', borderRadius: 4 },
                        { label: 'Rejected', data: data.rejected, backgroundColor: '#ef476f', borderRadius: 4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { stacked: true, grid: { borderDash: [5,5] } }, x: { stacked: true, grid: { display: false } } } }
            });
        });

    // 3. Fetch & Render Donut Chart
    fetch("{{ route('dashboard.data.advanced-stats') }}")
        .then(res => res.json())
        .then(data => {
            const comp = data.bg_composition;
            new Chart(document.getElementById('bgCompositionChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['New', 'Extension', 'Existing'],
                    datasets: [{ 
                        data: [comp.new, comp.extension, comp.existing], 
                        backgroundColor: ['#485ede', '#1aac6e', '#17a2b8'],
                        borderWidth: 0, hoverOffset: 5
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false, cutout: '75%',
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } } 
                }
            });
        });
});
</script>
@endpush
</x-app-layout>