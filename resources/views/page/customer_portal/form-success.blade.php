<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type == 'input' ? 'Input Berhasil' : 'Upload Berhasil' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #f0f2f5; font-family: sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="success-card" style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 40px; text-align: center; position: relative;">

                {{-- Icon --}}
                <div class="icon-box" style="font-size: 80px; color: #198754; margin-bottom: 20px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                {{-- Dynamic Title --}}
                <h3 class="fw-bold text-success mb-3">
                    {{ $type == 'input' ? 'Data Berhasil Disimpan!' : 'Dokumen Berhasil Diterima!' }}
                </h3>

                {{-- KONDISI 1: SETELAH INPUT FORM (STEP 1) --}}
                @if($type == 'input')
                    <p class="text-muted">Dokumen PDF formulir sedang didownload otomatis...</p>

                    <div class="alert alert-warning text-start mt-4" role="alert" style="font-size: 0.9rem;">
                        <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Langkah Selanjutnya:</h6>
                        <ol class="mb-0 ps-3">
                            <li>Cetak & Tanda tangani dokumen PDF.</li>
                            <li>Scan dokumen tersebut.</li>
                            <li>Klik tombol <b>"Lanjut Upload Dokumen"</b> di bawah.</li>
                        </ol>
                    </div>

                    {{-- Iframe Auto Download --}}
                    <iframe id="downloadFrame" style="display:none;"></iframe>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ $downloadUrl }}" class="btn btn-outline-secondary btn-sm" download>
                            <i class="bi bi-download me-2"></i> Download Ulang PDF
                        </a>
                        <a href="{{ route('customer.portal.upload-form', $uploadToken) }}" class="btn btn-primary fw-bold">
                            Lanjut Upload Dokumen <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>

                {{-- KONDISI 2: SETELAH UPLOAD FILE (STEP 2) --}}
                @elseif($type == 'upload')
                    <p class="text-muted mb-4">
                        Terima kasih. Dokumen Anda sedang kami validasi.<br>
                        Anda akan menerima notifikasi email jika BG diterbitkan.
                    </p>

                    <div class="alert alert-info border-0 d-inline-block text-start py-3 px-4 rounded-3" style="background-color: #e0f2f1; color: #00695c;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-2 me-3"></i>
                            <div>
                                <strong class="d-block">Proses Selesai</strong>
                                <small>Data Anda aman bersama kami.</small>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- AUTO CLOSE NOTICE --}}
                <div class="mt-4 pt-3 border-top">
                    <p class="text-muted small mb-2">
                        Tab ini akan tertutup otomatis dalam <b id="countdown" style="color: #dc3545; font-size: 1.2em;">7</b> detik.
                    </p>
                    <button type="button" onclick="window.close()" class="btn btn-link text-muted text-decoration-none btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Tutup Sekarang
                    </button>
                </div>

            </div>

            <p class="text-center text-muted mt-4 small">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Logic Auto Download (Hanya jika tipe input)
        @if($type == 'input' && isset($downloadUrl))
            const pdfUrl = "{{ $downloadUrl }}";
            const iframe = document.getElementById('downloadFrame');
            if(pdfUrl) {
                setTimeout(() => { iframe.src = pdfUrl; }, 1000);
            }
        @endif

        // 2. Logic Auto Close (7 Detik)
        let seconds = 7;
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            if(countdownEl) countdownEl.innerText = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.close();
                // Fallback visual jika browser memblokir close
                document.body.innerHTML = "<div style='display:flex; height:100vh; justify-content:center; align-items:center; color:#64748b; font-family:sans-serif;'>Sesi selesai. Silakan tutup tab ini secara manual.</div>";
            }
        }, 1000);
    });
</script>

</body>
</html>
