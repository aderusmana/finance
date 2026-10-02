<x-app-layout>
    @section('title')
        Pengajuan BG Sales
    @endsection

    @include('components.sample-table-styles')

    {{-- Breadcrumb & Title --}}
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Pengajuan Sales: Adendum & Tambah BG</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}"><i class="ph-duotone ph-bank f-s-16"></i> Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Pengajuan Sales</a></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="ph-bold ph-check-circle me-2 fs-5"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="ph-bold ph-x-circle me-2 fs-5"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="fw-bold mb-1"><i class="ph-bold ph-warning-circle me-1"></i> Terdapat kendala pada pengajuan:</div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">

            {{-- Table Controls Header: Filters & Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center mb-4 table-controls-header flex-wrap">
                <div class="d-flex align-items-center gap-2 mb-3 filter-container-responsive flex-wrap customer-filter-bar">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                    <select id="typeFilter" class="form-select select2" style="width: 170px;">
                        <option value="all">Semua Tipe</option>
                        <option value="adendum">Adendum BG</option>
                        <option value="tambah_bg">Tambah BG</option>
                    </select>
                    <select id="statusFilter" class="form-select select2" style="width: 200px;">
                        <option value="all">Semua Status</option>
                        <option value="waiting_approval">Menunggu Validasi</option>
                        <option value="completed">Approved</option>
                        <option value="rejected_by_finance">Rejected</option>
                    </select>
                    <button id="resetFilters" class="btn btn-sm btn-secondary border" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                {{-- Quick Action Buttons to Trigger Modal --}}
                <div class="ms-auto d-flex gap-2 page-action-buttons mb-3">
                    <button type="button" class="btn btn-primary shadow-sm btn-open-sales-modal" data-type="tambah_bg" id="btn-tambah-bg">
                        <i class="ph-bold ph-plus-circle me-1"></i> <span>Tambah BG</span>
                    </button>
                    <button type="button" class="btn btn-outline-warning text-dark fw-semibold shadow-sm btn-open-sales-modal" data-type="adendum" id="btn-adendum-bg">
                        <i class="ph-bold ph-pencil-simple me-1"></i> <span>Adendum BG</span>
                    </button>
                </div>
            </div>

            {{-- Info Alert --}}
            <div class="alert shadow-sm d-flex align-items-center mb-4 p-3 rounded-3" role="alert" style="background-color: #e7f1ff; border: none; border-left: 5px solid #0d6efd; color: #084298;">
                <div class="me-3">
                    <span class="d-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm" style="width: 45px; height: 45px;">
                        <i class="ph-duotone ph-info fs-3"></i>
                    </span>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color: #052c65;">Alur Pengajuan Sales Tanpa Rekomendasi Awal</h6>
                    <p class="mb-0 small" style="line-height: 1.6; color: #084298;">
                        Menu ini digunakan Sales untuk update <strong>Adendum BG</strong> atau <strong>Tambah Bank Garansi Baru</strong> langsung ke sistem melalui modal aksi. Seluruh pengajuan berstatus <em>Waiting Approval</em> dan <strong>wajib divalidasi oleh Bu Rita (Secretary Finance)</strong> sebelum masuk ke daftar aktif Bank Garansi.
                    </p>
                </div>
            </div>

            {{-- Main Table Container --}}
            <div class="main-table-container">
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="table-title mb-1"><i class="ph-duotone ph-file-plus me-2"></i> Data Pengajuan Sales</h4>
                        <small class="text-white opacity-75 f-s-12">Monitoring pengajuan Adendum dan Tambah Bank Garansi dari tim Sales.</small>
                    </div>
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-files text-white f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Total</span>
                                <span class="f-s-14 fw-bold">{{ $stats['total'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-hourglass-high text-white f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Menunggu Validasi</span>
                                <span class="f-s-14 fw-bold">{{ $stats['pending'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display align-middle" id="salesSubmissionsTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Kode Pengajuan</th>
                                <th>Customer</th>
                                <th class="text-center">Tipe</th>
                                <th>Nomor BG</th>
                                <th>Nominal (IDR)</th>
                                <th>Exp Date</th>
                                <th class="text-center">Status</th>
                                <th width="10%" class="text-center">Dokumen</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL AKSI PENGAJUAN SALES (ADENDUM & TAMBAH BG) --}}
    <div class="modal fade" id="modalSalesBg" tabindex="-1" aria-labelledby="modalSalesBgLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header py-3" style="background: linear-gradient(135deg, #1e293b 0%, #1e40af 100%); color: #fff;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 bg-white bg-opacity-10 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="ph-bold ph-file-plus fs-5 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="modalSalesBgLabel">Formulir Pengajuan Bank Garansi</h5>
                            <small class="text-white opacity-75 f-s-12">Pengajuan langsung Sales (Wajib divalidasi oleh Bu Rita - Secretary Finance)</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('sales-submissions.store') }}" method="POST" enctype="multipart/form-data" id="salesBgForm">
                    @csrf
                    <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">

                        {{-- 1. PILIH DISTRIBUTOR --}}
                        <div class="card mb-3 border bg-light-subtle">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold text-dark fs-6">
                                    <i class="ph-bold ph-buildings text-primary me-1"></i> 1. Informasi Distributor
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label small text-muted fw-semibold" for="customer_id">
                                            Distributor / Customer <span class="text-danger">*</span>
                                        </label>
                                        <select name="customer_id" id="customer_id" class="form-select select2-sales-modal" required style="width: 100%;">
                                            <option value="">-- Pilih Distributor --</option>
                                            @if(isset($customers))
                                                @foreach($customers as $c)
                                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }} ({{ $c->no_pkd ?? $c->code ?? '-' }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. TIPE PENGAJUAN --}}
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold text-dark fs-6">
                                    <i class="ph-bold ph-tag text-primary me-1"></i> 2. Jenis Pengajuan
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="submission_type" id="type_tambah" value="tambah_bg" {{ old('submission_type', 'tambah_bg') === 'tambah_bg' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary py-2 fw-semibold" for="type_tambah">
                                                <i class="ph-bold ph-plus-circle me-1"></i> Tambah Bank Garansi Baru
                                            </label>

                                            <input type="radio" class="btn-check" name="submission_type" id="type_adendum" value="adendum" {{ old('submission_type') === 'adendum' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning py-2 fw-semibold" for="type_adendum">
                                                <i class="ph-bold ph-pencil-simple me-1"></i> Adendum Bank Garansi (Update)
                                            </label>
                                        </div>
                                    </div>

                                    {{-- DROPDOWN BG EXISTING (HANYA JIKA ADENDUM) --}}
                                    <div class="col-md-12 d-none" id="container_existing_bg">
                                        <label class="form-label small text-muted fw-semibold" for="existing_bg_id">
                                            Pilih Bank Garansi yang Di-Adendum <span class="text-danger">*</span>
                                        </label>
                                        <select name="existing_bg_id" id="existing_bg_id" class="form-select select2-sales-modal" style="width: 100%;">
                                            <option value="">-- Pilih Bank Garansi Existing --</option>
                                        </select>
                                        <small class="text-muted" style="font-size: 0.75rem;">Pilih data Bank Garansi aktif distributor yang nominal atau masa berlakunya disesuaikan.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 3. RINCIAN BANK GARANSI --}}
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold text-dark fs-6">
                                    <i class="ph-bold ph-bank text-primary me-1"></i> 3. Rincian Bank Garansi
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="bank_name">
                                            Nama Bank Penerbit <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Contoh: Bank Mandiri" value="{{ old('bank_name') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="branch_name">Cabang Bank</label>
                                        <input type="text" name="branch_name" id="branch_name" class="form-control" placeholder="Contoh: Cabang Sudirman Jakarta" value="{{ old('branch_name') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small text-muted fw-semibold" for="bg_number">
                                            Nomor Bank Garansi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="bg_number" id="bg_number" class="form-control font-monospace" placeholder="Nomor resmi Bank Garansi" value="{{ old('bg_number') }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted fw-semibold" for="bg_nominal_display">
                                            Nominal Bank Garansi (IDR) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold">Rp</span>
                                            <input type="text" class="form-control fw-bold rupiah-input" id="bg_nominal_display" placeholder="0" onkeyup="formatRupiah(this)" required>
                                            <input type="hidden" name="bg_nominal" id="bg_nominal" value="{{ old('bg_nominal') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted fw-semibold" for="exp_date">
                                            Tanggal Jatuh Tempo (Expired) <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="exp_date" id="exp_date" class="form-control" value="{{ old('exp_date') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="issued_date">Tanggal Terbit</label>
                                        <input type="date" name="issued_date" id="issued_date" class="form-control" value="{{ old('issued_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="notes">Catatan / Alasan Pengajuan</label>
                                        <input type="text" name="notes" id="notes" class="form-control" placeholder="Keterangan pengajuan sales..." value="{{ old('notes') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. UPLOAD DOKUMEN --}}
                        <div class="card mb-3 border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold text-dark fs-6">
                                    <i class="ph-bold ph-upload-simple text-primary me-1"></i> 4. Upload Dokumen Bank Garansi & Lampiran
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="warkat_file">
                                            Scan Bank Garansi Asli <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" name="warkat_file" id="warkat_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                        <small class="text-muted" style="font-size: 0.75rem;">Format PDF, JPG, PNG (Maks 10MB)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted fw-semibold" for="signed_document">
                                            Dokumen Adendum / Surat Pengantar / Lampiran
                                        </label>
                                        <input type="file" name="signed_document" id="signed_document" class="form-control" accept=".pdf">
                                        <small class="text-muted" style="font-size: 0.75rem;">Opsional. Format PDF (Maks 10MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ALUR NOTICE --}}
                        <div class="alert alert-info border-0 d-flex align-items-center mb-0 p-3 rounded-3" style="background-color: #eff6ff; color: #1e40af;">
                            <i class="ph-duotone ph-shield-check fs-3 me-3 text-primary flex-shrink-0"></i>
                            <div class="small">
                                <strong>Validasi Otomatis:</strong> Setelah disubmit, notifikasi email dan link validasi langsung dikirimkan ke <strong>Bu Rita (Secretary Finance)</strong>. Bank Garansi akan otomatis aktif di BG List setelah Bu Rita menyetujui.
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light d-flex justify-content-between py-3">
                        <button type="button" class="btn btn-secondary border" data-bs-dismiss="modal">
                            <i class="ph-bold ph-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary shadow-sm px-4" id="btnSubmit">
                            <i class="ph-bold ph-paper-plane-tilt me-1"></i> Submit Pengajuan ke Bu Rita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function formatRupiah(element) {
            let value = element.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            element.value = rupiah;
            document.getElementById('bg_nominal').value = value.replace(/\./g, '');
        }

        $(document).ready(function() {
            // Initialize main page select2
            $('#typeFilter, #statusFilter').select2({ theme: 'bootstrap-5' });

            const table = $('#salesSubmissionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sales-submissions.index') }}",
                    data: function(d) {
                        d.submission_type = $('#typeFilter').val();
                        d.status = $('#statusFilter').val();
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center'},
                    {data: 'form_code', name: 'form_code'},
                    {data: 'customer_name', name: 'recommendation.customer.name'},
                    {data: 'submission_type', name: 'submission_type', className: 'text-center'},
                    {data: 'bg_number', name: 'bg_number'},
                    {data: 'bg_nominal', name: 'bg_nominal', className: 'text-end fw-bold'},
                    {data: 'exp_date', name: 'exp_date'},
                    {data: 'status', name: 'status', className: 'text-center'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });

            $('#typeFilter, #statusFilter').on('change', function() { table.ajax.reload(); });
            $('#resetFilters').on('click', function() {
                $('#typeFilter, #statusFilter').val('all').trigger('change');
            });

            // Initialize select2 inside modal when modal opens
            $('#modalSalesBg').on('shown.bs.modal', function () {
                $('.select2-sales-modal').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalSalesBg'),
                    width: '100%'
                });
            });

            // Action Buttons to open modal with preselected type
            $('.btn-open-sales-modal').on('click', function() {
                const type = $(this).data('type');
                if (type === 'adendum') {
                    $('#type_adendum').prop('checked', true).trigger('change');
                } else {
                    $('#type_tambah').prop('checked', true).trigger('change');
                }
                $('#modalSalesBg').modal('show');
            });

            // If URL has ?action=create or ?type=adendum/tambah_bg, open modal automatically
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create' || urlParams.has('type')) {
                const reqType = urlParams.get('type') || 'tambah_bg';
                if (reqType === 'adendum') {
                    $('#type_adendum').prop('checked', true).trigger('change');
                } else {
                    $('#type_tambah').prop('checked', true).trigger('change');
                }
                $('#modalSalesBg').modal('show');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Auto-open modal if validation errors occurred on submit
            @if($errors->any())
                $('#modalSalesBg').modal('show');
            @endif

            // Initialize display nominal if old value exists
            const rawNominal = $('#bg_nominal').val();
            if (rawNominal) {
                $('#bg_nominal_display').val(new Intl.NumberFormat('id-ID').format(rawNominal));
            }

            function toggleExistingBgContainer() {
                const isAdendum = $('#type_adendum').is(':checked');
                if (isAdendum) {
                    $('#container_existing_bg').removeClass('d-none');
                    $('#existing_bg_id').prop('required', true);
                    loadCustomerBgs();
                } else {
                    $('#container_existing_bg').addClass('d-none');
                    $('#existing_bg_id').prop('required', false);
                }
            }

            $('input[name="submission_type"]').on('change', toggleExistingBgContainer);
            $('#customer_id').on('change', loadCustomerBgs);

            function loadCustomerBgs() {
                const custId = $('#customer_id').val();
                const isAdendum = $('#type_adendum').is(':checked');

                if (!custId || !isAdendum) {
                    return;
                }

                $('#existing_bg_id').html('<option value="">Memuat data Bank Garansi...</option>');

                $.get("{{ url('bg/sales-submissions/customer-bgs') }}/" + custId, function(res) {
                    if (res.success) {
                        let html = '<option value="">-- Pilih Bank Garansi Existing --</option>';
                        if (res.bgs && res.bgs.length > 0) {
                            res.bgs.forEach(function(bg) {
                                const bank = (bg.details && bg.details.length > 0) ? bg.details[0].bank_name : (bg.bank_name || 'Bank');
                                const nom = new Intl.NumberFormat('id-ID').format(bg.bg_nominal);
                                const exp = bg.exp_date ? bg.exp_date.substring(0, 10) : '-';
                                html += `<option value="${bg.id}">${bg.bg_number} - ${bank} (Rp ${nom}) - Exp: ${exp}</option>`;
                            });
                        } else {
                            html += '<option value="" disabled>Tidak ada Bank Garansi aktif untuk customer ini</option>';
                        }
                        $('#existing_bg_id').html(html);
                    }
                });
            }

            // Autofill details when existing BG is picked
            $('#existing_bg_id').on('change', function() {
                const bgId = $(this).val();
                if (!bgId) return;

                const custId = $('#customer_id').val();
                $.get("{{ url('bg/sales-submissions/customer-bgs') }}/" + custId, function(res) {
                    if (res.success && res.bgs) {
                        const picked = res.bgs.find(b => b.id == bgId);
                        if (picked) {
                            if (!$('#bg_number').val()) $('#bg_number').val(picked.bg_number);
                            if (!$('#bg_nominal').val()) {
                                $('#bg_nominal').val(picked.bg_nominal);
                                $('#bg_nominal_display').val(new Intl.NumberFormat('id-ID').format(picked.bg_nominal));
                            }
                            if (picked.details && picked.details.length > 0) {
                                if (!$('#bank_name').val()) $('#bank_name').val(picked.details[0].bank_name);
                                if (!$('#branch_name').val()) $('#branch_name').val(picked.details[0].branch_name);
                            }
                        }
                    }
                });
            });

            // Initial check
            toggleExistingBgContainer();

            $('#salesBgForm').on('submit', function() {
                $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');
            });
        });
    </script>
    @endpush
</x-app-layout>
