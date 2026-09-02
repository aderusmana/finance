<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Document - {{ $submission->recommendation->customer->name ?? 'Customer' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; min-height: 100vh; display: flex; align-items: center; padding: 20px 0;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            {{-- CARD CONTAINER --}}
            <div style="background: white; border-radius: 16px; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1); overflow: hidden; border: none;">

                {{-- HEADER --}}
                <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%); padding: 40px 30px; text-align: center; color: white; position: relative;">
                    <h3 class="fw-bold mb-1" style="font-weight: 700;">Review Document</h3>
                    <p class="mb-0 opacity-75 small" style="opacity: 0.75;">Bank Guarantee Verification (Admin Review)</p>

                    <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-building"></i>
                        {{ $submission->recommendation->customer->name ?? 'Customer' }}
                    </div>

                    <div style="position: absolute; bottom: -20px; left: 0; right: 0; height: 40px; background: white; border-radius: 50% 50% 0 0 / 100% 100% 0 0;"></div>
                </div>

                <div class="card-body p-4 pt-2">

                    {{-- DETAIL INFO BOX --}}
                    <div style="background: linear-gradient(to bottom right, #eff6ff, #f8fafc); border: 1px solid #bfdbfe; border-radius: 16px; padding: 20px; margin-bottom: 25px; position: relative; overflow: hidden;">

                        {{-- Watermark Icon --}}
                        <i class="bi bi-file-earmark-check" style="position: absolute; right: -15px; top: -10px; font-size: 6rem; opacity: 0.03; transform: rotate(15deg); pointer-events: none;"></i>

                        <div class="d-flex flex-column gap-3 position-relative">
                            {{-- Item 1: Kode --}}
                            <div class="d-flex justify-content-between align-items-center border-bottom border-primary border-opacity-10 pb-2">
                                <span class="text-muted small d-flex align-items-center gap-2">
                                    <i class="bi bi-qr-code text-primary opacity-75"></i> Form Code
                                </span>
                                <span class="fw-bold text-primary" style="font-family: monospace; font-size: 0.95rem; letter-spacing: 0.5px;">{{ $submission->form_code }}</span>
                            </div>
                            
                            {{-- Item 2: Status --}}
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="text-muted small d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle text-primary opacity-75"></i> Upload Status
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1">
                                    <i class="bi bi-check-circle me-1"></i> Uploaded
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- PDF PREVIEW SECTION --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small text-muted text-uppercase">Document Preview</label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#fullScreenModal">
                                <i class="bi bi-arrows-fullscreen me-1"></i> View Full
                            </button>
                        </div>
                        <div class="border rounded-4 overflow-hidden shadow-sm" style="height: 400px; background: #e2e8f0; position: relative;">
                            @if(file_exists(public_path($submission->signed_document_path)))
                                <iframe src="{{ asset($submission->signed_document_path) }}#toolbar=0&navpanes=0" width="100%" height="100%" style="border: none;"></iframe>
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-danger">
                                    <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                                    <h5 class="fw-bold">File Not Found</h5>
                                    <p class="small text-muted">The document could not be found on the server.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="text-center">
                        <a href="{{ route('bg-approvals.index') }}" class="btn btn-primary fw-bold rounded-pill px-5 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Go to Approval Inbox
                        </a>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div style="background: #f8fafc; padding: 15px; text-align: center; border-top: 1px solid #e2e8f0;">
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">&copy; {{ date('Y') }} PT SMII - Finance Department</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL FULL SCREEN --}}
<div class="modal fade" id="fullScreenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: rgba(0,0,0,0.85);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i> {{ $submission->form_code }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 d-flex justify-content-center">
                <div class="w-100 h-100 bg-white rounded-3 overflow-hidden shadow">
                    @if(file_exists(public_path($submission->signed_document_path)))
                        <iframe src="{{ asset($submission->signed_document_path) }}" width="100%" height="100%" style="border: none;"></iframe>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-danger">
                            <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                            <h5 class="fw-bold">File Not Found</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
