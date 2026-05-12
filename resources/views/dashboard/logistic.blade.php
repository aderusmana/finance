<x-app-layout>
    @section('title', 'Logistic Dashboard')
    
    {{-- Custom CSS untuk Tampilan Modern --}}
    <style>
        .stat-card {
            border: none;
            border-radius: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .stat-icon-wrapper {
            position: absolute;
            right: -15px;
            top: -15px;
            opacity: 0.15;
            transform: rotate(-15deg);
            transition: all 0.3s ease;
        }
        .stat-card:hover .stat-icon-wrapper {
            transform: rotate(0deg) scale(1.1);
            opacity: 0.25;
        }
        
        /* Gradients */
        .bg-grad-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .bg-grad-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-grad-success { background: linear-gradient(135deg, #10b981 0%, #047857 100%); }
        .bg-grad-purple { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }

        .dashboard-panel {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }
        
        .table-custom th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-custom td {
            font-size: 0.875rem;
            vertical-align: middle;
            color: #334155;
        }
    </style>

    {{-- Breadcrumbs --}}
    <div class="row m-1 mb-4 align-items-center">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bolder text-dark mb-1" style="letter-spacing: -0.5px;">Logistic Recap Dashboard</h3>
                <p class="text-muted mb-0 small">Ringkasan aktivitas Logistic Order & Logistic Fee</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-primary border border-primary px-3 py-2 rounded-pill shadow-sm" id="live-time">
                    <i class="ph-bold ph-clock"></i> Memuat waktu...
                </span>
            </div>
        </div>
    </div>

    {{-- ROW 1: TOP SUMMARY CARDS --}}
    <div class="row g-4 mb-4">
        {{-- Card 1 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card bg-grad-primary text-white h-100 p-4 shadow-sm">
                <div class="stat-icon-wrapper"><i class="ph-fill ph-shopping-cart" style="font-size: 8rem;"></i></div>
                <div class="position-relative z-1">
                    <p class="mb-1 text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Total Logistic Orders</p>
                    <h2 class="fw-black mb-0 display-5" id="stat_total_orders">0</h2>
                </div>
            </div>
        </div>
        
        {{-- Card 2 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card bg-grad-warning text-white h-100 p-4 shadow-sm">
                <div class="stat-icon-wrapper"><i class="ph-fill ph-download-simple" style="font-size: 8rem;"></i></div>
                <div class="position-relative z-1">
                    <p class="mb-1 text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">DN Belum Diunduh</p>
                    <h2 class="fw-black mb-0 display-5" id="stat_pending_downloads">0</h2>
                </div>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card bg-grad-success text-white h-100 p-4 shadow-sm">
                <div class="stat-icon-wrapper"><i class="ph-fill ph-check-circle" style="font-size: 8rem;"></i></div>
                <div class="position-relative z-1">
                    <p class="mb-1 text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Harga Logistik Aktif</p>
                    <h2 class="fw-black mb-0 display-5" id="stat_active_fees">0</h2>
                </div>
            </div>
        </div>

        {{-- Card 4 --}}
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card bg-grad-purple text-white h-100 p-4 shadow-sm">
                <div class="stat-icon-wrapper"><i class="ph-fill ph-hourglass-medium" style="font-size: 8rem;"></i></div>
                <div class="position-relative z-1">
                    <p class="mb-1 text-white-50 fw-semibold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Antrean Approval Harga</p>
                    <h2 class="fw-black mb-0 display-5" id="stat_pending_fees">0</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: CHART & RECENT FEE LOGS --}}
    <div class="row g-4 mb-4">
        {{-- Area Chart --}}
        <div class="col-xl-8 col-lg-7">
            <div class="dashboard-panel p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="ph-duotone ph-trend-up text-primary me-2"></i>Trend Logistic Order (6 Bulan)</h6>
                </div>
                <div style="height: 300px;">
                    <canvas id="orderChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Aktivitas Perubahan Harga Terkini --}}
        <div class="col-xl-4 col-lg-5">
            <div class="dashboard-panel p-4 h-100 d-flex flex-column">
                <h6 class="fw-bold text-dark mb-4"><i class="ph-duotone ph-clock-counter-clockwise text-primary me-2"></i>Histori Harga Terkini</h6>
                
                <div class="flex-grow-1 overflow-auto" id="recent_fee_logs_container" style="max-height: 300px;">
                    {{-- Diisi via AJAX --}}
                    <div class="text-center text-muted my-5"><div class="spinner-border spinner-border-sm me-2"></div>Memuat data...</div>
                </div>
                
                <div class="mt-3 text-center border-top pt-3">
                    <a href="{{ route('logistic-fees.log') }}" class="text-primary text-decoration-none fw-semibold small">Lihat Semua Histori <i class="ph-bold ph-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: TABEL RECENT LOGISTIC ORDERS --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="dashboard-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="ph-duotone ph-shopping-bag text-primary me-2"></i>Transaksi Order Terbaru</h6>
                    <a href="{{ route('logistic-orders.index') }}" class="btn btn-sm btn-info text-white border fw-semibold text-secondary px-3 rounded-pill">Kelola Orders</a>
                </div>
                <div class="table-responsive rounded-3 border border-light">
                    <table class="table table-hover table-custom mb-0 align-middle">
                        <thead>
                            <tr>
                                <th width="15%">Order No</th>
                                <th width="20%">Customer</th>
                                <th width="25%">Distributor</th>
                                <th width="15%">DN No</th>
                                <th width="15%">Tanggal</th>
                                <th width="10%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="recent_orders_table">
                            {{-- Diisi via AJAX --}}
                            <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Import Chart.js via CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Jam Real-time
            setInterval(() => {
                let d = new Date();
                $('#live-time').html(`<i class="ph-bold ph-clock"></i> ${d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' })} - ${d.toLocaleTimeString('id-ID')}`);
            }, 1000);

            // Inisialisasi Variabel Chart
            let orderChartInstance = null;

            // Panggil API Statistik
            function loadDashboardData() {
                $.get("{{ route('dashboard.data.logistic-stats') }}", function(res) {
                    
                    // 1. Update Cards dengan Animasi Counter
                    animateValue("stat_total_orders", 0, res.summary.total_orders, 1000);
                    animateValue("stat_pending_downloads", 0, res.summary.pending_downloads, 1000);
                    animateValue("stat_active_fees", 0, res.summary.active_fees, 1000);
                    animateValue("stat_pending_fees", 0, res.summary.pending_fees, 1000);

                    // 2. Render Chart
                    renderChart(res.chart.labels, res.chart.data);

                    // 3. Render Tabel Recent Orders
                    let ordersHtml = '';
                    if(res.recent_orders.length > 0) {
                        res.recent_orders.forEach(function(o) {
                            let statusBadge = o.status === 'Downloaded' 
                                ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 rounded-pill"><i class="ph-bold ph-check me-1"></i>Downloaded</span>`
                                : `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 rounded-pill"><i class="ph-bold ph-clock me-1"></i>Pending</span>`;
                            
                            ordersHtml += `
                                <tr>
                                    <td class="fw-bold text-primary">${o.lo_no}</td>
                                    <td class="fw-semibold">${o.customer}</td>
                                    <td>${o.distributor}</td>
                                    <td class="text-muted"><i class="ph-bold ph-barcode"></i> ${o.do_no}</td>
                                    <td class="text-muted">${o.date}</td>
                                    <td class="text-center">${statusBadge}</td>
                                </tr>
                            `;
                        });
                    } else {
                        ordersHtml = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>';
                    }
                    $('#recent_orders_table').html(ordersHtml);

                    // 4. Render Recent Fee Logs
                    let logsHtml = '';
                    if(res.recent_fee_logs.length > 0) {
                        res.recent_fee_logs.forEach(function(l) {
                            let icon, color;
                            if (l.status === 'Approved') { icon = 'ph-check-circle'; color = 'text-success'; }
                            else if (l.status === 'Rejected') { icon = 'ph-x-circle'; color = 'text-danger'; }
                            else { icon = 'ph-paper-plane-tilt'; color = 'text-warning'; }

                            logsHtml += `
                                <div class="d-flex align-items-start mb-3 pb-3 border-bottom border-light">
                                    <div class="bg-light rounded-circle p-2 me-3 shadow-sm">
                                        <i class="ph-fill ${icon} ${color}" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 0.85rem;">${l.customer}</span>
                                            <small class="text-muted" style="font-size: 0.7rem;">${l.time}</small>
                                        </div>
                                        <div class="text-muted mb-1" style="font-size: 0.75rem;">Oleh: <span class="fw-semibold">${l.action_by}</span></div>
                                        <div><span class="badge bg-secondary bg-opacity-10 text-dark border px-2">${l.new_fee}</span></div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        logsHtml = '<div class="text-center text-muted py-3">Tidak ada aktivitas terbaru.</div>';
                    }
                    $('#recent_fee_logs_container').html(logsHtml);

                }).fail(function() {
                    console.error("Gagal memuat data dashboard logistik.");
                });
            }

            // Fungsi Render Chart.js (Style Modern dengan Gradient)
            function renderChart(labels, data) {
                const ctx = document.getElementById('orderChart').getContext('2d');
                
                if (orderChartInstance) { orderChartInstance.destroy(); }

                // Buat Gradient untuk garis grafik
                let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // Blue-500 opasitas 40%
                gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)'); // Transparan di bawah

                orderChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Logistic Order',
                            data: data,
                            borderColor: '#3b82f6', // Blue-500
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.4 // Membuat garis melengkung (smooth)
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1e293b',
                                padding: 12,
                                titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                                bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                                displayColors: false,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9', borderDash: [5, 5] },
                                border: { display: false },
                                ticks: { precision: 0, color: '#94a3b8' }
                            },
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { color: '#94a3b8', font: { weight: '600' } }
                            }
                        }
                    }
                });
            }

            // Fungsi Efek Counter Angka Berjalan
            function animateValue(id, start, end, duration) {
                if (start === end) { document.getElementById(id).innerHTML = end; return; }
                let range = end - start;
                let current = start;
                let increment = end > start ? 1 : -1;
                let stepTime = Math.abs(Math.floor(duration / range));
                let obj = document.getElementById(id);
                let timer = setInterval(function() {
                    current += increment;
                    obj.innerHTML = current;
                    if (current == end) { clearInterval(timer); }
                }, stepTime);
            }

            // Panggil fungsi saat halaman diload
            loadDashboardData();
        });
    </script>
    @endpush
</x-app-layout>