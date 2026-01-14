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
            <div class="d-flex justify-content-between align-items-center mb-4">
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
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                            <select id="statusFilter" class="form-select select2" style="width: 180px;">
                                <option value="all">Show All Active</option>
                                <option value="pending_print">Pending Print</option>
                                <option value="awaiting_upload">Awaiting Upload</option>
                                <option value="uploaded">Uploaded (Need Review)</option>
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
                            <h5 class="fw-bold mb-1" style="color: #052c65;">Panduan Proses Approval</h5>
                            <p class="mb-0 small" style="line-height: 2.5; color: #084298;">
                                1. Klik tombol <span class="badge bg-primary text-light border border-warning shadow-sm"><i class="ph-bold ph-file-search me-1"></i> Review & Process</span> pada kolom <b>Signed Doc</b> untuk memeriksa dokumen, mengoreksi data, dan melanjutkan ke <b>Lampiran D</b>.<br>
                                2. Tombol <span class="badge bg-warning text-light border"><i class="ph-bold ph-pencil-simple"></i></span> pada kolom <i>Action</i> hanya digunakan untuk <b>Upload Ulang / Edit Administrasi</b> (Tanpa Approval).
                            </p>
                        </div>
                    </div>

                    {{-- Table Active --}}
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-primary text-white">
                            <h4 class="table-title mb-1"><i class="ph-duotone ph-list-checks me-2"></i> Active To-Do List</h4>
                            <small class="opacity-75 f-s-12">Daftar pengajuan yang memerlukan tindakan.</small>
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
                            <h6 class="fw-bold mb-0">Arsip Dokumen Selesai</h6>
                            <small>Data di bawah ini adalah pengajuan yang telah selesai (Completed/Approved). Bersifat Read-Only.</small>
                        </div>
                    </div>

                    {{-- Table History --}}
                    <div class="main-table-container">
                        <div class="table-header-enhanced bg-success text-white">
                            <h4 class="table-title mb-1"><i class="ph-bold ph-check-circle me-2"></i> Completed Archives</h4>
                            <small class="opacity-75 f-s-12">Riwayat pengajuan Bank Garansi yang telah disetujui.</small>
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
                        <small class="text-muted">Buat pengajuan baru atau edit data administrasi.</small>
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
                                           placeholder="Contoh: NEW-20250112-ABCD-1">
                                </div>
                                <div class="form-text small text-muted"><i class="ph-bold ph-info me-1"></i> Gunakan format yang sesuai dengan standar perusahaan.</div>
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
                                        <span class="text-danger fst-italic" id="upload-note">* Wajib diupload untuk pengajuan baru.</span>
                                    </div>

                                    {{-- Preview Link if Edit --}}
                                    <div id="current_file_preview" class="d-none mt-2 p-2 bg-white border rounded d-flex align-items-center gap-2">
                                        <i class="ph-fill ph-check-circle text-success fs-5"></i>
                                        <span class="small text-success fw-bold">File sudah ada. Upload ulang untuk mengganti.</span>
                                        <a href="#" id="link_view_file_modal" target="_blank" class="btn btn-sm btn-outline-success ms-auto">Lihat File</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3 border-top-0">
                        <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                            <i class="ph-bold ph-paper-plane-right me-2"></i> Simpan & Upload
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

                {{-- Footer Action (Hanya muncul jika status process) --}}
                <div class="modal-footer bg-white shadow-lg py-3" id="viewFileFooter" style="z-index: 1050;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div>
                            <button type="button" class="btn btn-warning text-white fw-bold" id="btn-trigger-edit">
                                <i class="ph-bold ph-pencil-simple me-1"></i> Edit Data
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success fw-bold px-4" id="btn-trigger-approve">
                                <i class="ph-bold ph-check-circle me-1"></i> Approve & Process
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Modal Edit Data (Correction) --}}
    <div class="modal fade" id="editBgDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="ph-bold ph-pencil"></i> Koreksi Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBgForm">
                    @csrf
                    <input type="hidden" name="submission_id" id="edit_submission_id">
                    <input type="hidden" name="action_type" value="edit_submit">
                    <div class="modal-body">
                        <div id="bankDetailsContainer"></div> {{-- Diisi AJAX --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">Simpan Perubahan</button>
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
                $('.select2-modal').select2({ dropdownParent: $('#submissionModal'), theme: 'bootstrap-5', placeholder: 'Pilih Customer...' });

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

                // --- DIRECT APPROVE ---
                $('#btn-trigger-approve').click(function() {
                    Swal.fire({
                        title: 'Konfirmasi Approve',
                        text: "Pastikan dokumen sudah sesuai. Status akan berubah menjadi Completed.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Approve',
                        confirmButtonColor: '#198754'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', currentSubmissionId);
                            Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });

                            $.post(url, { _token: "{{ csrf_token() }}", action_type: 'direct_submit' }, function(res) {
                                if(res.success) {
                                    $('#viewFileModal').modal('hide');
                                    Swal.fire('Success', res.message, 'success');
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
                                    <div class="col-12"><h6 class="fw-bold text-primary border-bottom pb-2">A. Informasi & Keuangan</h6></div>
                                    <div class="col-md-12"><label class="small fw-bold">1. Nama</label><input type="text" class="form-control" name="nama_distributor" value="${d.nama_distributor}"></div>
                                    <div class="col-md-6"><label class="small fw-bold">2. Kota</label><input type="text" class="form-control" name="kota" value="${d.kota}"></div>
                                    <div class="col-md-6"><label class="small fw-bold">3. Wilayah</label><input type="text" class="form-control" name="wilayah_kerja" value="${d.wilayah_kerja}"></div>

                                    <div class="col-md-6">
                                        <label class="small fw-bold">4. Rata-rata Sales (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="rata_rata_penjualan" value="${formatRupiah(d.rata_rata_penjualan)}">
                                    </div>
                                    <div class="col-md-3"><label class="small fw-bold">5. TOP</label>
                                        <input type="number" class="form-control" name="syarat_pembayaran" value="${d.syarat_pembayaran}">
                                    </div>
                                    <div class="col-md-3"><label class="small fw-bold">6. Lead Time</label>
                                        <input type="number" class="form-control" name="lead_time" value="${d.lead_time}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">7. Faktor Fluktuasi (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="faktor_fluktuasi" value="${d.faktor_fluktuasi}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold">8. Limit Kredit (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="limit_kredit" value="${formatRupiah(d.limit_kredit)}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold">9. Nilai BG Ditetapkan (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="nilai_bg_ditetapkan" value="${formatRupiah(d.nilai_bg_ditetapkan)}">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-primary border-bottom pb-2 mt-4">B. Rincian Bank</h6>
                            `;

                            if(d.details) {
                                d.details.forEach((item, index) => {
                                    html += `
                                        <div class="card mb-2 border-start border-3 border-primary">
                                            <div class="card-body p-2">
                                                <input type="hidden" name="details[${item.id}][id]" value="${item.id}">
                                                <div class="d-flex justify-content-between mb-1"><strong class="text-primary small">Bank ${index+1}</strong></div>
                                                <div class="row g-2">
                                                    <div class="col-md-4"><label class="small text-muted">Bank</label><input type="text" class="form-control form-control-sm" name="details[${item.id}][bank_name]" value="${item.bank_name}"></div>
                                                    <div class="col-md-4"><label class="small text-muted">Cabang</label><input type="text" class="form-control form-control-sm" name="details[${item.id}][branch_name]" value="${item.branch_name}"></div>
                                                    <div class="col-md-4">
                                                        <label class="small text-muted">Nominal</label>
                                                        <input type="text" class="form-control form-control-sm rupiah-input" name="details[${item.id}][nominal]" value="${formatRupiah(item.nominal)}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });
                            }

                            $('#bankDetailsContainer').html(html);
                            $('#editBgDataModal').modal('show');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    });
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

                // --- SAVE EDIT FORM (CLEANING DATA SEBELUM KIRIM) ---
                $('#editBgForm').on('submit', function(e) {
                    e.preventDefault();

                    // [UPDATE] Gunakan serializeArray agar bisa dimanipulasi
                    let formDataArray = $(this).serializeArray();

                    // Loop untuk membersihkan titik (.) pada field rupiah sebelum dikirim ke Controller
                    formDataArray.forEach(function(item) {
                        // Cek jika field adalah field uang
                        if (['rata_rata_penjualan', 'limit_kredit', 'nilai_bg_ditetapkan'].includes(item.name) || item.name.includes('[nominal]')) {
                            // Hapus titik agar menjadi angka murni (contoh: 2.000.000 -> 2000000)
                            item.value = item.value.replace(/\./g, '');
                        }
                    });

                    let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', $('#edit_submission_id').val());

                    Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                    // Gunakan $.param untuk mengubah array kembali menjadi query string
                    $.post(url, $.param(formDataArray), function(res) {
                        if(res.success) {
                            Swal.fire('Success', res.message, 'success');
                            $('#editBgDataModal').modal('hide');
                            sampleTable.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
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
                    $('#upload-note').text('* Wajib diupload untuk pengajuan baru.');

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
                            $('#link_view_file_modal').attr('href', "{{ asset('') }}" + data.signed_document_path);

                            // File jadi opsional kalau edit dan file sudah ada
                            $('#req-star').addClass('d-none');
                            $('#upload-note').text('Biarkan kosong jika tidak ingin mengubah file.');
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
                            title: 'Dokumen Wajib Diupload',
                            text: 'Untuk pengajuan baru, Anda wajib mengupload dokumen yang telah ditandatangani.',
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
