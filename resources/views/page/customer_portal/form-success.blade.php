<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type == 'input' ? 'Input Berhasil' : 'Proses Berhasil' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body style="background-color: #f0fdf4; font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; color: #334155;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">

            <div style="background: white; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); padding: 40px; text-align: center; position: relative; width: 100%; border: 1px solid #f0fdf4;">
                
                {{-- Icon Check --}}
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 24px;">
                    <i class="bi bi-check-lg"></i>
                </div>

                {{-- Dynamic Title --}}
                <h3 class="fw-bold text-dark mb-2">
                    {{ $title ?? ($type == 'input' ? 'Data Berhasil Disimpan!' : 'Berhasil Diproses!') }}
                </h3>
                
                {{-- KONDISI 1: SETELAH INPUT FORM (STEP 1) --}}
                @if($type == 'input')
                    <p class="text-muted mb-4">Dokumen PDF formulir sedang didownload otomatis...</p>

                    <div class="bg-light p-3 rounded-3 text-start mb-4 border">
                        <h6 class="fw-bold text-dark mb-2" style="font-size: 0.9rem;">
                            <i class="bi bi-list-check me-2 text-primary"></i>Langkah Selanjutnya:
                        </h6>
                        <ul class="mb-0 ps-3 text-muted small" style="line-height: 1.6;">
                            <li>Cetak & Tanda tangani dokumen PDF.</li>
                            <li>Scan dokumen tersebut (Format PDF).</li>
                            <li>Klik tombol <b>"Lanjut Upload"</b> di bawah.</li>
                        </ul>
                    </div>

                    {{-- Iframe Auto Download --}}
                    <iframe id="downloadFrame" style="display:none;"></iframe>

                    <div class="d-grid gap-2">
                        <a href="{{ route('customer.portal.upload-form', $uploadToken ?? '') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 12px; font-weight: 600;">
                            Lanjut Upload Dokumen <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ $downloadUrl ?? '#' }}" class="btn btn-outline-secondary border-0" download style="padding: 12px 24px; border-radius: 12px; font-weight: 600;">
                            <i class="bi bi-download me-1"></i> Download Ulang PDF
                        </a>
                    </div>

                {{-- KONDISI 2: SETELAH UPLOAD / APPROVAL --}}
                @else
                    <p class="text-muted mb-4">
                        Terima kasih. Tindakan Anda telah tercatat di dalam sistem kami.<br>
                        Anda dapat menutup halaman ini sekarang.
                    </p>

                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-light border text-muted small mb-3">
                        <i class="bi bi-shield-check text-success"></i> Transaksi Aman & Terenkripsi
                    </div>
                @endif

                {{-- AUTO CLOSE NOTICE --}}
                <div class="mt-4 pt-4 border-top">
                    <p class="text-muted small mb-2">
                        Halaman ini akan tertutup otomatis dalam <b id="countdown" class="text-danger">7</b> detik.
                    </p>
                    <button type="button" onclick="window.close()" class="btn btn-sm btn-link text-decoration-none text-muted">
                        Tutup Sekarang
                    </button>
                </div>
            </div>

            <p class="text-center text-muted mt-4 small opacity-75">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Logic Auto Download
        @if($type == 'input' && isset($downloadUrl))
            const pdfUrl = "{{ $downloadUrl }}";
            const iframe = document.getElementById('downloadFrame');
            if(pdfUrl) {
                setTimeout(() => { iframe.src = pdfUrl; }, 1000);
            }
        @endif

        // 2. Logic Auto Close
        let seconds = 7;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            if(countdownEl) countdownEl.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.close();
                // Fallback visual
                document.body.innerHTML = "<div style='display:flex; height:100vh; justify-content:center; align-items:center; color:#64748b; font-family:sans-serif; background:#f0fdf4;'>Sesi selesai. Silakan tutup tab ini secara manual.</div>";
            }
        }, 1000);
    });
</script>

</body>
</html>