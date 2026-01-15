<x-app-layout>
    @section('title', 'System Activity Logs')
    @include('components.sample-table-styles')

    {{-- HEADER --}}
    <div class="row m-1 mb-4">
        <div class="col-12">
            <h4 class="main-title text-dark fw-bold" style="letter-spacing: -0.5px;">System Activity Logs</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li><a class="f-s-14 f-w-500" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Activity Logs</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="ph-duotone ph-activity me-2 text-primary"></i> Audit Trail</h5>
                            <small class="text-muted">Mencatat seluruh aktivitas sistem, user, dan customer.</small>
                        </div>
                        <div>
                            <button class="btn btn-light btn-sm rounded-pill border" onclick="window.location.reload()">
                                <i class="ph-bold ph-arrows-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="w-100 display align-middle" id="sampleTable">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Timestamp</th>
                                    <th width="15%">User / Actor</th>
                                    <th>Activity Description</th>
                                    <th width="20%">Subject / Target</th>
                                    <th width="10%" class="text-center">Data</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL JSON VIEWER --}}
    <div class="modal fade" id="jsonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="ph-bold ph-code me-2"></i> Activity Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-dark text-white p-0">
                    <pre class="m-0 p-3" id="jsonContent" style="font-family: monospace; font-size: 12px; color: #a5d6ff;"></pre>
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
                    ajax: "{{ route('system-logs.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'causer_name', name: 'causer.name' },
                        { data: 'description', name: 'description' },
                        { data: 'subject_description', name: 'subject_type' },
                        { data: 'properties', name: 'properties', className: 'text-center', orderable: false, searchable: false }
                    ],
                    order: [[1, 'desc']], // Urutkan berdasarkan timestamp terbaru
                    pageLength: 25
                });

                // JSON Viewer Logic
                $(document).on('click', '.btn-view-json', function() {
                    var json = $(this).data('json');
                    // Pretty print JSON
                    var formatted = JSON.stringify(json, null, 4);
                    $('#jsonContent').text(formatted);
                    $('#jsonModal').modal('show');
                });
            });
        </script>
    @endpush
</x-app-layout>
