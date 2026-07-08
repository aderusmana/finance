<x-app-layout>
    @section('title', 'Activity Audit Trail')
    @include('components.sample-table-styles')

    {{-- 1. HEADER & BREADCRUMB --}}
    <div class="row m-1 mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="main-title text-dark fw-bold" style="letter-spacing: -0.5px; font-size: 1.5rem;">Activity Audit Trail</h4>
            <ul class="app-line-breadcrumbs mb-0" style="padding-left: 0; list-style: none; display: flex; gap: 10px;">
                <li><a class="text-muted text-decoration-none" href="{{ route('dashboard') }}" style="font-size: 0.85rem;">Dashboard</a></li>
                <li style="color: #ccc;">/</li>
                <li class="active"><a class="text-primary text-decoration-none fw-bold" href="#" style="font-size: 0.85rem;">Logs</a></li>
            </ul>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-white border shadow-sm rounded-pill px-3" onclick="window.location.reload()" style="font-size: 0.9rem; font-weight: 600; color: #4b5563;">
                <i class="ph-bold ph-arrows-clockwise me-2 text-primary"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- 2. STATISTIC WIDGETS (MENGISI RUANG & WARNA) --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Logs --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-uppercase fw-bold" style="opacity: 0.8; font-size: 0.7rem;">Total Activities</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ number_format($stats['total_logs']) }}</h3>
                        </div>
                        <div style="background: rgba(255,255,255,0.2); padding: 8px; border-radius: 8px;">
                            <i class="ph-bold ph-database f-s-20"></i>
                        </div>
                    </div>
                    <div class="mt-3" style="font-size: 0.75rem; opacity: 0.9;">
                        Recorded since system start
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Card 2: Today --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Today's Entries</small>
                            <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($stats['today_logs']) }}</h3>
                        </div>
                        <div class="text-success" style="background: #e6fdf4; padding: 8px; border-radius: 8px;">
                            <i class="ph-bold ph-calendar-check f-s-20"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted" style="font-size: 0.75rem;">
                        <span class="text-success fw-bold"><i class="ph-bold ph-trend-up"></i> Active</span> today
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Users --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #36b9cc;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Unique Actors</small>
                            <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($stats['unique_users']) }}</h3>
                        </div>
                        <div class="text-info" style="background: #e6f8fb; padding: 8px; border-radius: 8px;">
                            <i class="ph-bold ph-users-three f-s-20"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted" style="font-size: 0.75rem;">
                        Interacting with system
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Last Activity --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #f6c23e;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Last Action</small>
                            <h4 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.1rem;">{{ $stats['last_activity'] }}</h4>
                        </div>
                        <div class="text-warning" style="background: #fffdf0; padding: 8px; border-radius: 8px;">
                            <i class="ph-bold ph-clock-counter-clockwise f-s-20"></i>
                        </div>
                    </div>
                    <div class="mt-3 text-muted" style="font-size: 0.75rem;">
                        Real-time updates
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. MAIN TABLE CARD --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                {{-- Toolbar Table --}}
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Recent Activities</h5>
                        <p class="text-muted mb-0 small">Monitoring real-time perubahan data sistem.</p>
                    </div>
                    <div class="d-flex gap-2">
                        {{-- Search box manual styling agar lebih cantik dari default datatable --}}
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px; padding-left: 15px;">
                                <i class="ph-bold ph-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" id="customSearch" class="form-control border-start-0 ps-0" placeholder="Search logs..." style="border-radius: 0 20px 20px 0; font-size: 0.9rem;">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="w-100 align-middle" id="sampleTable" style="margin-top: 0 !important; border-collapse: separate; border-spacing: 0;">
                            <thead style="background-color: #f1f5f9;"> <tr>
                                    <th class="py-3 ps-4 text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="5%">No</th>
                                    <th class="py-3 text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="15%">Actor</th>
                                    <th class="py-3 text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="35%">Activity Details</th>
                                    <th class="py-3 text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="20%">Subject</th>
                                    <th class="py-3 text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="15%">Time</th>
                                    <th class="py-3 pe-4 text-center text-dark text-uppercase" style="font-size: 0.75rem; font-weight: 800; border-bottom: 3px solid #cbd5e1;" width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data populated by DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>  
            </div>
        </div>
    </div>

    {{-- MODAL JSON VIEWER (REVISED) --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                
                {{-- Header --}}
                <div class="modal-header py-3 px-4 bg-dark text-white">
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white">
                            <i class="ph-bold ph-read-cv-logo me-2"></i> Activity Details
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                {{-- Body --}}
                <div class="modal-body p-0 bg-light">
                    {{-- 1. HEADER SUMMARY --}}
                    <div class="bg-white p-4 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1" id="modalDesc"></h5>
                                <div class="text-muted small mb-2" id="modalTime"></div>
                                <div class="d-flex gap-2" id="modalBadges"></div>
                            </div>
                            <div class="text-end ps-3 border-start">
                                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.65rem;">ACTOR</small>
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="fw-bold text-dark me-2" id="modalActor"></div>
                                    <i class="ph-duotone ph-user-circle f-s-32 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. UNIFIED DATA GRID (Properties + Tech Info merged) --}}
                    <div class="p-4">
                        <h6 class="fw-bold text-secondary small text-uppercase mb-3 d-flex align-items-center">
                            <i class="ph-bold ph-database me-2"></i> Activity Data & Changes
                        </h6>
                        
                        {{-- Container Grid --}}
                        <div id="propertiesGrid" class="border rounded bg-white overflow-hidden shadow-sm">
                            {{-- Diisi JS --}}
                        </div>
                    </div>
                    
                    {{-- (SECTION TECHNICAL ACCORDION DIHAPUS DARI SINI) --}}

                </div>

                <div class="modal-footer bg-white py-2 px-4 border-top">
                    <button type="button" class="btn btn-sm btn-dark fw-bold rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    dom: 'rt<"d-flex justify-content-between align-items-center p-4"ip>',
                    ajax: "{{ route('system-logs.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center ps-4 text-muted fw-bold', orderable: false, searchable: false },
                        { data: 'causer_name', name: 'causer.name' },
                        { data: 'description', name: 'description' },
                        { data: 'subject_description', name: 'subject_type' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'properties', name: 'properties', className: 'text-center pe-4', orderable: false, searchable: false }
                    ],
                    order: [[4, 'desc']],
                    pageLength: 10,
                    createdRow: function(row, data, dataIndex) {
                        $(row).find('td').css({
                            'border-bottom': '2px solid #e2e8f0',
                            'padding': '12px 8px'
                        });
                    }
                });

                $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });

                $(document).on('click', '.btn-view-json', function() {
                    var d = $(this).data('row'); 

                    $('#modalDesc').text(d.description);
                    
                    let timeInfo = `<i class="ph-bold ph-clock me-1"></i> ${d.created_at}`;
                    if(d.created_at !== d.updated_at) {
                        timeInfo += ` <span class="text-muted ms-2 small">(Updated: ${d.updated_at})</span>`;
                    }
                    $('#modalTime').html(timeInfo);
                    
                    $('#modalActor').text(d.causer_name);
                    
                    let eventColor = d.event === 'created' ? 'success' : (d.event === 'updated' ? 'warning text-dark' : (d.event === 'deleted' ? 'danger' : 'primary'));
                    $('#modalBadges').html(`
                        <span class="badge bg-${eventColor} text-uppercase border">${d.event}</span>
                        <span class="badge bg-light text-dark border">${d.log_name}</span>
                        <span class="badge bg-info text-dark border">ID #${d.id}</span>
                    `);

                    renderUnifiedGrid(d);

                    $('#detailModal').modal('show');
                });

                function renderUnifiedGrid(d) {
                    let html = '<div class="row g-0">';
                    html += renderGridItem(`Target Data (${d.subject_type})`, `<div class="fw-bold text-dark text-break">${d.subject_name}</div>`, false);
                    html += renderGridItem(`Actor Role (${d.causer_type})`, `<div class="fw-bold text-dark text-break">${d.causer_name}</div>`, false);


                    let props = d.properties;
                    
                    if (Array.isArray(props) && props.length === 0) {
                        html += renderGridItem('Changes', '<span class="text-muted fst-italic">No specific properties recorded.</span>', false, 'col-12');
                    } 
                    else if (props.hasOwnProperty('attributes') && props.hasOwnProperty('old')) {
                        let allKeys = [...new Set([...Object.keys(props.old), ...Object.keys(props.attributes)])];
                        
                        allKeys.forEach((key) => {
                            let oldVal = props.old[key] ?? '-';
                            let newVal = props.attributes[key] ?? '-';
                            let isDiff = JSON.stringify(oldVal) !== JSON.stringify(newVal);
                            
                            let content = `
                                <div class="d-flex justify-content-between mt-1 small">
                                    <div class="text-danger text-decoration-line-through me-2 opacity-75" title="Old Value">${formatValueShort(oldVal)}</div>
                                    <div class="text-success fw-bold text-end" title="New Value">${formatValueShort(newVal)}</div>
                                </div>
                            `;
                            html += renderGridItem(formatKey(key), content, isDiff);
                        });

                    } else {
                        let dataObj = props.hasOwnProperty('attributes') ? props.attributes : props;
                        Object.keys(dataObj).forEach(key => {
                            if(key === 'attributes') return;
                            html += renderGridItem(formatKey(key), `<div class="text-dark fw-bold small mt-1">${formatValue(dataObj[key])}</div>`, false);
                        });
                    }
                    
                    html += '</div>'; // Tutup Row
                    $('#propertiesGrid').html(html);
                }

                function renderGridItem(label, contentHtml, highlight = false, colClass = 'col-md-6') {
                    let bgClass = highlight ? 'bg-warning bg-opacity-10' : '';
                    return `
                        <div class="${colClass} border-bottom border-end ${bgClass} p-3">
                            <div class="d-flex flex-column h-100">
                                <small class="text-uppercase text-muted fw-bold" style="font-size:0.65rem; letter-spacing:0.5px;">${label}</small>
                                <div class="mt-1">${contentHtml}</div>
                            </div>
                        </div>
                    `;
                }

                function formatKey(str) { return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); }
                
                function formatValue(val) {
                    if (val === null || val === 'null') return '<span class="badge bg-light text-muted border">null</span>';
                    if (val === true) return '<span class="text-success fw-bold">True</span>';
                    if (val === false) return '<span class="text-danger fw-bold">False</span>';
                    if (typeof val === 'object') return '<pre class="mb-0 p-1 bg-light border rounded" style="font-size: 0.7rem; max-height:100px; overflow:auto;">'+JSON.stringify(val, null, 2)+'</pre>';
                    return val;
                }

                function formatValueShort(val) {
                    if (val === null || val === 'null') return 'null';
                    if (typeof val === 'object') return '{...}';
                    return val;
                }
            });
        </script>
    @endpush
</x-app-layout>