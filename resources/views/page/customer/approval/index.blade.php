<x-app-layout>
    @section('title')
        Approvals List
    @endsection

    @include('components.sample-table-styles')

    {{-- Loading Overlay --}}
    <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-primary">Processing...</h5>
        <p class="text-muted">Please wait while we update the status.</p>
    </div>

    {{-- Stats and Header Section (Unchanged) --}}
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Approvals Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="/"><i class="ph-duotone ph-address-book f-s-16"></i> Home</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Approvals List</a></li>
            </ul>
        </div>
    </div>

    {{-- Filters and Table Section (Unchanged) --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-none d-md-flex align-items-center gap-2 mb-3">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                    
                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Account</option>
                        <option value="Active">Active (BG)</option>
                        <option value="Inactive">Inactive (BG)</option>
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
                        <h4 class="table-title mb-1"><i class="ph-duotone ph-users-three me-2"></i> Approval Queue</h4>
                        <small class="text-white opacity-75 f-s-12">Review customer requests, check credit limits, and approve or reject.</small>
                    </div>
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">
                        {{-- Stats (Pending, Approved, etc) --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-clock-countdown text-warning f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Pending</span>
                                <span class="f-s-14 fw-bold">{{ $pendingCount }}</span>
                            </div>
                        </div>
                         <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-seal-check text-success f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Approved</span>
                                <span class="f-s-14 fw-bold">{{ $approvedCount }}</span>
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

    {{-- ========================================================================================= --}}
    {{-- NEW MODAL VIEW DETAIL & ACTION (UPDATED DESIGN) --}}
    {{-- ========================================================================================= --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 overflow-hidden">

                <div class="modal-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="ph-bold ph-user-circle f-s-32"></i>
                        </div>
                        <div>
                            <h4 class="modal-title mb-0 fw-bold" id="view_header_name">Customer Name</h4>
                            <div class="opacity-75 f-s-14 mt-1" id="view_header_code">CODE-001</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light p-4" style="max-height: 85vh; overflow-y: auto;">

                    {{-- Status Banner (Tetap Sama) --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-5">
                                    <div>
                                        <label class="fw-bold text-muted text-uppercase f-s-11 mb-1">Account Status</label>
                                        <div><span id="view_status_badge" class="badge bg-secondary f-s-12 px-3 py-2">STATUS</span></div>
                                    </div>
                                    <div class="vr" style="height: 40px; opacity: 0.1;"></div>
                                    <div>
                                        <label class="fw-bold text-muted text-uppercase f-s-11 mb-1">Approval Progress</label>
                                        <div id="view_approval_badge" class="fw-bold text-dark f-s-16">Pending</div>
                                    </div>
                                    <div class="vr" style="height: 40px; opacity: 0.1;"></div>
                                    <div>
                                        <label class="fw-bold text-muted text-uppercase f-s-11 mb-1">Sales Person</label>
                                        <div id="view_user_name" class="fw-bold text-dark f-s-16">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- General Info (Tetap Sama) --}}
                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="ph-fill ph-info me-2"></i> General Information</h5>
                    <div class="card border-0 shadow-sm mb-4">
                         <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Customer Name</label>
                                            <div class="fw-bold text-dark f-s-16" id="view_name">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Sort Name / Alias</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_sort_name">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">No. PKD</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_no_pkd">-</div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Email Address</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_email">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Main Address</label>
                                            <div class="fw-bold text-dark f-s-14 lh-base" id="view_full_address">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">City</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_city">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Area</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_area">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Postal Code</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_postal_code">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Financial & Tax (DIMODIFIKASI UNTUK INPUT FINANCE) --}}
                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="ph-fill ph-currency-dollar me-2"></i> Financial & Tax</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <label class="text-white text-opacity-75 text-uppercase f-s-12 fw-bold">Credit Limit</label>
                                            {{-- Container Credit Limit agar bisa diubah JS --}}
                                            <div id="container_credit_limit">
                                                <h3 class="mb-0 fw-bold mt-1" id="view_credit_limit">IDR 0</h3>
                                            </div>
                                        </div>
                                        <i class="ph-duotone ph-wallet f-s-40 text-white text-opacity-50"></i>
                                    </div>
                                    <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                                        <span class="f-s-13 opacity-75">Term of Payment</span>
                                        {{-- Container TOP --}}
                                        <div id="container_top">
                                            <span class="fw-bold f-s-16 bg-warning bg-opacity-20 px-2 py-1 rounded"><span id="view_term_of_payment">-</span></span>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <span class="f-s-13 opacity-75">Lead Time</span>
                                        {{-- Container Lead Time --}}
                                        <div id="container_lead_time">
                                            <span class="fw-bold f-s-14"><span id="view_lead_time">0</span> Days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card Tax & Billing (Tetap Sama) --}}
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">Tax Information</h6>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPWP No.</span>
                                        <span class="fw-bold text-dark" id="view_npwp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPWP Date</span>
                                        <span class="fw-bold text-dark" id="view_tanggal_npwp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPPKP</span>
                                        <span class="fw-bold text-dark" id="view_nppkp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold text-secondary f-s-13">Output Tax</span>
                                        <span class="fw-bold text-dark" id="view_output_tax">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">Billing Contact</h6>
                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Contact Name</label>
                                        <div class="fw-bold text-dark f-s-15" id="view_penagihan_nama_kontak">-</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Phone Number</label>
                                        <div class="fw-bold text-dark f-s-15" id="view_penagihan_telepon">-</div>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Billing Address</label>
                                        <div class="fw-bold text-dark f-s-14 lh-sm" id="view_penagihan_address">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Management & Shipping (Tetap Sama) --}}
                     <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="ph-fill ph-users-three me-2"></i> Management & Logistics</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-0">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 fw-bold text-secondary text-uppercase f-s-12">Position Role</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Full Name</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Email Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Purchasing Mgr</td>
                                                <td class="fw-bold text-dark" id="view_purchasing_manager_name">-</td>
                                                <td class="text-dark" id="view_purchasing_manager_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Finance Mgr</td>
                                                <td class="fw-bold text-dark" id="view_finance_manager_name">-</td>
                                                <td class="text-dark" id="view_finance_manager_email">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10 border-warning border-opacity-25">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom border-warning border-opacity-25">
                                        <i class="ph-fill ph-truck me-2 text-warning"></i>Shipping Destination
                                    </h6>
                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Recipient Name</label>
                                        <div class="fw-bold text-dark f-s-16" id="view_shipping_to_name">-</div>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Shipping Address</label>
                                        <div class="fw-bold text-dark f-s-14 lh-base" id="view_shipping_to_address">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Documents (Tetap Sama) --}}
                     <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="ph-fill ph-files me-2"></i> Documents</h5>
                    <div class="row g-3" id="document_grid"></div>
                    <div id="no_documents" class="text-center py-5 text-muted border border-dashed rounded bg-white" style="display:none;">
                        <i class="ph-duotone ph-folder-notch-open f-s-48 mb-3 opacity-50"></i>
                        <p class="mb-0 f-s-16">No documents uploaded for this customer.</p>
                    </div>

                    <hr class="my-4">

                    {{-- Action Form Container (Injected by JS) --}}
                    <div id="viewModalActionFormContainer" class="mt-4"></div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-white border-top py-3" id="viewModalFooter">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close Detail</button>
                    {{-- JS will append Submit button here --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview File (Standard) --}}
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title text-white fw-bold f-s-14" id="filePreviewModalLabel">
                        FILE PREVIEW
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex align-items-center justify-content-center bg-dark" style="min-height: 500px;">
                    <img id="previewImageContent" src="" class="img-fluid" style="max-height: 80vh; display: none;" alt="File Preview">
                    <iframe id="previewPdfContent" src="" style="width: 100%; height: 80vh; border: none; display: none;"></iframe>
                    <div id="previewErrorMessage" class="text-white p-5 text-center" style="display: none;">
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
            $('.select2').select2({ theme: 'bootstrap-5', minimumResultsForSearch: 10 });

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
                    // UBAH DISINI: Arahkan ke index kolom updated_at (Index 7)
                    order: [[7, 'desc']], 
                    columns: [
                        // Index 0
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center dt-no-wrap' },
                        // Index 1
                        { data: 'approver_nik', name: 'approver_nik', className: 'dt-no-wrap' },
                        // Index 2
                        { data: 'customer_name', name: 'customers.name', className: 'dt-wrap' },
                        // Index 3
                        { data: 'level', name: 'approval_logs.level', className: 'text-center dt-no-wrap' },
                        // Index 4
                        { data: 'status_approval', name: 'customers.status_approval', className: 'text-center dt-no-wrap' },
                        // Index 5
                        { data: 'route_to', name: 'customers.route_to', className: 'text-center dt-wrap' },
                        // Index 6
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center dt-no-wrap' },
                        
                        // Index 7: HIDDEN COLUMN (UPDATED_AT)
                        // Pastikan 'approval_logs.updated_at' atau 'approval_logs.created_at' terpilih di Controller
                        { 
                            data: 'updated_at', 
                            name: 'approval_logs.updated_at', 
                            visible: false, 
                            searchable: false 
                        }
                    ],
                    autoWidth: false
                });

                $('#statusFilter, #approvalStatusFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                    $('#approvalStatusFilter').val('all').trigger('change');
                });

                // --- 1. POPULATE FORM FUNCTION (UPDATED FOR NEW LAYOUT) ---
                window.populateViewForm = function(data) {
                    // Header & General Info (Tetap sama)
                    $('#view_header_name').text(data.name || 'Unknown Customer');
                    $('#view_header_code').text(data.code || 'New Customer (No Code)');
                    $('#view_status_badge').text(data.status || '-');

                    const status = data.status_approval || 'Pending';
                    let badgeClass = 'text-warning';
                    if(status === 'Approved' || status === 'Completed') badgeClass = 'text-success';
                    if(status === 'Rejected') badgeClass = 'text-danger';
                    $('#view_approval_badge').removeClass().addClass('fw-bold f-s-16 ' + badgeClass).text(status);

                    let salesName = '-';
                    if (data.sales && data.sales.user) salesName = data.sales.user.name;
                    else if (data.user) salesName = data.user.name;
                    $('#view_user_name').text(salesName);

                    $('#view_name').text(data.name);
                    $('#view_sort_name').text(data.sort_name || '-');
                    $('#view_no_pkd').text(data.no_pkd || '-');
                    $('#view_email').text(data.email || '-');

                    const fullAddr = [data.address1, data.address2, data.address3].filter(Boolean).join(', ');
                    $('#view_full_address').text(fullAddr || '-');
                    $('#view_city').text(data.city || '-');
                    $('#view_area').text(data.area || '-');
                    $('#view_postal_code').text(data.postal_code || '-');


                    // === FINANCE SECTION LOGIC (INPUT vs TEXT) ===
                    const limit = parseFloat(data.credit_limit) || 0;
                    const formattedLimit = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(limit);

                    if (data.can_adjust_finance) {
                        // RENDER INPUTS FOR FINANCE MANAGER

                        // 1. Credit Limit (Hidden Input + Display)
                        $('#container_credit_limit').html(`
                            <h3 class="mb-0 fw-bold mt-1 text-primary" id="view_credit_limit">${formattedLimit}</h3>
                            <input type="hidden" name="update_credit_limit_value" id="hidden_credit_limit" value="${limit}" form="modalResponseForm">
                            <small id="calc_badge" class="badge bg-warning text-dark mt-1" style="display:none; font-size: 0.65rem;">
                                <i class="fas fa-calculator me-1"></i> Updated
                            </small>
                        `);

                        // 2. Term of Payment (Select)
                        let topOptions = `
                            <option value="7">Net 7 Days</option>
                            <option value="14">Net 14 Days</option>
                            <option value="30">Net 30 Days</option>
                            <option value="45">Net 45 Days</option>
                            <option value="60">Net 60 Days</option>
                            <option value="CBD">Cash Before Delivery (CBD)</option>
                        `;
                        $('#container_top').html(`
                            <select class="form-select form-select-sm fw-bold text-primary border-primary"
                                    name="update_top" id="input_top" form="modalResponseForm">
                                ${topOptions}
                            </select>
                        `);
                        $('#input_top').val(data.term_of_payment || '30');

                        // 3. Lead Time (Input Number)
                        $('#container_lead_time').html(`
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control fw-bold text-primary border-primary"
                                       name="update_lead_time" id="input_lead_time"
                                       value="${data.lead_time || 0}" form="modalResponseForm">
                                <span class="input-group-text bg-primary text-white border-primary">Days</span>
                            </div>
                        `);

                        // --- CALCULATOR LOGIC ---
                        const baseAmount = parseFloat(data.base_total_amount) || 0;

                        function calculateFinanceLimit() {
                            const topStr = $('#input_top').val();
                            const lt = parseFloat($('#input_lead_time').val()) || 0;

                            let topDays = 0;
                            let divider = 30;

                            if (topStr === '7') { topDays = 7; divider = 7.5; }
                            else if (topStr === '14') { topDays = 14; divider = 15; }
                            else if (topStr === '30') { topDays = 30; divider = 30; }
                            else if (topStr === '45') { topDays = 45; divider = 45; }
                            else if (topStr === '60') { topDays = 60; divider = 60; }
                            else if (topStr === 'CBD') { topDays = 0; divider = 30; }
                            else { topDays = parseInt(topStr) || 0; divider = topDays > 0 ? topDays : 30; }

                            const result = ((topDays + lt) * baseAmount) / divider;
                            const rounded = Math.round(result);

                            $('#view_credit_limit').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(rounded));
                            $('#hidden_credit_limit').val(rounded);
                            $('#calc_badge').show();
                        }

                        // Attach Event Listeners
                        $('#input_top, #input_lead_time').on('change input', calculateFinanceLimit);

                    } else {
                        // RENDER PLAIN TEXT (STANDARD)
                        $('#container_credit_limit').html(`<h3 class="mb-0 fw-bold mt-1" id="view_credit_limit">${formattedLimit}</h3>`);
                        $('#container_top').html(`<span class="fw-bold f-s-16 bg-warning bg-opacity-20 px-2 py-1 rounded">${data.term_of_payment || '-'}</span>`);
                        $('#container_lead_time').html(`<span class="fw-bold f-s-14">${data.lead_time || 0} Days</span>`);
                    }

                    // --- Sisa Data (Tax, Billing, dll) tetap sama ---
                    $('#view_npwp').text(data.npwp || '-');
                    $('#view_tanggal_npwp').text(data.tanggal_npwp || '-');
                    $('#view_nppkp').text(data.nppkp || '-');
                    $('#view_output_tax').text(data.output_tax || '-');

                    $('#view_penagihan_nama_kontak').text(data.penagihan_nama_kontak || '-');
                    $('#view_penagihan_telepon').text(data.penagihan_telepon || '-');
                    $('#view_penagihan_address').text(data.penagihan_address || '-');

                    $('#view_purchasing_manager_name').text(data.purchasing_manager_name || '-');
                    $('#view_purchasing_manager_email').text(data.purchasing_manager_email || '-');
                    $('#view_finance_manager_name').text(data.finance_manager_name || '-');
                    $('#view_finance_manager_email').text(data.finance_manager_email || '-');

                    $('#view_shipping_to_name').text(data.shipping_to_name || '-');
                    $('#view_shipping_to_address').text(data.shipping_to_address || '-');

                    // Files (Grid Generation - Sama)
                    const gridContainer = $('#document_grid');
                    gridContainer.empty();
                    $('#no_documents').hide();

                    let fileCount = 0;
                    const storageBase = "{{ asset('storage') }}";

                    function appendFileCard(label, filename) {
                        if(!filename) return;
                        fileCount++;
                        const cleanFileName = filename.startsWith('/') ? filename.substring(1) : filename;
                        const fullUrl = `${storageBase}/${cleanFileName}`;
                        const ext = cleanFileName.split('.').pop().toLowerCase();
                        let icon = 'ph-file-text';
                        if(['jpg','jpeg','png'].includes(ext)) icon = 'ph-image';
                        if(ext === 'pdf') icon = 'ph-file-pdf';

                        const html = `
                            <div class="col-md-3">
                                <div class="card h-100 border shadow-sm btn-preview-file cursor-pointer"
                                     data-url="${fullUrl}" data-filename="${cleanFileName}" data-title="${label}" data-customer-name="${data.name}"
                                     style="transition: all 0.2s;" onmouseover="this.style.borderColor='#0d6efd'" onmouseout="this.style.borderColor='#dee2e6'">
                                    <div class="card-body p-3 text-center">
                                        <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                            <i class="ph-duotone ${icon} f-s-24 text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark f-s-13 mb-1">${label}</h6>
                                        <span class="text-muted f-s-11 text-truncate d-block">Click to view</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        gridContainer.append(html);
                    }

                    if(data.files && data.files.length > 0) {
                        const f = data.files[0];
                        appendFileCard('NPWP Document', f.npwp_file);
                        appendFileCard('NIB/SIUP Document', f.nib_siup_file);
                        appendFileCard('KTP Document', f.ktp_file);
                    } else {
                        appendFileCard('NPWP Document', data.file_npwp);
                        appendFileCard('NIB/SIUP Document', data.file_nib);
                        appendFileCard('KTP Document', data.file_ktp);
                    }

                    if(fileCount === 0) $('#no_documents').show();
                };

                // --- 2. FILE PREVIEW HANDLER (Unchanged logic, just ID references) ---
                $(document).on('click', '.btn-preview-file', function() {
                    const url = $(this).data('url');
                    const filename = $(this).data('filename');
                    const title = $(this).data('title');
                    const customerName = $(this).data('customer-name');

                    $('#previewImageContent').hide();
                    $('#previewPdfContent').hide();
                    $('#previewErrorMessage').hide();

                    const headerTitle = `<i class="ph-bold ph-image me-2"></i> ${title} <span class="text-white-50 mx-2">|</span> <span class="fw-light">${customerName}</span>`;
                    $('#filePreviewModalLabel').html(headerTitle);

                    if (!url) return;
                    const extension = filename.split('.').pop().toLowerCase();

                    if (['jpg', 'jpeg', 'png', 'bmp', 'webp'].includes(extension)) {
                        $('#previewImageContent').attr('src', url).show();
                    } else if (extension === 'pdf') {
                        $('#previewPdfContent').attr('src', url).show();
                    } else {
                        $('#downloadFallbackLink').attr('href', url);
                        $('#previewErrorMessage').show();
                    }

                    const fileModal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
                    fileModal.show();
                });

                $(document).on('click', '.action-btn-modal, .action-btn', function() {
                    const button = $(this);

                    // Kita treat .action-btn (tombol Quick Approve lama) sama seperti modal btn
                    const customerId = button.data('id');
                    const token = button.data('token');

                    // Baca action. Jika tombol Quick Approve (yg lama), kita paksa action = 'approve'
                    let action = button.data('action');
                    if(button.hasClass('action-btn')) {
                        action = 'approve';
                    }

                    const customerName = button.data('name');
                    const btnTitle = button.attr('title') || '';
                    const isITInput = btnTitle.includes('Input Customer Code');

                    const originalIcon = button.html();
                    button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                    $.ajax({
                        url: `/customers/${customerId}`,
                        type: 'GET',
                        success: function(response) {
                            populateViewForm(response); // Ini akan merender input finance jika permission ada

                            const isReject = action === 'reject';
                            const isApprove = action === 'approve';

                            // Styles
                            const cardClass = isReject ? 'bg-danger bg-opacity-10 border-danger' : 'bg-success bg-opacity-10 border-success';
                            const titleClass = isReject ? 'text-danger' : 'text-success';
                            const btnClass = isReject ? 'btn-danger' : 'btn-success';
                            const iconClass = isReject ? 'ph-x-circle' : 'ph-check-circle';

                            let btnText = isReject ? 'Submit Reject' : 'Submit Approval';
                            if (isITInput) btnText = 'Save & Activate';
                            if (isApprove) btnText = 'Confirm Approve'; // Teks khusus quick approve

                            let notesLabel = isReject ? 'Rejection Reason' : 'Review Notes';
                            if (isITInput) notesLabel = 'Notes';

                            // Notes visibility logic: Jika Approve, sembunyikan notes
                            const notesDisplay = isApprove ? 'display:none;' : '';
                            const notesRequired = isReject ? 'required' : '';

                            let additionalInputs = '';
                            if (isITInput) {
                                // (Logic IT Inputs sama)
                                let today = new Date().toISOString().split('T')[0];
                                let joinVal = response.join_date ? response.join_date.split(' ')[0] : today;
                                let codeVal = response.code || '';
                                additionalInputs = `
                                    <div class="alert alert-light border border-info border-start-4 shadow-sm py-2 small mb-3">
                                        <div class="d-flex align-items-center text-info"><i class="ph-bold ph-info me-2 f-s-18"></i><span class="fw-bold">IT Approval:</span> Complete data to activate.</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6"><label class="form-label fw-bold small">Code <span class="text-danger">*</span></label><input type="text" class="form-control fw-bold" name="update_code" value="${codeVal}" placeholder="e.g. ID-001" required></div>
                                        <div class="col-md-6"><label class="form-label fw-bold small">Join Date <span class="text-danger">*</span></label><input type="date" class="form-control" name="update_join_date" value="${joinVal}" required></div>
                                    </div>`;
                            }

                            // Build Form HTML
                            const actionFormHtml = `
                                <div class="card ${cardClass} shadow-sm">
                                    <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                        <h6 class="mb-0 fw-bold ${titleClass}"><i class="ph-bold ${isReject ? 'ph-gavel' : 'ph-seal-check'} me-2"></i>Decision: ${isITInput ? 'ACTIVATE' : action.toUpperCase()}</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <form id="modalResponseForm" action="{{ route('customers.approval_action', ':id') }}".replace(':id', customerId) method="POST">
                                            @csrf
                                            <input type="hidden" name="token" value="${token}">
                                            <input type="hidden" name="action" value="${action}">

                                            ${additionalInputs}

                                            <div class="mb-2" style="${notesDisplay}">
                                                <label for="modal_notes" class="form-label fw-bold small text-dark">${notesLabel} ${isReject ? '<span class="text-danger">*</span>' : ''}</label>
                                                <textarea class="form-control bg-white" id="modal_notes" name="notes" rows="3" placeholder="Type your notes here..." ${notesRequired}></textarea>
                                            </div>

                                            ${isApprove ? '<p class="mb-0 text-success small fw-bold"><i class="ph-bold ph-info me-1"></i> You are approving this customer without notes.</p>' : ''}
                                        </form>
                                    </div>
                                </div>
                            `;

                            const submitUrl = "{{ route('customers.approval_action', ':id') }}".replace(':id', customerId);
                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalActionFormContainer form').attr('action', submitUrl);

                            const submitBtnHtml = `
                                <button type="submit" form="modalResponseForm" class="btn ${btnClass} px-4 rounded-pill fw-bold shadow-sm">
                                    <i class="ph-bold ${iconClass} me-2"></i> ${btnText}
                                </button>
                            `;
                            $('#viewModalFooter button[type="submit"]').remove();
                            $('#viewModalFooter').prepend(submitBtnHtml);

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

                // --- 4. SUBMIT HANDLER (UPDATED FOR QUICK APPROVE CONFIRMATION) ---
                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const notesValue = $('#modal_notes').val().trim();
                    const action = form.find('input[name="action"]').val();
                    const isReject = action === 'reject';
                    const isApprove = action === 'approve';

                    // Validasi Notes (Hanya jika tidak Approve)
                    if (!isApprove && (!notesValue || !/[a-zA-Z]/.test(notesValue))) {
                         Swal.fire({ icon: 'warning', title: 'Invalid Notes', text: 'Please provide valid text notes.', target: document.getElementById('viewModal') });
                         return;
                    }

                    // Tentukan Pesan Konfirmasi
                    let title = 'Confirm Action?';
                    let text = 'Proceed with this decision?';
                    let confirmColor = '#3085d6';
                    let icon = 'question';

                    if (isReject) {
                        title = 'Confirm Rejection?';
                        text = "This request will be rejected.";
                        icon = 'warning';
                        confirmColor = '#d33';
                    } else if (isApprove) {
                        // Quick Approve Confirmation
                        title = 'Approve without Notes?';
                        text = "Are you sure you want to approve this customer immediately?";
                        confirmColor = '#28a745';
                    } else {
                        // Review
                         title = 'Submit Review?';
                         text = "Submit approval with notes/changes?";
                    }

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: confirmColor,
                        confirmButtonText: 'Yes, Submit!',
                        target: document.getElementById('viewModal')
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#loading-overlay').css('display', 'flex');
                            $('#viewModal').modal('hide');

                            // Pastikan input hidden credit limit terupdate sebelum submit jika ada
                            if($('#hidden_credit_limit').length){
                                // Value sudah diupdate oleh event listener 'change'
                            }

                            $.ajax({
                                url: form.attr('action'), method: 'POST', data: form.serialize(),
                                success: function(res) {
                                    $('#loading-overlay').hide();
                                    Swal.fire('Success!', res.message, 'success');
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    $('#loading-overlay').hide();
                                    Swal.fire('Error!', xhr.responseJSON?.message || 'Error processing request.', 'error');
                                }
                            });
                        }
                    });
                });

                // Clear injected form when modal closes
                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove();
                });

                // Resend Email (Logic Unchanged)
                 $(document).on('click', '.btn-resend-email', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const approverName = button.data('approver-name') || 'User';

                    Swal.fire({
                        title: 'Resend Email?', html: `Resend approval email to <b>${approverName}</b>?`, icon: 'question',
                        showCancelButton: true, confirmButtonColor: '#ffc107', confirmButtonText: 'Yes, Resend!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const original = button.html();
                            button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);
                            $.ajax({
                                url: `/approvals/resend/${token}`, method: 'POST', data: { _token: '{{ csrf_token() }}' },
                                success: function(res) { Swal.fire('Sent!', res.message, 'success'); },
                                error: function(xhr) { Swal.fire('Error!', xhr.responseJSON?.message || 'Failed.', 'error'); },
                                complete: function() { button.html(original).prop('disabled', false); }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
