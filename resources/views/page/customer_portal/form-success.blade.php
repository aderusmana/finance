<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Berhasil' }} | Corporate Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* Animasi & Hover tidak bisa ditaruh inline style, jadi tetap disini */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            to { transform: scale(1); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.8; }
            100% { transform: scale(2); opacity: 0; }
        }
        .btn-hover-primary:hover {
            background-color: #1e293b !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .btn-hover-outline:hover {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
        }
    </style>
</head>
<body style="font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px;">

    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); overflow: hidden; width: 100%; max-width: 480px; opacity: 0; animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;">
        
        <div style="height: 6px; width: 100%; background: linear-gradient(90deg, #34d399, #3b82f6);"></div>

        <div style="padding: 40px 32px; text-align: center;">
            
            <div style="width: 90px; height: 90px; background-color: #d1fae5; color: #10b981; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 24px; position: relative; transform: scale(0); animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s forwards;">
                <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 2px solid #10b981; animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;"></div>
                <i class="bi bi-check-lg" style="font-size: 48px; position: relative; z-index: 2;"></i>
            </div>

            <h2 style="color: #1e293b; font-weight: 700; font-size: 1.75rem; margin-top: 0; margin-bottom: 16px;">
                {{ $title ?? 'Permintaan Diterima!' }}
            </h2>

            @if(isset($type) && $type == 'input')
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    Sistem sedang memproses dokumen formulir Anda.<br>
                    Pengunduhan akan dimulai secara otomatis.
                </p>

                <div style="text-align: left; padding: 16px; border-radius: 12px; margin-bottom: 24px; background-color: #f0f9ff; border-left: 4px solid #3b82f6;">
                    <h6 style="color: #0f172a; font-weight: 700; margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-printer-fill" style="color: #3b82f6;"></i> Instruksi Selanjutnya:
                    </h6>
                    <ul style="color: #64748b; font-size: 0.875rem; margin-bottom: 0; padding-left: 1rem;">
                        <li style="margin-bottom: 4px;">Cetak & tanda tangani dokumen yang terunduh.</li>
                        <li style="margin-bottom: 4px;">Scan kembali menjadi format <strong>PDF</strong>.</li>
                        <li>Unggah melalui tombol di bawah ini.</li>
                    </ul>
                </div>

                {{-- Hidden Iframe for Download --}}
                <iframe id="downloadFrame" style="display:none;"></iframe>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @if(isset($uploadToken))
                    <a href="{{ route('customer.portal.upload-form', $uploadToken) }}" class="btn-hover-primary" style="background-color: #0f172a; color: white; padding: 12px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; border: none; transition: all 0.2s ease;">
                        Lanjut Upload Dokumen <i class="bi bi-cloud-upload"></i>
                    </a>
                    @endif

                    @if(isset($downloadUrl))
                    <a href="{{ $downloadUrl }}" class="btn-hover-outline" style="background-color: transparent; color: #0f172a; border: 2px solid #e2e8f0; padding: 12px 20px; border-radius: 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease;">
                        <i class="bi bi-download"></i> Unduh Manual
                    </a>
                    @endif
                </div>

            @elseif(isset($type) && $type == 'input_multi')
                <div style="text-align: left; padding: 16px; border-radius: 12px; margin-bottom: 24px; background-color: #ecfdf5; border-left: 4px solid #10b981;">
                    <h6 style="color: #10b981; font-weight: 700; margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-envelope-check-fill"></i> Submission Berhasil
                    </h6>
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0;">
                        {{ $message ?? 'Data pengajuan Anda telah tersimpan aman di database kami.' }}
                    </p>
                </div>

                <p style="font-size: 0.875rem; color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    Instruksi pembayaran dan kelengkapan berkas untuk masing-masing bank telah dikirimkan ke email Anda. Mohon cek folder Inbox/Spam.
                </p>

                <div style="display: grid;">
                    <button type="button" onclick="window.close()" class="btn-hover-primary" style="background-color: #0f172a; color: white; padding: 12px 20px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s ease;">
                        Tutup Halaman
                    </button>
                </div>

            @else
                <p style="color: #64748b; line-height: 1.6; margin-bottom: 24px;">
                    Terima kasih telah melengkapi data. Perubahan Anda telah berhasil direkam.<br>
                    Halaman ini akan tertutup otomatis.
                </p>

                <div style="display: grid;">
                    <button type="button" onclick="window.close()" class="btn-hover-outline" style="background-color: transparent; color: #0f172a; border: 2px solid #e2e8f0; padding: 12px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                        Tutup Sekarang
                    </button>
                </div>
            @endif
        </div>

        <div style="background-color: #f8fafc; padding: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #94a3b8;">
            <span>
                <i class="bi bi-shield-check" style="color: #10b981; margin-right: 4px;"></i> SSL Secured
            </span>
            
            @if(!isset($type) || ($type != 'input' && $type != 'input_multi'))
                <span>
                    Menutup dalam <strong id="countdown" style="color: #0f172a;">5</strong>s
                </span>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Logic Auto Download
            @if(isset($type) && $type == 'input' && isset($downloadUrl))
                const pdfUrl = "{{ $downloadUrl }}";
                const iframe = document.getElementById('downloadFrame');
                if(pdfUrl && iframe) {
                    setTimeout(() => {
                        iframe.src = pdfUrl;
                    }, 1000);
                }
            @endif

            // 2. Logic Auto Close / Redirect
            @if(!isset($type) || ($type != 'input' && $type != 'input_multi'))
                let seconds = 5;
                const countdownEl = document.getElementById('countdown');
                
                if(countdownEl) {
                    const interval = setInterval(() => {
                        seconds--;
                        countdownEl.innerText = seconds;
                        
                        if (seconds <= 0) {
                            clearInterval(interval);
                            
                            // Attempt Close
                            window.close();

                            // Fallback UI
                            setTimeout(() => {
                                document.querySelector('.card-body').innerHTML = `
                                    <div style="padding: 24px 0;">
                                        <i class="bi bi-check-circle" style="color: #94a3b8; font-size: 2.5rem;"></i>
                                        <p style="margin-top: 16px; color: #64748b;">Sesi Anda telah selesai.</p>
                                        <p style="font-size: 0.875rem; color: #94a3b8;">Anda dapat menutup tab ini secara manual.</p>
                                    </div>
                                `;
                            }, 500);
                        }
                    }, 1000);
                }
            @endif
        });
    </script>
</body>
</html>