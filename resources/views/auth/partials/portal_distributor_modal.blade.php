{{-- ======================================================== --}}
{{-- 1. MODAL VERIFIKASI OTP                                  --}}
{{-- ======================================================== --}}
<div class="modal fade" id="portalOtpModal" tabindex="-1" aria-labelledby="portalOtpModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 560px; width: 95%;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-3 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-shield-halved text-primary fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-dark" id="portalOtpModalLabel">Verifikasi Kode OTP</h5>
                        <small class="text-muted" style="font-size: 0.8rem;">Keamanan Akses Mandiri Dokumen Distributor</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="filter: none; opacity: 0.8;"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                {{-- Alert Container for OTP Modal --}}
                <div id="portal-otp-modal-alert" class="mb-3" style="display: none;"></div>

                {{-- Information text with masked email --}}
                <div class="p-3 p-md-4 mb-4 bg-primary-subtle border border-primary-subtle rounded-3 text-center">
                    <i class="fa-solid fa-envelope-circle-check text-primary fs-2 mb-2 d-block"></i>
                    <p class="text-dark mb-2" style="font-size: 0.95rem;">
                        Kode OTP 6-digit telah dikirimkan ke Email BuPot terdaftar:
                    </p>
                    <div class="bg-white py-2 px-3 rounded-2 border d-inline-block font-monospace text-primary fw-bold fs-5 shadow-sm my-1" id="portalOtpModalMaskedEmail" style="letter-spacing: 0.5px; min-width: 220px;">
                    </div>
                    <small class="text-danger d-block mt-2 fw-medium">
                        <i class="fa-solid fa-stopwatch me-1"></i>Kode OTP Berlaku selama 5 menit
                    </small>
                </div>

                {{-- OTP Input Box --}}
                <div class="mb-4 text-center">
                    <label for="portal_modal_otp" class="form-label fw-semibold small text-muted text-uppercase mb-2" style="letter-spacing: 0.75px;">
                        Masukkan 6 Digit Kode OTP
                    </label>
                    <input type="text"
                           class="form-control otp-input-field mx-auto"
                           id="portal_modal_otp"
                           maxlength="6"
                           placeholder="••••••"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           style="max-width: 320px;">
                </div>

                {{-- Resend OTP Section --}}
                <div class="text-center pt-1">
                    <button type="button" id="btn-modal-resend-otp" class="btn btn-link text-decoration-none text-muted py-1" onclick="resendPortalOtp()" disabled>
                        <i class="fa-solid fa-rotate-right me-1"></i> Kirim Ulang OTP (<span id="modal-resend-timer">60</span>s)
                    </button>
                </div>
            </div>

            <div class="modal-footer py-3 px-4 bg-light border-top d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary px-4 py-2 fw-semibold" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" id="btn-modal-confirm-otp" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" onclick="confirmPortalOtp()">
                    <i class="fa-solid fa-circle-check me-1"></i> Konfirmasi & Buka Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 2. MODAL PORTAL DOKUMEN DISTRIBUTOR                      --}}
{{-- ======================================================== --}}
<div class="modal fade" id="distributorDocsModal" tabindex="-1" aria-labelledby="distributorDocsModalLabel" aria-hidden="true" style="z-index: 1060;" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 94vw; width: 94vw;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 px-3 bg-white border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-folder text-primary fs-5"></i>
                    <h6 class="modal-title fw-bold mb-0 text-dark" id="distributorDocsModalLabel">Portal Dokumen Distributor</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" onclick="closePortalModalAndLogout()" style="filter: none; opacity: 0.8;"></button>
            </div>
            <div class="modal-body p-3 bg-light" id="distributorDocsModalBody" style="min-height: 480px;">
                <div id="distributorDocsContainer">
                    {{-- Dynamically populated via AJAX --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- 3. PDF DOCUMENT PREVIEW MODAL                            --}}
{{-- ======================================================== --}}
<div class="modal fade" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true" style="z-index: 1075;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 px-3 bg-white border-bottom">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="fa-solid fa-file-pdf text-danger fs-5"></i>
                    <div>
                        <h6 class="modal-title text-truncate fw-bold mb-0 text-dark" id="previewPdfModalLabel">Pratinjau Dokumen PDF</h6>
                        <small class="text-muted" id="previewPdfModalSubtitle">Memuat berkas PDF...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="#" id="previewPdfDownloadBtn" class="btn btn-sm btn-outline-primary" download>
                        <i class="fa-solid fa-download me-1"></i> Unduh PDF
                    </a>
                    <a href="#" id="previewPdfNewTabBtn" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka di tab baru">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="filter: none; opacity: 0.8;"></button>
                </div>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <iframe id="previewPdfIframe" src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
