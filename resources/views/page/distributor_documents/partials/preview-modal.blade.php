{{-- ======================================================== --}}
{{-- PDF DOCUMENT PREVIEW MODAL (IFRAME EMBED VIEWER)          --}}
{{-- ======================================================== --}}
<div class="modal fade" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true" style="z-index: 1075;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 px-3 text-white border-bottom" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="iconoir-page text-white fs-5"></i>
                    <div>
                        <h6 class="modal-title text-truncate fw-bold mb-0 text-white" id="previewPdfModalLabel">Pratinjau Dokumen PDF</h6>
                        <small class="text-white-50" id="previewPdfModalSubtitle">Memuat berkas...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    @if (!auth()->check() || auth()->user()->can('download distributor-docs'))
                        <a href="#" id="previewPdfDownloadBtn" class="btn btn-sm btn-light text-primary fw-semibold" download>
                            <i class="iconoir-download me-1"></i> Unduh PDF
                        </a>
                    @endif
                    <a href="#" id="previewPdfNewTabBtn" target="_blank" class="btn btn-sm btn-outline-light" title="Buka di tab baru">
                        <i class="iconoir-open-new-window"></i>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-dark" style="min-height: 550px; height: 75vh;">
                <iframe id="previewPdfIframe" src="" class="w-100 h-100" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
