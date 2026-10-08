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
