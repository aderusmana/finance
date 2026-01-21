<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen {{ ucfirst($type) }}</title>
    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body style="font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #334155; min-height: 100vh; display: flex; align-items: center; background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 24px 24px; margin: 0;">

    @php
        if ($type == 'existing') {
            $primaryColor = '#4f46e5';
            $bgGradient   = 'linear-gradient(135deg, #4f46e5 0%, #4338ca 100%)';
            $bgColorLight = '#eef2ff';
        } else {
            $primaryColor = '#059669';
            $bgGradient   = 'linear-gradient(135deg, #059669 0%, #047857 100%)';
            $bgColorLight = '#ecfdf5';
        }
        $cardShadow = '0 20px 40px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';
    @endphp

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="main-card" style="border: none; border-radius: 24px; box-shadow: {{ $cardShadow }}; overflow: hidden; background: white; transition: transform 0.3s ease;">

                {{-- HEADER SECTION --}}
                <div style="background: {{ $bgGradient }}; padding: 3rem; text-align: center; color: white; position: relative;">
                    <div style="position: absolute; top: -20px; right: -20px; opacity: 0.1; pointer-events: none;">
                        <i class="bi bi-file-earmark-richtext" style="font-size: 8rem;"></i>
                    </div>
                    <span style="background-color: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.25); border-radius: 50rem; padding: 4px 16px; margin-bottom: 16px; display: inline-block; font-weight: 400; backdrop-filter: blur(4px);">
                        {{ ucfirst($type) }} Process
                    </span>
                    <h3 class="fw-bold mb-1" style="font-weight: 700; margin-bottom: 4px;">Upload Dokumen</h3>
                    <p class="small" style="margin: 0; opacity: 0.9;">{{ $submission->recommendation->customer->name ?? 'Customer Name' }}</p>
                </div>

                <div style="padding: 3rem;">

                    {{-- ERROR ALERTS --}}
                    @if ($errors->any())
                        <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 14px;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('error'))
                        <div style="background-color: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; text-align: center;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    {{-- INFO BOX --}}
                    <div style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                        <div class="row g-3">
                            <div class="col-6" style="border-right: 1px solid rgba(0,0,0,0.1);">
                                <label style="display: block; margin-bottom: 4px; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Kode Form</label>
                                <span style="font-family: monospace; font-weight: 700; color: #0f172a;">{{ $submission->form_code }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <label style="display: block; margin-bottom: 4px; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nominal</label>
                                @if($bg)
                                    <span style="font-weight: 700; color: {{ $primaryColor }};">
                                        Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-danger small">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- FORM AREA --}}
                    <form action="{{ route('customer.portal.store-upload', $token) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        {{-- FIX 1: Input File dipindah ke sini, DI LUAR DropZone --}}
                        <input type="file" name="signed_document" id="fileInput" accept=".pdf" hidden>

                        {{-- DROP ZONE --}}
                        <div id="dropZone"
                             style="border: 2px dashed #cbd5e1; border-radius: 16px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background-color: #ffffff; color: {{ $primaryColor }}; position: relative;"
                             onmouseover="this.style.borderColor='{{ $primaryColor }}'; this.style.backgroundColor='{{ $bgColorLight }}'; this.style.transform='scale(1.01)';"
                             onmouseout="if(document.getElementById('fileInput').files.length === 0){ this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#ffffff'; this.style.transform='scale(1)'; } else { this.style.transform='scale(1)'; }">

                            {{-- State Awal --}}
                            <div id="emptyState">
                                <i class="bi bi-cloud-arrow-up-fill mb-3 d-block" style="font-size: 3rem; opacity: 0.75; transition: transform 0.3s ease;"></i>
                                <h6 style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Klik atau Drop File PDF</h6>
                                <p class="text-muted small" style="margin: 0;">Format PDF (Max. 5MB)</p>
                            </div>

                            {{-- State Sukses (Hidden by default) --}}
                            <div id="successState" style="display: none;">
                                <div style="color: {{ $primaryColor }}; font-weight: 700;">
                                    <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i><br>
                                    File Siap Dikirim
                                </div>
                            </div>
                        </div>

                        {{-- FILE PREVIEW CARD --}}
                        <div id="filePreview" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px 16px; align-items: center; margin-top: 15px;">
                            <div class="d-flex w-100 align-items-center">
                                <i class="bi bi-file-earmark-pdf-fill fs-3 me-3 text-danger"></i>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate" id="fileNameDisplay" style="font-size: 14px; color: #1e293b;">document.pdf</h6>
                                    <small class="text-muted" id="fileSizeDisplay" style="font-size: 12px;">0 MB</small>
                                </div>
                                <button type="button" id="removeFileBtn" style="border: none; background: #fff; color: #ef4444; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); cursor: pointer;" title="Hapus File">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        {{-- SUBMIT BUTTON --}}
                        <button type="submit" id="submitBtn"
                                style="width: 100%; margin-top: 24px; padding: 14px; border-radius: 50px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.2s; border: none; color: white; background: {{ $bgGradient }}; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';">
                            <i class="bi bi-send-fill me-2"></i> Kirim Dokumen
                        </button>
                    </form>

                </div>
            </div>

            <div class="text-center mt-4 small" style="color: #94a3b8; opacity: 0.6;">
                &copy; {{ date('Y') }} Financial System Secure Upload
            </div>

        </div>
    </div>
</div>

{{-- LOADING OVERLAY --}}
<div id="loadingOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(5px); z-index: 9999; display: none; flex-direction: column; justify-content: center; align-items: center;">
    <div class="spinner-border mb-3" role="status" style="width: 3rem; height: 3rem; color: {{ $primaryColor }};"></div>
    <h5 style="font-weight: 700; color: #1e293b;" id="loadingText">Mengupload Dokumen...</h5>
    <p class="text-muted small mb-0">Mohon jangan tutup halaman ini.</p>

    <div style="width: 300px; height: 6px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-top: 20px;">
        <div id="progressBar" style="height: 100%; width: 0%; transition: width 0.2s ease; border-radius: 10px; background: {{ $bgGradient }};"></div>
    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput'); // Sekarang ambil dari luar dropzone
        const emptyState = document.getElementById('emptyState');
        const successState = document.getElementById('successState');
        const filePreview = document.getElementById('filePreview');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const fileSizeDisplay = document.getElementById('fileSizeDisplay');
        const removeFileBtn = document.getElementById('removeFileBtn');
        const uploadForm = document.getElementById('uploadForm');

        const loadingOverlay = document.getElementById('loadingOverlay');
        const progressBar = document.getElementById('progressBar');

        const themeColor = "{{ $primaryColor }}";
        const themeBg = "{{ $bgColorLight }}";

        // --- DRAG & DROP HANDLERS ---
        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => dropZone.addEventListener(evt, preventDefaults, false));

        ['dragenter', 'dragover'].forEach(evt => {
            dropZone.addEventListener(evt, () => {
                dropZone.style.borderColor = themeColor;
                dropZone.style.backgroundColor = themeBg;
                dropZone.style.transform = 'scale(1.01)';
            }, false);
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, () => {
                if(fileInput.files.length === 0) {
                    dropZone.style.borderColor = '#cbd5e1';
                    dropZone.style.backgroundColor = '#ffffff';
                    dropZone.style.transform = 'scale(1)';
                }
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            handleFiles(dt.files);
        });

        dropZone.addEventListener('click', (e) => {
            fileInput.click();
        });

        fileInput.addEventListener('change', function() { handleFiles(this.files); });

        // --- FILE VALIDATION & UI UPDATE ---
        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                // Validasi Tipe
                if (file.type !== 'application/pdf') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Salah',
                        text: 'Mohon upload file dengan format PDF.',
                        confirmButtonColor: themeColor
                    });
                    resetForm();
                    return;
                }

                // Validasi Size (Max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File Terlalu Besar',
                        text: 'Maksimal ukuran file adalah 5MB.',
                        confirmButtonColor: themeColor
                    });
                    resetForm();
                    return;
                }

                // FIX 2: Update UI tanpa menghancurkan Input
                // (Input sekarang aman di luar dropzone, tapi kita tetep pakai logic show/hide)
                // Assign files ke input (jika dari drag & drop)
                if (fileInput.files !== files) {
                    fileInput.files = files;
                }

                emptyState.style.display = 'none';
                successState.style.display = 'block'; // Show custom success div
                filePreview.style.display = 'flex';

                dropZone.style.padding = '20px';
                dropZone.style.borderColor = themeColor;
                dropZone.style.backgroundColor = themeBg;

                fileNameDisplay.textContent = file.name;
                fileSizeDisplay.textContent = formatBytes(file.size);
            }
        }

        // --- UTILS ---
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function resetForm() {
            fileInput.value = '';

            // Toggle Visibility
            emptyState.style.display = 'block';
            successState.style.display = 'none';
            filePreview.style.display = 'none';

            dropZone.style.padding = '40px 20px';
            dropZone.style.backgroundColor = '#ffffff';
            dropZone.style.borderColor = '#cbd5e1';
        }

        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            resetForm();
        });

        // --- SUBMIT ---
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!fileInput.files.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada File',
                    text: 'Silakan pilih atau drop file PDF terlebih dahulu.',
                    confirmButtonColor: themeColor
                });
                return;
            }

            loadingOverlay.style.display = 'flex';

            let width = 0;
            const interval = setInterval(() => {
                width += Math.floor(Math.random() * 15) + 5;
                if (width > 90) width = 90;
                progressBar.style.width = width + '%';
            }, 200);

            setTimeout(() => {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    uploadForm.submit();
                }, 300);
            }, 1500);
        });
    });
</script>

</body>
</html>
