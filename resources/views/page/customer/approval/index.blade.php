<x-app-layout>
    @section('title')
        Approvals List
    @endsection

    @include('components.sample-table-styles')

    {{-- Loading Overlay dengan Inline CSS --}}
    <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-primary">Processing...</h5>
        <p class="text-muted">Please wait while we update the status.</p>
    </div>

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Approvals Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Approvals
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approvals List</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>

                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Account</option>
                        @foreach($accountStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <select id="approvalStatusFilter" class="form-select select2" style="width: 175px;">
                        <option value="all">All Approval</option>
                        @foreach($approvalStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <button id="resetFilters" class="btn btn-sm btn-secondary border" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                {{-- Stats Header --}}
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="table-title mb-1">
                            <i class="ph-duotone ph-users-three me-2"></i> Approval Queue
                        </h4>
                        <small class="text-white opacity-75 f-s-12">
                            Review customer requests, check credit limits, and approve or reject.
                        </small>
                    </div>
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">

                        {{-- Stat 1: Pending (Tugas Saya) --}}
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="My Pending Tasks">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-clock-countdown text-warning f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Pending</span>
                                <span class="f-s-14 fw-bold">{{ $pendingCount }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="My Approved History">
                                <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-seal-check text-success f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Approved</span>
                                <span class="f-s-14 fw-bold">{{ $approvedCount }}</span>
                            </div>
                        </div>

                        <div class="vr opacity-100 bg-white" style="height: 50px;"></div>

                        {{-- Stat 3: Active (Hijau - Centang) --}}
                        <div class="d-flex align-items-center gap-4">

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Active Customers">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                    <i class="ph-fill ph-check-circle text-success f-s-18"></i>
                                </div>
                                <div class="d-flex flex-column line-height-sm">
                                    <span class="f-s-11 opacity-75 text-uppercase fw-bold">Active</span>
                                    <span class="f-s-14 fw-bold">{{ $activeCount }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Inactive Customers">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                    <i class="ph-fill ph-x-circle text-danger f-s-18"></i>
                                </div>
                                <div class="d-flex flex-column line-height-sm">
                                    <span class="f-s-11 opacity-75 text-uppercase fw-bold">Inactive</span>
                                    <span class="f-s-14 fw-bold">{{ $inactiveCount }}</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="10%">Approver NIK</th>
                                <th>Customer</th>
                                <th width="5%" class="text-center">Level</th>
                                <th class="text-center">Status</th>
                                <th>Route To</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VIEW DETAIL & ACTION --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-light" id="viewModalLabel">
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light bg-opacity-10">

                    {{-- 1. BAGIAN FORM DATA (READ ONLY) --}}
                    @php
                        $readonlyStyle = 'background-color: #f8f9fa; color: #212529; opacity: 1; font-weight: 500; border: 1px solid #dee2e6;';
                    @endphp

                    <div id="viewModalBodyContent">
                        {{-- Field User Info --}}
                        <div class="card mb-3 border-primary shadow-sm">
                            <div class="card-header bg-light-primary">
                                <h6 class="mb-0 fw-bold text-primary"><i class="ph-bold ph-user-circle me-2"></i>Requester Info</h6>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="small text-muted fw-bold">Sales / User</label>
                                        <input type="text" class="form-control" id="view_user_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Main Customer Info --}}
                        <div class="card mb-3 border-secondary shadow-sm">
                            <div class="card-header bg-light-success">
                                <h6 class="mb-0 fw-bold text-success"><i class="ph-bold ph-identification-card me-2"></i>Customer Detail</h6>
                            </div>
                            <div class="card-body">
                                {{-- Account Group & Class --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Account Group</label>
                                        <select class="form-select" id="view_account_group" disabled style="{{ $readonlyStyle }}">
                                            <option></option>
                                            @foreach ($accountgroup as $ag)
                                                <option value="{{ $ag->id }}">{{ $ag->name_account_group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Customer Class</label>
                                        <select class="form-select" id="view_customer_class" disabled style="{{ $readonlyStyle }}">
                                            <option></option>
                                            @foreach ($customerClass as $cc)
                                                <option value="{{ $cc->id }}">{{ $cc->name_class }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr class="opacity-25">

                                {{-- Files Section --}}
                                <div class="row g-3 mb-4">
                                    <h6 class="fw-bold text-secondary mb-0">Uploaded Documents</h6>

                                    {{-- NPWP --}}
                                    <div class="col-md-4" id="view_div_npwp" style="display:none;">
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-preview-file" data-title="NPWP Preview">
                                            <i class="ph-bold ph-file-text me-1"></i> View NPWP
                                        </button>
                                    </div>

                                    {{-- NIB --}}
                                    <div class="col-md-4" id="view_div_nib" style="display:none;">
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-preview-file" data-title="NIB/SIUP Preview">
                                            <i class="ph-bold ph-file-text me-1"></i> View NIB/SIUP
                                        </button>
                                    </div>

                                    {{-- KTP --}}
                                    <div class="col-md-4" id="view_div_ktp" style="display:none;">
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-preview-file" data-title="KTP Preview">
                                            <i class="ph-bold ph-id-card me-1"></i> View KTP
                                        </button>
                                    </div>

                                    <div class="col-12 text-muted f-s-12" id="no_files_msg" style="display:none;">No documents uploaded.</div>
                                </div>
                                <hr class="opacity-25">

                                {{-- General Info --}}
                                <div class="row g-3 mb-4">
                                    <h6 class="fw-bold text-secondary mb-0">General Information</h6>
                                    <div class="col-md-12">
                                        <label class="form-label small text-muted">Customer Name</label>
                                        <input type="text" class="form-control fw-bold" id="view_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small text-muted">Sort Name</label>
                                        <input type="text" class="form-control" id="view_sort_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small text-muted">Address</label>
                                        <input type="text" class="form-control mb-1" id="view_address1" disabled style="{{ $readonlyStyle }}">
                                        <input type="text" class="form-control mb-1" id="view_address2" disabled style="{{ $readonlyStyle }}">
                                        <input type="text" class="form-control" id="view_address3" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">City</label>
                                        <input type="text" class="form-control" id="view_city" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Postal Code</label>
                                        <input type="text" class="form-control" id="view_postal_code" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Country</label>
                                        <input type="text" class="form-control" id="view_country" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Email (General)</label>
                                        <input type="text" class="form-control" id="view_email" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Area</label>
                                        <input type="text" class="form-control" id="view_area" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                </div>
                                <hr class="opacity-25">

                                {{-- Shipping & Managers --}}
                                <div class="row g-3 mb-4">
                                    <h6 class="fw-bold text-secondary mb-0">Shipping & Key Personnel</h6>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Shipping To</label>
                                        <input type="text" class="form-control" id="view_shipping_to_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Shipping Address</label>
                                        <textarea class="form-control" id="view_shipping_to_address" rows="1" disabled style="{{ $readonlyStyle }}"></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Purchasing Mgr</label>
                                        <input type="text" class="form-control" id="view_purchasing_manager_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Purchasing Email</label>
                                        <input type="text" class="form-control" id="view_purchasing_manager_email" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Finance Mgr</label>
                                        <input type="text" class="form-control" id="view_finance_manager_name" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">Finance Email</label>
                                        <input type="text" class="form-control" id="view_finance_manager_email" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                </div>
                                <hr class="opacity-25">

                                {{-- Billing & Tax --}}
                                <div class="row g-3 mb-4">
                                    <h6 class="fw-bold text-secondary mb-0">Billing & Tax Data</h6>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Billing Contact</label>
                                        <input type="text" class="form-control" id="view_penagihan_nama_kontak" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Billing Phone</label>
                                        <input type="text" class="form-control" id="view_penagihan_telepon" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Billing Address</label>
                                        <textarea class="form-control" id="view_penagihan_address" rows="1" disabled style="{{ $readonlyStyle }}"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small text-muted">Correspondence Address</label>
                                        <textarea class="form-control" id="view_surat_menyurat_address" rows="1" disabled style="{{ $readonlyStyle }}"></textarea>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">NPWP</label>
                                        <input type="text" class="form-control fw-bold" id="view_npwp" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">NPWP Date</label>
                                        <input type="text" class="form-control" id="view_tanggal_npwp" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">NPPKP</label>
                                        <input type="text" class="form-control" id="view_nppkp" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">NPPKP Date</label>
                                        <input type="text" class="form-control" id="view_tanggal_nppkp" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                </div>
                                <hr class="opacity-25">

                                {{-- Financial Terms --}}
                                <div class="row g-3">
                                    <h6 class="fw-bold text-secondary mb-0">Financial Terms</h6>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Term of Payment</label>
                                        <input type="text" class="form-control fw-bold text-primary" id="view_term_of_payment" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Output Tax</label>
                                        <input type="text" class="form-control" id="view_output_tax" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Lead Time</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="view_lead_time" disabled style="{{ $readonlyStyle }}">
                                            <span class="input-group-text bg-light">Days</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Credit Limit</label>
                                        <input type="text" class="form-control fw-bold text-success" id="view_credit_limit" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">CCAR</label>
                                        <input type="text" class="form-control" id="view_ccar" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Bank Garansi</label>
                                        <input type="text" class="form-control" id="view_bank_garansi" disabled style="{{ $readonlyStyle }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="viewModalActionFormContainer" class="mt-4">
                    </div>

                </div>

                <div class="modal-footer" id="viewModalFooter">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title" id="filePreviewModalLabel">
                        <i class="ph-bold ph-image me-2"></i>File Preview
                    </h6>
                    {{-- Tombol close khusus agar hanya menutup modal preview, bukan modal utama --}}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-light" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    {{-- Container Gambar --}}
                    <img id="previewImageContent" src="" class="img-fluid" style="max-height: 80vh; display: none;" alt="File Preview">

                    {{-- Container Iframe (Untuk PDF) --}}
                    <iframe id="previewPdfContent" src="" style="width: 100%; height: 70vh; border: none; display: none;"></iframe>

                    {{-- Pesan Error jika file tidak support --}}
                    <div id="previewErrorMessage" class="text-muted p-5" style="display: none;">
                        <i class="ph-bold ph-file-x f-s-30 mb-2"></i><br>
                        File type not supported for preview.<br>
                        <a href="#" id="downloadFallbackLink" target="_blank" class="btn btn-sm btn-primary mt-2">Download File</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $('.select2').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: 10
            });

            $(document).ready(function() {
                const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.approval.data') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                            d.approval_status = $('#approvalStatusFilter').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center dt-no-wrap' },
                        { data: 'approver_nik', name: 'approver_nik', className: 'dt-no-wrap' },
                        { data: 'customer_name', name: 'customers.name', className: 'dt-wrap' },
                        { data: 'level', name: 'approval_logs.level', className: 'text-center dt-no-wrap' },
                        { data: 'status_approval', name: 'customers.status_approval', className: 'text-center dt-no-wrap' },
                        { data: 'route_to', name: 'customers.route_to', className: 'text-center dt-wrap' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center dt-no-wrap' }
                    ],
                    order: [[3, 'asc'], [0, 'asc']],
                    autoWidth: false
                });

                $('#statusFilter, #approvalStatusFilter').on('change', function() {
                    table.ajax.reload();
                });

                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all');
                    $('#approvalStatusFilter').val('all');
                    $('#statusFilter').trigger('change');
                    $('#approvalStatusFilter').trigger('change');
                });

                // --- 1. POPULATE FORM FUNCTION ---
                window.populateViewForm = function(data) {
                    let salesName = '-';
                    if (data.sales && data.sales.user) {
                        salesName = data.sales.user.name;
                    } else if (data.user) {
                        salesName = data.user.name;
                    }
                    $('#view_user_name').val(salesName);

                    // Account Info
                    $('#view_account_group').val(data.account_group);
                    $('#view_customer_class').val(data.customer_class);

                    // General
                    $('#view_name').val(data.name);
                    $('#view_sort_name').val(data.sort_name || '-');
                    $('#view_address1').val(data.address1);
                    $('#view_address2').val(data.address2);
                    $('#view_address3').val(data.address3);
                    $('#view_city').val(data.city);
                    $('#view_postal_code').val(data.postal_code);
                    $('#view_country').val(data.country);
                    $('#view_email').val(data.email);
                    $('#view_area').val(data.area);

                    // Shipping
                    $('#view_shipping_to_name').val(data.shipping_to_name);
                    $('#view_shipping_to_address').val(data.shipping_to_address);
                    $('#view_purchasing_manager_name').val(data.purchasing_manager_name);
                    $('#view_purchasing_manager_email').val(data.purchasing_manager_email);
                    $('#view_finance_manager_name').val(data.finance_manager_name);
                    $('#view_finance_manager_email').val(data.finance_manager_email);

                    // Billing
                    $('#view_penagihan_nama_kontak').val(data.penagihan_nama_kontak);
                    $('#view_penagihan_telepon').val(data.penagihan_telepon);
                    $('#view_penagihan_address').val(data.penagihan_address);
                    $('#view_surat_menyurat_address').val(data.surat_menyurat_address);

                    // Tax
                    $('#view_npwp').val(data.npwp);
                    $('#view_tanggal_npwp').val(data.tanggal_npwp);
                    $('#view_nppkp').val(data.nppkp);
                    $('#view_tanggal_nppkp').val(data.tanggal_nppkp);

                    // Financial
                    $('#view_term_of_payment').val(data.term_of_payment);
                    $('#view_output_tax').val(data.output_tax);
                    $('#view_lead_time').val(data.lead_time);

                    const limit = parseFloat(data.credit_limit) || 0;
                    $('#view_credit_limit').val(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(limit));

                    $('#view_ccar').val(data.ccar === 'smd_idr' ? 'SMD (IDR)' : (data.ccar === 'smd_usd' ? 'SMD USD' : data.ccar));
                    $('#view_bank_garansi').val(data.bank_garansi === 'YA' ? 'Yes' : 'No');

                    // FILES Handling
                    $('#view_div_npwp, #view_div_nib, #view_div_ktp').hide();
                    $('#no_files_msg').show();
                    let fileFound = false;

                    const storageBase = "{{ asset('storage') }}";

                    function setupFileBtn(divId, fileName) {
                        if(fileName) {
                            const cleanFileName = fileName.startsWith('/') ? fileName.substring(1) : fileName;
                            const fullUrl = `${storageBase}/${cleanFileName}`;

                            const btn = $(divId + ' .btn-preview-file');
                            btn.data('url', fullUrl);
                            btn.data('filename', cleanFileName);
                            btn.data('customer-name', data.name);

                            $(divId).show();
                            fileFound = true;
                        }
                    }

                    if(data.files && data.files.length > 0) {
                            const f = data.files[0];
                            setupFileBtn('#view_div_npwp', f.npwp_file);
                            setupFileBtn('#view_div_nib', f.nib_siup_file);
                            setupFileBtn('#view_div_ktp', f.ktp_file);
                    }
                    // Fallback data di root object (jika controller mengirim flat object)
                    else {
                        setupFileBtn('#view_div_npwp', data.file_npwp);
                        setupFileBtn('#view_div_nib', data.file_nib);
                        setupFileBtn('#view_div_ktp', data.file_ktp);
                    }

                    if(fileFound) $('#no_files_msg').hide();
                };

                $(document).on('click', '.btn-preview-file', function() {
                    const url = $(this).data('url');
                    const filename = $(this).data('filename');
                    const title = $(this).data('title') || 'File Preview';
                    const customerName = $(this).data('customer-name') || '';

                    // Reset konten modal
                    $('#previewImageContent').hide();
                    $('#previewPdfContent').hide();
                    $('#previewErrorMessage').hide();

                    const headerTitle = `<i class="ph-bold ph-image me-2"></i> ${title} <span class="text-white-50 mx-2">|</span> <span class="fw-light">${customerName}</span>`;
                    $('#filePreviewModalLabel').html(headerTitle);

                    if (!url) return;

                    // Cek Ekstensi File
                    const extension = filename.split('.').pop().toLowerCase();

                    // Jika Gambar (jpg, jpeg, png, bmp, webp)
                    if (['jpg', 'jpeg', 'png', 'bmp', 'webp'].includes(extension)) {
                        $('#previewImageContent').attr('src', url).show();
                    }
                    // Jika PDF
                    else if (extension === 'pdf') {
                        $('#previewPdfContent').attr('src', url).show();
                    }
                    // Format lain (doc, zip, dll) -> Tampilkan tombol download
                    else {
                        $('#downloadFallbackLink').attr('href', url);
                        $('#previewErrorMessage').show();
                    }

                    const fileModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                    fileModal.show();
                });

                $(document).on('click', '.action-btn-modal', function() {
                    const button = $(this);
                    const customerId = button.data('id');
                    const token = button.data('token');
                    const action = button.data('action');
                    const customerName = button.data('name');
                    
                    const btnTitle = button.attr('title') || '';
                    const isITInput = btnTitle.includes('Input Customer Code'); 

                    const originalIcon = button.html();
                    button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                    $.ajax({
                        url: `/customers/${customerId}`,
                        type: 'GET',
                        success: function(response) {
                            populateViewForm(response);

                            const isReject = action === 'reject';
                            
                            let modalTitle = isReject ? 'Reject Customer' : 'Approve with Review';
                            
                            if (isITInput) {
                                modalTitle = 'Input Customer Code & Activate';
                            }

                            const cardBorder = isReject ? 'border-danger' : 'border-primary';
                            const headerBg = isReject ? 'bg-danger text-white' : 'bg-primary text-white';
                            const btnClass = isReject ? 'btn-danger' : 'btn-success'; 
                            
                            let btnText = isReject ? 'Submit Reject' : 'Submit Approval';
                            if (isITInput) btnText = 'Save & Activate';
                            
                            let notesLabel = isReject ? 'Rejection Reason' : 'Review Notes';
                            if (isITInput) notesLabel = 'Notes (Optional)';

                            const notesPlaceholder = isReject ? 'Please provide a valid reason...' : 'Optional notes...';

                            $('#viewModalLabel').text(`${modalTitle} : ${customerName}`);

                            let urlTemplate = "{{ route('customers.approval_action', ':id') }}";
                            let finalUrl = urlTemplate.replace(':id', customerId);

                            let additionalInputs = '';
                            if (isITInput) {
                                let today = new Date().toISOString().split('T')[0];
                                let joinVal = response.join_date ? response.join_date.split(' ')[0] : today;
                                let codeVal = response.code || '';

                                additionalInputs = `
                                    <div class="alert alert-info py-2 small mb-3 border-info">
                                        <i class="ph-bold ph-info me-1"></i> 
                                        Anda sedang dalam mode <b>IT Approval</b>. Silahkan lengkapi data berikut.
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-primary">Customer Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control fw-bold" name="update_code" value="${codeVal}" required placeholder="e.g. ID-001">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-primary">Join Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="update_join_date" value="${joinVal}" required>
                                        </div>
                                    </div>
                                `;
                            }

                            const actionFormHtml = `
                                <div class="card ${cardBorder} shadow-sm">
                                    <div class="card-header ${headerBg}">
                                        <h6 class="mb-0 fw-bold"><i class="ph-bold ph-gavel me-2"></i>Decision: ${isITInput ? 'ACTIVATE' : action.toUpperCase()}</h6>
                                    </div>
                                    <div class="card-body p-4 bg-white">
                                        <form id="modalResponseForm" action="${finalUrl}" method="POST">
                                            @csrf
                                            <input type="hidden" name="token" value="${token}">
                                            <input type="hidden" name="action" value="${action}">
                                            
                                            ${additionalInputs}

                                            <div class="mb-3">
                                                <label for="modal_notes" class="form-label fw-bold">${notesLabel} ${isReject ? '<span class="text-danger">*</span>' : ''}</label>
                                                <textarea class="form-control border-${isReject ? 'danger' : 'primary'}" id="modal_notes" name="notes" rows="3" placeholder="${notesPlaceholder}" ${isReject ? 'required' : ''}></textarea>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            `;

                            const submitButtonHtml = `
                                <button type="submit" form="modalResponseForm" class="btn ${btnClass} px-4 py-2 fw-bold">
                                    <i class="ph-bold ${isReject ? 'ph-x-circle' : 'ph-check-circle'} me-2"></i> ${btnText}
                                </button>
                            `;

                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalFooter button[type="submit"]').remove();
                            $('#viewModalFooter').prepend(submitButtonHtml);

                            $('#viewModal').modal('show');
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to fetch customer data.', 'error');
                        },
                        complete: function() {
                            button.html(originalIcon).prop('disabled', false);
                        }
                    });
                });

                // --- 3. HANDLER SUBMIT FORM (VALIDASI & OVERLAY) ---
                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const notesField = $('#modal_notes');
                    const notesValue = notesField.val().trim();
                    const action = form.find('input[name="action"]').val();
                    const isReject = action === 'reject';

                    if (!notesValue || !/[a-zA-Z]/.test(notesValue)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Notes',
                            text: 'Please provide a valid reason/note containing letters.',
                            target: document.getElementById('viewModal')
                        });
                        return;
                    }

                    Swal.fire({
                        title: isReject ? 'Confirm Rejection?' : 'Confirm Approval?',
                        text: isReject ? "This request will be rejected permanently." : "You are about to approve with notes.",
                        icon: isReject ? 'warning' : 'question',
                        showCancelButton: true,
                        confirmButtonColor: isReject ? '#d33' : '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: isReject ? 'Yes, Reject it!' : 'Yes, Submit!',
                        target: document.getElementById('viewModal')
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loading-overlay').css('display', 'flex');
                            $('#viewModal').modal('hide');

                            $.ajax({
                                url: form.attr('action'),
                                method: 'POST',
                                data: form.serialize(),
                                success: function(response) {
                                    $('#loading-overlay').hide();
                                    Swal.fire('Success!', response.message, 'success');
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    $('#loading-overlay').hide();
                                    const errorMsg = xhr.responseJSON?.message || 'An error occurred processing the request.';
                                    Swal.fire('Error!', errorMsg, 'error');
                                }
                            });
                        }
                    });
                });

                // Quick Approve Handler
                $('#customerTable').on('click', '.action-btn', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const customerId = button.data('id');
                    const name = button.data('name');

                    let url = "{{ route('customers.approval_action', ':id') }}".replace(':id', customerId);

                    Swal.fire({
                        title: 'Quick Approve?',
                        text: `Approve customer ${name} without notes?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Yes, Approve!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loading-overlay').css('display', 'flex');

                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    token: token,
                                    action: 'approve',
                                    notes: 'Approved without Review'
                                },
                                success: function(res) {
                                    $('#loading-overlay').hide();
                                    Swal.fire('Approved!', res.message, 'success');
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    $('#loading-overlay').hide();
                                    Swal.fire('Error!', xhr.responseJSON?.message || 'Error', 'error');
                                }
                            });
                        }
                    });
                });

                $(document).on('click', '.btn-resend-email', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const approverName = button.data('approver-name') || 'User Terkait';

                    Swal.fire({
                        title: 'Resend Email?',
                        html: `Kirim ulang notifikasi email approval ke <b>${approverName}</b>?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107', // Warna Warning
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Resend!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const originalContent = button.html();
                            button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                            $.ajax({
                                // Pastikan route ini sesuai dengan definisi di web.php
                                // Contoh: Route::post('/approvals/resend/{token}', ...)
                                url: `/approvals/resend/${token}`,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire('Sent!', response.message, 'success');
                                },
                                error: function(xhr) {
                                    const errorMsg = xhr.responseJSON?.message || 'Gagal mengirim ulang email.';
                                    Swal.fire('Error!', errorMsg, 'error');
                                },
                                complete: function() {
                                    button.html(originalContent).prop('disabled', false);
                                }
                            });
                        }
                    });
                });

                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove();
                });
            });
        </script>
    @endpush
</x-app-layout>
