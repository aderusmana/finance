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
                    <!-- <div class="flex-shrink-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: rgba(0,0,0,0.2); padding: 0.6rem 1.2rem; border-radius: 50rem; display: inline-flex; flex-wrap: nowrap; border: 1px solid rgba(255,255,255,0.1);">
                                <li class="breadcrumb-item"><a href="/" class="text-white text-decoration-none"><i class="ph-fill ph-monitor-play me-1"></i> Monitoring</a></li>
                                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Activity Logs</li>
                            </ol>
                        </nav>
                    </div> -->
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
                                        <th style="background-color: #f8fafc; color: #2563eb; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 25%;"><i class="ph-bold ph-braces me-1"></i> Properties</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. MODAL PREVIEW JSON BERGAYA TERMINAL DARK MODE --}}
        <div class="modal fade" id="jsonPreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 1.25rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                    <div class="modal-header d-flex justify-content-between align-items-center" style="background: #1e293b; border-bottom: 1px solid #334155; padding: 1rem 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex gap-1 me-3">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #ef4444;"></div>
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></div>
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
                            </div>
                            <h6 class="modal-title fw-bolder mb-0 text-white" style="letter-spacing: 0.5px; font-family: monospace;">~/log_details.json</h6>
                        </div>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0" style="position: relative;">
                        <button id="btnCopyJson" class="btn btn-sm d-flex align-items-center gap-1" style="position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.1); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; font-size: 0.75rem; font-weight: 600; transition: all 0.2s;">
                            <i class="ph-bold ph-copy"></i> Copy Data
                        </button>
                        <pre id="jsonContent" style="margin: 0; padding: 1.5rem; color: #a5b4fc; font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; line-height: 1.6; max-height: 60vh; overflow-y: auto;"></pre>
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
                    ajax: "{{ route('customers.log.data') }}", // Sesuaikan dengan route kamu
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'log_name', name: 'log_name', className: 'text-center' },
                        { data: 'event', name: 'event', className: 'text-center' },
                        { data: 'causer_info', name: 'causer_info', orderable: false },
                        { data: 'subject_info', name: 'subject_info', orderable: false },
                        { data: 'subject_id', name: 'subject_id', className: 'text-center', orderable: false },
                        { data: 'properties', name: 'properties', orderable: false, searchable: false }
                    ],
                    order: [[ 1, 'desc' ]], // Urutkan berdasarkan created_at (kolom index 1)
                    language: {
                        search: "",
                        searchPlaceholder: "🔍 Search data log/history...",
                        lengthMenu: "Show _MENU_ rows",
                        info: "Showing _START_ to _END_ of _TOTAL_ records"
                    },
                    drawCallback: function(settings) {
                        // Inject CSS secara inline ke dalam TR dan TD setelah data selesai di-render
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

                // --- FUNGSI KLIK: VIEW JSON TERMINAL ---
                $(document).on('click', '.btn-view-json', function() {
                    let rawData = $(this).data('json');
                    
                    try {
                        let parsedData = typeof rawData === 'string' ? JSON.parse(rawData) : rawData;
                        // Format JSON agar menjorok rapi (indent 4 spasi)
                        let formattedJson = JSON.stringify(parsedData, null, 4);
                        
                        // Highlight syntax JSON sederhana agar berwarna-warni
                        formattedJson = formattedJson.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                            let color = '#38bdf8'; // Default number/boolean color (cyan)
                            if (/^"/.test(match)) {
                                if (/:$/.test(match)) {
                                    color = '#f472b6'; // Key (pink/rose)
                                } else {
                                    color = '#a3e635'; // String value (lime green)
                                }
                            } else if (/true|false/.test(match)) {
                                color = '#fbbf24'; // Boolean (amber)
                            } else if (/null/.test(match)) {
                                color = '#94a3b8'; // Null (slate)
                            }
                            return '<span style="color: ' + color + ';">' + match + '</span>';
                        });

                        $('#jsonContent').html(formattedJson);
                    } catch (e) {
                        $('#jsonContent').html('<span style="color: #ef4444;">Error parsing JSON data:</span>\n' + rawData);
                    }

                    // Tampilkan Modal
                    let jsonModal = new bootstrap.Modal(document.getElementById('jsonPreviewModal'));
                    jsonModal.show();
                });

                // --- FUNGSI COPY JSON ---
                $('#btnCopyJson').on('click', function() {
                    // Ambil teks murni (tanpa tag HTML pewarnaan)
                    let textToCopy = $('#jsonContent').text();
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        let originalHtml = $(this).html();
                        $(this).html('<i class="ph-bold ph-check text-emerald-400"></i> Copied!')
                               .css({'background': 'rgba(16,185,129,0.2)', 'border-color': 'rgba(16,185,129,0.5)'});
                        
                        setTimeout(() => {
                            $(this).html(originalHtml).css({'background': 'rgba(255,255,255,0.1)', 'border-color': 'rgba(255,255,255,0.2)'});
                        }, 2000);
                    });
                });

            });
        </script>
    @endpush
</x-app-layout>