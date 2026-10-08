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
                        <small class="text-muted" id="previewPdfModalSubtitle">Memuat berkas...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="#" id="previewPdfDownloadBtn" class="btn btn-sm btn-outline-primary" download>
                        <i class="iconoir-download me-1"></i> Unduh PDF
                    </a>
                    <a href="#" id="previewPdfNewTabBtn" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka di tab baru">
                        <i class="iconoir-open-new-window"></i>
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-dark" style="min-height: 550px; height: 75vh;">
                <iframe id="previewPdfIframe" src="" class="w-100 h-100" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
