<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Berhasil' }} | Corporate Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        /* Animasi & Hover tidak bisa ditaruh inline style secara teknis, jadi tetap disini */
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes cardEntrance {
            0% { opacity: 0; transform: translateY(30px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes iconPop {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 20px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.8); opacity: 0.5; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Class helper untuk hover effect */
        .btn-hover-primary:hover {
            background-color: #1e293b !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.25);
        }
        .btn-hover-outline:hover {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            transform: translateY(-1px);
        }
    </style>
</head>
<body style="font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; overflow: hidden; background: linear-gradient(-45deg, #e2e8f0, #f8fafc, #dbeafe, #eff6ff); background-size: 400% 400%; animation: gradientBG 15s ease infinite;">

    <div style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.6); border-radius: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(0,0,0,0.02); width: 100%; max-width: 500px; overflow: hidden; position: relative; animation: cardEntrance 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;">

        <div style="height: 6px; width: 100%; background: linear-gradient(90deg, #10b981, #3b82f6);"></div>

        <div style="padding: 48px 40px; text-align: center;" id="mainContent">

            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 28px; position: relative; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1); opacity: 0; animation: iconPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s forwards;">
                <div style="position: absolute; width: 100%; height: 100%; border-radius: 50%; border: 2px solid #10b981; animation: pulse-ring 2s infinite;"></div>

                <i class="bi bi-check-lg" style="font-size: 52px; color: #059669; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); position: relative; z-index: 2;"></i>
            </div>

            <h2 style="color: #1e293b; font-weight: 800; font-size: 1.85rem; margin: 0 0 12px 0; letter-spacing: -0.025em;">
                {{ $title ?? 'Permintaan Diterima!' }}
            </h2>

            @if(isset($type) && $type == 'input')
                <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 32px;">
                    Sistem sedang memproses dokumen formulir Anda.<br>
                    Pengunduhan akan dimulai secara otomatis.
                </p>

                <div style="text-align: left; padding: 20px; border-radius: 16px; margin-bottom: 24px; background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-printer-fill" style="color: #3b82f6;"></i> Instruksi Selanjutnya
                    </div>
                    <ul style="margin: 0; padding-left: 24px; font-size: 0.9rem; color: #475569;">
                        <li style="margin-bottom: 6px;">Cetak & tanda tangani dokumen yang terunduh.</li>
                        <li style="margin-bottom: 6px;">Scan kembali menjadi format <strong>PDF</strong>.</li>
                        <li>Unggah melalui tombol di bawah ini.</li>
                    </ul>
                </div>

                {{-- Hidden Iframe for Download --}}
                <iframe id="downloadFrame" style="display:none;"></iframe>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @if(isset($uploadToken))
                    <a href="{{ route('customer.portal.upload-form', $uploadToken) }}" class="btn-hover-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 24px; border-radius: 14px; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.3s ease; cursor: pointer; border: none; background: #0f172a; color: white; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2); box-sizing: border-box;">
                        Lanjut Upload Dokumen <i class="bi bi-cloud-upload-fill"></i>
                    </a>
                    @endif

                    @if(isset($downloadUrl))
                    <a href="{{ $downloadUrl }}" class="btn-hover-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 24px; border-radius: 14px; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.3s ease; cursor: pointer; background: transparent; color: #334155; border: 2px solid #e2e8f0; box-sizing: border-box;">
                        <i class="bi bi-download"></i> Unduh Manual
                    </a>
                    @endif
                </div>

            @elseif(isset($type) && $type == 'input_multi')
                <div style="text-align: left; padding: 20px; border-radius: 16px; margin-bottom: 24px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; color: #059669;">
                        <i class="bi bi-envelope-check-fill"></i> Submission Berhasil
                    </div>
                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">
                        {{ $message ?? 'Data pengajuan Anda telah tersimpan aman di database kami.' }}
                    </p>
                </div>

                <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 32px;">
                    Instruksi pembayaran dan kelengkapan berkas untuk masing-masing bank telah dikirimkan ke <strong>email Anda</strong>. Mohon cek folder Inbox/Spam.
                </p>

                <button type="button" onclick="forceCloseWindow()" class="btn-hover-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 24px; border-radius: 14px; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.3s ease; cursor: pointer; border: none; background: #0f172a; color: white; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2);">
                    Tutup Halaman
                </button>

            @else
                <p style="color: #64748b; font-size: 1rem; line-height: 1.6; margin-bottom: 32px;">
                    Terima kasih telah melengkapi data. Perubahan Anda telah berhasil direkam ke dalam sistem.<br>
                    Halaman ini akan tertutup otomatis.
                </p>

                <button type="button" onclick="forceCloseWindow()" class="btn-hover-outline" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px 24px; border-radius: 14px; font-weight: 600; font-size: 0.95rem; text-decoration: none; transition: all 0.3s ease; cursor: pointer; background: transparent; color: #334155; border: 2px solid #e2e8f0;">
                    Tutup Sekarang
                </button>
            @endif
        </div>

        <div style="background: rgba(248, 250, 252, 0.8); padding: 16px 32px; border-top: 1px solid rgba(226, 232, 240, 0.8); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #94a3b8; font-weight: 500;" id="mainFooter">
            <div style="display: flex; align-items: center; gap: 6px; color: #059669; background: rgba(16, 185, 129, 0.1); padding: 4px 10px; border-radius: 20px;">
                <i class="bi bi-shield-fill-check"></i> SSL Secured
            </div>

            @if(!isset($type) || ($type != 'input' && $type != 'input_multi'))
                <span id="countdown-wrapper">
                    Menutup dalam <strong id="countdown" style="color: #0f172a; font-size: 1.1em;">3</strong>s
                </span>
            @endif
        </div>

        <div id="fallbackUI" style="display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; height: 100%; padding: 60px 20px; color: #64748b;">
            <i class="bi bi-check-circle" style="color: #cbd5e1; font-size: 4rem; margin-bottom: 20px;"></i>
            <h3 style="color: #334155; margin: 0 0 10px 0;">Sesi Selesai</h3>
            <p style="margin: 0;">Anda dapat menutup tab browser ini secara manual.</p>
        </div>
    </div>

    <script>
        // Fungsi Custom Close Window
        function forceCloseWindow() {
            // Coba tutup standar
            window.opener = null;
            window.open('', '_self');
            window.close();

            // Fallback jika browser memblokir
            setTimeout(() => {
                const content = document.getElementById('mainContent');
                const footer = document.getElementById('mainFooter');
                const fallback = document.getElementById('fallbackUI');

                if(content) content.style.display = 'none';
                if(footer) footer.style.display = 'none';
                if(fallback) {
                    fallback.style.display = 'flex';
                    fallback.style.animation = 'cardEntrance 0.5s ease';
                }
            }, 200);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Logic Auto Download
            @if(isset($type) && $type == 'input' && isset($downloadUrl))
                const pdfUrl = "{{ $downloadUrl }}";
                const iframe = document.getElementById('downloadFrame');
                if(pdfUrl && iframe) {
                    setTimeout(() => {
                        iframe.src = pdfUrl;
                    }, 1500);
                }
            @endif

            // 2. Logic Auto Close dengan Countdown 3 Detik
            @if(!isset($type) || ($type != 'input' && $type != 'input_multi'))
                let seconds = 3;
                const countdownEl = document.getElementById('countdown');

                if(countdownEl) {
                    const interval = setInterval(() => {
                        seconds--;
                        countdownEl.innerText = seconds;

                        if (seconds <= 0) {
                            clearInterval(interval);
                            forceCloseWindow();
                        }
                    }, 1000);
                }
            @endif
        });
    </script>
</body>
</html>
