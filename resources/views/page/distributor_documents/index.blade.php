<x-app-layout>
    @section('title', 'Manajemen Dokumen Distributor')

    <!-- Select2 & Select2 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        /* Select2 inside Bootstrap 5 Modal */
        .select2-container--bootstrap-5 {
            z-index: 1066;
            width: 100% !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            z-index: 1075 !important;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
        }
        .select2-container--bootstrap-5 .select2-results__options {
            max-height: 240px !important;
            overflow-y: auto !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #e0e7ff !important;
            color: #3730a3 !important;
        }

        .month-matrix-container {
            display: inline-flex;
            gap: 4px;
            align-items: center;
            flex-wrap: nowrap;
        }

        .month-matrix-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 38px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
            font-size: 0.72rem;
            cursor: pointer;
        }

        .month-matrix-pill .month-num {
            font-size: 0.68rem;
            font-weight: 700;
            line-height: 1;
        }

        .month-matrix-pill .month-icon {
            font-size: 0.8rem;
            line-height: 1;
            margin-top: 2px;
        }

        /* Complete: BuPot & TOP exists */
        .month-matrix-pill.complete {
            background-color: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
        }
        .month-matrix-pill.complete:hover {
            background-color: #a7f3d0;
            color: #064e3b;
        }

        /* Partial: BuPot or TOP exists */
        .month-matrix-pill.partial {
            background-color: #fef3c7;
            border-color: #fcd34d;
            color: #92400e;
        }
        .month-matrix-pill.partial:hover {
            background-color: #fde68a;
            color: #78350f;
        }

        /* empty */
        .month-matrix-pill.empty {
            background-color: #f3f4f6;
            border-color: #e5e7eb;
            color: #9ca3af;
        }
        .month-matrix-pill.empty:hover {
            background-color: #e5e7eb;
            color: #6b7280;
        }

        .legend-indicator {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 3px;
            vertical-align: middle;
            margin-right: 5px;
        }
        .legend-complete { background-color: #10b981; border: 1px solid #059669; }
        .legend-partial  { background-color: #f59e0b; border: 1px solid #d97706; }
        .legend-empty    { background-color: #d1d5db; border: 1px solid #9ca3af; }

        .distributor-code-badge {
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e3a8a;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* Detail Modal Style */
        .detail-header-card {
            background: linear-gradient(135deg, #152238 0%, #1e3a5f 100%);
            border-radius: 0.75rem;
            color: #fff;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 12px rgba(21, 34, 56, 0.15);
        }

        .distributor-code-badge-light {
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 600;
            color: #93c5fd;
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(147, 197, 253, 0.3);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid #e2e8f0;
            gap: 8px;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #64748b;
            font-weight: 600;
            padding: 0.65rem 1.15rem;
            border-radius: 0;
            background: transparent;
            transition: all 0.2s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #1e3a8a;
            border-bottom-color: #cbd5e1;
        }

        .nav-tabs-custom .nav-link.active {
            color: #1e3a8a;
            border-bottom-color: #2563eb;
            background: transparent;
        }

        .month-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .month-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .month-card-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .doc-section {
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.65rem 0.85rem;
        }

        .doc-item-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.45rem 0.65rem;
            margin-bottom: 0.5rem;
        }
        .doc-item-row:last-child {
            margin-bottom: 0;
        }

        .btn-add-quick {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.45rem;
            border-radius: 0.375rem;
        }

        .badge-status-complete {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-status-partial {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-status-empty {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        /* Distributor Document file upload zone */
        .distributor-file-zone {
            transition: all 0.2s ease-in-out;
        }
        .distributor-file-zone .file-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .distributor-file-zone .file-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }
        .distributor-file-zone .btn-remove-file {
            transition: all 0.15s ease;
        }
        .distributor-file-zone .btn-remove-file:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            transform: scale(1.1);
        }
        #distributorTableContainer {
            transition: opacity 0.2s ease;
        }
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>

    {{-- Header Section --}}
    <div class="row m-1 mb-3">
        <div class="col-12 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h4 class="main-title mb-1">
                    <i class="iconoir-folder me-2 text-primary"></i>Manajemen Dokumen Distributor
                </h4>
                <p class="text-muted small mb-0">Pantau kelengkapan Bukti Potong Pajak, TOP Insentif, dan Penjelasan Transfer distributor.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('distributor.documents.index') }}" method="GET" class="d-inline-flex align-items-center gap-2" id="headerYearForm" onsubmit="event.preventDefault(); handleYearFilterChange($('#header_year').val());">
                    <label for="header_year" class="fw-semibold text-muted small mb-0 text-nowrap"><i class="iconoir-calendar me-1"></i>Filter Tahun:</label>
                    <select id="header_year" name="year" class="form-select form-select-sm" style="min-width: 105px;" onchange="handleYearFilterChange(this.value)" aria-label="Filter Tahun Dokumen">
                        @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                            <option value="{{ $optionYear }}" @selected((int) $year === $optionYear)>{{ $optionYear }}</option>
                        @endfor
                    </select>
                </form>
                <button type="button" class="btn btn-sm btn-primary text-nowrap d-flex align-items-center gap-1 shadow-sm" onclick="openAddDistributorModal()">
                    <i class="ph-bold ph-plus"></i> Tambah Distributor
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Search Card --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form id="searchDistributorForm" onsubmit="handleSearchSubmit(event)" class="row g-2 align-items-center">
                        <div class="col-12 col-md-8 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="iconoir-search"></i>
                                </span>
                                <input type="search" id="search" name="search" aria-label="Cari nama atau kode distributor" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode distributor...">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" aria-label="Cari distributor">
                                <i class="iconoir-filter me-1"></i> Cari
                            </button>
                            <button type="button" onclick="handleResetFilter()" class="btn btn-light border" title="Reset filter" aria-label="Reset filter">
                                <i class="iconoir-refresh"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="row mb-5 pb-5">
        <div class="col-12">
            <div id="distributorTableContainer">
                @include('page.distributor_documents.partials.table', ['year' => $year])
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- DISTRIBUTOR DETAIL MODAL (FULL DETAIL IN MODAL)           --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="distributorDetailModal" tabindex="-1" aria-labelledby="distributorDetailModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 94vw; width: 94vw;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-2 px-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="iconoir-folder text-primary fs-5"></i>
                        <h6 class="modal-title fw-bold mb-0 text-dark" id="distributorDetailModalLabel">Kelola Dokumen Distributor</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="filter: none; opacity: 0.8;"></button>
                </div>
                <div class="modal-body p-3 bg-light" id="distributorDetailModalBody" style="min-height: 480px;">
                    <div class="d-flex justify-content-center align-items-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF DOCUMENT PREVIEW MODAL (IFRAME EMBED VIEWER) --}}
    @include('page.distributor_documents.partials.preview-modal')

    {{-- DOCUMENT UPLOAD MODAL --}}
    @include('page.distributor_documents.partials.upload-modal', [
        'year' => $year,
    ])

    {{-- MODAL ADD DISTRIBUTOR TO DOCUMENT LIST --}}
    @include('page.distributor_documents.partials.add-distributor-modal', [
        'year' => $year,
        'availableDistributors' => $availableDistributors,
    ])

    @push('scripts')
        <script>
            let distributorTable = null;
            let currentDistributorId = null;
            let currentYear = {{ (int) $year }};
            let currentTab = 'monthly';
            let lastActiveTrigger = null;

            let distributorDetailModalInstance = null;
            let previewPdfModalInstance = null;
            let uploadModalInstance = null;
            let addDistributorModalInstance = null;

            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Initialize DataTables
                distributorTable = $('#distributorDocumentsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    dom: "<'d-none'l>" +
                         "<'row'<'col-12'tr>>" +
                         "<'row p-3 border-top align-items-center'<'col-12 col-md-6 text-muted small'i><'col-12 col-md-6 d-flex justify-content-md-end'p>>",
                    ajax: {
                        url: "{{ route('distributor.documents.index') }}",
                        data: function (d) {
                            d.year = $('#header_year').val() || currentYear;
                        }
                    },
                    columns: [
                        { data: 'code', name: 'distributors.code', className: 'ps-3', orderable: true, searchable: true },
                        { data: 'name', name: 'distributors.name', orderable: true, searchable: true },
                        { data: 'monthly_progress', name: 'monthly_progress', className: 'text-center', orderable: false, searchable: false },
                        { data: 'transfer_running', name: 'transfer_running', className: 'text-center', orderable: false, searchable: false },
                        { data: 'action', name: 'action', className: 'text-end pe-3', orderable: false, searchable: false }
                    ],
                    order: [[1, 'asc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    initComplete: function () {
                        const lengthEl = $('#distributorDocumentsTable_length');
                        if (lengthEl.length) {
                            lengthEl.appendTo('#tableLengthContainer');
                            lengthEl.removeClass('d-none');
                            lengthEl.find('label').addClass('d-flex align-items-center gap-2 mb-0 small text-muted');
                            lengthEl.find('select').addClass('form-select form-select-sm').attr('aria-label', 'Jumlah baris per halaman');
                        }
                    },
                    drawCallback: function () {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('#distributorDocumentsTable [data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                });

                $('#search').on('search', function() {
                    if (!this.value && distributorTable) {
                        distributorTable.search('').draw();
                    }
                });

                const detailModalEl = document.getElementById('distributorDetailModal');
                if (detailModalEl) {
                    distributorDetailModalInstance = new bootstrap.Modal(detailModalEl);
                    detailModalEl.addEventListener('hidden.bs.modal', function () {
                        if (lastActiveTrigger && typeof lastActiveTrigger.focus === 'function') {
                            lastActiveTrigger.focus();
                        }
                    });
                }

                const previewModalEl = document.getElementById('previewPdfModal');
                if (previewModalEl) {
                    previewPdfModalInstance = new bootstrap.Modal(previewModalEl);
                    previewModalEl.addEventListener('hidden.bs.modal', function () {
                        document.getElementById('previewPdfIframe').src = '';
                    });
                }

                const uploadModalEl = document.getElementById('uploadDocumentModal');
                if (uploadModalEl) {
                    uploadModalInstance = new bootstrap.Modal(uploadModalEl);
                }

                const addDistModalEl = document.getElementById('addDistributorModal');
                if (addDistModalEl) {
                    addDistributorModalInstance = new bootstrap.Modal(addDistModalEl);
                }

                if (window.jQuery && $.fn.select2) {
                    $('#add_distributor_id').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#addDistributorModal'),
                        placeholder: '-- Cari Kode atau Nama Distributor --',
                        allowClear: true,
                        width: '100%',
                        matcher: function(params, data) {
                            if ($.trim(params.term) === '') {
                                return data;
                            }
                            if (typeof data.text === 'undefined') {
                                return null;
                            }
                            var term = params.term.toLowerCase();
                            var text = data.text.toLowerCase();
                            var code = ($(data.element).data('code') || '').toString().toLowerCase();
                            var name = ($(data.element).data('name') || '').toString().toLowerCase();

                            if (text.indexOf(term) > -1 || code.indexOf(term) > -1 || name.indexOf(term) > -1) {
                                return data;
                            }
                            return null;
                        },
                        templateResult: function(data) {
                            if (!data.id) {
                                return data.text;
                            }
                            var code = $(data.element).data('code') || '';
                            var name = $(data.element).data('name') || data.text;
                            return $(
                                '<div class="d-flex align-items-center py-1">' +
                                    '<span class="badge bg-light text-primary border font-monospace me-2 px-2 py-1" style="font-size: 0.78rem;">' + $('<div>').text(code).html() + '</span>' +
                                    '<span class="fw-semibold text-dark">' + $('<div>').text(name).html() + '</span>' +
                                '</div>'
                            );
                        }
                    }).on('change', function () {
                        let opt = $(this).find('option:selected');
                        let code = opt.data('code');
                        let name = opt.data('name');
                        let bupot = opt.data('bupot');

                        if (code && name) {
                            $('#previewDistCode').text(code);
                            $('#previewDistName').text(`[${code}] ${name}`);
                            let bupotContainer = $('#previewDistBupotEmail');
                            bupotContainer.empty();
                            if (bupot && typeof bupot === 'string' && bupot.trim() !== '') {
                                let emails = bupot.split(/[,;]+/).map(s => s.trim()).filter(s => s.length > 0);
                                if (emails.length > 0) {
                                    let badgesHtml = emails.map(em => `<span class="badge bg-white text-dark border fw-normal me-1 mb-1">${$('<div>').text(em).html()}</span>`).join('');
                                    bupotContainer.html(badgesHtml);
                                } else {
                                    bupotContainer.html('<span class="fst-italic text-muted">-</span>');
                                }
                            } else {
                                bupotContainer.html('<span class="fst-italic text-muted">-</span>');
                            }
                            $('#distributorInfoPreview').slideDown(150);
                        } else {
                            $('#distributorInfoPreview').slideUp(150);
                        }
                    });
                }

                // ==========================================
                // DISTRIBUTOR FILE ZONE EVENT LISTENERS
                // ==========================================
                $(document).on('change', '.distributor-file-input', function(e) {
                    const input = this;
                    const zone = $(this).closest('.distributor-file-zone');
                    const browseBox = zone.find('.file-browse-box');
                    const uploadedBox = zone.find('.file-uploaded-box');

                    if (input.files && input.files.length > 0) {
                        const file = input.files[0];
                        const ext = file.name.split('.').pop().toLowerCase();
                        const isPdf = file.type === 'application/pdf' || ext === 'pdf';

                        if (!isPdf) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Format File Tidak Sesuai',
                                text: 'Hanya berkas format PDF yang diizinkan.',
                                confirmButtonColor: '#3085d6'
                            });
                            input.value = '';
                            uploadedBox.addClass('d-none');
                            browseBox.removeClass('d-none');
                            return;
                        }

                        // Client-side file size limit check (Max 1MB)
                        const maxSizeBytes = 1 * 1024 * 1024;
                        if (file.size > maxSizeBytes) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Ukuran File Terlalu Besar',
                                html: `Berkas <b>${file.name}</b> berukuran <b>${formatDocBytes(file.size)}</b>.<br>Maksimal ukuran file PDF adalah <b>1 MB</b>.`,
                                confirmButtonColor: '#3085d6'
                            });
                            input.value = '';
                            uploadedBox.addClass('d-none');
                            browseBox.removeClass('d-none');
                            return;
                        }

                        // Populate file info display
                        uploadedBox.find('.file-name-display').text(file.name).attr('title', file.name);
                        uploadedBox.find('.file-size-display').text(formatDocBytes(file.size));
                        browseBox.addClass('d-none');
                        uploadedBox.removeClass('d-none');
                    } else {
                        uploadedBox.addClass('d-none');
                        browseBox.removeClass('d-none');
                    }
                });

                // Handle remove file ("x" button)
                $(document).on('click', '.btn-remove-file', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const zone = $(this).closest('.distributor-file-zone');
                    const input = zone.find('.distributor-file-input');

                    input.val('');
                    zone.find('.file-uploaded-box').addClass('d-none');
                    zone.find('.file-browse-box').removeClass('d-none');
                    input.trigger('change');
                });

                // Handle local file preview in modal
                let addDistributorLocalBlobUrl = null;
                $(document).on('click', '.btn-preview-file', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const zone = $(this).closest('.distributor-file-zone');
                    const input = zone.find('.distributor-file-input')[0];
                    const file = input && input.files && input.files[0];

                    if (!file) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Berkas',
                            text: 'Pilih file PDF terlebih dahulu sebelum melakukan pratinjau.',
                            confirmButtonColor: '#3085d6'
                        });
                        return;
                    }

                    if (addDistributorLocalBlobUrl) {
                        URL.revokeObjectURL(addDistributorLocalBlobUrl);
                        addDistributorLocalBlobUrl = null;
                    }

                    addDistributorLocalBlobUrl = URL.createObjectURL(file);
                    document.getElementById('previewPdfIframe').src = addDistributorLocalBlobUrl;
                    document.getElementById('previewPdfModalLabel').textContent = 'Pratinjau: ' + file.name;
                    document.getElementById('previewPdfModalSubtitle').textContent = formatDocBytes(file.size);

                    const dlBtn = document.getElementById('previewPdfDownloadBtn');
                    if (dlBtn) {
                        dlBtn.href = addDistributorLocalBlobUrl;
                        dlBtn.download = file.name;
                        dlBtn.style.display = 'inline-flex';
                    }
                    const newTabBtn = document.getElementById('previewPdfNewTabBtn');
                    if (newTabBtn) {
                        newTabBtn.href = addDistributorLocalBlobUrl;
                        newTabBtn.style.display = 'inline-flex';
                    }

                    if (previewPdfModalInstance) {
                        previewPdfModalInstance.show();
                    }
                });



                // Global fallback for Escape key to close active modal
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' || e.keyCode === 27) {
                        const previewModal = document.getElementById('previewPdfModal');
                        const uploadModal = document.getElementById('uploadDocumentModal');
                        const isPreviewOpen = previewModal && previewModal.classList.contains('show');
                        const isUploadOpen = uploadModal && uploadModal.classList.contains('show');

                        if (!isPreviewOpen && !isUploadOpen) {
                            const detailModal = document.getElementById('distributorDetailModal');
                            if (detailModal && detailModal.classList.contains('show') && distributorDetailModalInstance) {
                                distributorDetailModalInstance.hide();
                            }
                        }
                    }
                });
            });

            // ==========================================
            // DISTRIBUTOR DETAIL MODAL AJAX FUNCTIONS
            // ==========================================
            function openDistributorDetailModal(distributorId, year, tab = 'monthly', targetMonth = null) {
                lastActiveTrigger = document.activeElement;
                currentDistributorId = distributorId;
                currentYear = year || currentYear;
                currentTab = tab || 'monthly';

                const modalBody = document.getElementById('distributorDetailModalBody');
                modalBody.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center py-5">
                        <div class="spinner-border text-primary me-2" role="status"></div>
                        <span class="text-muted">Memuat data dokumen distributor...</span>
                    </div>
                `;

                if (distributorDetailModalInstance) {
                    distributorDetailModalInstance.show();
                }

                loadDistributorDetail(currentDistributorId, currentYear, currentTab, targetMonth);
            }

            function loadDistributorDetail(distributorId, year, tab, targetMonth = null, transferYear = null) {
                currentDistributorId = distributorId;
                currentYear = year;
                currentTab = tab;

                let url = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${year}&tab=${tab}&ajax=1`;
                if (transferYear) {
                    url += `&transfer_year=${transferYear}`;
                }

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat detail distributor.');
                    return response.text();
                })
                .then(html => {
                    const modalBody = document.getElementById('distributorDetailModalBody');
                    modalBody.innerHTML = html;

                    // Restore focus inside modal for keyboard accessibility
                    setTimeout(() => {
                        const closeBtn = document.querySelector('#distributorDetailModal .btn-close');
                        if (closeBtn) {
                            closeBtn.focus();
                        }
                    }, 50);

                    if (targetMonth) {
                        setTimeout(() => {
                            const monthCard = document.getElementById(`month-card-${targetMonth}`);
                            if (monthCard) {
                                monthCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                monthCard.classList.add('border-primary');
                                setTimeout(() => monthCard.classList.remove('border-primary'), 2000);
                            }
                        }, 200);
                    }
                })
                .catch(err => {
                    document.getElementById('distributorDetailModalBody').innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="iconoir-warning-triangle me-1"></i> ${err.message}
                        </div>
                    `;
                });
            }

            function changeDetailYear(distributorId, newYear, tab) {
                loadDistributorDetail(distributorId, newYear, tab);
            }

            function switchDetailTab(distributorId, year, tab) {
                loadDistributorDetail(distributorId, year, tab);
            }

            function changeTransferYear(distributorId, year, transferYear) {
                loadDistributorDetail(distributorId, year, 'transfer', null, transferYear);
            }

            // ==========================================
            // PDF PREVIEW MODAL FUNCTIONS
            // ==========================================
            function openPdfPreview(previewUrl, title, downloadUrl) {
                document.getElementById('previewPdfModalLabel').textContent = title || 'Pratinjau Dokumen PDF';
                document.getElementById('previewPdfModalSubtitle').textContent = 'Memuat berkas PDF...';
                document.getElementById('previewPdfDownloadBtn').href = downloadUrl;
                document.getElementById('previewPdfNewTabBtn').href = previewUrl;

                const iframe = document.getElementById('previewPdfIframe');
                iframe.src = previewUrl;
                iframe.onload = function () {
                    document.getElementById('previewPdfModalSubtitle').textContent = 'Selesai dimuat.';
                };

                if (previewPdfModalInstance) {
                    previewPdfModalInstance.show();
                }
            }

            // ==========================================
            // DOCUMENT UPLOAD MODAL FUNCTIONS
            // ==========================================
            function openUploadModal(docType, month, distributorId, year) {
                document.getElementById('uploadAlertContainer').innerHTML = '';
                document.getElementById('uploadDocumentForm').reset();

                const distId = distributorId || currentDistributorId;
                const yr = year || currentYear;

                document.getElementById('modal_distributor_id').value = distId;
                document.getElementById('modal_year_input').value = yr;

                if (docType) document.getElementById('modal_doc_type').value = docType;
                if (month) document.getElementById('modal_month').value = month;

                onDocTypeChanged();

                if (uploadModalInstance) {
                    uploadModalInstance.show();
                }
            }

            function onDocTypeChanged() {
                const docType = document.getElementById('modal_doc_type').value;
                const monthlyWrapper = document.getElementById('modal_monthly_wrapper');
                const transferWrapper = document.getElementById('modal_transfer_wrapper');
                const monthInput = document.getElementById('modal_month');
                const dateInput = document.getElementById('modal_transaction_date');

                if (docType === 'transfer') {
                    monthlyWrapper.style.display = 'none';
                    transferWrapper.style.display = 'block';
                    monthInput.required = false;
                    monthInput.disabled = true;
                    dateInput.required = true;
                    dateInput.disabled = false;
                } else {
                    monthlyWrapper.style.display = 'block';
                    transferWrapper.style.display = 'none';
                    monthInput.required = true;
                    monthInput.disabled = false;
                    dateInput.required = false;
                    dateInput.disabled = true;
                }
            }

            function handleUploadSubmit(e) {
                e.preventDefault();
                const form = document.getElementById('uploadDocumentForm');
                const btnSubmit = document.getElementById('btnSubmitUpload');
                const alertContainer = document.getElementById('uploadAlertContainer');

                const fileInput = document.getElementById('modal_file');
                if (fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name.toLowerCase();
                    if (!fileName.endsWith('.pdf')) {
                        alertContainer.innerHTML = `
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="iconoir-warning-triangle me-1"></i> Hanya format berkas PDF yang diperbolehkan.
                            </div>
                        `;
                        return;
                    }
                }

                const formData = new FormData(form);
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...`;
                alertContainer.innerHTML = '';

                fetch(`{{ route('distributor.documents.upload') }}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        let errMsg = data.message || 'Gagal mengunggah berkas.';
                        if (data.errors) {
                            errMsg = Object.values(data.errors).flat().join('<br>');
                        }
                        throw new Error(errMsg);
                    }
                    return data;
                })
                .then(data => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = `<i class="iconoir-upload me-1"></i> Simpan & Unggah Dokumen`;

                    if (uploadModalInstance) {
                        uploadModalInstance.hide();
                    }

                    // Refresh Distributor Detail Modal Content
                    loadDistributorDetail(currentDistributorId, currentYear, currentTab);

                    // Auto-refresh main table in background
                    if (distributorTable) {
                        distributorTable.ajax.reload(null, false);
                    }

                    // Toast notification for user confirmation
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Dokumen berhasil diunggah.',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(err => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = `<i class="iconoir-upload me-1"></i> Simpan & Unggah Dokumen`;
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="iconoir-warning-triangle me-1"></i> ${err.message}
                        </div>
                    `;
                });
            }

            // ==========================================
            // DELETE DOCUMENT VIA AJAX
            // ==========================================
            function deleteDocument(deleteUrl, docTitle) {
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text || '';
                    return div.innerHTML;
                };

                const safeTitle = escapeHtml(docTitle);

                const executeDelete = () => {
                    Swal.fire({
                        title: 'Menghapus Dokumen...',
                        html: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal menghapus dokumen.');
                        return response.json();
                    })
                    .then(data => {
                        Swal.close();

                        // Refresh Distributor Detail Modal Content
                        loadDistributorDetail(currentDistributorId, currentYear, currentTab);

                        // Auto-refresh main table in background
                        if (distributorTable) {
                            distributorTable.ajax.reload(null, false);
                        }

                        // Toast notification for user confirmation
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Dokumen berhasil dihapus.',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: err.message || 'Terjadi kesalahan saat menghapus dokumen.'
                        });
                    });
                };

                if (window.Swal) {
                    Swal.fire({
                        title: 'Hapus Dokumen?',
                        html: `Apakah Anda yakin ingin menghapus dokumen <strong>"${safeTitle}"</strong>?<br><small class="text-muted">Berkas akan dihapus secara permanen dari server.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="iconoir-trash me-1"></i> Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            executeDelete();
                        }
                    });
                } else {
                    if (confirm(`Hapus dokumen "${docTitle}"?`)) {
                        executeDelete();
                    }
                }
            }

            // ==========================================
            // ADD DISTRIBUTOR TO LIST & UPLOAD FUNCTIONS
            // ==========================================
            function formatDocBytes(bytes, decimals = 2) {
                if (!bytes || bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            function resetAddDistributorFileZones() {
                $('.distributor-file-zone').each(function() {
                    $(this).find('.distributor-file-input').val('');
                    $(this).find('.file-uploaded-box').addClass('d-none');
                    $(this).find('.file-browse-box').removeClass('d-none');
                });
            }

            function openAddDistributorModal() {
                const form = document.getElementById('addDistributorForm');
                if (form) form.reset();

                resetAddDistributorFileZones();

                if (window.jQuery && $.fn.select2) {
                    $('#add_distributor_id').val('').trigger('change');
                }
                const yearSelect = document.getElementById('add_year');
                if (yearSelect) {
                    yearSelect.value = currentYear;
                }
                const switchEl = document.getElementById('with_upload_switch');
                if (switchEl) {
                    switchEl.checked = false;
                }
                toggleUploadSection();
                onAddDocTypeChanged();

                const alertContainer = document.getElementById('addDistributorAlertContainer');
                if (alertContainer) alertContainer.innerHTML = '';

                const previewEl = document.getElementById('distributorInfoPreview');
                if (previewEl) previewEl.style.display = 'none';

                if (addDistributorModalInstance) {
                    addDistributorModalInstance.show();
                }
            }

            function toggleUploadSection() {
                const switchEl = document.getElementById('with_upload_switch');
                const uploadSection = document.getElementById('add_upload_section');
                const btnSubmit = document.getElementById('btnSubmitAddDistributor');
                const fileInput = document.getElementById('add_file');

                if (switchEl && switchEl.checked) {
                    if (uploadSection) $(uploadSection).slideDown(200);
                    if (btnSubmit) btnSubmit.innerHTML = `<i class="ph-bold ph-upload-simple me-1"></i> Simpan & Unggah Dokumen`;
                    if (fileInput) fileInput.required = true;
                } else {
                    if (uploadSection) $(uploadSection).slideUp(200);
                    if (btnSubmit) btnSubmit.innerHTML = `<i class="ph-bold ph-check me-1"></i> Simpan ke List`;
                    if (fileInput) {
                        fileInput.required = false;
                        fileInput.value = '';
                    }
                    resetAddDistributorFileZones();
                }
            }

            function onAddDocTypeChanged() {
                const docType = document.getElementById('add_doc_type').value;
                const monthlyWrapper = document.getElementById('add_monthly_wrapper');
                const transferWrapper = document.getElementById('add_transfer_wrapper');
                const monthInput = document.getElementById('add_month');
                const dateInput = document.getElementById('add_transaction_date');

                if (docType === 'transfer') {
                    if (monthlyWrapper) monthlyWrapper.style.display = 'none';
                    if (transferWrapper) transferWrapper.style.display = 'block';
                    if (monthInput) {
                        monthInput.required = false;
                        monthInput.disabled = true;
                    }
                    if (dateInput) {
                        dateInput.required = true;
                        dateInput.disabled = false;
                    }
                } else {
                    if (monthlyWrapper) monthlyWrapper.style.display = 'block';
                    if (transferWrapper) transferWrapper.style.display = 'none';
                    if (monthInput) {
                        monthInput.required = true;
                        monthInput.disabled = false;
                    }
                    if (dateInput) {
                        dateInput.required = false;
                        dateInput.disabled = true;
                    }
                }
            }

            function handleAddDistributorSubmit(e) {
                e.preventDefault();
                const form = e.target;

                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.reportValidity();
                    return;
                }

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    text: 'Pastikan data distributor dan dokumen yang diunggah sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan Data...',
                            html: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const formData = new FormData(form);

                        fetch("{{ route('distributor.documents.list.store') }}", {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                let msg = data.message || 'Terjadi kesalahan saat menyimpan distributor.';
                                if (data.errors) {
                                    msg = Object.values(data.errors).flat().join('<br>');
                                }
                                throw new Error(msg);
                            }
                            return data;
                        })
                        .then(data => {
                            Swal.close();
                            if (addDistributorModalInstance) {
                                addDistributorModalInstance.hide();
                            }
                            form.reset();
                            resetAddDistributorFileZones();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            });

                            // Reload table via AJAX
                            if (data.year && parseInt(data.year) !== currentYear) {
                                $('#header_year').val(data.year);
                                handleYearFilterChange(data.year);
                            } else if (distributorTable) {
                                distributorTable.ajax.reload(null, false);
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan!',
                                html: err.message,
                                confirmButtonColor: '#d33'
                            });
                        });
                    }
                });
            }

            // ==========================================
            // DATATABLES FILTER & SEARCH FUNCTIONS
            // ==========================================
            function handleYearFilterChange(newYear) {
                currentYear = parseInt(newYear);
                $('#activeYearBadge').text(newYear);
                $('#headerMonthlyProgress').text('PROGRES BULANAN ' + newYear + ' (JAN - DES)');
                if (distributorTable) {
                    distributorTable.ajax.reload();
                }
            }

            function handleSearchSubmit(e) {
                e.preventDefault();
                const keyword = $('#search').val();
                if (distributorTable) {
                    distributorTable.search(keyword).draw();
                }
            }

            function handleResetFilter() {
                $('#search').val('');
                if (distributorTable) {
                    distributorTable.search('').draw();
                }
            }
        </script>
    @endpush
</x-app-layout>