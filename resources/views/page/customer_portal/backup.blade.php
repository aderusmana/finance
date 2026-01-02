<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Tidak Valid | Corporate System</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        /* Animasi Masuk yang Elegan */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Animasi Icon Berdenyut Halus */
        @keyframes pulseSoft {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.2); }
            70% { box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; height: 100vh; font-family: 'Manrope', sans-serif; background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">

    {{-- Background Geometris Professional (Subtle) --}}
    <div style="position: absolute; width: 100%; height: 50%; top: 0; background: #0f172a;"></div> {{-- Navy Header Background --}}
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: radial-gradient(#334155 1px, transparent 1px); background-size: 30px 30px; opacity: 0.1;"></div>

    {{-- Main Card --}}
    <div style="background: white; width: 90%; max-width: 520px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; position: relative; z-index: 10; animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
        
        {{-- Status Bar Atas --}}
        <div style="background: #dc2626; height: 8px; width: 100%;"></div>

        <div style="padding: 45px 40px;">

            {{-- Header Section --}}
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                {{-- Icon Container --}}
                <div style="width: 64px; height: 64px; background: #fef2f2; border: 1px solid #fee2e2; color: #dc2626; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 32px; flex-shrink: 0; animation: pulseSoft 2s infinite;">
                    <i class="bi bi-file-earmark-x-fill"></i>
                </div>
                <div>
                    <h6 style="text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-size: 0.75rem; font-weight: 700; margin-bottom: 5px;">System Notification</h6>
                    <h2 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 1.5rem;">Tautan Tidak Valid</h2>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 25px;">

            {{-- Message Body --}}
            <p style="color: #475569; font-size: 1rem; line-height: 1.7; margin-bottom: 30px;">
                Mohon maaf, sistem tidak dapat memproses permintaan Anda. Hal ini biasanya terjadi karena alasan keamanan perbankan berikut:
            </p>

            {{-- List Alasan (Style Corporate) --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 35px;">
                <ul style="margin: 0; padding-left: 20px; color: #334155; font-size: 0.9rem; font-weight: 500; line-height: 1.8;">
                    <li>Dokumen Bank Garansi sudah <b>Selesai Diproses</b>.</li>
                    <li>Token otorisasi telah <b>Kadaluarsa</b>.</li>
                    <li>Terdeteksi perubahan data pada URL.</li>
                </ul>
            </div>

            {{-- Action Button (Warna Bank: Navy Blue) --}}
            <button onclick="window.close()" 
                style="width: 100%; background: #0f172a; color: white; border: none; padding: 16px; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: background 0.2s, transform 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="bi bi-shield-check"></i> Tutup Halaman Aman
            </button>

            <div style="text-align: center; margin-top: 25px;">
                <small style="color: #94a3b8; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="bi bi-lock-fill"></i> 256-bit SSL Secured Connection
                </small>
            </div>

        </div>
    </div>

    {{-- Hover Effect Script --}}
    <script>
        const btn = document.querySelector('button');
        btn.addEventListener('mouseover', () => { 
            btn.style.background = '#1e293b'; 
            btn.style.transform = 'translateY(-2px)';
        });
        btn.addEventListener('mouseout', () => { 
            btn.style.background = '#0f172a'; 
            btn.style.transform = 'translateY(0)';
        });
    </script>
</body>
</html>