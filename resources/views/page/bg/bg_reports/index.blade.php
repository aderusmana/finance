<x-app-layout>
    @section('title', 'Document Center')
    @include('components.sample-table-styles')

    {{-- HEADER & BREADCRUMB --}}
    <div class="row m-1 mb-4">
        <div class="col-12">
            <h4 class="main-title fw-bold text-dark">Document Center</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Reports & Documents</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- HERO INFO CARD --}}
            <div class="card border-0 shadow-sm overflow-hidden mb-4" style="border-radius: 16px;">
                <div class="card-body p-4 d-flex align-items-center gap-4"
                     style="background: linear-gradient(120deg, #eff6ff 0%, #f8fafc 100%); border: 1px solid #dbeafe;">
                    <div class="bg-white rounded-circle p-3 shadow-sm text-primary d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="ph-duotone ph-printer fs-2"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-2">Pusat Cetak Dokumen</h5>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.6; max-width: 700px;">
                            Gunakan halaman ini untuk mencetak dokumen fisik secara massal. Anda dapat memilih beberapa customer sekaligus dan mengunduhnya dalam bentuk <strong>ZIP</strong> (terpisah) atau <strong>PDF Gabungan</strong> (merged).
                        </p>
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="row mb-4">
                <div class="col-12">

                    {{-- NAVIGATION TABS --}}
                    <ul class="nav nav-pills nav-pills-custom row g-3 mb-4" id="reportTabs" role="tablist">
                        {{-- TAB 1: TRANSACTION --}}
                        <li class="nav-item col-md-6" role="presentation">
                            <button class="nav-link active w-100 text-start" id="trans-tab" data-bs-toggle="pill" data-bs-target="#trans" type="button">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-wrapper"><i class="ph-bold ph-files"></i></div>
                                    <div>
                                        <div class="fw-bold fs-6 mb-1 text-dark">Transaction Documents</div>
                                        <div class="small text-muted">Cetak Lampiran D & Formulir Pengajuan.</div>
                                    </div>
                                </div>
                            </button>
                        </li>
                        {{-- TAB 2: LETTERS --}}
                        <li class="nav-item col-md-6" role="presentation">
                            <button class="nav-link w-100 text-start" id="letters-tab" data-bs-toggle="pill" data-bs-target="#letters" type="button">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-wrapper"><i class="ph-bold ph-envelope-open"></i></div>
                                    <div>
                                        <div class="fw-bold fs-6 mb-1 text-dark">Expiring Letters</div>
                                        <div class="small text-muted">Cetak Surat Pengantar Bank & Distributor.</div>
                                    </div>
                                </div>
                            </button>
                        </li>
                    </ul>

                    {{-- TAB CONTENT AREA --}}
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-0">
                            <div class="tab-content" id="reportTabContent">

                                {{-- CONTENT 1: TRANSACTION LIST --}}
                                <div class="tab-pane fade show active" id="trans" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-white rounded-top-4 gap-3 flex-wrap">

                                        {{-- KIRI: JUDUL --}}
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                                <i class="ph-fill ph-stack text-primary"></i> Daftar Dokumen Transaksi
                                            </h6>
                                            <div class="small text-muted mt-1">Data real-time dari database</div>
                                        </div>

                                        {{-- KANAN: CUSTOM SEARCH INPUT --}}
                                        <div class="position-relative" style="min-width: 250px;">
                                            <input type="text" id="searchTrans" class="form-control ps-5 rounded-pill border-0 bg-light" placeholder="Cari Ref No / Customer...">
                                            <i class="ph-bold ph-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table w-100 mb-0" id="transTable" style="border-collapse: separate; border-spacing: 0 4px;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input checkbox-trans" type="checkbox" id="checkAllTrans">
                                                        </div>
                                                    </th>
                                                    <th width="5%" class="text-center">No</th>
                                                    <th width="15%">Date</th>
                                                    <th width="20%">Reference Code</th>
                                                    <th width="25%">Customer</th>
                                                    <th width="15%" class="text-center">Status</th>
                                                    <th width="15%" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                                {{-- CONTENT 2: LETTERS LIST --}}
                                <div class="tab-pane fade" id="letters" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-white rounded-top-4 gap-3 flex-wrap">

                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                                <i class="ph-fill ph-clock-countdown text-warning"></i> Daftar BG Jatuh Tempo
                                            </h6>
                                            <div class="small text-muted mt-1">Menampilkan BG Aktif yang mendekati Expired</div>
                                        </div>

                                        {{-- CUSTOM SEARCH INPUT --}}
                                        <div class="position-relative" style="min-width: 250px;">
                                            <input type="text" id="searchLetters" class="form-control ps-5 rounded-pill border-0 bg-light" placeholder="Cari No BG / Customer...">
                                            <i class="ph-bold ph-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table w-100 mb-0" id="lettersTable" style="border-collapse: separate; border-spacing: 0 4px;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input checkbox-letter" type="checkbox" id="checkAllLetters">
                                                        </div>
                                                    </th>
                                                    <th width="5%" class="text-center">No</th>
                                                    <th width="20%">BG Number</th>
                                                    <th width="25%">Customer</th>
                                                    <th width="15%">Exp. Date</th>
                                                    <th width="20%">Nominal</th>
                                                    <th width="10%" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- FLOATING ACTION BAR (Untuk Bulk Download) --}}
    <div id="bulkActionBar" class="bulk-action-bar">
        <div class="d-flex align-items-center gap-3">
            <span class="selected-badge"><span id="countSelected">0</span> Selected</span>
            <span class="small text-white-50 d-none d-md-block" id="selectionLabel">Ready to print</span>
        </div>

        <div class="bulk-separator d-none d-md-block"></div>

        {{-- FORM DOWNLOAD MASSAL --}}
        <form id="bulkDownloadForm" action="{{ route('bg-reports.bulk-download') }}" method="POST" target="_blank" class="d-flex align-items-center gap-3">
            @csrf
            <input type="hidden" name="ids[]" id="bulkIdsInput">
            <input type="hidden" name="category" id="bulkCategoryInput">

            {{-- 1. PILIH JENIS FILE --}}
            <select name="doc_type" id="bulkDocType" class="form-select form-select-sm border-0" style="border-radius: 20px; width: 180px; cursor: pointer; background-color: rgba(255,255,255,0.9);">
                {{-- Opsi diisi via JS --}}
            </select>

            {{-- 2. PILIH OUTPUT MODE --}}
            <div class="output-mode-group">
                <input type="radio" class="output-mode-input" name="output_mode" id="modeZip" value="zip" checked>
                <label class="output-mode-label" for="modeZip" title="Download as separate files in a ZIP">
                    <i class="ph-bold ph-file-zip me-1"></i> ZIP
                </label>

                <input type="radio" class="output-mode-input" name="output_mode" id="modeMerged" value="merged">
                <label class="output-mode-label" for="modeMerged" title="Merge all into one single PDF file">
                    <i class="ph-bold ph-files me-1"></i> Merged PDF
                </label>
            </div>

            {{-- 3. TOMBOL DOWNLOAD --}}
            <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm d-flex align-items-center">
                <i class="ph-bold ph-download-simple me-2"></i> Download
            </button>

            {{-- 4. TOMBOL CANCEL (BARU) --}}
            <button type="button" id="btnCancelSelection" class="btn btn-light text-primary fw-bold rounded-pill shadow-sm d-flex align-items-center px-3" title="Batalkan Pilihan">
                <i class="ph-bold ph-x me-2"></i> Cancel
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {

            let selectedIds = [];
            let currentTab = 'transactions'; // Default

            // Opsi Dokumen per Kategori
            const docOptions = {
                'transactions': [
                    {val: 'lampiran_d', text: 'Lampiran D'},
                    {val: 'submission_form', text: 'Formulir Pengajuan'}
                ],
                'expiring': [
                    {val: 'distributor', text: 'Surat Distributor'},
                    {val: 'bank', text: 'Surat Bank'}
                ]
            };

            // --- FUNGSI UPDATE UI FLOATING BAR ---
            function updateBulkUI() {
                if (selectedIds.length > 0) {
                    $('#bulkActionBar').addClass('active');
                    $('#countSelected').text(selectedIds.length);

                    // Update Dropdown Options hanya jika belum ada (agar tidak reset pilihan user)
                    let currentOptions = $('#bulkDocType option').map(function() { return $(this).val(); }).get();
                    let targetOptions = docOptions[currentTab].map(o => o.val);

                    // Simple check if options need update (bedakan array)
                    if(JSON.stringify(currentOptions) !== JSON.stringify(targetOptions)) {
                        let opts = docOptions[currentTab].map(o => `<option value="${o.val}">${o.text}</option>`).join('');
                        $('#bulkDocType').html(opts);
                    }

                    // Isi Input Hidden Form
                    $('#bulkCategoryInput').val(currentTab);

                    // Reset input ID di form dan isi ulang
                    $('#bulkDownloadForm input[name="ids[]"]').remove();
                    selectedIds.forEach(id => {
                        $('#bulkDownloadForm').append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });

                } else {
                    $('#bulkActionBar').removeClass('active');
                }
            }

            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                transTable.columns.adjust().draw();
                lettersTable.columns.adjust().draw();
            });

            $('#btnCancelSelection').on('click', function() {
                // 1. Kosongkan Array
                selectedIds = [];

                // 2. Uncheck semua checkbox fisik di tabel
                $('.dt-checkbox').prop('checked', false);
                $('#checkAllTrans, #checkAllLetters').prop('checked', false);

                // 3. Update UI (Otomatis menyembunyikan floating bar karena selectedIds kosong)
                updateBulkUI();
            });

            // --- TABLE 1: TRANSACTIONS ---
            var transTable = $('#transTable').DataTable({
                processing: true, serverSide: true, responsive: true,
                ajax: { url: "{{ route('bg-reports.index') }}", data: { type: 'transactions' } },
                dom: 't<"d-flex justify-content-between align-items-center p-4"ip>',
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center align-middle'},
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center text-muted fw-bold align-middle'},
                    {data: 'date', name: 'created_at', className: 'text-secondary'},
                    {data: 'form_code', name: 'form_code', className: 'fw-bold text-dark align-middle'}, // Emerald text handled in Controller or here
                    {data: 'customer', name: 'recommendation.customer.name', className: 'fw-bold text-dark align-middle'},
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center align-middle'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                ],
                columnDefs: [ { targets: 0, orderable: false } ],
                language: { searchPlaceholder: "Search Reference / Customer...", search: "" },
            });

            $('#searchTrans').keyup(function(){
                transTable.search($(this).val()).draw();
            });

            // --- TABLE 2: LETTERS ---
            var lettersTable = $('#lettersTable').DataTable({
                processing: true, serverSide: true, responsive: true,
                ajax: { url: "{{ route('bg-reports.index') }}", data: { type: 'expiring' } },
                dom: 't<"d-flex justify-content-between align-items-center p-4"ip>',
                columns: [
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center text-muted fw-bold'},
                    {data: 'bg_number', name: 'bg_number', className: 'text-primary fw-bold'},
                    {data: 'customer', name: 'customer.name', className: 'fw-bold text-dark'},
                    {data: 'exp_date', name: 'exp_date', className: 'text-danger fw-bold'},
                    {data: 'nominal', name: 'bg_nominal', className: 'fw-semibold'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle'
                    },
                ],
                columnDefs: [ { targets: 0, orderable: false } ],
                language: { searchPlaceholder: "Search BG Number / Customer...", search: "" },
            });

            $('#searchLetters').keyup(function(){
                lettersTable.search($(this).val()).draw();
            });

            // Styling Input Search
            $('.dataTables_filter input').addClass('form-control form-control-sm ps-4').css({
                'border-radius': '20px', 'min-width': '250px', 'background-color': '#f8fafc', 'border': '1px solid #e2e8f0'
            });

            // 1. Check All Handler
            $('#checkAllTrans').on('click', function() {
                let checked = this.checked;
                $('#transTable .dt-checkbox').prop('checked', checked).trigger('change');
            });
            $('#checkAllLetters').on('click', function() {
                let checked = this.checked;
                $('#lettersTable .dt-checkbox').prop('checked', checked).trigger('change');
            });

            // 2. Individual Check Handler
            $(document).on('change', '.dt-checkbox', function() {
                let id = $(this).val();
                if(this.checked) {
                    if(!selectedIds.includes(id)) selectedIds.push(id);
                } else {
                    selectedIds = selectedIds.filter(item => item !== id);
                    $('#checkAllTrans, #checkAllLetters').prop('checked', false);
                }
                updateBulkUI();
            });

            // 3. Maintain State on Page Change
            transTable.on('draw', function(){
                $('.dt-checkbox').each(function(){
                    if(selectedIds.includes($(this).val())) $(this).prop('checked', true);
                });
            });
            lettersTable.on('draw', function(){
                $('.dt-checkbox').each(function(){
                    if(selectedIds.includes($(this).val())) $(this).prop('checked', true);
                });
            });

        });
    </script>
    @endpush
</x-app-layout>
