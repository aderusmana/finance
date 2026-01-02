<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen - {{ $submission->recommendation->customer->name ?? 'Customer' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; min-height: 100vh; display: flex; align-items: center; padding: 20px 0;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            {{-- CARD CONTAINER --}}
            <div style="background: white; border-radius: 16px; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1); overflow: hidden; border: none;">

                {{-- HEADER --}}
                <div style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 40px 30px; text-align: center; color: white; position: relative;">
                    <h3 class="fw-bold mb-1" style="font-weight: 700;">Upload Dokumen Resmi</h3>
                    <p class="mb-0 opacity-75 small" style="opacity: 0.75;">Verifikasi Bank Garansi</p>

                    <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-building"></i>
                        {{ $submission->recommendation->customer->name ?? 'Nama Customer Tidak Tersedia' }}
                    </div>

                    <div style="position: absolute; bottom: -20px; left: 0; right: 0; height: 40px; background: white; border-radius: 50% 50% 0 0 / 100% 100% 0 0;"></div>
                </div>

                <div class="card-body p-4 pt-2">

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mt-3 mb-4" style="background-color: #fef2f2; color: #991b1b;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-4 me-2"></i>
                                <div><small>{{ session('error') }}</small></div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('customer.portal.store-upload', $token) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        {{-- DROP ZONE --}}
                        <div id="dropZone" style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 40px 20px; text-align: center; background-color: #f8fafc; transition: all 0.3s ease; cursor: pointer; margin-top: 10px;">
                            <input type="file" name="signed_document" id="fileInput" accept=".pdf" style="display: none;">


                            <i class="bi bi-file-earmark-pdf icon-cloud" style="font-size: 3.5rem; color: #94a3b8; margin-bottom: 15px; display: block; transition: color 0.3s;"></i>

                            <h5 class="fw-bold text-dark mb-2">Klik atau Drop File di Sini</h5>
                            <p class="text-muted small mb-0">Hanya format PDF (Maks. 2MB)</p>

                            {{-- FILE INFO --}}
                            <div id="fileInfo" class="mt-3" style="display: none;">
                                <div class="d-inline-flex align-items-center bg-white border px-3 py-2 rounded-3 shadow-sm">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-5 me-2"></i>
                                    <div class="text-start">
                                        <div class="fw-bold text-dark small" id="fileName">doc.pdf</div>
                                        <div class="text-muted" style="font-size: 10px;" id="fileSize">0 KB</div>
                                    </div>
                                    <button type="button" class="btn btn-link text-danger p-0 ms-3" id="btnRemoveFile">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary w-100" id="btnSubmit" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); border: none; padding: 14px; font-weight: 700; border-radius: 10px; color: white; transition: transform 0.2s;">
                                <i class="bi bi-send-fill me-2"></i> Kirim Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted opacity-75">&copy; {{ date('Y') }} Secure Document Portal</small>
            </div>

        </div>
    </div>
</div>

{{-- MODAL LOADING (POP UP TENGAH) --}}
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                <h6 class="fw-bold text-dark mb-1" id="loadingTitle">Memproses File...</h6>
                <p class="text-muted small mb-3" id="loadingText">Mohon tunggu sebentar.</p>

                <div class="progress" style="height: 8px; border-radius: 10px; background-color: #e2e8f0;">
                    <div id="modalProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%; background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileNameDisplay = document.getElementById('fileName');
        const fileSizeDisplay = document.getElementById('fileSize');
        const btnRemove = document.getElementById('btnRemoveFile');
        const iconCloud = document.querySelector('.icon-cloud');
        const form = document.getElementById('uploadForm');

        // Modal & Progress Elements
        const loadingModalEl = document.getElementById('loadingModal');
        const loadingModal = new bootstrap.Modal(loadingModalEl);
        const loadingTitle = document.getElementById('loadingTitle');
        const loadingText = document.getElementById('loadingText');
        const modalProgressBar = document.getElementById('modalProgressBar');

        const MAX_SIZE_MB = 2;
        const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // --- 1. HANDLE FILE SELECTION ---
        dropZone.addEventListener('click', (e) => {
            if(e.target !== btnRemove && !btnRemove.contains(e.target)) fileInput.click();
        });

        fileInput.addEventListener('change', handleFileSelect);

        // Drag Effect
        ['dragenter', 'dragover'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => { e.preventDefault(); dropZone.style.backgroundColor = '#eff6ff'; dropZone.style.borderColor = '#3b82f6'; }, false);
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => { e.preventDefault(); dropZone.style.backgroundColor = '#f8fafc'; dropZone.style.borderColor = '#cbd5e1'; }, false);
        });
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt.files && dt.files.length > 0) {
                fileInput.files = dt.files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];

                if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                    Swal.fire({ icon: 'error', title: 'Format Salah', text: 'Mohon upload file PDF.', confirmButtonColor: '#1e40af' });
                    resetFile();
                    return;
                }
                if (file.size > MAX_SIZE_BYTES) {
                    Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: `Maksimal ukuran file adalah ${MAX_SIZE_MB}MB.`, confirmButtonColor: '#1e40af' });
                    resetFile();
                    return;
                }

                // ANIMASI LOADING SAAT PILIH FILE
                runLoadingAnimation('Memproses File...', 'Membaca dokumen...', () => {
                    fileNameDisplay.textContent = file.name;
                    fileSizeDisplay.textContent = formatBytes(file.size);
                    fileInfo.style.display = 'block';
                    iconCloud.classList.replace('bi-file-earmark-pdf', 'bi-file-earmark-check-fill');
                    iconCloud.style.color = '#10b981';
                });
            }
        }

            // --- 2. HANDLE SUBMIT DENGAN PREVIEW ---
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if(fileInput.files.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada File',
                    text: 'Silahkan upload file terlebih dahulu.',
                    confirmButtonColor: '#1e40af'
                });
                return;
            }

            const file = fileInput.files[0];
            const fileUrl = URL.createObjectURL(file); // Buat URL preview sementara

            // TAMPILKAN SWEETALERT DENGAN PREVIEW PDF
            Swal.fire({
                title: 'Konfirmasi Upload',
                html: `
                    <p class="text-muted small mb-3">Pastikan dokumen ini benar sebelum dikirim.</p>
                    <div style="width: 100%; height: 350px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                        <embed src="${fileUrl}" type="application/pdf" width="100%" height="100%" />
                    </div>
                    <div class="text-start bg-light p-2 rounded small border">
                        <div><strong>Nama File:</strong> ${file.name}</div>
                        <div><strong>Ukuran:</strong> ${formatBytes(file.size)}</div>
                    </div>
                `,
                width: 600, // Lebar pop-up diperbesar untuk preview
                showCancelButton: true,
                confirmButtonColor: '#1e40af',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bi bi-send-fill me-1"></i> Ya, Kirim Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ANIMASI LOADING UPLOAD FINAL
                    runLoadingAnimation('Mengupload...', 'Jangan tutup halaman ini...', () => {
                        form.submit();
                    });
                }
            });
        });

        // --- HELPER: ANIMASI PROGRESS ---
        function runLoadingAnimation(title, text, callback) {
            loadingTitle.innerText = title;
            loadingText.innerText = text;
            modalProgressBar.style.width = '0%';
            loadingModal.show();

            let width = 0;
            const interval = setInterval(() => {
                width += Math.floor(Math.random() * 10) + 5;
                if (width > 100) width = 100;

                modalProgressBar.style.width = width + '%';

                if (width === 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        if(title !== 'Mengupload...') { // Kalau upload, biarkan modal terbuka
                            loadingModal.hide();
                        }
                        if(callback) callback();
                    }, 500);
                }
            }, 100);
        }

        btnRemove.addEventListener('click', (e) => {
            e.stopPropagation();
            resetFile();
        });

        function resetFile() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            iconCloud.classList.replace('bi-file-earmark-check-fill', 'bi-file-earmark-pdf');
            iconCloud.style.color = '#94a3b8';
        }
    });
</script>
</body>
</html>
