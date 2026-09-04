<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen Konfirmasi Bank Garansi - {{ $submission->recommendation->customer->name ?? 'Customer' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 30px 0;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
            padding: 35px 30px;
            color: white;
            text-align: center;
            position: relative;
        }
        .header-chip {
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }
        .upload-card {
            border: 2px dashed #93c5fd;
            border-radius: 14px;
            padding: 32px 20px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .upload-card:hover, .upload-card.dragover {
            border-color: #2563eb;
            background-color: #eff6ff;
        }
        .upload-icon {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.8rem;
            color: #2563eb;
        }
        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .btn-submit {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
            border: none;
            padding: 14px;
            font-weight: 700;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            box-shadow: 0 8px 16px -4px rgba(30, 64, 175, 0.3);
            transition: all 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px -4px rgba(30, 64, 175, 0.4);
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card-custom">
                {{-- HEADER --}}
                <div class="header-bg">
                    <h4 class="fw-bold mb-1">Konfirmasi & Upload Dokumen</h4>
                    <p class="mb-0 opacity-75 small">Unggah 1 file formulir konfirmasi yang telah ditandatangani dan dicap perusahaan</p>

                    <div class="header-chip">
                        <i class="bi bi-building"></i>
                        {{ $submission->recommendation->customer->name ?? 'Customer' }}
                    </div>
                </div>

                <div class="p-4 p-md-5">

                    {{-- DETAIL RINGKASAN --}}
                    <div class="info-box">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-qr-code me-1 text-primary"></i> Kode Formulir</span>
                            <span class="fw-bold text-primary font-monospace">{{ $submission->form_code }}</span>
                        </div>

                        @if($submission->custom_address)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted small"><i class="bi bi-geo-alt me-1 text-danger"></i> Alamat Operasional</span>
                                <span class="fw-semibold text-dark small text-end" style="max-width: 65%;">{{ $submission->custom_address }}</span>
                            </div>
                        @endif

                        {{-- DAFTAR BANK LENGKAP --}}
                        @if(isset($bgs) && $bgs->count() > 1)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-bank2 me-1 text-primary"></i> Rincian Bank Penjamin ({{ $bgs->count() }} Bank)
                                    </span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0 align-middle">
                                        <tbody>
                                            @foreach($bgs as $index => $bgItem)
                                                @php
                                                    $detail = $bgItem->details ? $bgItem->details->first() : null;
                                                    $bankName = $detail && $detail->bank_name ? $detail->bank_name : ($bgItem->bank_name ?? 'Bank');
                                                    $branchName = $detail && $detail->branch_name ? $detail->branch_name : '';
                                                @endphp
                                                <tr style="border-bottom: 1px dashed #e2e8f0;">
                                                    <td class="py-2 ps-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-primary-subtle text-primary fw-bold me-2 px-2 py-1 rounded-pill" style="font-size: 0.75rem;">Bank {{ $index + 1 }}</span>
                                                            <div>
                                                                <span class="fw-bold text-dark fs-6">{{ $bankName }}</span>
                                                                @if($branchName)
                                                                    <span class="text-muted small ms-1">({{ $branchName }})</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end text-success fw-bold py-2 pe-0 font-monospace fs-6">
                                                        Rp {{ number_format($bgItem->bg_nominal, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.8rem;">Total Nominal Bank Garansi</span>
                                <span class="fw-bold text-success fs-5 font-monospace">Rp {{ number_format($bgs->sum('bg_nominal'), 0, ',', '.') }}</span>
                            </div>
                        @elseif(isset($bg))
                            @php
                                $detail = $bg && $bg->details ? $bg->details->first() : null;
                                $bankName = $detail && $detail->bank_name ? $detail->bank_name : ($bg->bank_name ?? 'Bank');
                                $branchName = $detail && $detail->branch_name ? ' ('.$detail->branch_name.')' : '';
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted small"><i class="bi bi-bank2 me-1 text-primary"></i> Bank Penjamin</span>
                                <span class="fw-bold text-dark small">{{ $bankName }}{{ $branchName }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="bi bi-cash-stack me-1 text-success"></i> Nominal Bank Guarantee</span>
                                <span class="fw-bold text-success fs-5 font-monospace">Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-x-circle-fill fs-4 me-2"></i>
                                <div><small>{{ session('error') }}</small></div>
                            </div>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon lengkapi isian berikut:</div>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM HANYA 1 UPLOAD FILE SAJA --}}
                    <form action="{{ route('customer.portal.store-upload', $token) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label mb-2 d-block">
                                <i class="bi bi-file-earmark-arrow-up text-primary me-1"></i> Upload Dokumen Formulir Konfirmasi (Bertandatangan & Cap) <span class="text-danger">*</span>
                            </label>

                            <div class="upload-card" id="signedDropZone">
                                <input type="file" name="signed_document" id="signedInput" accept=".pdf" style="display: none;" required>
                                
                                <div class="upload-icon" id="signedIcon">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                
                                <div class="fw-bold text-dark fs-6 mb-1" id="signedTitle">Pilih atau Seret Formulir PDF ke Sini</div>
                                <div class="text-muted small" id="signedSubtitle" style="font-size: 0.8rem;">
                                    Unggah 1 file dokumen konfirmasi yang telah Anda unduh, ditandatangani oleh pimpinan, dan dicap basah perusahaan (Format PDF, Maks. 10MB)
                                </div>
                                
                                <div id="signedBadge" class="badge bg-success-subtle text-success mt-3 py-2 px-3 fs-6 d-none">
                                    <i class="bi bi-check-circle-fill me-1"></i> <span id="signedName"></span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border rounded-3 p-3 small text-muted mb-4 shadow-sm" style="background-color: #f8fafc;">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill text-primary fs-5 me-2 mt-n1 flex-shrink-0"></i>
                                <div>
                                    <strong>Petunjuk:</strong> Anda hanya perlu mengunggah 1 file formulir konfirmasi yang telah ditandatangani dan dicap perusahaan. Pengisian nomor resmi Bank Garansi dan tanggal jatuh tempo akan dilengkapi dan diverifikasi oleh tim Sales & Finance.
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-submit" id="btnSubmit">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Kirim Dokumen Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">&copy; {{ date('Y') }} PT SMII - Portal Verifikasi Bank Guarantee</small>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const form = document.getElementById('uploadForm');
    const dropZone = document.getElementById('signedDropZone');
    const input = document.getElementById('signedInput');
    const icon = document.getElementById('signedIcon');
    const title = document.getElementById('signedTitle');
    const badge = document.getElementById('signedBadge');
    const nameSpan = document.getElementById('signedName');

    dropZone.addEventListener('click', () => input.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#eff6ff';
        dropZone.style.borderColor = '#2563eb';
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8fafc';
        dropZone.style.borderColor = '#93c5fd';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8fafc';
        dropZone.style.borderColor = '#93c5fd';
        if (e.dataTransfer.files.length > 0) {
            input.files = e.dataTransfer.files;
            handleFileSelect(input.files[0]);
        }
    });

    input.addEventListener('change', function() {
        if (this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    function handleFileSelect(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Format Tidak Sesuai',
                text: 'Hanya file format PDF yang diperbolehkan.',
                confirmButtonColor: '#1e40af'
            });
            input.value = '';
            badge.classList.add('d-none');
            title.innerText = 'Pilih atau Seret Formulir PDF ke Sini';
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Terlalu Besar',
                text: 'Ukuran file maksimal adalah 10MB.',
                confirmButtonColor: '#1e40af'
            });
            input.value = '';
            badge.classList.add('d-none');
            title.innerText = 'Pilih atau Seret Formulir PDF ke Sini';
            return;
        }

        nameSpan.innerText = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(2) + ' MB)';
        badge.classList.remove('d-none');
        title.innerText = 'File Berhasil Dipilih';
        icon.style.color = '#16a34a';
    }

    form.addEventListener('submit', function(e) {
        const files = input.files;
        if (files.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Mohon unggah file formulir konfirmasi yang telah ditandatangani dan dicap perusahaan.',
                confirmButtonColor: '#1e40af'
            });
            return;
        }

        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengunggah Dokumen...';
    });
</script>
</body>
</html>
