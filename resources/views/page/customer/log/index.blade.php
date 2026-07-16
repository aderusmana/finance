<x-app-layout>
    @section('title', 'System Activity Logs')

    @include('components.sample-table-styles')

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        {{-- 1. HEADER BANNER MEWAH (TEMA MIDNIGHT/AUDIT) --}}
        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(30, 27, 75, 0.3); position: relative; overflow: hidden; margin-bottom: -1rem; z-index: 1;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; padding: 2px 8px; font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; color: #a5b4fc;"><i class="ph-fill ph-shield-check me-1"></i> SECURITY & AUDIT</span>
                        </div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">System Activity Logs</h3>
                        <p class="mb-0" style="color: #c7d2fe; font-size: 0.95rem;">Record user activities, data changes, and system events for security and audit purposes.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. TABEL DATA AUDIT TRAIL --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-fingerprint me-2" style="color: #2563eb;"></i>Activity Log</h5>
                        <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3 shadow-sm" style="color: #475569;" onclick="table.ajax.reload()"><i class="ph-bold ph-arrows-clockwise me-1"></i> Refresh Log</button>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 3%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 12%;">Timestamp</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 10%;">Module Type</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 10%;">Action (Event)</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 12%;">Actor (Causer)</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 18%;">Target Data</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 5%;">Tag ID</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 22%;"><i class="ph-bold ph-braces me-1"></i> Properties</th>
                                        <!-- KOLOM ACTION DITAMBAHKAN DI SINI -->
                                        <th class="text-center" style="background-color: #f8fafc; color: #2563eb; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 8%;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL PREVIEW BERGAYA LIGHT & ELEGAN (COMPACT) --}}
        <div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 overflow-hidden shadow-lg">
                    <!-- HEADER MODAL -->
                    <div class="modal-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded p-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                <i class="ph-bold ph-list-magnifying-glass f-s-24"></i>
                            </div>
                            <div>
                                <h4 class="modal-title mb-0 fw-bold">Detail Approval & Log Data</h4>
                                <div class="opacity-75 f-s-13 mt-1">Seluruh data komprehensif pada riwayat log ini</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- BODY MODAL (Dibuat lebih padat) -->
                    <div class="modal-body bg-light p-3" style="max-height: 75vh; overflow-y: auto;">
                        <div id="logDetailContent">
                            <!-- Tabel Log akan di-inject ke sini -->
                        </div>
                    </div>

                    <!-- FOOTER MODAL -->
                    <div class="modal-footer bg-white border-top py-2">
                        <button type="button" class="btn btn-secondary px-4 rounded shadow-sm fw-bold" data-bs-dismiss="modal">Tutup Detail</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            let table;
            
            $(document).ready(function() {
                // Konfigurasi DataTable
                table = $('#sampleTable').DataTable({
                    processing: true, 
                    serverSide: true,
                    ajax: "{{ route('customers.log.data') }}", 
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'log_name', name: 'log_name', className: 'text-center' },
                        { data: 'event', name: 'event', className: 'text-center' },
                        { data: 'causer_info', name: 'causer_info', orderable: false },
                        { data: 'subject_info', name: 'subject_info', orderable: false },
                        { data: 'subject_id', name: 'subject_id', className: 'text-center', orderable: false },
                        { data: 'properties', name: 'properties', orderable: false, searchable: false },
                        // PANGGIL DATA ACTION DI SINI
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center align-middle' }
                    ],
                    order: [[ 1, 'desc' ]], 
                    language: {
                        search: "",
                        searchPlaceholder: "🔍 Search data log/history...",
                        lengthMenu: "Show _MENU_ rows",
                        info: "Showing _START_ to _END_ of _TOTAL_ records"
                    },
                    drawCallback: function(settings) {
                        $('#sampleTable tbody tr').css({
                            'transition': 'background-color 0.2s ease'
                        }).hover(
                            function() { $(this).css('background-color', '#f8fafc'); },
                            function() { $(this).css('background-color', 'transparent'); }
                        );

                        $('#sampleTable tbody td').css({
                            'padding': '1.25rem 1rem',
                            'vertical-align': 'top',
                            'border-bottom': '1px solid #f1f5f9'
                        });
                    }
                });

                // Styling Search Box DataTable
                $('.dataTables_filter input').css({
                    'width': '300px', 
                    'margin-left': '10px',
                    'border-radius': '50rem',
                    'border': '1px solid #cbd5e1',
                    'padding': '0.5rem 1.2rem',
                    'background-color': '#ffffff',
                    'box-shadow': 'inset 0 1px 2px rgba(0,0,0,0.02)'
                });

                // --- FUNGSI KLIK: VIEW FULL DATA (TABEL COMPACT & PLAIN TEXT PROPERTIES) ---
                $(document).on('click', '.btn-view-full', function() {
                    let rawData = $(this).data('json');
                    let contentDiv = $('#logDetailContent');
                    contentDiv.empty();
                    
                    try {
                        let parsedData = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
                        
                        if (typeof parsedData !== 'object' || parsedData === null) {
                            contentDiv.html('<div class="alert alert-warning p-2 m-0 border-0 shadow-sm">Data log ini kosong.</div>');
                        } else {
                            // Render Data Json Menjadi Tabel Padat (Compact Table)
                            let html = '<div class="table-responsive m-0 shadow-sm border rounded bg-white"><table class="table table-sm table-bordered m-0" style="font-size: 13px; table-layout: fixed; width: 100%;"><tbody>';
                            
                            for (let key in parsedData) {
                                let val = parsedData[key];
                                let label = key.replace(/_/g, ' ').toUpperCase();
                                let displayVal = '';
                                
                                // Khusus untuk array/object Properties (Diubah menjadi Teks Biasa tanpa scroll samping)
                                if (key === 'Properties') {
                                    if (typeof val === 'object' && val !== null && Object.keys(val).length > 0) {
                                        
                                        // Buat container dengan word-wrap agar teks panjang turun ke bawah
                                        displayVal = '<div class="p-2 bg-light border rounded" style="font-size: 13px; max-height: 40vh; overflow-y: auto; word-wrap: break-word; white-space: normal;">';
                                        
                                        // Looping isi properties agar tampil sebagai teks biasa (Key: Value)
                                        for (let propKey in val) {
                                            let propVal = val[propKey];
                                            
                                            // Jika di dalam properties masih ada array/object (misal array attributes), jadikan string biasa
                                            if (typeof propVal === 'object' && propVal !== null) {
                                                propVal = JSON.stringify(propVal);
                                            }
                                            
                                            let cleanPropKey = propKey.replace(/_/g, ' ').toUpperCase();
                                            displayVal += `<div class="mb-1 pb-1 border-bottom border-light">
                                                              <span class="fw-bold text-dark">${cleanPropKey}:</span> 
                                                              <span class="text-secondary" style="word-break: break-word;">${propVal}</span>
                                                           </div>`;
                                        }
                                        displayVal += '</div>';

                                    } else {
                                        displayVal = '<span class="text-muted fst-italic">Tidak ada detail properties</span>';
                                    }
                                } else {
                                    // Teks Biasa untuk kolom selain Properties
                                    displayVal = '<span class="fw-bold text-dark" style="word-wrap: break-word; white-space: normal;">' + (val ?? '-') + '</span>';
                                }
                                
                                html += `
                                    <tr>
                                        <td class="bg-light text-secondary fw-bold align-middle" style="width: 30%; padding: 10px 14px; border-right: 1px solid #e2e8f0; word-wrap: break-word; white-space: normal;">
                                            <i class="ph-bold ph-caret-right me-1 text-primary"></i> ${label}
                                        </td>
                                        <td class="align-middle" style="padding: 10px 14px; word-wrap: break-word; white-space: normal;">${displayVal}</td>
                                    </tr>
                                `;
                            }
                            
                            html += '</tbody></table></div>';
                            contentDiv.html(html);
                        }
                    } catch (e) {
                        contentDiv.html('<div class="alert alert-danger p-2 m-0 border-0 shadow-sm">Error saat melakukan parsing data log.</div>');
                    }

                    // Buka Modal
                    let logModal = new bootstrap.Modal(document.getElementById('logDetailModal'));
                    logModal.show();
                });
            });
        </script>
    @endpush
</x-app-layout>