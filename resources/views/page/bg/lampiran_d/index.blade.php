<x-app-layout>
    @section('title', 'Lampiran D')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Lampiran D Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Lampiran D</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            
            {{-- TAB NAVIGATION --}}
            <ul class="nav nav-tabs nav-tabs-custom mb-3" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="ph-bold ph-files me-1"></i> Overview (Active)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="versions-tab" data-bs-toggle="tab" data-bs-target="#versions" type="button" role="tab">
                        <i class="ph-bold ph-clock-counter-clockwise me-1"></i> Versions (History)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                
                {{-- TAB 1: OVERVIEW --}}
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-primary text-white">
                            <h6 class="mb-0"><i class="ph-bold ph-list me-2"></i> Active Documents</h6>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="w-100 display" id="tableOverview">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Customer</th>
                                        <th>Ref Code</th>
                                        <th class="text-center">Latest Version</th>
                                        <th>Last Updated</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: VERSIONS (GLOBAL HISTORY) --}}
                <div class="tab-pane fade" id="versions" role="tabpanel">
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-secondary text-white">
                            <h6 class="mb-0"><i class="ph-bold ph-clock-counter-clockwise me-2"></i> Global Revision Log</h6>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="w-100 display" id="tableVersions">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Ref Code</th>
                                        <th class="text-center">Version</th>
                                        <th>Modified By</th>
                                        <th class="text-center">Details</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL EDIT LAMPIRAN D --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="ph-bold ph-pencil"></i> Edit & Create New Version</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="lampiran_id">
                    <div class="modal-body">
                        <div class="alert alert-info small py-2">
                            <i class="ph-bold ph-info me-1"></i> Saving this form will increment the version number.
                        </div>
                        
                        <div class="row g-3">
                            {{-- FORM FIELDS SAMA SEPERTI SEBELUMNYA --}}
                            <div class="col-12 border-bottom pb-2 fw-bold text-primary">Data Customer</div>
                            <div class="col-md-6">
                                <label class="form-label small">Nama Customer</label>
                                <input type="text" class="form-control" name="customer_name" id="customer_name">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Kota</label>
                                <input type="text" class="form-control" name="customer_city" id="customer_city">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Wilayah</label>
                                <input type="text" class="form-control" name="customer_area" id="customer_area">
                            </div>

                            <div class="col-12 border-bottom pb-2 fw-bold text-primary mt-3">Financial Data</div>
                            <div class="col-md-4">
                                <label class="form-label small">Average Sales</label>
                                <input type="number" class="form-control" name="average" id="average">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">TOP</label>
                                <input type="number" class="form-control" name="top" id="top">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Lead Time</label>
                                <input type="number" class="form-control" name="lead_time" id="lead_time">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Inflation (%)</label>
                                <input type="number" step="0.01" class="form-control" name="inflation" id="inflation">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Credit Limit</label>
                                <input type="number" class="form-control" name="credit_limit" id="credit_limit">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Set BG (Ditetapkan)</label>
                                <input type="number" class="form-control" name="set_bg" id="set_bg">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">BG Nominal (Diserahkan)</label>
                                <input type="number" class="form-control" name="bg_nominal" id="bg_nominal">
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold">Revision Remarks (Alasan Revisi)</label>
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Contoh: Koreksi nilai sales bulan lalu..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save New Version</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL SNAPSHOT VIEWER --}}
    <div class="modal fade" id="snapshotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold">Detail Data Snapshot</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="jsonViewer" class="bg-dark text-white p-3 rounded small" style="max-height: 400px; overflow: auto; font-family: monospace;"></pre>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            
            // 1. DATA TABLE OVERVIEW (Tab 1)
            var tableOverview = $('#tableOverview').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: "{{ route('lampiran-d.index') }}",
                    data: { mode: 'overview' } // Default active docs
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'customer', name: 'customer'},
                    {data: 'form_code', name: 'form_code'},
                    {data: 'version', name: 'version', className: 'text-center'},
                    {data: 'last_updated', name: 'updated_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });

            // 2. DATA TABLE VERSIONS (Tab 2) - Load saat tab diklik atau langsung
            var tableVersions = $('#tableVersions').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: "{{ route('lampiran-d.index') }}",
                    data: { mode: 'versions' } // Load global history
                },
                order: [[1, 'desc']], // Urutkan by Date terbaru
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center'},
                    {data: 'date', name: 'generated_at'},
                    {data: 'customer', name: 'lampiranD.submission.recommendation.customer.name'},
                    {data: 'form_code', name: 'lampiranD.submission.form_code'},
                    {data: 'version', name: 'version_no', className: 'text-center'},
                    {data: 'modified_by', name: 'generator.name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
                ]
            });

            // Reload tables when tabs change (Optional, biar refresh data)
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("id"); 
                if (target === 'overview-tab') {
                    tableOverview.ajax.reload(null, false);
                } else {
                    tableVersions.ajax.reload(null, false);
                }
            });

            // --- LOGIC EDIT (Sama seperti sebelumnya) ---
            $(document).on('click', '.btn-edit-lampiran', function() {
                var id = $(this).data('id');
                $('#lampiran_id').val(id);
                
                $.get("{{ url('bg/lampiran-d') }}/" + id, function(data) {
                    $('#customer_name').val(data.customer_name);
                    $('#customer_city').val(data.customer_city);
                    $('#customer_area').val(data.customer_area);
                    $('#average').val(data.average);
                    $('#top').val(data.top);
                    $('#lead_time').val(data.lead_time);
                    $('#inflation').val(data.inflation);
                    $('#credit_limit').val(data.credit_limit);
                    $('#set_bg').val(data.set_bg);
                    $('#bg_nominal').val(data.bg_nominal);
                    
                    $('#editModal').modal('show');
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                var id = $('#lampiran_id').val();
                var formData = $(this).serialize();

                Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: "{{ url('bg/lampiran-d') }}/" + id,
                    type: "POST",
                    data: formData,
                    success: function(res) {
                        if(res.success) {
                            Swal.fire('Success', res.message, 'success');
                            $('#editModal').modal('hide');
                            tableOverview.ajax.reload();
                            tableVersions.ajax.reload(); // Reload history juga
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            });

            // --- VIEW SNAPSHOT ---
            $(document).on('click', '.btn-view-snapshot', function() {
                var vId = $(this).data('id');
                $.get("{{ url('bg/lampiran-d/version') }}/" + vId, function(data) {
                    var json = JSON.stringify(data.data_snapshot, null, 4);
                    $('#jsonViewer').text(json);
                    $('#snapshotModal').modal('show');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>