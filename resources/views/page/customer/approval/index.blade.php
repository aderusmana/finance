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

    {{-- Stats and Header Section --}}
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Approvals Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="/"><i class="ph-duotone ph-address-book f-s-16"></i> Home</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Approvals List</a></li>
            </ul>
        </div>
    </div>

    {{-- Filters and Table Section --}}
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
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="table-title mb-1"><i class="ph-duotone ph-users-three me-2"></i> Approval Queue</h4>
                        <small class="text-white opacity-75 f-s-12">Review customer requests, check credit limits, and approve or reject.</small>
                    </div>
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">
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

    {{-- MODAL VIEW DETAIL --}}
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

                    {{-- Status Banner --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-5">
                                    <div>
                                        <label class="fw-bold text-muted text-uppercase f-s-11 mb-1">Account Status</label>
                                        <div><span id="view_status_badge" class="badge bg-secondary f-s-12 px-3 py-2">-</span></div>
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

                    {{-- General Info --}}
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
                                            <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">PIC (Penanggung Jawab)</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_pic">-</div>
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

                    {{-- Financial & Tax --}}
                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="ph-fill ph-currency-dollar me-2"></i> Financial & Tax</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <label class="text-white text-opacity-75 text-uppercase f-s-12 fw-bold">Credit Limit</label>
                                            <div id="container_credit_limit">
                                                <h3 class="mb-0 fw-bold mt-1" id="view_credit_limit">IDR 0</h3>
                                            </div>
                                        </div>
                                        <i class="ph-duotone ph-wallet f-s-40 text-white text-opacity-50"></i>
                                    </div>
                                    <div class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                                        <span class="f-s-13 opacity-75">Term of Payment</span>
                                        <div id="container_top">
                                            <span class="fw-bold f-s-16 bg-warning bg-opacity-20 px-2 py-1 rounded" id="view_term_of_payment">-</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <span class="f-s-13 opacity-75">Lead Time</span>
                                        <div id="container_lead_time">
                                            <span class="fw-bold f-s-14"><span id="view_lead_time">0</span> Days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Tax & NPWP (TARGET UTAMA UNTUK DITIMPA JIKA FINANCE) --}}
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">Tax Information</h6>
                                    <div class="mb-3">
                                        <span class="fw-bold text-secondary f-s-13 d-block mb-1">NPWP No.</span>
                                        <div id="container_npwp">
                                            <span class="fw-bold text-dark" id="view_npwp">-</span>
                                        </div>
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

                        {{-- 3. Billing --}}
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

                    {{-- NEW: Container Khusus Schedule (Hanya Muncul Jika Finance) --}}
                    <div id="finance_schedule_container" style="display: none;">
                    </div>

                    {{-- Management & Logistics --}}
                     <h5 class="fw-bold text-primary mb-3 d-flex align-items-center mt-4"><i class="ph-fill ph-users-three me-2"></i> Management & Logistics</h5>
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
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Phone Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Purchasing Mgr</td>
                                                <td class="fw-bold text-dark" id="view_purchasing_manager_name">-</td>
                                                <td class="text-dark" id="view_purchasing_manager_email">-</td>
                                                <td class="text-dark" id="view_purchasing_manager_phone">-</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Finance Mgr</td>
                                                <td class="fw-bold text-dark" id="view_finance_manager_name">-</td>
                                                <td class="text-dark" id="view_finance_manager_email">-</td>
                                                <td class="text-dark" id="view_finance_manager_phone">-</td>
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

                    {{-- Documents --}}
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

    {{-- Modal Preview File --}}
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title text-white fw-bold f-s-14" id="filePreviewModalLabel">FILE PREVIEW</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0 d-flex align-items-center justify-content-center bg-dark" style="min-height: 400px;">

                    <img id="previewImageContent" src="" class="img-fluid"
                        style="max-height: 60vh; max-width: 100%; display: none;" alt="File Preview">

                    <iframe id="previewPdfContent" src=""
                            style="width: 100%; height: 60vh; border: none; display: none;"></iframe>

                    <div id="previewErrorMessage" class="text-white p-5 text-center" style="display: none;">
                        <i class="ph-bold ph-file-x f-s-30 mb-2"></i><br>
                        File type not supported for preview.<br>
                        <a href="#" id="downloadFallbackLink" target="_blank" class="btn btn-sm btn-primary mt-2">Download File</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL KHUSUS: VERIFY & EDIT NPWP --}}
    <div class="modal fade" id="modalVerifyNpwpSystem" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content" style="min-height: auto;">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title fw-bold"><i class="ph-bold ph-scan me-2"></i> Verify NPWP Data</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row g-0">
                            <div class="col-lg-7 d-flex align-items-center justify-content-center bg-secondary bg-opacity-25 border-end"
                                style="background-color: #525252; min-height: 400px; max-height: 500px;">
                                <div id="npwp_preview_container" class="w-100 h-100 d-flex align-items-center justify-content-center p-2">
                                </div>
                            </div>

                            <div class="col-lg-5 p-3 bg-white d-flex flex-column justify-content-center">
                                <h6 class="fw-bold text-primary mb-2">Verify NPWP Data</h6>

                                <div class="alert alert-info small mb-3 border-info p-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ph-fill ph-info f-s-16 me-2"></i>
                                        <div style="line-height: 1.2;">Match the left document with the input.</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-muted small text-uppercase mb-1">No NPWP (System)</label>
                                    <input type="text" id="input_npwp_verification" class="form-control fw-bold text-dark border-primary"
                                        placeholder="No NPWP">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success shadow-sm" id="btn_save_npwp_verification">
                                        <i class="ph-bold ph-check me-2">s</i> Save
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $('.select2').select2({ theme: 'bootstrap-5', minimumResultsForSearch: 10 });

            // --- GLOBAL FUNCTION: Toggle Schedule ---
            window.toggleSchedule = function(btn, type) {
                const button = $(btn);
                const container = $('#' + type + '_container_modal');
                const inputContainer = $('#' + type + '_inputs_modal');
                const value = button.data('val');
                const isAll = value === 'All';

                const colorClass = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                const dateColor = 'btn-info';
                const isDateBtn = button.hasClass('btn-date-schedule');

                if (isAll) {
                    const isActive = button.hasClass('active');
                    if (!isActive) {
                        button.addClass('active btn-dark').removeClass('btn-outline-dark');
                        container.find('button:not([data-val="All"])').each(function() {
                            const childIsDate = $(this).hasClass('btn-date-schedule');
                            $(this).addClass('active text-white').removeClass('btn-outline-secondary btn-outline-primary btn-outline-success');
                            if (childIsDate) $(this).addClass(dateColor); else $(this).addClass(colorClass);
                        });
                    } else {
                        button.removeClass('active btn-dark').addClass('btn-outline-dark');
                        container.find('button').each(function() {
                            const childIsDate = $(this).hasClass('btn-date-schedule');
                            $(this).removeClass('active text-white ' + colorClass + ' ' + dateColor);
                            if (childIsDate) $(this).addClass('btn-outline-secondary');
                            else if ($(this).data('val') !== 'All') {
                                $(this).addClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                            }
                        });
                    }
                } else {
                    const allBtn = container.find('button[data-val="All"]');
                    allBtn.removeClass('active btn-dark').addClass('btn-outline-dark');
                    button.toggleClass('active');
                    if (button.hasClass('active')) {
                        button.addClass('text-white');
                        button.removeClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                        if (isDateBtn) button.addClass(dateColor).removeClass('btn-outline-secondary');
                        else button.addClass(colorClass);
                    } else {
                        button.removeClass('text-white ' + colorClass + ' ' + dateColor);
                        if (isDateBtn) button.addClass('btn-outline-secondary');
                        else button.addClass(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                    }
                }

                inputContainer.empty();
                if (container.find('button[data-val="All"]').hasClass('active')) {
                    inputContainer.append(`<input type="hidden" name="update_${type}[]" value="All" form="modalResponseForm">`);
                } else {
                    container.find('button.active:not([data-val="All"])').each(function() {
                        inputContainer.append(`<input type="hidden" name="update_${type}[]" value="${$(this).data('val')}" form="modalResponseForm">`);
                    });
                }
            };

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
                    order: [[7, 'desc']],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center dt-no-wrap' },
                        { data: 'approver_nik', name: 'approver_nik', className: 'dt-no-wrap' },
                        { data: 'customer_name', name: 'customers.name', className: 'dt-wrap' },
                        { data: 'level', name: 'approval_logs.level', className: 'text-center dt-no-wrap' },
                        { data: 'status_approval', name: 'customers.status_approval', className: 'text-center dt-no-wrap' },
                        { data: 'route_to', name: 'customers.route_to', className: 'text-center dt-wrap' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center dt-no-wrap' },
                        { data: 'updated_at', name: 'approval_logs.updated_at', visible: false, searchable: false }
                    ],
                    autoWidth: false
                });

                $('#statusFilter, #approvalStatusFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                    $('#approvalStatusFilter').val('all').trigger('change');
                });

                // Resend approval email handler
                $(document).on('click', '.btn-resend-email', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const approverName = button.data('approver-name') || 'Approver';

                    Swal.fire({
                        title: 'Resend Approval Email?',
                        text: `Resend approval link to ${approverName}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Resend',
                        confirmButtonColor: '#3085d6'
                    }).then((result) => {
                        if (!result.isConfirmed) return;

                        const originalHtml = button.html();
                        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

                        const url = "{{ route('approvals.resend', ':token') }}".replace(':token', token);
                        const csrf = $('meta[name="csrf-token"]').attr('content');

                        $.ajax({
                            url: url,
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf },
                            data: {},
                            success: function(res) {
                                Swal.fire('Sent', res.message || 'Approval email resent.', 'success');
                                if (typeof table !== 'undefined' && table.ajax) table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                const msg = xhr.responseJSON?.message || 'Failed to resend approval email.';
                                Swal.fire('Error', msg, 'error');
                            },
                            complete: function() {
                                button.prop('disabled', false).html(originalHtml);
                            }
                        });
                    });
                });

                window.populateViewForm = function(data) {
                    console.log('Populate View Form Data:', data);

                    // --- Basic Info ---
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
                    $('#view_pic').text(data.pic || '-');
                    $('#view_email').text(data.email || '-');
                    const fullAddr = [data.address1, data.address2, data.address3].filter(Boolean).join(', ');
                    $('#view_full_address').text(fullAddr || '-');
                    $('#view_city').text(data.city || '-');
                    $('#view_area').text(data.area || '-');
                    $('#view_postal_code').text(data.postal_code || '-');

                    // --- FINANCE SECTION ---
                    const limit = parseFloat(data.credit_limit) || 0;
                    const formattedLimit = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(limit);

                    // RESET CONTAINER DULU AGAR BERSIH
                    $('#container_top, #container_lead_time, #container_credit_limit, #container_npwp, #finance_schedule_container').empty();
                    $('#finance_schedule_container').hide();
                    $('#calc_badge').hide();

                    if (data.can_adjust_finance) {
                        $('#container_credit_limit').html(`
                            <h3 class="mb-0 fw-bold mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>
                            <input type="hidden" name="update_credit_limit_value" id="hidden_credit_limit" value="${limit}" form="modalResponseForm">
                            <small id="calc_badge" class="badge bg-warning text-dark mt-1" style="display:none; font-size: 0.65rem;">
                                <i class="fas fa-calculator me-1"></i> Auto-Calculated
                            </small>
                        `);

                        let currentTop = data.term_of_payment || '30'; // Ambil data dari DB

                        let topOptions = `
                            <option value="7">Net 7 Days</option>
                            <option value="14">Net 14 Days</option>
                            <option value="30">Net 30 Days</option>
                            <option value="45">Net 45 Days</option>
                            <option value="CBD">Cash Before Delivery (CBD)</option>
                        `;

                        // Pastikan attribute data-original="${currentTop}" ADA DISINI
                        $('#container_top').html(`
                            <select class="form-select form-select-sm fw-bold text-primary border-primary"
                                    name="update_top"
                                    id="input_top"
                                    data-original="${currentTop}"
                                    form="modalResponseForm">
                                ${topOptions}
                            </select>
                        `);
                        $('#input_top').val(currentTop);

                        let leadTimeValue = (data.lead_time && data.lead_time != 0) ? data.lead_time : '';
                        $('#container_lead_time').html(`
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control fw-bold text-primary border-primary"
                                    name="update_lead_time" id="input_lead_time"
                                    value="${leadTimeValue}"
                                    placeholder="0"
                                    form="modalResponseForm">
                                <span class="input-group-text bg-primary text-white border-primary">Days</span>
                            </div>
                        `);

                        $('#container_npwp').html(`
                            <input type="text" class="form-control form-control-sm fw-bold border-primary text-dark"
                                name="update_npwp" value="${data.npwp || ''}" form="modalResponseForm" placeholder="Edit NPWP">
                        `);

                        let npwpUrl = null;
                            if (data.files && data.files.length > 0) {
                                const file = data.files.find(f => f.npwp_file);
                                if (file && file.npwp_file) {
                                    npwpUrl = "{{ asset('storage') }}/" + file.npwp_file;
                                }
                            }

                            if (!npwpUrl && data.file_npwp_path) npwpUrl = data.file_npwp_path;

                            $('#container_npwp').html(`
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm fw-bold border-primary text-dark"
                                        id="display_npwp_main" value="${data.npwp || ''}" readonly>

                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="openNpwpVerificationModal('${npwpUrl}', '${data.npwp || ''}')">
                                        <i class="ph-bold ph-pencil-simple me-1"></i> Verify
                                    </button>
                                    <input type="hidden" name="update_npwp" id="real_update_npwp"
                                        value="${data.npwp || ''}" form="modalResponseForm">
                                </div>
                            `);

                        const genBtn = (type, val, label, isDate = false) => {
                            const activeArr = data[type] || [];
                            let activeClass = '';

                            const color = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                            const dateColor = 'btn-info';

                            let isActive = false;
                            if (activeArr.includes('All')) {
                                isActive = true;
                            } else if (activeArr.includes(String(val))) {
                                isActive = true;
                            }

                            if (isActive) {
                                if(val === 'All') activeClass = 'active btn-dark';
                                else activeClass = `active text-white ${isDate ? dateColor : color}`;
                            } else {
                                if(val === 'All') activeClass = 'btn-outline-dark';
                                else activeClass = isDate ? 'btn-outline-secondary' : (type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                            }

                            let style = 'font-size: 0.75rem !important; font-weight: 600;';
                            if(isDate) style += 'width: 32px !important; height: 32px !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; line-height: 1 !important;';
                            else style += 'padding: 4px 12px !important;';

                            const identifierClass = isDate ? 'btn-date-schedule' : 'btn-day-schedule';

                            return `<button type="button" class="btn btn-sm ${activeClass} mb-1 me-1 ${identifierClass}"
                                    style="${style}" data-val="${val}" onclick="toggleSchedule(this, '${type}')">${label}</button>`;
                        };

                        let payDays = genBtn('payment_days', 'All', 'All Days');
                        ['Senin','Selasa','Rabu','Kamis','Jumat'].forEach(d => payDays += genBtn('payment_days', d, d));

                        let payDates = genBtn('payment_date', 'All', 'All Dates');
                        payDates += '<div class="d-flex flex-wrap gap-1 mt-2">';
                        for(let i=1; i<=31; i++) payDates += genBtn('payment_date', i, i, true);
                        payDates += '</div>';

                        let fakDays = genBtn('faktur_days', 'All', 'All Days');
                        ['Senin','Selasa','Rabu','Kamis','Jumat'].forEach(d => fakDays += genBtn('faktur_days', d, d));

                        let fakDates = genBtn('faktur_date', 'All', 'All Dates');
                        fakDates += '<div class="d-flex flex-wrap gap-1 mt-2">';
                        for(let i=1; i<=31; i++) fakDates += genBtn('faktur_date', i, i, true);
                        fakDates += '</div>';

                        $('#finance_schedule_container').html(`
                            <div class="card border border-primary border-opacity-25 shadow-sm mt-3">
                                <div class="card-header bg-primary bg-opacity-10 py-2">
                                    <i class="ph-bold ph-calendar-check me-2 text-primary"></i>
                                    <span class="fw-bold text-primary">Payment & Faktur Schedule (Finance Adjustment)</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted text-uppercase mb-1">Virtual Account</label>
                                        <input type="text" class="form-control form-control-sm border-primary fw-bold"
                                            name="update_va" value="${data.virtual_account || ''}"
                                            placeholder="Virtual Account Number" form="modalResponseForm">
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6 border-end">
                                            <h6 class="text-primary fw-bold small mb-2">Payment Schedule</h6>
                                            <div class="mb-3">
                                                <label class="small text-muted d-block mb-1">Payment Days</label>
                                                <div id="payment_days_container_modal">${payDays}</div>
                                                <div id="payment_days_inputs_modal"></div> </div>
                                            <div>
                                                <label class="small text-muted d-block mb-1">Payment Dates</label>
                                                <div id="payment_date_container_modal">${payDates}</div>
                                                <div id="payment_date_inputs_modal"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-success fw-bold small mb-2">Faktur Schedule</h6>
                                            <div class="mb-3">
                                                <label class="small text-muted d-block mb-1">Faktur Days</label>
                                                <div id="faktur_days_container_modal">${fakDays}</div>
                                                <div id="faktur_days_inputs_modal"></div>
                                            </div>
                                            <div>
                                                <label class="small text-muted d-block mb-1">Faktur Dates</label>
                                                <div id="faktur_date_container_modal">${fakDates}</div>
                                                <div id="faktur_date_inputs_modal"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        $('#finance_schedule_container').show();

                        ['payment_days', 'payment_date', 'faktur_days', 'faktur_date'].forEach(type => {
                            const arr = data[type] || [];
                            const inputDiv = $('#' + type + '_inputs_modal');
                            inputDiv.empty();
                            if(arr.includes('All')) {
                                inputDiv.append(`<input type="hidden" name="update_${type}[]" value="All" form="modalResponseForm">`);
                            } else {
                                arr.forEach(val => {
                                    inputDiv.append(`<input type="hidden" name="update_${type}[]" value="${val}" form="modalResponseForm">`);
                                });
                            }
                        });

                        const baseAmount = parseFloat(data.base_total_amount) || 0;

                        function calculateFinanceLimit() {
                            const topStr = $('#input_top').val();
                            const lt = parseFloat($('#input_lead_time').val()) || 0;
                            let topDays = 0, divider = 30;

                            if (topStr === 'CBD') {
                                topDays = 0;
                                divider = 30;
                            } else {
                                topDays = parseInt(topStr) || 0;
                                divider = topDays > 0 ? topDays : 30;
                            }

                            if (topStr === '7') divider = 7.5;
                            if (topStr === '14') divider = 15;

                            let result = ((topDays + lt) * baseAmount) / divider;

                            if (topStr === 'CBD') result = 0;

                            const rounded = Math.round(result);

                            $('#view_credit_limit').text(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(rounded));
                            $('#hidden_credit_limit').val(rounded);
                            $('#calc_badge').show();
                        }

                        $('#input_top, #input_lead_time').on('change input', calculateFinanceLimit);

                    } else {
                        $('#container_credit_limit').html(`<h3 class="mb-0 fw-bold mt-1 text-white" id="view_credit_limit">${formattedLimit}</h3>`);
                        $('#container_top').html(`<span class="fw-bold f-s-16 bg-warning bg-opacity-20 px-2 py-1 rounded">${data.term_of_payment || '-'}</span>`);
                        $('#container_lead_time').html(`<span class="fw-bold f-s-14">${data.lead_time || 0} Days</span>`);
                        $('#container_npwp').html(`<span class="fw-bold text-dark" id="view_npwp">${data.npwp || '-'}</span>`);
                        $('#finance_schedule_container').hide();
                    }

                    $('#view_tanggal_npwp').text(data.tanggal_npwp || '-');
                    $('#view_nppkp').text(data.nppkp || '-');
                    $('#view_output_tax').text(data.output_tax || '-');
                    $('#view_penagihan_nama_kontak').text(data.penagihan_nama_kontak || '-');
                    $('#view_penagihan_telepon').text(data.penagihan_telepon || '-');
                    $('#view_penagihan_address').text(data.penagihan_address || '-');
                    $('#view_purchasing_manager_name').text(data.purchasing_manager_name || '-');
                    $('#view_purchasing_manager_email').text(data.purchasing_manager_email || '-');
                    $('#view_purchasing_manager_phone').text(data.purchasing_manager_phone || '-');
                    $('#view_finance_manager_name').text(data.finance_manager_name || '-');
                    $('#view_finance_manager_email').text(data.finance_manager_email || '-');
                    $('#view_finance_manager_phone').text(data.finance_manager_phone || '-');
                    $('#view_shipping_to_name').text(data.shipping_to_name || '-');
                    $('#view_shipping_to_address').text(data.shipping_to_address || '-');

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
                        appendFileCard('Akte Pendirian', f.akte_file);
                        appendFileCard('Company Profile', f.company_profile_file);
                    } else {
                        appendFileCard('NPWP Document', data.file_npwp);
                        appendFileCard('NIB/SIUP Document', data.file_nib);
                        appendFileCard('KTP Document', data.file_ktp);
                        appendFileCard('Akte Pendirian', data.file_akte);
                        appendFileCard('Company Profile', data.file_company_profile);
                    }
                    if(fileCount === 0) $('#no_documents').show();
                };

                window.openNpwpVerificationModal = function(fileUrl, currentNpwp) {
                    $('#input_npwp_verification').val(currentNpwp);

                    const container = $('#npwp_preview_container');
                    container.empty();

                    if (fileUrl) {
                        const ext = fileUrl.split('.').pop().toLowerCase();

                        if (['jpg', 'jpeg', 'png', 'webp', 'bmp'].includes(ext)) {
                            container.html(`<img src="${fileUrl}" class="img-fluid shadow-sm" style="max-height: 400px; max-width: 100%;">`);
                        } else if (ext === 'pdf') {
                            container.html(`<iframe src="${fileUrl}" width="100%" height="100%" style="min-height: 400px; border:none;" class="shadow-sm rounded"></iframe>`);
                        } else {
                            container.html(`
                                <div class="text-center text-white">
                                    <i class="ph-duotone ph-file-x f-s-48 mb-3 opacity-50"></i>
                                    <p>Preview tidak tersedia untuk format file ini.</p>
                                    <a href="${fileUrl}" target="_blank" class="btn btn-primary btn-sm mt-2">Download File</a>
                                </div>
                            `);
                        }
                    } else {
                        container.html(`
                            <div class="text-center text-white-50">
                                <i class="ph-duotone ph-file-dashed f-s-64 mb-3"></i>
                                <p class="h6">No NPWP file uploaded.</p>
                            </div>
                        `);
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalVerifyNpwpSystem'));
                    modal.show();
                };

                $(document).on('click', '#btn_save_npwp_verification', function() {
                    const newVal = $('#input_npwp_verification').val();
                    $('#display_npwp_main').val(newVal);
                    $('#real_update_npwp').val(newVal);

                    const modalEl = document.getElementById('modalVerifyNpwpSystem');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    $('#display_npwp_main').addClass('bg-warning bg-opacity-10').focus();
                    setTimeout(() => $('#display_npwp_main').removeClass('bg-warning bg-opacity-10'), 1000);
                });

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

                $(document).on('click', '.action-btn-modal', function() {
                    const button = $(this);
                    const customerId = button.data('id');
                    const token = button.data('token');

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

                            let actionFormHtml = '';
                            let submitBtnHtml = '';

                            if (isITInput) {
                                let today = new Date().toISOString().split('T')[0];
                                let joinVal = response.join_date ? response.join_date.split(' ')[0] : today;
                                let codeVal = response.code || '';

                                actionFormHtml = `
                                    <div class="card shadow-sm border-info border-opacity-25 mt-3">
                                        <div class="card-header bg-info bg-opacity-10 pt-3 pb-2 border-0">
                                            <h6 class="mb-0 fw-bold text-info"><i class="ph-bold ph-pencil-simple me-2"></i>IT ACTIVATION: SET CUSTOMER CODE</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form id="modalResponseForm" action="{{ route('customers.approval_action', ':id') }}".replace(':id', customerId) method="POST">
                                                @csrf
                                                <input type="hidden" name="token" value="${token}">
                                                <input type="hidden" name="action" id="final_action" value="review"> <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">Customer Code <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control fw-bold border-primary" id="it_update_code" name="update_code" value="${codeVal}" placeholder="e.g. CUST-001" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">Join Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="update_join_date" value="${joinVal}" required>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label for="modal_notes" class="form-label fw-bold small text-dark">Notes (Optional)</label>
                                                    <textarea class="form-control bg-light" id="modal_notes" name="notes" rows="2" placeholder="Enter notes here..."></textarea>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;

                                submitBtnHtml = `
                                    <button type="submit" form="modalResponseForm" id="final_submit_btn" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
                                        <i class="ph-bold ph-check-circle me-2"></i> Save & Activate
                                    </button>
                                `;

                            } else {
                                actionFormHtml = `
                                    <div class="card shadow-sm border-primary border-opacity-25 mt-3">
                                        <div class="card-header bg-primary bg-opacity-10 pt-3 pb-2 border-0">
                                            <h6 class="mb-0 fw-bold text-primary"><i class="ph-bold ph-gavel me-2"></i>Decision: APPROVAL REVIEW</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <form id="modalResponseForm" action="{{ route('customers.approval_action', ':id') }}".replace(':id', customerId) method="POST">
                                                @csrf
                                                <input type="hidden" name="token" value="${token}">
                                                <input type="hidden" name="action" id="final_action" value="">

                                                <label class="form-label fw-bold small text-dark mb-2">Select your decision <span class="text-danger">*</span></label>
                                                <div class="d-flex flex-column flex-md-row gap-3 mb-4">
                                                    <div class="card decision-btn flex-fill cursor-pointer shadow-sm border" data-select-action="approve" style="cursor: pointer; transition: all 0.2s;">
                                                        <div class="card-body p-3 d-flex align-items-center">
                                                            <div class="bg-success text-white rounded p-2 me-3 d-flex align-items-center justify-content-center shadow-sm">
                                                                <i class="ph-bold ph-check-circle f-s-20"></i>
                                                            </div>
                                                            <div><h6 class="mb-0 fw-bold text-dark">Approve Without Notes</h6></div>
                                                        </div>
                                                    </div>
                                                    <div class="card decision-btn flex-fill cursor-pointer shadow-sm border" data-select-action="review" style="cursor: pointer; transition: all 0.2s;">
                                                        <div class="card-body p-3 d-flex align-items-center">
                                                            <div class="bg-primary text-white rounded p-2 me-3 d-flex align-items-center justify-content-center shadow-sm">
                                                                <i class="ph-bold ph-note-pencil f-s-20"></i>
                                                            </div>
                                                            <div><h6 class="mb-0 fw-bold text-dark">Approve With Notes</h6></div>
                                                        </div>
                                                    </div>
                                                    <div class="card decision-btn flex-fill cursor-pointer shadow-sm border" data-select-action="reject" style="cursor: pointer; transition: all 0.2s;">
                                                        <div class="card-body p-3 d-flex align-items-center">
                                                            <div class="bg-danger text-white rounded p-2 me-3 d-flex align-items-center justify-content-center shadow-sm">
                                                                <i class="ph-bold ph-x-circle f-s-20"></i>
                                                            </div>
                                                            <div><h6 class="mb-0 fw-bold text-dark">Reject Without Notes</h6></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="notes_container" style="display: none;" class="mb-2">
                                                    <label for="modal_notes" class="form-label fw-bold small text-dark">Notes / Reason <span class="text-danger">*</span></label>
                                                    <textarea class="form-control bg-light border-secondary" id="modal_notes" name="notes" rows="3" placeholder="Enter notes here..."></textarea>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;

                                submitBtnHtml = `
                                    <button type="submit" form="modalResponseForm" id="final_submit_btn" class="btn px-4 rounded-pill fw-bold shadow-sm" style="display: none;">
                                        Submit Decision
                                    </button>
                                `;
                            }

                            const submitUrl = "{{ route('customers.approval_action', ':id') }}".replace(':id', customerId);
                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalActionFormContainer form').attr('action', submitUrl);

                            $('#viewModalFooter button[type="submit"]').remove();
                            $('#viewModalFooter').prepend(submitBtnHtml);
                            $('#viewModal').modal('show');
                        },
                        error: function() { Swal.fire('Error', 'Failed to fetch data.', 'error'); },
                        complete: function() { button.html(originalIcon).prop('disabled', false); }
                    });
                });

                $(document).on('click', '.decision-btn', function() {

                    $('.decision-btn')
                        .removeClass('border-success border-primary border-danger bg-success bg-primary bg-danger bg-opacity-10')
                        .addClass('opacity-50')
                        .css('border-width', '1px');

                    $('.decision-btn h6').removeClass('text-success text-primary text-danger');

                    $(this).removeClass('opacity-50').css('border-width', '2px');

                    const selectedAction = $(this).data('select-action');
                    $('#final_action').val(selectedAction);

                    if (selectedAction === 'approve') {
                        $(this).addClass('border-success bg-success bg-opacity-10');
                        $(this).find('h6').addClass('text-success');
                    } else if (selectedAction === 'review') {
                        $(this).addClass('border-primary bg-primary bg-opacity-10');
                        $(this).find('h6').addClass('text-primary');
                    } else if (selectedAction === 'reject') {
                        $(this).addClass('border-danger bg-danger bg-opacity-10');
                        $(this).find('h6').addClass('text-danger');
                    }

                    const notesContainer = $('#notes_container');
                    const notesInput = $('#modal_notes');
                    const submitBtn = $('#final_submit_btn');

                    if(selectedAction === 'approve') {
                        notesContainer.slideUp();
                        notesInput.removeAttr('required').val('');
                        submitBtn.removeClass('btn-primary btn-danger').addClass('btn-success')
                                .html('<i class="ph-bold ph-check-circle me-2"></i> Submit Approve').fadeIn();
                    } else if (selectedAction === 'review') {
                        notesContainer.slideDown();
                        notesInput.attr('required', 'required');
                        submitBtn.removeClass('btn-success btn-danger').addClass('btn-primary')
                                .html('<i class="ph-bold ph-paper-plane-tilt me-2"></i> Submit Approve with Notes').fadeIn();
                    } else if (selectedAction === 'reject') {
                        notesContainer.slideDown();
                        notesInput.attr('required', 'required');
                        submitBtn.removeClass('btn-success btn-primary').addClass('btn-danger')
                                .html('<i class="ph-bold ph-x-circle me-2"></i> Submit Reject').fadeIn();
                    }
                });

                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const action = $('#final_action').val();
                    const notesValue = $('#modal_notes').val() ? $('#modal_notes').val().trim() : '';

                    const customerCodeInput = $('#it_update_code');
                    const isITForm = customerCodeInput.length > 0;

                    const topInput = $('#input_top');
                    const isFinanceForm = topInput.length > 0;

                    if (isITForm) {
                        const inputCode = customerCodeInput.val().trim();

                        if (!inputCode) {
                            Swal.fire('Error', 'Customer Code is required!', 'error');
                            return;
                        }

                        Swal.fire({
                            title: 'Confirm Activation?',
                            html: `Please confirm that the Customer Code is correct:<br><br><h2 class="text-primary fw-bold mb-0">${inputCode}</h2>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#059669',
                            confirmButtonText: 'Yes, Activate Now!',
                            cancelButtonText: 'Batal',
                            target: document.getElementById('viewModal')
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processApprovalAjax(form);
                            }
                        });

                    }
                    else {
                        if (!action) {
                            Swal.fire('Warning', 'Please select your decision (Approve / Reject) first.', 'warning');
                            return;
                        }

                        const isReject = action === 'reject';
                        const isApprove = action === 'approve';

                        if (isReject) {
                            if (!notesValue || !/[a-zA-Z]/.test(notesValue)) {
                                Swal.fire('Warning', 'Reason for rejection is required and must be clear.', 'warning');
                                return;
                            }
                        }
                        else if (action === 'review') {
                            if (isFinanceForm) {
                                const currentTop = String(topInput.val() || '').trim();
                                const originalTop = String(topInput.attr('data-original') || '').trim();

                                if (currentTop !== originalTop) {
                                    if (!notesValue) {
                                        Swal.fire('Warning', 'Notes are required because you have changed the Term of Payment (TOP).', 'warning');
                                        return;
                                    }
                                }
                            }
                            else {
                                if (!notesValue) {
                                    Swal.fire('Warning', 'Notes are required for this approval.', 'warning');
                                    return;
                                }
                            }
                        }

                        let title = 'Confirm Action?';
                        let text = 'Proceed with this decision?';
                        let confirmColor = '#3085d6';
                        let icon = 'question';

                        if (isReject) {
                            title = 'Confirm Rejection?';
                            text = "This application will be rejected and returned.";
                            icon = 'warning';
                            confirmColor = '#d33';
                        } else if (isApprove) {
                            title = 'Approve without Notes?';
                            text = "You will approve this application without providing any notes.";
                            confirmColor = '#28a745';
                        } else {
                            title = 'Submit Approval?';
                            text = "Submit the approval along with any notes or data changes?";
                        }

                        Swal.fire({
                            title: title,
                            text: text,
                            icon: icon,
                            showCancelButton: true,
                            confirmButtonColor: confirmColor,
                            confirmButtonText: 'Yes, Submit!',
                            cancelButtonText: 'Batal',
                            target: document.getElementById('viewModal')
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processApprovalAjax(form);
                            }
                        });
                    }
                });

                function processApprovalAjax(form) {
                    $('#loading-overlay').css('display', 'flex');
                    $('#viewModal').modal('hide');

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function(res) {
                            $('#loading-overlay').hide();
                            Swal.fire('Success!', res.message, 'success');

                            if (typeof table !== 'undefined') {
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function(xhr) {
                            $('#loading-overlay').hide();
                            const errMsg = xhr.responseJSON?.message || 'An error occurred while processing the request.';
                            Swal.fire('Error!', errMsg, 'error');
                        }
                    });
                }

                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove();
                });
            });
        </script>
    @endpush
</x-app-layout>
