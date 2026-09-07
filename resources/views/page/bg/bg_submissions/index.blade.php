<x-app-layout>
    @section('title', 'Submission Center')
    @include('components.sample-table-styles')

    {{-- HEADER --}}
    <div class="row m-1 mb-4">
        <div class="col-12">
            <h4 class="main-title text-dark fw-bold" style="letter-spacing: -0.5px;">Submission Center</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Submissions</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- NAVIGATION PILLS (TABS) --}}
            <div class="d-flex justify-content-between align-items-center mb-4 tab-header-container flex-wrap">
                <ul class="nav nav-pills gap-2" id="pills-tab" role="tablist" style="background: #f1f5f9; padding: 5px; border-radius: 12px; display: inline-flex;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-4 rounded-pill" id="pills-active-tab" data-bs-toggle="pill" data-bs-target="#pills-active" type="button" role="tab">
                            <i class="ph-bold ph-list-dashes me-2"></i> Active Tasks
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-4 rounded-pill" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab">
                            <i class="ph-bold ph-clock-counter-clockwise me-2"></i> History / Archive
                        </button>
                    </li>
                </ul>

                {{-- Action Button (Hanya muncul di Active Tab nanti) --}}
                <div id="active-actions">
                    <button class="btn btn-primary shadow-sm rounded-pill px-4" type="button" id="btn-create">
                        <i class="ph-bold ph-plus-circle me-2"></i> <span>New Submission</span>
                    </button>
                </div>
            </div>

            <div class="tab-content" id="pills-tabContent">

                {{-- === TAB 1: ACTIVE SUBMISSIONS === --}}
                <div class="tab-pane fade show active" id="pills-active" role="tabpanel">

                    {{-- Filter --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2 filter-container-responsive flex-wrap">
                            <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                            <select id="statusFilter" class="form-select select2" style="width: 220px;">
                                <option value="all">Show All Active</option>
                                <option value="pending_print">Pending Print</option>
                                <option value="awaiting_upload">Awaiting Upload</option>
                                <option value="uploaded">Uploaded (Need Verification)</option>
                                <option value="waiting_sales_input">Menunggu Lengkapi BG</option>
                                <option value="waiting_approval">Waiting Finance (Bu Rita)</option>
                            </select>
                        </div>
                    </div>

                    {{-- PANDUAN PROSES --}}
                    <div class="alert shadow-sm d-flex align-items-center mb-4 p-3 rounded-3" role="alert" style="background-color: #e7f1ff; border: none; border-left: 5px solid #0d6efd; color: #084298;">
                        <div class="me-3">
                            <span class="d-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm" style="width: 45px; height: 45px;">
                                <i class="ph-duotone ph-info fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1" style="color: #052c65;">Approval Process Guide</h5>
                            <p class="mb-0 small" style="line-height: 2.5; color: #084298;">
                                1. Klik tombol <span class="badge bg-primary text-light border border-warning shadow-sm"><i class="ph-bold ph-file-search me-1"></i> Review & Process</span> in the <b>Signed Doc</b> column to inspect the document, correct the data, and proceed to <b>Attachment D</b>.<br>
                                2. The <span class="badge bg-warning text-light border"><i class="ph-bold ph-pencil-simple"></i></span> button in the <i>Action</i> column is only used for <b>Re-upload / Administrative Edit</b> (Without Approval).
                            </p>
                        </div>
                    </div>

                    {{-- Table Active --}}
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-primary text-white">
                            <h4 class="table-title mb-1"><i class="ph-duotone ph-list-checks me-2"></i> Active To-Do List</h4>
                            <small class="opacity-75 f-s-12">List of submissions requiring action.</small>
                        </div>
                        <div class="table-responsive">
                            <table class="w-100 display align-middle" id="sampleTable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Customer & Bank Ref</th>
                                        <th>Form Code</th>
                                        <th>Date Info</th>
                                        <th class="text-center">Document</th>
                                        <th class="text-center">Status</th>
                                        <th width="10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- === TAB 2: HISTORY SUBMISSIONS === --}}
                <div class="tab-pane fade" id="pills-history" role="tabpanel">

                    {{-- Info Card --}}
                    <div class="alert shadow-sm border-0 d-flex align-items-center mb-4 p-3 rounded-3" style="background-color: #f0fdf4; color: #166534; border-left: 5px solid #198754;">
                        <i class="ph-duotone ph-archive-box fs-3 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Completed Document Archives</h6>
                            <small>The data below are completed/approved submissions. Read-Only.</small>
                        </div>
                    </div>

                    {{-- Table History --}}
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-success text-white">
                            <h4 class="table-title mb-1"><i class="ph-bold ph-check-circle me-2"></i> Completed Archives</h4>
                            <small class="opacity-75 f-s-12">History of approved Bank Guarantee submissions.</small>
                        </div>
                        <div class="table-responsive">
                            <table class="w-100 display align-middle" id="historyTable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Customer & Bank Ref</th>
                                        <th>Form Code</th>
                                        <th>Completion Date</th>
                                        <th class="text-center">Final Document</th>
                                        <th class="text-center">Status</th>
                                        <th width="10%" class="text-center">Info</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= MODALS SECTION ================= --}}

    {{-- 1. Modal Create/Edit Submission (Desain Baru & Validation Ready) --}}
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                {{-- Header --}}
                <div class="modal-header bg-white border-bottom p-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalLabel">
                            <i class="ph-bold ph-folder-plus me-2 text-primary"></i>Manage Submission
                        </h5>
                        <small class="text-muted">Create a new submission or edit administrative data.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="submissionForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="submissionId">
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-body p-4">
                        <div class="row g-4">

                            {{-- Customer Select --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Customer / Recommendation <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ph-bold ph-user"></i></span>
                                    <select name="bg_recommendation_id" id="bg_recommendation_id" class="form-select select2-modal border-start-0 ps-0" required style="width: 100%;">
                                        <option></option>
                                        @foreach($recommendations as $r)
                                            <option value="{{ $r->id }}">
                                                {{ $r->customer->name ?? 'Unknown' }} - Limit: {{ number_format($r->credit_limit_updated, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Form Code --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase text-muted">Form Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="ph-bold ph-qr-code"></i></span>
                                    <input type="text" class="form-control" name="form_code" id="form_code" required
                                           placeholder="Example: NEW-20250112-ABCD-1">
                                </div>
                                <div class="form-text small text-muted"><i class="ph-bold ph-info me-1"></i> Use a format that complies with company standards.</div>
                            </div>

                            {{-- File Upload --}}
                            <div class="col-12">
                                <div class="p-3 border rounded-3 bg-light position-relative">
                                    <label class="form-label fw-bold small text-uppercase text-dark mb-2">
                                        <i class="ph-bold ph-file-pdf me-1 text-danger"></i> Upload Signed Document <span class="text-danger" id="req-star">*</span>
                                    </label>
                                    <input type="file" name="signed_document" id="signed_document" class="form-control" accept=".pdf,.jpg,.png">

                                    <div class="form-text mt-2 small text-muted">
                                        Format: PDF, JPG, PNG. Max 5MB.<br>
                                        <span class="text-danger fst-italic" id="upload-note">* Must be uploaded for new submissions.</span>
                                    </div>

                                    {{-- Preview Link if Edit --}}
                                    <div id="current_file_preview" class="d-none mt-2 p-2 bg-white border rounded d-flex align-items-center gap-2">
                                        <i id="current_file_preview_icon" class="ph-fill ph-check-circle text-success fs-5"></i>
                                        <span id="current_file_preview_text" class="small text-success fw-bold">File is available and can be viewed.</span>
                                        <a href="#" id="link_view_file_modal" target="_blank" class="btn btn-sm btn-outline-success ms-auto">View File</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3 border-top-0">
                        <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                            <i class="ph-bold ph-paper-plane-right me-2"></i> Save & Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 2. Modal View File & Process --}}
    <div class="modal fade" id="viewFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="height: 90vh;">
                <div class="modal-header bg-dark text-white py-2.5 px-3">
                    <h6 class="modal-title text-white d-flex align-items-center gap-2 mb-0"><i class="ph-bold ph-file-text"></i> Document Preview & Action</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light position-relative" id="fileContentArea" style="height: 100%;"></div>

                {{-- Footer Action --}}
                <div class="modal-footer bg-white shadow-lg py-2.5 px-3" id="viewFileFooter" style="z-index: 1050;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div>
                            <button type="button" class="btn btn-sm btn-primary fw-semibold px-3 py-2 rounded-2 shadow-sm d-inline-flex align-items-center gap-1.5" id="btn-trigger-edit">
                                <i class="ph-bold ph-pencil-simple"></i> <span>Lengkapi Data BG</span>
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-success fw-semibold px-4 py-2 rounded-2 shadow-sm d-inline-flex align-items-center gap-1.5" id="btn-trigger-approve">
                                <i class="ph-bold ph-check-circle"></i> <span>Verifikasi Dokumen</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Modal Edit Data (Kelengkapan Bank Garansi) --}}
    <div class="modal fade" id="editBgDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1d4ed8, #2563eb);">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="bg-white bg-opacity-20 p-2 rounded-3 text-white">
                            <i class="ph-bold ph-shield-check fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">Kelengkapan Data Bank Garansi</h5>
                            <small class="text-white text-opacity-75" id="modalSubTitle">Input Nomor BG resmi dari bank, tanggal jatuh tempo, dan unggah scan warkat fisik.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBgForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="submission_id" id="edit_submission_id">
                    <input type="hidden" name="action_type" value="edit_submit">
                    <div class="modal-body p-4 bg-light bg-opacity-40" style="max-height: 78vh; overflow-y: auto;">
                        <div id="bankDetailsContainer"></div> {{-- Diisi AJAX --}}
                    </div>
                    <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary fw-semibold rounded-2 px-3 py-2" data-bs-dismiss="modal">
                            <i class="ph-bold ph-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success fw-bold rounded-2 px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2" id="btn-save-edit-bg">
                            <i class="ph-bold ph-check-circle fs-5"></i> <span>Simpan & Ajukan ke Finance (Bu Rita)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 in Modal
                $('.select2').select2({ theme: 'bootstrap-5' });
                $('.select2-modal').select2({ dropdownParent: $('#submissionModal'), theme: 'bootstrap-5', placeholder: 'Select Customer...' });

                let currentSubmissionId = null;

                // --- 1. INITIALIZE DATATABLE ACTIVE ---
                const sampleTable = $('#sampleTable').DataTable({
                    processing: true, serverSide: true,
                    ajax: {
                        url: "{{ route('bg-submissions.index') }}",
                        data: function(d) {
                            d.type = 'active';
                            d.status_filter = $('#statusFilter').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                        { data: 'customer_name', name: 'recommendation.customer.name' },
                        { data: 'form_code', name: 'form_code' },
                        { data: 'date_info', name: 'created_at' },
                        { data: 'file', name: 'signed_document_path', className: 'text-center', orderable: false, searchable: false },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ]
                });

                // --- 2. INITIALIZE DATATABLE HISTORY (Lazy Load) ---
                let historyTable;
                let isHistoryInitialized = false;

                $('#pills-history-tab').on('shown.bs.tab', function (e) {
                    $('#active-actions').hide(); // Hide create button in history

                    if (!isHistoryInitialized) {
                        historyTable = $('#historyTable').DataTable({
                            processing: true, serverSide: true,
                            ajax: {
                                url: "{{ route('bg-submissions.index') }}",
                                data: function(d) { d.type = 'history'; }
                            },
                            columns: [
                                { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                                { data: 'customer_name', name: 'recommendation.customer.name' },
                                { data: 'form_code', name: 'form_code' },
                                { data: 'date_info', name: 'updated_at' },
                                { data: 'file', name: 'signed_document_path', className: 'text-center', orderable: false, searchable: false },
                                { data: 'status', name: 'status', className: 'text-center' },
                                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                            ],
                            order: [[3, 'desc']]
                        });
                        isHistoryInitialized = true;
                    } else {
                        historyTable.ajax.reload();
                    }
                });

                $('#pills-active-tab').on('shown.bs.tab', function (e) {
                    $('#active-actions').show();
                    sampleTable.ajax.reload();
                });

                // --- FILTER EVENT ---
                $('#statusFilter').change(function() {
                    sampleTable.ajax.reload();
                });

                // --- VIEW FILE & APPROVE LOGIC ---
                $(document).on('click', '.btn-view-file', function() {
                    let url = $(this).data('url');
                    let id = $(this).data('id');
                    let status = $(this).data('status');
                    currentSubmissionId = id;

                    let container = $('#fileContentArea');
                    container.html('<div class="d-flex h-100 justify-content-center align-items-center"><div class="spinner-border text-primary"></div></div>');

                    if (status === 'completed') {
                        $('#viewFileFooter').hide(); // Sembunyikan tombol aksi di history
                        $('#viewFileModal .modal-header').removeClass('bg-dark').addClass('bg-success');
                    } else {
                        $('#viewFileFooter').show();
                        $('#viewFileModal .modal-header').removeClass('bg-success').addClass('bg-dark');

                        if (status === 'uploaded') {
                            $('#btn-trigger-approve').show().html('<i class="ph-bold ph-check-circle me-1"></i> Verifikasi Dokumen');
                            $('#btn-trigger-edit').hide();
                        } else if (status === 'waiting_sales_input') {
                            $('#btn-trigger-approve').hide();
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Lengkapi Data BG');
                        } else if (status === 'waiting_approval') {
                            $('#btn-trigger-approve').hide();
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Koreksi Data BG');
                        } else {
                            $('#btn-trigger-approve').show().html('<i class="ph-bold ph-check-circle me-1"></i> Verifikasi Dokumen');
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Edit Data BG');
                        }
                    }

                    $('#viewFileModal').modal('show');

                    setTimeout(() => {
                        let extension = url.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png'].includes(extension)) {
                            container.html(`<img src="${url}" class="img-fluid h-100 w-100" style="object-fit: contain;">`);
                        } else {
                            container.html(`<iframe src="${url}" style="width: 100%; height: 100%; border: none;"></iframe>`);
                        }
                    }, 500);
                });

                // --- DIRECT INPUT SALES BUTTON FROM TABLE ---
                $(document).on('click', '.btn-input-sales', function() {
                    currentSubmissionId = $(this).data('id');
                    $('#btn-trigger-edit').trigger('click');
                });

                // --- VERIFY & FORWARD TO SALES ---
                $('#btn-trigger-approve').click(function() {
                    Swal.fire({
                        title: 'Verifikasi Dokumen Upload?',
                        text: "Dokumen hasil upload customer dinyatakan valid dan dapat dilengkapi data Bank Garansi oleh Admin-RTM.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Verifikasi Dokumen',
                        confirmButtonColor: '#198754',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', currentSubmissionId);
                            Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });

                            $.post(url, { _token: "{{ csrf_token() }}", action_type: 'verify_upload' }, function(res) {
                                if(res.success) {
                                    $('#viewFileModal').modal('hide');
                                    Swal.fire('Berhasil', res.message, 'success');
                                    sampleTable.ajax.reload();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            });
                        }
                    });
                });

                function formatRupiah(angka) {
                    if (!angka) return '';
                    let raw = Math.floor(angka);
                    return new Intl.NumberFormat('id-ID').format(raw);
                }

                $('#btn-trigger-edit').click(function() {
                    $('#viewFileModal').modal('hide');
                    Swal.fire({ title: 'Opening Editor...', didOpen: () => Swal.showLoading() });

                    let url = "{{ route('bg-submissions.get-edit-data', ':id') }}".replace(':id', currentSubmissionId);

                    $.get(url, function(res) {
                        Swal.close();
                        if(res.success) {
                            let d = res.data;
                            $('#edit_submission_id').val(d.submission_id);

                            if (d.form_code) {
                                $('#modalSubTitle').html(`<span class="fw-semibold text-white">${d.form_code}</span> • <span class="text-white-50">${d.nama_distributor || 'Bank Guarantee'}</span>`);
                            }

                            let html = `
                                {{-- TOP INFO BANNER --}}
                                <div class="card border-0 mb-3 rounded-3 shadow-xs" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
                                    <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="bg-primary text-white p-2 rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 36px; height: 36px;">
                                                <i class="ph-bold ph-identification-badge fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted" style="font-size: 11px;">Nama Distributor:</div>
                                                <strong class="text-dark fs-6">${d.nama_distributor || '-'}</strong>
                                                <span class="badge bg-white text-primary border border-primary-subtle rounded-pill ms-1 px-2" style="font-size: 10px;">${d.form_code}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            ${d.signed_document_url ? `
                                                <a href="${d.signed_document_url}" target="_blank" class="btn btn-sm btn-white text-primary border border-primary-subtle rounded-2 shadow-xs fw-semibold px-3 py-1.5 d-inline-flex align-items-center gap-1.5" title="Buka berkas pengajuan bertanda tangan customer">
                                                    <i class="ph-bold ph-file-text fs-6"></i> <span>Lihat Dokumen Pengajuan (TTD)</span>
                                                </a>
                                            ` : ''}
                                            <div class="text-end ps-2 border-start">
                                                <div class="small text-muted" style="font-size: 11px;">Total BG Diserahkan:</div>
                                                <strong class="text-success fs-6">Rp ${formatRupiah(d.nilai_bg_diserahkan)}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECTION A: COLLAPSIBLE DISTRIBUTOR & FINANCIAL DATA --}}
                                <div class="card border rounded-3 mb-3 shadow-xs overflow-hidden">
                                    <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center cursor-pointer" 
                                         data-bs-toggle="collapse" data-bs-target="#collapseDistributorInfo" style="cursor: pointer;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ph-bold ph-caret-right text-primary fs-6 transition-all" id="caretDistributorInfo"></i>
                                            <span class="fw-bold text-dark small"><i class="ph-bold ph-sliders text-primary me-1"></i> A. Parameter Finansial & Data Tambahan</span>
                                            <span class="badge bg-light text-muted border" style="font-size: 10px;">10 Field • Klik untuk Buka/Ubah</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted small">Periode: <strong>${d.periode || '-'}</strong></span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle small px-2 py-0.5">Buka Form Finansial</span>
                                        </div>
                                    </div>
                                    <div class="collapse" id="collapseDistributorInfo">
                                        <div class="card-body p-3 bg-light bg-opacity-25 border-top">
                                            <div class="row g-2">
                                                <div class="col-md-12"><label class="small fw-semibold text-secondary">1. Nama Distributor</label><input type="text" class="form-control form-control-sm" name="nama_distributor" value="${d.nama_distributor || ''}"></div>
                                                <div class="col-md-6"><label class="small fw-semibold text-secondary">2. Kota</label><input type="text" class="form-control form-control-sm" name="kota" value="${d.kota || ''}"></div>
                                                <div class="col-md-6"><label class="small fw-semibold text-secondary">3. Wilayah Kerja</label><input type="text" class="form-control form-control-sm" name="wilayah_kerja" value="${d.wilayah_kerja || ''}"></div>

                                                <div class="col-md-6">
                                                    <label class="small fw-semibold text-secondary">4. Rata-rata Penjualan (Rp)</label>
                                                    <input type="text" class="form-control form-control-sm rupiah-input" name="rata_rata_penjualan" value="${formatRupiah(d.rata_rata_penjualan)}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-semibold text-secondary">5. TOP (Hari)</label>
                                                    <input type="number" class="form-control form-control-sm" name="syarat_pembayaran" value="${d.syarat_pembayaran || ''}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="small fw-semibold text-secondary">6. Lead Time (Hari)</label>
                                                    <input type="number" class="form-control form-control-sm" name="lead_time" value="${d.lead_time || ''}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-semibold text-secondary">7. Faktor Fluktuasi (%)</label>
                                                    <input type="number" step="0.01" class="form-control form-control-sm" name="faktor_fluktuasi" value="${d.faktor_fluktuasi || ''}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-semibold text-secondary">8. Credit Limit Disetujui (Rp)</label>
                                                    <input type="text" class="form-control form-control-sm rupiah-input" name="limit_kredit" value="${formatRupiah(d.limit_kredit)}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-semibold text-secondary">9. Nilai BG Ditetapkan (Rp)</label>
                                                    <input type="text" class="form-control form-control-sm rupiah-input" name="nilai_bg_ditetapkan" value="${formatRupiah(d.nilai_bg_ditetapkan)}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="small fw-semibold text-secondary">10. Nilai BG Diserahkan (Total Rp)</label>
                                                    <input type="text" class="form-control form-control-sm rupiah-input bg-light fw-bold text-success" id="input_total_bg_diserahkan" name="nilai_bg_diserahkan" value="${formatRupiah(d.nilai_bg_diserahkan)}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECTION B: RINCIAN BANK GARANSI --}}
                                <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                            <i class="ph-bold ph-bank text-primary fs-5"></i>
                                            <span>B. Rincian & Kelengkapan Bank Garansi</span>
                                        </h6>
                                        ${d.is_multi_bank ? `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small"><i class="ph-bold ph-stack me-1"></i>Multi-Bank (${d.details.length} Bank Penerbit)</span>` : '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 small">Single-Bank (1 Bank)</span>'}
                                    </div>
                                    <span class="text-muted small">Input Nomor BG Resmi & Unggah Berkas Warkat Asli</span>
                                </div>
                            `;

                            if(d.details && d.details.length > 0) {
                                d.details.forEach((item, index) => {
                                    html += `
                                        <div class="card mb-3 border rounded-3 shadow-xs bg-white overflow-hidden">
                                            <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center border-bottom">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary rounded-pill px-2.5 py-1.5 text-white fw-bold"><i class="ph-bold ph-bank me-1"></i>Bank ${index+1}: ${item.bank_name || 'Bank'}</span>
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill fw-bold">Nominal: Rp ${formatRupiah(item.nominal)}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-1.5">
                                                    ${item.is_temporary ? `
                                                        <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill" title="Nomor referensi sementara yang akan ditimpa dengan nomor resmi dari warkat">
                                                            <i class="ph-bold ph-clock-countdown me-1"></i>Ref Draft: ${item.parent_bg_number || 'Belum Diisi'}
                                                        </span>
                                                    ` : `
                                                        <span class="badge bg-success bg-opacity-15 text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">
                                                            <i class="ph-bold ph-shield-check me-1"></i>No. BG: ${item.parent_bg_number}
                                                        </span>
                                                    `}
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <input type="hidden" name="details[${item.id}][id]" value="${item.id}">
                                                
                                                {{-- BARIS 1: 3 KOLOM SEJAJAR (Bank, Cabang, Nominal) --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                                            <i class="ph-bold ph-bank me-1 text-primary"></i>Nama Bank <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control rounded-2" name="details[${item.id}][bank_name]" value="${item.bank_name || ''}" placeholder="Nama Bank Penerbit" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                                            <i class="ph-bold ph-map-pin me-1 text-secondary"></i>Kantor Cabang
                                                        </label>
                                                        <input type="text" class="form-control rounded-2" name="details[${item.id}][branch_name]" value="${item.branch_name || ''}" placeholder="Contoh: KCU Sunter">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                                            <i class="ph-bold ph-money me-1 text-success"></i>Nominal BG (Rp) <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted small fw-semibold">Rp</span>
                                                            <input type="text" class="form-control rounded-end-2 rupiah-input detail-nominal-input fw-semibold" name="details[${item.id}][nominal]" value="${formatRupiah(item.nominal)}" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- BARIS 2: 2 KOLOM SEJAJAR (No BG Resmi Bank & Tanggal Jatuh Tempo) --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-dark mb-1">
                                                            <i class="ph-bold ph-hash me-1 text-primary"></i>Nomor BG Resmi Bank <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control rounded-2 fw-semibold" 
                                                               name="details[${item.id}][bg_number]" 
                                                               value="${item.is_temporary ? '' : (item.parent_bg_number || '')}" 
                                                               placeholder="Contoh: 0021/BG/BCA/2026 (Wajib diisi)" required>
                                                        <div class="form-text text-muted mt-1" style="font-size: 11px;">
                                                            <i class="ph-bold ph-info text-primary me-0.5"></i> Masukkan nomor resmi dari warkat bank fisik (akan menimpa ref draft: ${item.parent_bg_number || '-'}).
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-dark mb-1">
                                                            <i class="ph-bold ph-calendar me-1 text-primary"></i>Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="date" class="form-control rounded-2" 
                                                               name="details[${item.id}][exp_date]" 
                                                               value="${item.parent_exp_date || d.exp_date || ''}" required>
                                                        <div class="form-text text-muted mt-1" style="font-size: 11px;">
                                                            <i class="ph-bold ph-calendar-check text-secondary me-0.5"></i> Tanggal berakhirnya masa berlaku warkat BG.
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- BARIS 3: 2 KOLOM SEJAJAR (Scan File Warkat BG & Lampiran D) --}}
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 h-100">
                                                            <label class="form-label small fw-semibold text-dark mb-1 d-flex justify-content-between align-items-center">
                                                                <span><i class="ph-bold ph-file-text text-primary me-1"></i>Scan Berkas Warkat BG Asli</span>
                                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle" style="font-size: 10px;">Bisa > 1 file</span>
                                                            </label>
                                                            <input type="file" class="form-control form-control-sm bg-white rounded-2 file-multi-input" name="details[${item.id}][warkat_files][]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                                            <div class="text-muted mt-1" style="font-size: 11px;"><i class="ph-bold ph-files me-0.5"></i> PDF, JPG, PNG (Maks 10MB/file)</div>
                                                            <div class="new-files-preview"></div>
                                                            ${item.parent_warkat_files && item.parent_warkat_files.length > 0 ? `
                                                                <div class="mt-2 p-2 bg-white rounded-2 border">
                                                                    <div class="small fw-semibold text-primary mb-1" style="font-size: 11px;"><i class="ph-bold ph-file-text me-1"></i>File BG Terupload Sebelumnya:</div>
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        ${item.parent_warkat_files.map((wf, wIdx) => `
                                                                            <a href="${wf.url}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center gap-1 shadow-xs" title="${wf.name}">
                                                                                <i class="ph-bold ph-file-pdf"></i> BG ${wIdx+1}
                                                                            </a>
                                                                        `).join('')}
                                                                    </div>
                                                                </div>
                                                            ` : (item.parent_warkat ? `
                                                                <div class="mt-2">
                                                                    <a href="${item.parent_warkat}" target="_blank" class="badge bg-light text-primary border text-decoration-none py-1 px-2 d-inline-flex align-items-center gap-1">
                                                                        <i class="ph-bold ph-file-pdf"></i> Buka File Warkat
                                                                    </a>
                                                                </div>
                                                            ` : '')}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 h-100">
                                                            <label class="form-label small fw-semibold text-dark mb-1 d-flex justify-content-between align-items-center">
                                                                <span><i class="ph-bold ph-file-check text-success me-1"></i>Scan Berkas Lampiran D Asli</span>
                                                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle" style="font-size: 10px;">Bisa > 1 file</span>
                                                            </label>
                                                            <input type="file" class="form-control form-control-sm bg-white rounded-2 file-multi-input" name="details[${item.id}][lampiran_d_files][]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                                            <div class="text-muted mt-1" style="font-size: 11px;"><i class="ph-bold ph-files me-0.5"></i> PDF, JPG, PNG (Maks 10MB/file)</div>
                                                            <div class="new-files-preview"></div>
                                                            ${item.parent_lampiran_d_files && item.parent_lampiran_d_files.length > 0 ? `
                                                                <div class="mt-2 p-2 bg-white rounded-2 border">
                                                                    <div class="small fw-semibold text-success mb-1" style="font-size: 11px;"><i class="ph-bold ph-file-check me-1"></i>File Lampiran D Terupload Sebelumnya:</div>
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        ${item.parent_lampiran_d_files.map((ldf, ldIdx) => `
                                                                            <a href="${ldf.url}" target="_blank" class="badge bg-light text-success border text-decoration-none py-1 px-2 d-inline-flex align-items-center gap-1 shadow-xs" title="${ldf.name}">
                                                                                <i class="ph-bold ph-file-check"></i> Lampiran D ${ldIdx+1}
                                                                            </a>
                                                                        `).join('')}
                                                                    </div>
                                                                </div>
                                                            ` : (item.parent_lampiran_d ? `
                                                                <div class="mt-2">
                                                                    <a href="${item.parent_lampiran_d}" target="_blank" class="badge bg-light text-success border text-decoration-none py-1 px-2 d-inline-flex align-items-center gap-1">
                                                                        <i class="ph-bold ph-file-check"></i> Buka File Lampiran D
                                                                    </a>
                                                                </div>
                                                            ` : '')}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    `;
                                });
                            }

                            html += `
                                <div class="alert alert-primary bg-primary bg-opacity-10 border border-primary-subtle rounded-3 p-3 mt-3 d-flex align-items-center gap-3">
                                    <div class="fs-4 text-primary flex-shrink-0"><i class="ph-duotone ph-info"></i></div>
                                    <div class="small text-dark">
                                        <strong>Pemberitahuan:</strong> Nomor BG resmi dan berkas fisik scan warkat yang Anda input akan <strong>otomatis menimpa (menggantikan)</strong> data referensi sementara pada sistem, dan pengajuan akan diteruskan ke Finance (Bu Rita) untuk validasi.
                                    </div>
                                </div>
                            `;

                            $('#bankDetailsContainer').html(html);
                            $('#editBgDataModal').modal('show');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    });
                });

                // Auto-sum details nominal to total BG diserahkan
                $(document).on('keyup', '.detail-nominal-input', function() {
                    let total = 0;
                    $('.detail-nominal-input').each(function() {
                        let val = $(this).val().replace(/[^0-9]/g, '');
                        if (val) total += parseInt(val, 10);
                    });
                    $('#input_total_bg_diserahkan').val(new Intl.NumberFormat('id-ID').format(total));
                });

                // --- LISTENER INPUT RUPIAH (AUTO FORMAT SAAT KETIK) ---
                $(document).on('keyup', '.rupiah-input', function() {
                    // Ambil value, hapus semua karakter selain angka
                    let val = $(this).val().replace(/[^0-9]/g, '');
                    if (val !== '') {
                        // Format kembali dengan titik
                        $(this).val(new Intl.NumberFormat('id-ID').format(val));
                    }
                });

                // --- LISTENER COLLAPSE CARET TOGGLE ---
                $(document).on('show.bs.collapse', '#collapseDistributorInfo', function () {
                    $('#caretDistributorInfo').removeClass('ph-caret-right').addClass('ph-caret-down');
                });
                $(document).on('hide.bs.collapse', '#collapseDistributorInfo', function () {
                    $('#caretDistributorInfo').removeClass('ph-caret-down').addClass('ph-caret-right');
                });

                // --- LISTENER MULTI-FILE SELECTION PREVIEW ---
                $(document).on('change', '.file-multi-input', function() {
                    let files = this.files;
                    let previewContainer = $(this).siblings('.new-files-preview');
                    if (files && files.length > 0) {
                        let listHtml = `<div class="mt-2 p-2 bg-white rounded-2 border shadow-xs"><div class="text-primary fw-semibold mb-1" style="font-size:11px;"><i class="ph-bold ph-paperclip me-1"></i>${files.length} file baru dipilih:</div><div class="d-flex flex-wrap gap-1">`;
                        for (let i = 0; i < files.length; i++) {
                            listHtml += `<span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle py-1 px-2" style="font-size: 10px;"><i class="ph-bold ph-file me-1"></i>${files[i].name}</span>`;
                        }
                        listHtml += `</div></div>`;
                        previewContainer.html(listHtml);
                    } else {
                        previewContainer.empty();
                    }
                });

                // --- SAVE EDIT FORM (WITH FILE SUPPORT & CLEANING) ---
                $('#editBgForm').on('submit', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    // Bersihkan titik (.) pada field rupiah sebelum dikirim ke Controller
                    ['rata_rata_penjualan', 'limit_kredit', 'nilai_bg_ditetapkan'].forEach(function(fieldName) {
                        if (formData.has(fieldName)) {
                            formData.set(fieldName, formData.get(fieldName).replace(/\./g, ''));
                        }
                    });

                    // Loop untuk field nominal pada details
                    for (let pair of formData.entries()) {
                        if (pair[0].includes('[nominal]') && typeof pair[1] === 'string') {
                            formData.set(pair[0], pair[1].replace(/\./g, ''));
                        }
                    }

                    let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', $('#edit_submission_id').val());

                    Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if(res.success) {
                                Swal.fire('Success', res.message, 'success');
                                $('#editBgDataModal').modal('hide');
                                sampleTable.ajax.reload();
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Failed to process request';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });

                // --- CREATE HANDLER (Show Modal) ---
                $('#btn-create').click(function() {
                    $('#submissionForm')[0].reset();
                    $('#submissionId').val('');
                    $('#formMethod').val('POST');
                    $('#bg_recommendation_id').val(null).trigger('change');

                    // Reset UI State for New Submission
                    $('#modalLabel').html('<i class="ph-bold ph-folder-plus me-2 text-primary"></i> Create New Submission');
                    $('#current_file_preview').addClass('d-none'); // Sembunyikan preview
                    $('#req-star').removeClass('d-none'); // Tampilkan bintang merah
                    $('#upload-note').text('* Must be uploaded for new submissions.');

                    $('#submissionModal').modal('show');
                });

                // --- EDIT SUBMISSION HANDLER ---
                $(document).on('click', '.btn-edit-submission', function() {
                    let id = $(this).data('id');
                    let url = "{{ route('bg-submissions.show', ':id') }}".replace(':id', id);

                    Swal.fire({ title: 'Loading...', didOpen: () => Swal.showLoading() });

                    $.get(url, function(data) {
                        Swal.close();
                        $('#submissionForm')[0].reset();
                        $('#submissionId').val(data.id);
                        $('#formMethod').val('PUT');

                        $('#bg_recommendation_id').val(data.bg_recommendation_id).trigger('change');
                        $('#form_code').val(data.form_code);

                        // UI State for Edit
                        $('#modalLabel').html('<i class="ph-bold ph-pencil-simple me-2 text-warning"></i> Edit Submission');

                        // Cek File
                        if(data.signed_document_path) {
                            $('#current_file_preview').removeClass('d-none');
                            
                            if (data.file_exists) {
                                $('#current_file_preview_icon').removeClass('ph-warning-circle text-danger').addClass('ph-check-circle text-success');
                                $('#current_file_preview_text').removeClass('text-danger').addClass('text-success').text('File is available and can be viewed.');
                                $('#link_view_file_modal').removeClass('disabled btn-outline-danger').addClass('btn-outline-success').attr('target', '_blank').attr('href', "{{ asset('') }}" + data.signed_document_path).text('View File').css('pointer-events', 'auto');
                            } else {
                                $('#current_file_preview_icon').removeClass('ph-check-circle text-success').addClass('ph-warning-circle text-danger');
                                $('#current_file_preview_text').removeClass('text-success').addClass('text-danger').text('File is missing or corrupted.');
                                $('#link_view_file_modal').addClass('disabled btn-outline-danger').removeClass('btn-outline-success').removeAttr('target').attr('href', '#').text('Error / Missing').css('pointer-events', 'none');
                            }

                            // File jadi opsional kalau edit dan file sudah ada
                            $('#req-star').addClass('d-none');
                            $('#upload-note').text('Leave empty if you don\'t want to change the file.');
                        } else {
                            $('#current_file_preview').addClass('d-none');
                            $('#req-star').removeClass('d-none');
                        }

                        $('#submissionModal').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    });
                });

                // --- SUBMIT FORM (CREATE/UPDATE) WITH VALIDATION ---
                $('#submissionForm').on('submit', function(e) {
                    e.preventDefault();

                    // VALIDASI MANUAL: Cek Dokumen untuk New Submission
                    let id = $('#submissionId').val();
                    let fileInput = $('#signed_document')[0];

                    if (!id && fileInput.files.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Document Required',
                            text: 'For new submissions, you must upload the signed document.',
                            confirmButtonColor: '#f59e0b'
                        });
                        return; // Stop process
                    }

                    // Lanjut Ajax Submit
                    let formData = new FormData(this);
                    let url = "{{ route('bg-submissions.store') }}";

                    if(id) {
                        url = "{{ route('bg-submissions.update', ':id') }}".replace(':id', id);
                        formData.append('_method', 'PUT'); // Laravel spoofing
                    }

                    Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: url, method: 'POST', data: formData, processData: false, contentType: false,
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#submissionModal').modal('hide');
                            sampleTable.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Failed to process request';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });

                // --- DELETE HANDLER ---
                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    let url = "{{ route('bg-submissions.destroy', ':id') }}".replace(':id', id);
                    Swal.fire({
                        title: 'Are you sure?', text: "File will be deleted.", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url, method: 'DELETE', data: { _token: "{{ csrf_token() }}" },
                                success: function(res) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    sampleTable.ajax.reload();
                                },
                                error: function(xhr) { Swal.fire('Error', 'Failed to delete', 'error'); }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
