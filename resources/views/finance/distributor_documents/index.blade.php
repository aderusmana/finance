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
                    <input type="hidden" name="search" id="header_search_input" value="{{ $search }}">
                    <label for="header_year" class="fw-semibold text-muted small mb-0 text-nowrap">Filter Tahun:</label>
                    <select id="header_year" name="year" class="form-select form-select-sm" style="min-width: 110px;" onchange="handleYearFilterChange(this.value)">
                        @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                            <option value="{{ $optionYear }}" @selected((int) $year === $optionYear)>{{ $optionYear }}</option>
                        @endfor
                    </select>
                </form>
                <button type="button" class="btn btn-sm btn-primary text-nowrap" onclick="openAddDistributorModal()">
                    <i class="ph-bold ph-plus me-1"></i> Tambah Distributor
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Search Card --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form id="searchDistributorForm" onsubmit="handleSearchSubmit(event)" action="{{ route('distributor.documents.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="year" id="search_year" value="{{ $year }}">
                        <div class="col-12 col-md-8 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="iconoir-search"></i>
                                </span>
                                <input type="search" id="search" name="search" value="{{ $search }}" class="form-control border-start-0 ps-0" placeholder="Cari nama atau kode distributor...">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
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
                @include('finance.distributor_documents.partials.table', ['distributors' => $distributors, 'year' => $year, 'search' => $search])
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

    {{-- ======================================================== --}}
    {{-- PDF DOCUMENT PREVIEW MODAL (IFRAME EMBED VIEWER)          --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true" style="z-index: 1075;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-2 px-3 bg-white border-bottom">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <i class="iconoir-page text-danger fs-5"></i>
                        <div>
                            <h6 class="modal-title text-truncate fw-bold mb-0 text-dark" id="previewPdfModalLabel">Pratinjau Dokumen PDF</h6>
                            <small class="text-muted" id="previewPdfModalSubtitle"></small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <a href="#" id="previewPdfDownloadBtn" class="btn btn-sm btn-outline-primary" download>
                            <i class="iconoir-download me-1"></i> Unduh PDF
                        </a>
                        <a href="#" id="previewPdfNewTabBtn" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka di tab baru">
                            <i class="iconoir-open-new-window"></i>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="filter: none; opacity: 0.8;"></button>
                    </div>
                </div>
                <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-dark" style="min-height: 550px; height: 75vh;">
                    <iframe id="previewPdfIframe" src="" class="w-100 h-100" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- DOCUMENT UPLOAD MODAL                                     --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="uploadDocumentForm" onsubmit="handleUploadSubmit(event)">
                    @csrf
                    <input type="hidden" id="modal_distributor_id" name="distributor_id" value="">
                    <input type="hidden" id="modal_year_input" name="year" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadDocumentModalLabel">
                            <i class="iconoir-upload me-2 text-primary"></i>Upload Dokumen Distributor
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="uploadAlertContainer"></div>

                        <div class="mb-3">
                            <label for="modal_doc_type" class="form-label fw-semibold">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select id="modal_doc_type" name="doc_type" class="form-select" required onchange="onDocTypeChanged()">
                                <option value="bupot">Bukti Potong (BuPot)</option>
                                <option value="top_insentif">TOP Insentif</option>
                                <option value="transfer">Penjelasan Transfer (Running)</option>
                            </select>
                        </div>

                        <div id="modal_monthly_wrapper" class="mb-3">
                            <label for="modal_month" class="form-label fw-semibold">Bulan Dokumen <span class="text-danger">*</span></label>
                            <select id="modal_month" name="month" class="form-select">
                                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $mNum => $mName)
                                    <option value="{{ $mNum }}">{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="modal_transfer_wrapper" class="mb-3" style="display: none;">
                            <label for="modal_transaction_date" class="form-label fw-semibold">Tanggal Transaksi Transfer <span class="text-danger">*</span></label>
                            <input type="date" id="modal_transaction_date" name="transaction_date" class="form-control">
                            <small class="text-muted">Tanggal bukti transaksi transfer.</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_title" class="form-label fw-semibold">Judul / Keterangan Tampilan Dokumen</label>
                            <input type="text" id="modal_title" name="title" class="form-control" maxlength="255" placeholder="Contoh: BuPot PPh 23 Jan 2026, Memo Deviasi TOP">
                        </div>

                        <div class="mb-3">
                            <label for="modal_notes" class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea id="modal_notes" name="notes" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
                        </div>

                        <div class="mb-2">
                            <label for="modal_file" class="form-label fw-semibold">Pilih Berkas PDF <span class="text-danger">*</span></label>
                            <input type="file" id="modal_file" name="file" class="form-control" accept=".pdf" required>
                            <small class="text-muted d-block mt-1">Hanya menerima format <strong>PDF</strong>. Maksimal 1 MB.</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitUpload">
                            <i class="iconoir-upload me-1"></i> Simpan & Unggah Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL ADD DISTRIBUTOR TO DOCUMENT LIST                   --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="addDistributorModal" tabindex="-1" aria-labelledby="addDistributorModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" style="max-width: 1050px; width: 95%;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 0.75rem; overflow: hidden;">
                <form id="addDistributorForm" onsubmit="handleAddDistributorSubmit(event)" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header py-3 px-4 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e3a5f 0%, #152238 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-bold ph-user-plus text-white fs-4"></i>
                            <div>
                                <h5 class="modal-title fw-bold mb-0 text-white" id="addDistributorModalLabel">Tambah Distributor ke Dokumen</h5>
                                <small class="text-white-50" style="font-size: 0.78rem;">Daftarkan distributor dan lampirkan dokumen awal ke periode aktif</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body py-3 px-4 bg-light" style="max-height: calc(85vh - 120px);">
                        <div id="addDistributorAlertContainer"></div>

                        {{-- CARD 1: Distributor Data & Period --}}
                        <div class="card mb-3 border-primary shadow-sm">
                            <div class="card-header bg-light-primary py-2 px-3 border-bottom border-primary border-opacity-25">
                                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                    <i class="ph-bold ph-identification-card"></i> Informasi Distributor & Tahun Periode
                                </h6>
                            </div>
                            <div class="card-body p-3 bg-white">
                                <div class="row g-3">
                                    {{-- Period Year Input --}}
                                    <div class="col-md-3 col-12">
                                        <label for="add_year" class="form-label fw-semibold small text-dark mb-1">Tahun Periode <span class="text-danger">*</span></label>
                                        <select id="add_year" name="year" class="form-select form-select-sm" required>
                                            @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                                                <option value="{{ $optionYear }}" @selected((int) $year === $optionYear)>{{ $optionYear }}</option>
                                            @endfor
                                        </select>
                                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Distributor masuk ke list tahun ini.</small>
                                    </div>

                                    {{-- Select Distributor Input --}}
                                    <div class="col-md-9 col-12">
                                        <label for="add_distributor_id" class="form-label fw-semibold small text-dark mb-1">Pilih Distributor <span class="text-danger">*</span></label>
                                        <select id="add_distributor_id" name="distributor_id" class="form-select form-select-sm" required style="width: 100%;">
                                            <option value="">-- Cari Kode atau Nama Distributor --</option>
                                            @if(isset($availableDistributors))
                                                @foreach($availableDistributors as $d)
                                                    <option value="{{ $d->id }}" 
                                                            data-code="{{ $d->code }}" 
                                                            data-name="{{ $d->name }}" 
                                                            data-email="{{ $d->email }}" 
                                                            data-bupot="{{ $d->bupot_email }}">
                                                        [{{ $d->code }}] {{ $d->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- Distributor Info Preview Card --}}
                                <div id="distributorInfoPreview" class="p-2 mt-3 bg-light rounded border" style="display: none; font-size: 0.8rem;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 font-monospace" id="previewDistCode">-</span>
                                        <span class="fw-bold text-dark" id="previewDistName">-</span>
                                    </div>
                                    <div class="text-muted d-flex align-items-start gap-1">
                                        <span class="text-nowrap"><i class="ph-bold ph-envelope me-1 text-primary"></i>Email BuPot:</span>
                                        <div id="previewDistBupotEmail" class="d-flex flex-wrap gap-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 2: Upload Document Toggle Switch --}}
                        <div class="card mb-3 border-0 shadow-sm bg-white">
                            <div class="card-body p-3">
                                <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                                    <div>
                                        <label class="form-check-label fw-bold text-dark mb-0 d-block cursor-pointer" for="with_upload_switch">
                                            Sekalian upload & isi dokumen sekarang?
                                        </label>
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                             Jika diaktifkan, Anda dapat langsung mengunggah berkas BuPot, TOP Insentif, atau Penjelasan Transfer.
                                        </span>
                                    </div>
                                    <input class="form-check-input ms-3 me-0" type="checkbox" role="switch" id="with_upload_switch" name="with_upload" value="1" onchange="toggleUploadSection()" style="width: 2.75em; height: 1.4em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        {{-- CARD 3: Document Upload Form Section (Hidden by default) --}}
                        <div id="add_upload_section" style="display: none;" class="card border-primary shadow-sm mb-2">
                            <div class="card-header bg-light-primary py-2 px-3 border-bottom border-primary border-opacity-25 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                    <i class="ph-bold ph-file-arrow-up"></i> Berkas Dokumen Pertama
                                </h6>
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.72rem;">Format PDF Maks 1 MB</span>
                            </div>

                            <div class="card-body p-3 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label for="add_doc_type" class="form-label fw-semibold small text-dark mb-1">Jenis Dokumen <span class="text-danger">*</span></label>
                                        <select id="add_doc_type" name="doc_type" class="form-select form-select-sm" onchange="onAddDocTypeChanged()">
                                            <option value="bupot">Bukti Potong (BuPot)</option>
                                            <option value="top_insentif">TOP Insentif</option>
                                            <option value="transfer">Penjelasan Transfer (Running)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 col-12" id="add_monthly_wrapper">
                                        <label for="add_month" class="form-label fw-semibold small text-dark mb-1">Bulan Dokumen <span class="text-danger">*</span></label>
                                        <select id="add_month" name="month" class="form-select form-select-sm">
                                            @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $mNum => $mName)
                                                <option value="{{ $mNum }}">{{ $mName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 col-12" id="add_transfer_wrapper" style="display: none;">
                                        <label for="add_transaction_date" class="form-label fw-semibold small text-dark mb-1">Tanggal Transaksi Transfer <span class="text-danger">*</span></label>
                                        <input type="date" id="add_transaction_date" name="transaction_date" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label for="add_title" class="form-label fw-semibold small text-dark mb-1">Judul / Keterangan Dokumen</label>
                                        <input type="text" id="add_title" name="title" class="form-control form-control-sm" maxlength="255" placeholder="Contoh: BuPot PPh 23 Jan 2026">
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <label for="add_notes" class="form-label fw-semibold small text-dark mb-1">Catatan Tambahan</label>
                                        <input type="text" id="add_notes" name="notes" class="form-control form-control-sm" maxlength="500" placeholder="Catatan opsional (misal: nomor referensi atau keterangan)">
                                    </div>

                                    {{-- Distributor File Upload Zone --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small text-dark mb-1">
                                            Pilih Berkas PDF <span class="text-danger">*</span>
                                        </label>
                                        <div class="distributor-file-zone" data-title="Dokumen Distributor">
                                            <!-- Browse state -->
                                            <div class="file-browse-box">
                                                <input type="file" class="form-control form-control-sm distributor-file-input"
                                                    name="file" id="add_file"
                                                    accept=".pdf">
                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Hanya format <strong>PDF</strong>. Maksimal 1 MB.</small>
                                            </div>

                                            <!-- Uploaded state -->
                                            <div class="file-uploaded-box d-none">
                                                <div class="card border mb-0 file-card shadow-sm" style="background: #f8fafc; border-color: #cbd5e1 !important; border-left: 3.5px solid #ef4444 !important; border-radius: 0.5rem;">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <div class="d-flex align-items-center gap-2 overflow-hidden me-1">
                                                                <span class="file-icon-badge rounded d-flex align-items-center justify-content-center p-1" style="width: 28px; height: 28px; background: #fee2e2; color: #ef4444; flex-shrink: 0;">
                                                                    <i class="ph-bold ph-file-pdf file-type-icon f-s-16 text-danger"></i>
                                                                </span>
                                                                <div class="overflow-hidden" style="line-height: 1.2;">
                                                                    <div class="fw-bold text-dark text-truncate file-name-display" style="font-size: 0.8rem;" title="">-</div>
                                                                    <div class="text-muted file-size-display" style="font-size: 0.72rem;">-</div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-icon p-0 text-danger btn-remove-file" title="Hapus dan pilih file lain" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; background: rgba(239, 68, 68, 0.1); border-radius: 50%; border: none;">
                                                                <i class="ph-bold ph-x f-s-12"></i>
                                                            </button>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between pt-1 border-top" style="border-color: #e2e8f0 !important;">
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem; padding: 2px 8px;">
                                                                <i class="ph-bold ph-check me-1"></i> Terpilih
                                                            </span>
                                                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0.5 btn-preview-file d-flex align-items-center gap-1" style="font-size: 0.72rem; font-weight: 600;">
                                                                <i class="ph-bold ph-eye"></i> Preview
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-3 px-4 bg-white border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4" id="btnSubmitAddDistributor">
                            <i class="ph-bold ph-check me-1"></i> Simpan ke List
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentDistributorId = null;
            let currentYear = {{ (int) $year }};
            let currentTab = 'monthly';
            let currentTableUrl = null;
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

                // Handle pagination click via AJAX
                $(document).on('click', '#distributorTableContainer .pagination a', function(e) {
                    e.preventDefault();
                    const url = $(this).attr('href');
                    if (url) {
                        currentTableUrl = url;
                        reloadDistributorTable(null, url);
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
                    reloadDistributorTable();

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
                        reloadDistributorTable();

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

                            // Reload table via AJAX - NO FULL PAGE RELOAD!
                            reloadDistributorTable(data.year);
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
            // DYNAMIC AJAX TABLE RELOAD FUNCTIONS
            // ==========================================
            function reloadDistributorTable(targetYear = null, targetUrl = null, customSearch = null) {
                const year = targetYear || $('#header_year').val() || currentYear;
                const search = customSearch !== null ? customSearch : ($('#search').val() || '');

                if (targetUrl) {
                    currentTableUrl = targetUrl;
                } else if (targetYear || customSearch !== null) {
                    currentTableUrl = null;
                }

                let url = targetUrl || currentTableUrl || "{{ route('distributor.documents.index') }}";

                if (!targetUrl && !currentTableUrl) {
                    url += `?year=${encodeURIComponent(year)}&search=${encodeURIComponent(search)}`;
                }

                const container = $('#distributorTableContainer');
                container.css('opacity', '0.5');

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat daftar dokumen distributor.');
                    return response.text();
                })
                .then(html => {
                    container.html(html).css('opacity', '1');

                    // Re-initialize bootstrap tooltips on new elements
                    const tooltipTriggerList = [].slice.call(container[0].querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Sync currentYear and inputs if targetYear provided
                    if (targetYear) {
                        currentYear = parseInt(targetYear);
                        $('#header_year').val(targetYear);
                        $('#search_year').val(targetYear);
                    }
                })
                .catch(err => {
                    container.css('opacity', '1');
                    console.error('Error reloading distributor table:', err);
                });
            }

            function handleYearFilterChange(newYear) {
                currentTableUrl = null;
                currentYear = parseInt(newYear);
                $('#search_year').val(newYear);
                reloadDistributorTable(newYear);
            }

            function handleSearchSubmit(e) {
                e.preventDefault();
                currentTableUrl = null;
                reloadDistributorTable(null, null, $('#search').val());
            }

            function handleResetFilter() {
                currentTableUrl = null;
                $('#search').val('');
                reloadDistributorTable(null, null, '');
            }
        </script>
    @endpush
</x-app-layout>