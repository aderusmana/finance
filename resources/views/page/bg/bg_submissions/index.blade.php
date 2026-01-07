<x-app-layout>
    @section('title', 'BG Submissions')
    @include('components.sample-table-styles')

    {{-- Breadcrumb & Header Tetap Sama --}}
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Submissions</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Submissions</a></li>
            </ul>
        </div>
    </div>

    {{-- Main Content Tetap Sama --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Status</option>
                        <option value="pending_print">Pending Print</option>
                        <option value="awaiting_upload">Awaiting Upload</option>
                        <option value="uploaded">Uploaded</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-primary" type="button" id="btn-create">
                        <i class="ph-bold ph-plus"></i> <span>New Submission</span>
                    </button>
                </div>
            </div>

            <div class="alert shadow-sm d-flex align-items-center mb-4 p-3 rounded-3" role="alert" style="background-color: #e7f1ff; border: none; border-left: 5px solid #0d6efd; color: #084298;">
                <div class="me-3">
                    <span class="d-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm" style="width: 45px; height: 45px;">
                        <i class="ph-duotone ph-info fs-3"></i>
                    </span>
                </div>
                <div>
                    <h5 class="fw-bold mb-1" style="color: #052c65;">Panduan Proses Approval</h5>
                    <p class="mb-0 small" style="line-height: 2.5; color: #084298;">
                        1. Klik tombol <span class="status-badge-lg bg-primary text-light border border-warning shadow-sm"><i class="ph-bold ph-file-search me-1"></i> Review & Process</span> pada kolom <b>Signed Doc</b> untuk memeriksa dokumen, mengoreksi data, dan melanjutkan ke <b>Lampiran D</b>.<br>
                        2. Tombol <span class="status-badge-lg bg-warning text-light border"><i class="ph-bold ph-pencil-simple"></i></span> pada kolom <i>Action</i> hanya digunakan untuk <b>Upload Ulang / Edit Administrasi</b> (Tanpa Approval).
                    </p>
                </div>
            </div>

            <div class="main-table-container">
                    <div class="table-header-enhanced">
                        <h4 class="table-title mb-1"><i class="ph-duotone ph-files me-2"></i> Submission Data</h4>
                        <small class="text-white opacity-75 f-s-12">Upload and manage signed BG application forms.</small>
                    </div>
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Customer (Recommendation)</th>
                                <th>Form Code</th>
                                <th>Signed Doc</th>
                                <th class="text-center">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel">Manage Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="submissionForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="submissionId">
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Dropdown Recommendation --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Select Recommendation <span class="text-danger">*</span></label>
                                <select name="bg_recommendation_id" id="bg_recommendation_id" class="form-select select2-modal" required style="width: 100%;">
                                    <option></option>
                                    @foreach($recommendations as $r)
                                        <option value="{{ $r->id }}">
                                            {{ $r->customer->name ?? 'Unknown' }} - (Rec. Limit: Rp {{ number_format($r->credit_limit_updated, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Form Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="form_code" id="form_code" required placeholder="e.g. SUB-...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Submitted At</label>
                                <input type="date" class="form-control" name="submitted_at" id="submitted_at">
                            </div>

                            {{-- File Upload Area --}}
                            <div class="col-12">
                                <div class="card bg-light border-dashed">
                                    <div class="card-body text-center p-3">
                                        <h6 class="fw-bold text-dark"><i class="ph-bold ph-upload-simple"></i> Upload Signed Document</h6>
                                        <p class="text-muted f-s-12 mb-2">If customer cannot upload via email, Admin can upload here.</p>

                                        <input type="file" name="signed_document" id="signed_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">

                                        <div id="current_file_info" class="mt-2 d-none">
                                            <div class="alert alert-success d-flex align-items-center justify-content-center gap-2 py-1 mb-0">
                                                <i class="ph-bold ph-check-circle"></i>
                                                <span>File already exists. Uploading new file will replace it.</span>
                                                <a href="#" id="link_view_file" target="_blank" class="fw-bold text-decoration-underline text-success">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Display (Read Only agar user tau statusnya berubah) --}}
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Current Status</label>
                                <select name="status" id="status" class="form-select select2-modal">
                                    <option value="pending_print">Pending Print</option>
                                    <option value="awaiting_upload">Awaiting Upload</option>
                                    <option value="uploaded">Uploaded</option>
                                    <option value="reviewed">Reviewed</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <div class="form-text text-primary"><i class="ph-bold ph-info"></i> Uploading a file will automatically set status to <b>Uploaded</b>.</div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Save & Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- GANTI BAGIAN MODAL VIEW FILE YANG LAMA DENGAN INI --}}
    <div class="modal fade" id="viewFileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="height: 90vh;">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title text-white"><i class="ph-bold ph-file-text me-2"></i> Document Preview & Action</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0 bg-light position-relative" id="fileContentArea" style="height: 100%;">
                    </div>

                {{-- FOOTER ACTION BARU --}}
                <div class="modal-footer bg-white shadow-lg py-3" style="z-index: 1050;">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div>
                            <small class="text-muted d-block">Ada data yang salah?</small>
                            {{-- TOMBOL EDIT --}}
                            <button type="button" class="btn btn-warning text-white fw-bold" id="btn-trigger-edit">
                                <i class="ph-bold ph-pencil-simple me-1"></i> Edit Data & Submit
                            </button>
                        </div>

                        <div>
                            <small class="text-muted d-block text-end">Data sudah sesuai?</small>
                            {{-- TOMBOL DIRECT SUBMIT --}}
                            <button type="button" class="btn btn-success fw-bold px-4" id="btn-trigger-approve">
                                <i class="ph-bold ph-check-circle me-1"></i> Approve & Notify Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAMBAHKAN MODAL BARU INI UNTUK EDIT DATA --}}
    <div class="modal fade" id="editBgDataModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="ph-bold ph-pencil"></i> Koreksi Data Bank Garansi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBgForm">
                    @csrf
                    <input type="hidden" name="submission_id" id="edit_submission_id">
                    <input type="hidden" name="action_type" value="edit_submit"> {{-- Penanda Action --}}

                    <div class="modal-body">
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="ph-bold ph-info me-2 fs-4"></i>
                            <small>Mengedit data ini akan mengirimkan notifikasi approval ke <b>Manager Finance</b>.</small>
                        </div>

                        {{-- Container Dynamic Fields --}}
                        <div id="bankDetailsContainer">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="ph-bold ph-paper-plane-right me-1"></i> Simpan & Kirim ke Finance
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
                $('.select2').select2({ theme: 'bootstrap-5' });
                $('.select2-modal').select2({ dropdownParent: $('#submissionModal'), theme: 'bootstrap-5', placeholder: 'Select Option' });

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                let currentSubmissionId = null;

                const table = $('#sampleTable').DataTable({
                    processing: true, serverSide: true,
                    ajax: {
                        url: "{{ route('bg-submissions.index') }}",
                        data: function(d) { d.status = $('#statusFilter').val(); }
                    },
                    columns: [
                        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                        { data: 'customer_name', name: 'recommendation.customer.name', className: 'fw-bold' },
                        { data: 'form_code', name: 'form_code' },
                        { data: 'file', name: 'signed_document_path', className: 'text-center', orderable: false, searchable: false },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ]
                });

                $('#statusFilter').change(function() { table.ajax.reload(); });

                function formatRupiah(angka, prefix = 'Rp ') {
                    var number_string = angka.toString().replace(/[^,\d]/g, '').toString();

                    if (number_string === '') return prefix;
                    var split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix + rupiah;
                }

                function cleanNumber(value) {
                    if (!value) return 0;
                    return value.toString().replace(/[^0-9]/g, '');
                }

                $(document).on('keyup', '.rupiah-input', function(e) {
                    if (e.keyCode >= 37 && e.keyCode <= 40) return;
                    $(this).val(formatRupiah($(this).val()));
                });

                // Create
                $('#btn-create').click(function() {
                    $('#submissionForm')[0].reset();
                    $('#submissionId').val('');
                    $('#formMethod').val('POST');
                    $('#bg_recommendation_id').val(null).trigger('change');
                    $('#status').val('pending_print').trigger('change'); // Default manual create
                    $('#current_file_info').addClass('d-none');
                    $('#modalLabel').text('Create New Submission');
                    $('#submissionModal').modal('show');
                });

                // Submit
                $('#submissionForm').on('submit', function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    const id = $('#submissionId').val();
                    let url = "{{ route('bg-submissions.store') }}";

                    if(id) {
                        url = "{{ route('bg-submissions.update', ':id') }}".replace(':id', id);
                        formData.append('_method', 'PUT');
                    }

                    Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: url, method: 'POST', data: formData, processData: false, contentType: false,
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#submissionModal').modal('hide');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Error occurred', 'error');
                        }
                    });
                });

                $(document).on('click', '.btn-view-file', function() {
                    let url = $(this).data('url');
                    let id = $(this).data('id');
                    let status = $(this).data('status');

                    currentSubmissionId = id;

                    let container = $('#fileContentArea');
                    container.html('<div class="d-flex h-100 justify-content-center align-items-center"><div class="spinner-border text-primary"></div></div>');

                    if (status === 'completed') {
                        $('#viewFileModal .modal-footer').hide(); 
                        $('#viewFileModal .modal-header').removeClass('bg-dark').addClass('bg-success');
                        $('#viewFileModal .modal-title').html('<i class="ph-bold ph-check-circle me-2"></i> Document Preview (Final)');
                    } else {
                        $('#viewFileModal .modal-footer').show();
                        $('#viewFileModal .modal-header').removeClass('bg-success').addClass('bg-dark');
                        $('#viewFileModal .modal-title').html('<i class="ph-bold ph-file-text me-2"></i> Document Preview & Action');
                    }

                    $('#viewFileModal').modal('show');

                    let extension = url.split('.').pop().toLowerCase();
                    setTimeout(() => {
                        if (['jpg', 'jpeg', 'png'].includes(extension)) {
                            container.html(`<img src="${url}" class="img-fluid h-100 w-100" style="object-fit: contain;">`);
                        } else {
                            container.html(`<iframe src="${url}" style="width: 100%; height: 100%; border: none;"></iframe>`);
                        }
                    }, 500);
                });

                // --- BAGIAN EDIT DATA (Updated Logic) ---
                $('#btn-trigger-edit').click(function() {
                    $('#viewFileModal').modal('hide');
                    Swal.fire({ title: 'Loading Data...', didOpen: () => Swal.showLoading() });

                    let url = "{{ route('bg-submissions.get-edit-data', ':id') }}".replace(':id', currentSubmissionId);

                    $.get(url, function(res) {
                        Swal.close();
                        if(res.success) {
                            let d = res.data;
                            $('#edit_submission_id').val(d.submission_id);

                            const valAvg = d.rata_rata_penjualan ? Math.floor(d.rata_rata_penjualan) : 0;
                            const valLimit = d.limit_kredit ? Math.floor(d.limit_kredit) : 0;
                            const valSetBg = d.nilai_bg_ditetapkan ? Math.floor(d.nilai_bg_ditetapkan) : 0;
                            const valDiserahkanTotal = d.nilai_bg_diserahkan ? Math.floor(d.nilai_bg_diserahkan) : 0;

                            let html = `
                                <input type="hidden" name="bg_id" value="${d.bg_id}">
                                <div class="row g-3">
                                    <div class="col-12"><h6 class="fw-bold text-primary border-bottom pb-2">A. Informasi Distributor</h6></div>

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">1. Nama Distributor</label>
                                        <input type="text" class="form-control" name="nama_distributor" value="${d.nama_distributor}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">2. Kota</label>
                                        <input type="text" class="form-control" name="kota" value="${d.kota}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">3. Wilayah Kerja</label>
                                        <input type="text" class="form-control" name="wilayah_kerja" value="${d.wilayah_kerja}">
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">4. Periode</label>
                                        <input type="text" class="form-control bg-light" name="periode" value="${d.periode}" readonly>
                                    </div>

                                    <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">B. Data Keuangan & Limit</h6></div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">5. Rata-rata Penjualan (Rp)</label>
                                        <input type="text" class="form-control rupiah-input" name="rata_rata_penjualan"
                                               value="${formatRupiah(valAvg)}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">6. TOP (Hari)</label>
                                        <input type="number" class="form-control" name="syarat_pembayaran" value="${d.syarat_pembayaran}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">7. Lead Time (Hari)</label>
                                        <input type="number" class="form-control" name="lead_time" value="${d.lead_time}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">8. Faktor Fluktuasi (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="faktor_fluktuasi" value="${d.faktor_fluktuasi}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">9. Limit Kredit (Rp)</label>
                                        <input type="text" class="form-control fw-bold rupiah-input" name="limit_kredit"
                                               value="${formatRupiah(valLimit)}">
                                    </div>

                                    <div class="col-12 mt-4"><h6 class="fw-bold text-primary border-bottom pb-2">C. Nilai Bank Garansi</h6></div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">10. Nilai BG Ditetapkan (Rp)</label>
                                        <input type="text" class="form-control border-success rupiah-input" name="nilai_bg_ditetapkan"
                                               value="${formatRupiah(valSetBg)}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">11. Total BG Diserahkan (Rp)</label>
                                        <input type="text" class="form-control border-primary bg-light fw-bold"
                                               value="${formatRupiah(valDiserahkanTotal)}" readonly>
                                        <div class="form-text text-muted small">Total akumulasi dari rincian bank di bawah.</div>
                                        <input type="hidden" name="nilai_bg_diserahkan" value="${valDiserahkanTotal}">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-primary border-bottom pb-2 mt-3">D. Rincian Bank</h6>
                            `;

                            if(d.details) {
                                d.details.forEach((item, index) => {
                                    let nominalDetail = item.nominal ? Math.floor(item.nominal) : 0;

                                    html += `
                                        <div class="card mb-2 border-start border-2 border-primary">
                                            <div class="card-body p-2">
                                                <input type="hidden" name="details[${item.id}][id]" value="${item.id}">

                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong class="text-primary small">Bank Ke-${index+1}</strong>
                                                </div>

                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="small text-muted fw-bold">Bank</label>
                                                        <input type="text" class="form-control form-control-sm" name="details[${item.id}][bank_name]" value="${item.bank_name}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small text-muted fw-bold">Cabang</label>
                                                        <input type="text" class="form-control form-control-sm" name="details[${item.id}][branch_name]" value="${item.branch_name}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small text-muted fw-bold">Nominal</label>
                                                        <input type="text" class="form-control form-control-sm rupiah-input"
                                                               name="details[${item.id}][nominal]"
                                                               value="${formatRupiah(nominalDetail)}">
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

                $('#editBgForm').on('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi Perubahan',
                        text: "Apakah Anda yakin data yang diedit sudah benar? Permintaan approval akan dikirim ke Manager Finance.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#eab308',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Simpan & Kirim',
                        cancelButtonText: 'Cek Lagi'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $('#editBgForm .rupiah-input').each(function() {
                                let raw = $(this).val();
                                let clean = cleanNumber(raw);
                                $(this).val(clean);
                            });

                            let formData = $('#editBgForm').serialize();
                            let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', $('#edit_submission_id').val());

                            Swal.fire({ title: 'Processing...', text: 'Updating data & Notifying Finance Manager', didOpen: () => Swal.showLoading() });

                            $.post(url, formData, function(res) {
                                if(res.success) {
                                    Swal.fire('Terkirim!', res.message, 'success');
                                    $('#editBgDataModal').modal('hide');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                    $('#editBgForm .rupiah-input').each(function() {
                                        $(this).val(formatRupiah($(this).val()));
                                    });
                                }
                            }).fail(function(xhr) {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            });
                        }
                    });
                });

                $('#btn-trigger-approve').click(function() {
                    Swal.fire({
                        title: 'Konfirmasi Approve',
                        text: "Data dianggap sudah benar. Email akan dikirim ke Customer, Dept Head Sales, dan Manager Finance.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        confirmButtonText: 'Ya, Approve & Kirim Email'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = "{{ route('bg-submissions.process-review', ':id') }}".replace(':id', currentSubmissionId);

                            Swal.fire({ title: 'Sending Emails...', didOpen: () => Swal.showLoading() });

                            $.post(url, {
                                _token: "{{ csrf_token() }}",
                                action_type: 'direct_submit'
                            }, function(res) {
                                if(res.success) {
                                    $('#viewFileModal').modal('hide');
                                    Swal.fire('Approved!', res.message, 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error', res.message, 'error');
                                }
                            });
                        }
                    });
                });

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
                        $('#status').val(data.status).trigger('change');

                        if(data.submitted_at) $('#submitted_at').val(data.submitted_at.substring(0, 10));

                        if(data.signed_document_path) {
                            $('#current_file_info').removeClass('d-none');
                            $('#link_view_file').attr('href', "{{ asset('') }}" + data.signed_document_path);
                        } else {
                            $('#current_file_info').addClass('d-none');
                        }

                        $('#modalLabel').text('Manage Submission (Upload/Edit)');
                        $('#submissionModal').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    });
                });

                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    let url = "{{ route('bg-submissions.destroy', ':id') }}".replace(':id', id);
                    Swal.fire({
                        title: 'Are you sure?', text: "File attached will also be deleted.", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url, method: 'DELETE', data: { _token: "{{ csrf_token() }}" },
                                success: function(res) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    table.ajax.reload();
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
