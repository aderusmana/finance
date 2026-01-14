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
                    <h3 class="fw-bold mb-1" style="font-weight: 700;">Upload Dokumen</h3>
                    <p class="mb-0 opacity-75 small" style="opacity: 0.75;">Verifikasi Bank Garansi</p>

                    <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(5px); padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="bi bi-building"></i>
                        {{ $submission->recommendation->customer->name ?? 'Customer' }}
                    </div>

                    <div style="position: absolute; bottom: -20px; left: 0; right: 0; height: 40px; background: white; border-radius: 50% 50% 0 0 / 100% 100% 0 0;"></div>
                </div>

                <div class="card-body p-4 pt-2">

                    {{-- DETAIL INFO BOX (REDESIGNED) --}}
                    <div style="background: linear-gradient(to bottom right, #eff6ff, #f8fafc); border: 1px solid #bfdbfe; border-radius: 16px; padding: 20px; margin-bottom: 25px; position: relative; overflow: hidden;">

                        {{-- Watermark Icon --}}
                        <i class="bi bi-file-earmark-text" style="position: absolute; right: -15px; top: -10px; font-size: 6rem; opacity: 0.03; transform: rotate(15deg); pointer-events: none;"></i>

                        <h6 class="fw-bold text-primary mb-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; display: flex; align-items: center; gap: 8px;">
                            <span style="width: 24px; height: 2px; background: #3b82f6; display: inline-block;"></span>
                            Detail Pengajuan
                        </h6>

                        <div class="d-flex flex-column gap-3 position-relative">
                            {{-- Item 1: Kode --}}
                            <div class="d-flex justify-content-between align-items-center border-bottom border-primary border-opacity-10 pb-2">
                                <span class="text-muted small d-flex align-items-center gap-2">
                                    <i class="bi bi-qr-code text-primary opacity-75"></i> Kode
                                </span>
                                <span class="fw-bold text-primary" style="font-family: monospace; font-size: 0.95rem; letter-spacing: 0.5px;">{{ $submission->form_code }}</span>
                            </div>

                            @if(isset($bg) && $bg->details->first())
                                {{-- Item 2: Bank --}}
                                <div class="d-flex justify-content-between align-items-start border-bottom border-primary border-opacity-10 pb-2">
                                    <span class="text-muted small d-flex align-items-center gap-2 mt-1">
                                        <i class="bi bi-bank2 text-primary opacity-75"></i> Bank
                                    </span>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark small d-block">{{ $bg->details->first()->bank_name }}</span>
                                        @if($bg->details->first()->branch_name)
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $bg->details->first()->branch_name }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Item 3: Nominal --}}
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    <span class="text-muted small d-flex align-items-center gap-2">
                                        <i class="bi bi-cash-stack text-success"></i> Nominal
                                    </span>
                                    <span class="fw-bold text-success" style="font-size: 1.1rem;">
                                        Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @else
                                <div class="text-center small text-danger mt-2 bg-white rounded p-2 border border-danger border-opacity-25">
                                    <i class="bi bi-exclamation-circle me-1"></i> Data Rincian Bank tidak ditemukan.
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-4 me-2"></i>
                                <div><small>{{ session('error') }}</small></div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('customer.portal.store-upload', $token) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        {{-- DROP ZONE --}}
                        <div id="dropZone" style="border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px 20px; text-align: center; background-color: #ffffff; transition: all 0.3s ease; cursor: pointer;">
                            <input type="file" name="signed_document" id="fileInput" accept=".pdf" style="display: none;">

                            <div style="width: 70px; height: 70px; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                                <i class="bi bi-cloud-arrow-up-fill icon-cloud" style="font-size: 2rem; color: #3b82f6;"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-1">Klik atau Drop File di Sini</h6>
                            <p class="text-muted small mb-0">Format PDF (Maks. 5MB)</p>

                            {{-- FILE INFO --}}
                            <div id="fileInfo" class="mt-3" style="display: none;">
                                <div class="d-inline-flex align-items-center bg-light border px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-file-earmark-check-fill text-success fs-5 me-2"></i>
                                    <div class="text-start me-3">
                                        <div class="fw-bold text-dark small" id="fileName">doc.pdf</div>
                                        <div class="text-muted" style="font-size: 10px;" id="fileSize">0 KB</div>
                                    </div>
                                    <button type="button" class="btn btn-link text-danger p-0 rounded-circle" id="btnRemoveFile" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-x fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary w-100" id="btnSubmit" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); border: none; padding: 14px; font-weight: 700; border-radius: 12px; color: white; box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.2); transition: all 0.2s;">
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

{{-- MODAL LOADING (Sama seperti sebelumnya) --}}
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

        const loadingModalEl = document.getElementById('loadingModal');
        const loadingModal = new bootstrap.Modal(loadingModalEl);
        const loadingTitle = document.getElementById('loadingTitle');
        const loadingText = document.getElementById('loadingText');
        const modalProgressBar = document.getElementById('modalProgressBar');

        const MAX_SIZE_MB = 5;
        const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        dropZone.addEventListener('click', (e) => {
            if(e.target !== btnRemove && !btnRemove.contains(e.target)) fileInput.click();
        });

        fileInput.addEventListener('change', handleFileSelect);

        ['dragenter', 'dragover'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => { e.preventDefault(); dropZone.style.backgroundColor = '#eff6ff'; dropZone.style.borderColor = '#3b82f6'; }, false);
        });
        ['dragleave', 'drop'].forEach(evt => {
            dropZone.addEventListener(evt, (e) => { e.preventDefault(); dropZone.style.backgroundColor = '#ffffff'; dropZone.style.borderColor = '#cbd5e1'; }, false);
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

                runLoadingAnimation('Memproses File...', 'Membaca dokumen...', () => {
                    fileNameDisplay.textContent = file.name;
                    fileSizeDisplay.textContent = formatBytes(file.size);
                    fileInfo.style.display = 'block';
                    // Ubah ikon saat file dipilih
                    iconCloud.classList.remove('bi-cloud-arrow-up-fill');
                    iconCloud.classList.add('bi-file-earmark-check-fill');
                    iconCloud.style.color = '#10b981';
                });
            }
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if(fileInput.files.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Belum Ada File', text: 'Silahkan upload file terlebih dahulu.', confirmButtonColor: '#1e40af' });
                return;
            }
            runLoadingAnimation('Mengupload...', 'Jangan tutup halaman ini...', () => {
                form.submit();
            });
        });

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
                        if(title !== 'Mengupload...') loadingModal.hide();
                        if(callback) callback();
                    }, 500);
                }
            }, 100);
        }

        btnRemove.addEventListener('click', (e) => { e.stopPropagation(); resetFile(); });

        function resetFile() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            // Reset ikon ke awal
            iconCloud.classList.remove('bi-file-earmark-check-fill');
            iconCloud.classList.add('bi-cloud-arrow-up-fill');
            iconCloud.style.color = '#3b82f6';
        }
    });
</script>
</body>
</html>
