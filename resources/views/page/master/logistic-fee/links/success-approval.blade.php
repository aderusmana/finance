<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Berhasil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { 
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #e2e8f0 100%); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; overflow: hidden;
        }
        .success-card { 
            background-color: #ffffff; border: none; border-radius: 28px; padding: 4rem 3rem; 
            text-align: center; max-width: 550px; width: 90%;
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15), 0 0 0 1px rgba(16, 185, 129, 0.05);
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        /* Animasi Ikon Mewah */
        .icon-circle { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; 
            width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            margin: 0 auto 2rem; position: relative; z-index: 1;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
            animation: popIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        /* Efek Gelombang (Ripple) di belakang ikon */
        .icon-circle::before {
            content: ''; position: absolute; top: -15px; left: -15px; right: -15px; bottom: -15px;
            border-radius: 50%; background: rgba(16, 185, 129, 0.15); z-index: -1;
            animation: pulseGlow 2s infinite;
        }

        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.5) rotate(-20deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        @keyframes pulseGlow {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.2; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        .btn-close-custom {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            background-color: #f1f5f9; color: #475569; font-weight: 700; font-size: 1rem;
            padding: 12px 28px; border-radius: 14px; border: 1px solid #e2e8f0; text-decoration: none;
            transition: all 0.3s ease; margin-top: 1.5rem; cursor: pointer; width: 100%;
        }
        .btn-close-custom:hover {
            background-color: #e2e8f0; color: #0f172a; transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <div class="success-card mx-auto">
                <div class="icon-circle">
                    <i class="ph-bold ph-check" style="font-size: 4.5rem;"></i>
                </div>
                <h2 style="font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; letter-spacing: -0.5px;">Tindakan Berhasil!</h2>
                <p style="font-size: 1.1rem; color: #475569; line-height: 1.6; margin-bottom: 2rem;">
                    @if(isset($successMessage))
                        {{ $successMessage }}
                    @else
                        Terima kasih. Keputusan dan catatan Anda untuk <strong style="color: #0f172a;">Logistic Fee</strong> telah berhasil disimpan.
                    @endif
                </p>
                
                <div style="background-color: #f8fafc; border-radius: 16px; padding: 1.5rem; border: 1px dashed #cbd5e1;">
                    <p style="margin: 0; font-size: 0.95rem; font-weight: 600; color: #64748b;">
                        Halaman ini akan ditutup otomatis dalam <br>
                        <span id="countdownText" style="font-size: 2rem; font-weight: 800; color: #10b981; display: block; margin-top: 5px;">4</span>
                    </p>
                    
                    <button onclick="closeWindow()" class="btn-close-custom">
                        <i class="ph-bold ph-x-circle" style="font-size: 1.25rem;"></i> Tutup Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi Menutup Jendela
    function closeWindow() {
        window.close();
        // Fallback jika window.close() diblokir oleh browser keamanan
        document.body.innerHTML = `
            <div style="text-align: center; font-family: 'Plus Jakarta Sans', sans-serif; color: #475569;">
                <i class="ph-bold ph-check-circle" style="font-size: 4rem; color: #10b981; margin-bottom: 1rem;"></i>
                <h2>Proses Selesai</h2>
                <p>Silakan tutup tab browser ini secara manual.</p>
            </div>
        `;
    }

    // Hitung Mundur Otomatis
    let timeLeft = 4;
    const countdownEl = document.getElementById('countdownText');
    
    const timer = setInterval(() => {
        timeLeft--;
        countdownEl.innerText = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            closeWindow();
        }
    }, 1000);
</script>
</body>
</html>