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

        /* Style Modal Detail */
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
                <form action="{{ route('distributor.documents.index') }}" method="GET" class="d-inline-flex align-items-center gap-2">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <label for="header_year" class="fw-semibold text-muted small mb-0 text-nowrap">Filter Tahun:</label>
                    <select id="header_year" name="year" class="form-select form-select-sm" style="min-width: 110px;" onchange="this.form.submit()">
                        @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                            <option value="{{ $optionYear }}" @selected((int) $year === $optionYear)>{{ $optionYear }}</option>
                        @endfor
                    </select>
                </form>
                <button type="button" class="btn btn-sm btn-primary text-nowrap" onclick="openAddDistributorModal()">
                    <i class="iconoir-plus me-1"></i> Tambah Distributor
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Search Card --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form action="{{ route('distributor.documents.index') }}" method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="year" value="{{ $year }}">
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
                            <a href="{{ route('distributor.documents.index', ['year' => $year]) }}" class="btn btn-light border" title="Reset filter" aria-label="Reset filter">
                                <i class="iconoir-refresh"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="row mb-5 pb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">Daftar Distributor & Progres Dokumen</h5>
                        <small class="text-muted">Periode Tahun Aktif: <span class="fw-bold text-primary">{{ $year }}</span></small>
                    </div>
                    <span class="badge bg-light text-secondary border px-2 py-1">
                        Total: {{ $distributors->total() }} Distributor
                    </span>
                </div>

                @if ($distributors->isEmpty())
                    <div class="card-body p-5 text-center">
                        <i class="iconoir-empty-page fs-1 text-muted"></i>
                        <h5 class="mt-3">Distributor Tidak Ditemukan</h5>
                        <p class="text-muted mb-0">Ubah kata kunci pencarian atau ganti filter tahun.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 140px;">KODE</th>
                                    <th style="min-width: 240px;">NAMA DISTRIBUTOR</th>
                                    <th class="text-center" style="min-width: 440px;">PROGRES BULANAN {{ $year }} (JAN - DES)</th>
                                    <th class="text-center" style="width: 170px;">TRF RUNNING</th>
                                    <th class="text-end pe-3" style="width: 150px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($distributors as $distributor)
                                    <tr id="distributor-row-{{ $distributor->id }}">
                                        <td class="ps-3">
                                            <span class="distributor-code-badge">{{ $distributor->code }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $distributor->name }}</div>
                                            @php
                                                $bupotEmails = $distributor->bupot_email_list;
                                            @endphp
                                            <div class="small mt-1">
                                                @if (!empty($bupotEmails))
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        @foreach ($bupotEmails as $bEmail)
                                                            <span class="badge bg-light text-dark border fw-normal text-truncate" style="font-size: 0.75rem; max-width: 250px;" title="{{ $bEmail }}">
                                                                <i class="iconoir-mail me-1 text-primary"></i>{{ $bEmail }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="month-matrix-container">
                                                @php
                                                    $monthNames = [
                                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                                        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                                    ];
                                                @endphp
                                                @for ($m = 1; $m <= 12; $m++)
                                                    @php
                                                        $summary = $distributor->monthly_summary[$m] ?? ['status' => 'empty', 'total' => 0, 'has_bupot' => false, 'has_top_insentif' => false];
                                                        $status = $summary['status'];
                                                        $tooltip = $monthNames[$m] . ': ' . ($status === 'complete' ? 'Lengkap (BuPot & TOP)' : ($status === 'partial' ? 'Sebagian (' . ($summary['has_bupot'] ? 'BuPot' : 'TOP') . ')' : 'Belum Ada Dokumen'));
                                                    @endphp
                                                    <button type="button"
                                                            class="month-matrix-pill {{ $status }}"
                                                            title="{{ $tooltip }}"
                                                            data-bs-toggle="tooltip"
                                                            onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'monthly', {{ $m }})">
                                                        <span class="month-num">{{ $m }}</span>
                                                        <span class="month-icon">
                                                            @if ($status === 'complete')
                                                                ✓
                                                            @elseif ($status === 'partial')
                                                                !
                                                            @else
                                                                •
                                                            @endif
                                                        </span>
                                                    </button>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($distributor->transfer_documents_count > 0)
                                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0" onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }}, 'transfer')">
                                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                                        {{ $distributor->transfer_documents_count }} File
                                                    </span>
                                                    @if ($distributor->transfer_range)
                                                        <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                                                            ({{ $distributor->transfer_range }})
                                                        </div>
                                                    @endif
                                                </button>
                                            @else
                                                <span class="text-muted small">0 File</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="openDistributorDetailModal({{ $distributor->id }}, {{ $year }})">
                                                <i class="iconoir-folder me-1"></i> Kelola File
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Card Footer: Legend & Pagination --}}
                    <div class="card-footer bg-white border-top p-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <div class="small text-muted">
                                <span class="fw-bold me-2">Keterangan Badge:</span>
                                <span class="me-3"><span class="legend-indicator legend-complete"></span> <strong>Lengkap</strong> (BuPot & TOP ada)</span>
                                <span class="me-3"><span class="legend-indicator legend-partial"></span> <strong>Sebagian</strong> terisi</span>
                                <span><span class="legend-indicator legend-empty"></span> <strong>Kosong</strong> (Belum ada dokumen)</span>
                            </div>
                            <div>
                                {{ $distributors->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL DETAIL DISTRIBUTOR (FULL DETAIL IN MODAL)           --}}
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
    {{-- MODAL PREVIEW DOKUMEN PDF (IFRAME EMBED VIEWER)          --}}
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
    {{-- MODAL UPLOAD DOKUMEN                                     --}}
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
    {{-- MODAL TAMBAH DISTRIBUTOR KE LIST DOKUMEN                 --}}
    {{-- ======================================================== --}}
    <div class="modal fade" id="addDistributorModal" tabindex="-1" aria-labelledby="addDistributorModalLabel" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="addDistributorForm" onsubmit="handleAddDistributorSubmit(event)">
                    @csrf
                    <div class="modal-header py-2 px-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="iconoir-user-plus text-primary fs-5"></i>
                            <h6 class="modal-title fw-bold mb-0 text-dark" id="addDistributorModalLabel">Tambah Distributor ke Dokumen</h6>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="filter: none; opacity: 0.8;"></button>
                    </div>

                    <div class="modal-body p-3">
                        <div id="addDistributorAlertContainer"></div>

                        {{-- Input Tahun Periode --}}
                        <div class="mb-3">
                            <label for="add_year" class="form-label fw-semibold">Tahun Periode <span class="text-danger">*</span></label>
                            <select id="add_year" name="year" class="form-select form-select-sm" required>
                                @for ($optionYear = now()->year + 1; $optionYear >= now()->year - 5; $optionYear--)
                                    <option value="{{ $optionYear }}" @selected((int) $year === $optionYear)>{{ $optionYear }}</option>
                                @endfor
                            </select>
                            <small class="text-muted" style="font-size: 0.75rem;">Distributor akan dimasukkan ke list aktif pada tahun ini.</small>
                        </div>

                        {{-- Input Pilih Distributor --}}
                        <div class="mb-3">
                            <label for="add_distributor_id" class="form-label fw-semibold">Pilih Distributor <span class="text-danger">*</span></label>
                            <select id="add_distributor_id" name="distributor_id" class="form-select" required style="width: 100%;">
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

                        {{-- Card Preview Info Distributor --}}
                        <div id="distributorInfoPreview" class="p-2 mb-3 bg-light rounded border" style="display: none; font-size: 0.8rem;">
                            <div class="fw-bold text-dark mb-1" id="previewDistName">-</div>
                            <div class="text-muted d-flex align-items-start gap-1">
                                <span class="text-nowrap"><i class="iconoir-mail me-1 text-primary"></i>Email BuPot:</span>
                                <div id="previewDistBupotEmail" class="d-flex flex-wrap gap-1"></div>
                            </div>
                        </div>

                        {{-- Switch Sekalian Upload Dokumen --}}
                        <div class="form-check form-switch p-2 ps-5 bg-light rounded border mb-3">
                            <input class="form-check-input ms-n4 me-2" type="checkbox" role="switch" id="with_upload_switch" name="with_upload" value="1" onchange="toggleUploadSection()">
                            <label class="form-check-label fw-semibold text-dark" for="with_upload_switch">
                                Sekalian upload & isi dokumen sekarang?
                            </label>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                Jika diaktifkan, Anda dapat langsung mengunggah file BuPot, TOP Insentif, atau Penjelasan Transfer.
                            </div>
                        </div>

                        {{-- Form Section Upload Dokumen (Hidden by default) --}}
                        <div id="add_upload_section" style="display: none;" class="p-3 border rounded bg-white mb-2 shadow-sm">
                            <h6 class="fw-bold text-primary mb-2" style="font-size: 0.85rem;">
                                <i class="iconoir-doc-upload me-1"></i>Unggah Dokumen Pertama
                            </h6>

                            <div class="mb-2">
                                <label for="add_doc_type" class="form-label fw-semibold small">Jenis Dokumen <span class="text-danger">*</span></label>
                                <select id="add_doc_type" name="doc_type" class="form-select form-select-sm" onchange="onAddDocTypeChanged()">
                                    <option value="bupot">Bukti Potong (BuPot)</option>
                                    <option value="top_insentif">TOP Insentif</option>
                                    <option value="transfer">Penjelasan Transfer (Running)</option>
                                </select>
                            </div>

                            <div id="add_monthly_wrapper" class="mb-2">
                                <label for="add_month" class="form-label fw-semibold small">Bulan Dokumen <span class="text-danger">*</span></label>
                                <select id="add_month" name="month" class="form-select form-select-sm">
                                    @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $mNum => $mName)
                                        <option value="{{ $mNum }}">{{ $mName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="add_transfer_wrapper" class="mb-2" style="display: none;">
                                <label for="add_transaction_date" class="form-label fw-semibold small">Tanggal Transaksi Transfer <span class="text-danger">*</span></label>
                                <input type="date" id="add_transaction_date" name="transaction_date" class="form-control form-control-sm">
                            </div>

                            <div class="mb-2">
                                <label for="add_title" class="form-label fw-semibold small">Judul / Keterangan Dokumen</label>
                                <input type="text" id="add_title" name="title" class="form-control form-control-sm" maxlength="255" placeholder="Contoh: BuPot PPh 23 Jan 2026">
                            </div>

                            <div class="mb-2">
                                <label for="add_notes" class="form-label fw-semibold small">Catatan Tambahan</label>
                                <textarea id="add_notes" name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan opsional"></textarea>
                            </div>

                            <div class="mb-1">
                                <label for="add_file" class="form-label fw-semibold small">Pilih Berkas PDF <span class="text-danger">*</span></label>
                                <input type="file" id="add_file" name="file" class="form-control form-control-sm" accept=".pdf">
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Hanya format <strong>PDF</strong>. Maksimal 1 MB.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-2 px-3 bg-white border-top">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitAddDistributor">
                            <i class="iconoir-check me-1"></i> Simpan ke List
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
            // MODAL DETAIL DISTRIBUTOR AJAX FUNCTIONS
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
            // MODAL PREVIEW PDF FUNCTIONS
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
            // MODAL UPLOAD FUNCTIONS
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
                    dateInput.required = true;
                } else {
                    monthlyWrapper.style.display = 'block';
                    transferWrapper.style.display = 'none';
                    monthInput.required = true;
                    dateInput.required = false;
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
                if (!confirm(`Hapus dokumen "${docTitle}"?`)) return;

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
                    // Refresh Distributor Detail Modal Content
                    loadDistributorDetail(currentDistributorId, currentYear, currentTab);
                })
                .catch(err => {
                    alert(err.message);
                });
            }

            // ==========================================
            // TAMBAH DISTRIBUTOR KE LIST & UPLOAD FUNCTIONS
            // ==========================================
            function openAddDistributorModal() {
                const form = document.getElementById('addDistributorForm');
                if (form) form.reset();

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
                    if (uploadSection) uploadSection.style.display = 'block';
                    if (btnSubmit) btnSubmit.innerHTML = `<i class="iconoir-upload me-1"></i> Simpan & Unggah Dokumen`;
                    if (fileInput) fileInput.required = true;
                } else {
                    if (uploadSection) uploadSection.style.display = 'none';
                    if (btnSubmit) btnSubmit.innerHTML = `<i class="iconoir-check me-1"></i> Simpan ke List`;
                    if (fileInput) fileInput.required = false;
                }
            }

            function onAddDocTypeChanged() {
                const docType = document.getElementById('add_doc_type').value;
                const monthlyWrapper = document.getElementById('add_monthly_wrapper');
                const transferWrapper = document.getElementById('add_transfer_wrapper');

                if (docType === 'transfer') {
                    if (monthlyWrapper) monthlyWrapper.style.display = 'none';
                    if (transferWrapper) transferWrapper.style.display = 'block';
                } else {
                    if (monthlyWrapper) monthlyWrapper.style.display = 'block';
                    if (transferWrapper) transferWrapper.style.display = 'none';
                }
            }

            function handleAddDistributorSubmit(e) {
                e.preventDefault();
                const form = e.target;
                const btnSubmit = document.getElementById('btnSubmitAddDistributor');
                const alertContainer = document.getElementById('addDistributorAlertContainer');
                alertContainer.innerHTML = '';

                btnSubmit.disabled = true;
                const originalText = btnSubmit.innerHTML;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...`;

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
                    if (addDistributorModalInstance) {
                        addDistributorModalInstance.hide();
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('distributor.documents.index') }}?year=" + data.year;
                    });
                })
                .catch(err => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalText;
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="iconoir-warning-triangle me-1"></i> ${err.message}
                        </div>
                    `;
                });
            }
        </script>
    @endpush
</x-app-layout>