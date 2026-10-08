{{-- ======================================================== --}}
{{-- UPLOAD DOCUMENT MODAL                                    --}}
{{-- ======================================================== --}}
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadDocumentForm" action="{{ route('distributor.documents.upload') }}" method="POST" enctype="multipart/form-data" onsubmit="typeof handleUploadSubmit === 'function' ? handleUploadSubmit(event) : true">
                @csrf
                <input type="hidden" id="modal_distributor_id" name="distributor_id" value="{{ $distributor->id ?? '' }}">
                <input type="hidden" id="modal_year_input" name="year" value="{{ $year ?? '' }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">
                        <i class="iconoir-upload me-2 text-primary"></i>Upload Dokumen Distributor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
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
