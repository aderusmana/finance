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
                                <option value="waiting_sales_input">Waiting Sales Input</option>
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
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title text-white"><i class="ph-bold ph-file-text me-2"></i> Document Preview & Action</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-light position-relative" id="fileContentArea" style="height: 100%;"></div>

                {{-- Footer Action --}}
                <div class="modal-footer bg-white shadow-lg py-3" id="viewFileFooter" style="z-index: 1050;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div>
                            <button type="button" class="btn btn-warning text-white fw-bold" id="btn-trigger-edit">
                                <i class="ph-bold ph-pencil-simple me-1"></i> Lengkapi / Edit Data BG
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success fw-bold px-4" id="btn-trigger-approve">
                                <i class="ph-bold ph-paper-plane-right me-1"></i> Verifikasi Dokumen & Kirim ke Sales
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Modal Edit Data (Correction / Sales Input) --}}
    <div class="modal fade" id="editBgDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-primary text-white p-3">
                    <h5 class="modal-title fw-bold"><i class="ph-bold ph-pencil-simple me-2"></i> Lengkapi Data Bank Garansi (Tim Sales)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBgForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="submission_id" id="edit_submission_id">
                    <input type="hidden" name="action_type" value="edit_submit">
                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                        <div id="bankDetailsContainer"></div> {{-- Diisi AJAX --}}
                    </div>
                    <div class="modal-footer bg-light p-3">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" id="btn-save-edit-bg">
                            <i class="ph-bold ph-paper-plane-right me-1"></i> Simpan & Ajukan ke Finance (Bu Rita)
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
                            $('#btn-trigger-approve').show().html('<i class="ph-bold ph-check-circle me-1"></i> Verifikasi Dokumen & Kirim ke Sales');
                            $('#btn-trigger-edit').hide();
                        } else if (status === 'waiting_sales_input') {
                            $('#btn-trigger-approve').hide();
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Lengkapi Data BG (Sales)');
                        } else if (status === 'waiting_approval') {
                            $('#btn-trigger-approve').hide();
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Koreksi Data BG');
                        } else {
                            $('#btn-trigger-approve').show().html('<i class="ph-bold ph-paper-plane-right me-1"></i> Verifikasi Dokumen');
                            $('#btn-trigger-edit').show().html('<i class="ph-bold ph-pencil-simple me-1"></i> Edit Data');
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
                        title: 'Verifikasi Dokumen & Kirim ke Sales?',
                        text: "Dokumen hasil upload customer dinyatakan valid dan akan diteruskan ke tim Sales untuk melengkapi nomor BG, expired date, nominal, dan scan warkat.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Verifikasi & Kirim ke Sales',
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

                            let html = `
                                <div class="row g-3">
                                    <div class="col-12"><h6 class="fw-bold text-primary border-bottom pb-2">A. Information & Financials</h6></div>
                                    <div class="col-md-12"><label class="small fw-bold">1. Name</label><input type="text" class="form-control" name="nama_distributor" value="${d.nama_distributor}"></div>
                                    <div class="col-md-6"><label class="small fw-bold">2. City</label><input type="text" class="form-control" name="kota" value="${d.kota}"></div>
                                    <div class="col-md-6"><label class="small fw-bold">3. Area</label><input type="text" class="form-control" name="wilayah_kerja" value="${d.wilayah_kerja}"></div>

                                    <div class="col-md-6">
                                        <label class="small fw-bold">4. Average Sales (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="rata_rata_penjualan" value="${formatRupiah(d.rata_rata_penjualan)}">
                                    </div>
                                    <div class="col-md-3"><label class="small fw-bold">5. TOP</label>
                                        <input type="number" class="form-control" name="syarat_pembayaran" value="${d.syarat_pembayaran}">
                                    </div>
                                    <div class="col-md-3"><label class="small fw-bold">6. Lead Time</label>
                                        <input type="number" class="form-control" name="lead_time" value="${d.lead_time}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">7. Fluctuation Factor (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="faktor_fluktuasi" value="${d.faktor_fluktuasi}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">8. Credit Limit (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="limit_kredit" value="${formatRupiah(d.limit_kredit)}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">9. Determined BG Amount (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="nilai_bg_ditetapkan" value="${formatRupiah(d.nilai_bg_ditetapkan)}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">10. Nilai BG Diserahkan (Total Rp)</label>
                                        <input type="text" class="form-control rupiah-input bg-light fw-bold text-success" id="input_total_bg_diserahkan" name="nilai_bg_diserahkan" value="${formatRupiah(d.nilai_bg_diserahkan)}" readonly>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mt-4">
                                    <h6 class="fw-bold text-primary mb-0">B. Bank Details ${d.is_multi_bank ? `<span class="badge bg-primary bg-opacity-10 text-primary border ms-2">Multi-Bank (${d.details.length} Bank)</span>` : ''}</h6>
                                </div>
                            `;

                            if(d.details && d.details.length > 0) {
                                d.details.forEach((item, index) => {
                                    html += `
                                        <div class="card mb-2 border-start border-3 border-primary shadow-sm">
                                            <div class="card-body p-2">
                                                <input type="hidden" name="details[${item.id}][id]" value="${item.id}">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="text-primary small"><i class="ph-bold ph-bank me-1"></i> Bank ${index+1}: ${item.bank_name || ''}</strong>
                                                    ${item.parent_bg_number ? `<span class="badge bg-light text-dark border"><i class="ph-bold ph-hash me-1"></i>${item.parent_bg_number}</span>` : ''}
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-md-3">
                                                        <label class="small text-muted">Bank Name</label>
                                                        <input type="text" class="form-control form-control-sm" name="details[${item.id}][bank_name]" value="${item.bank_name}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small text-muted">Branch</label>
                                                        <input type="text" class="form-control form-control-sm" name="details[${item.id}][branch_name]" value="${item.branch_name}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small text-muted">Nominal (Rp)</label>
                                                        <input type="text" class="form-control form-control-sm rupiah-input detail-nominal-input" name="details[${item.id}][nominal]" value="${formatRupiah(item.nominal)}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small text-muted">No BG Bank</label>
                                                        <input type="text" class="form-control form-control-sm" name="details[${item.id}][bg_number]" value="${item.parent_bg_number || ''}" placeholder="No BG Bank">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            }

                            html += `
                                <h6 class="fw-bold text-primary border-bottom pb-2 mt-4"><i class="ph-bold ph-shield-check me-1"></i> C. Kelengkapan Bank Garansi (Diisi Tim Sales)</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="small fw-bold">Nomor Dokumen / Ref BG <span class="${d.is_multi_bank ? 'text-muted' : 'text-danger'}">${d.is_multi_bank ? '(Opsional - Multi Bank)' : '*'}</span></label>
                                        <input type="text" class="form-control" name="bg_number" value="${d.bg_number || ''}" placeholder="Contoh: BG-2026-0003" ${d.is_multi_bank ? '' : 'required'}>
                                        ${d.is_multi_bank ? '<small class="text-muted d-block" style="font-size:11px;">Nomor masing-masing bank dapat diisi pada kolom "No BG Bank" di atas.</small>' : ''}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">Tanggal Jatuh Tempo (Expired Date) <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="exp_date" value="${d.exp_date || ''}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold">Scan Dokumen Bank Garansi Asli (Warkat)</label>
                                        <input type="file" class="form-control" name="warkat_file" accept=".pdf,.jpg,.jpeg,.png">
                                        ${d.warkat_file_url ? `
                                            <div class="mt-2 small text-muted">
                                                <i class="ph-bold ph-file-text text-primary me-1"></i> File scan BG saat ini: 
                                                <a href="${d.warkat_file_url}" target="_blank" class="fw-bold text-primary text-decoration-underline">Buka File Warkat</a>
                                            </div>
                                        ` : '<small class="text-muted d-block mt-1">Unggah scan file Bank Garansi asli (PDF/JPG/PNG, Maks. 10MB) jika sudah tersedia.</small>'}
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
