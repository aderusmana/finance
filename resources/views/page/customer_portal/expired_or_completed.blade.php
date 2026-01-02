<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Tidak Tersedia | PT Sinar Meadow</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body style="background: #fffbeb; background-image: radial-gradient(#fde68a 1px, transparent 1px); background-size: 24px 24px; font-family: 'Plus Jakarta Sans', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; color: #334155;">

    <div style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border: 1px solid #ffffff; border-radius: 24px; box-shadow: 0 20px 40px -10px rgba(245, 158, 11, 0.15); max-width: 500px; width: 90%; padding: 40px; text-align: center; position: relative; overflow: hidden;">
        
        {{-- Garis Gradasi Atas (Orange Theme) --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(90deg, #f59e0b, #fbbf24);"></div>

        {{-- Icon Container --}}
        <div style="width: 80px; height: 80px; background: #fff7ed; color: #d97706; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 36px; box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.2);">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        
        <h2 class="fw-bold text-dark mb-2">Akses Ditutup</h2>
        
        <p class="text-muted mb-4" style="line-height: 1.6; font-size: 0.95rem;">
            Mohon maaf, Anda tidak dapat mengakses halaman ini. <br>
            Hal ini mungkin disebabkan oleh beberapa alasan berikut:
        </p>

        {{-- List Alasan --}}
        <div class="text-start bg-white p-4 rounded-4 mb-4 border border-warning border-opacity-25" style="background-color: #fffaf0;">
            <ul class="mb-0 ps-3 text-muted small" style="line-height: 1.8; color: #78350f;">
                <li class="mb-2">Anda sudah pernah <b>mengisi dan mengirim</b> formulir ini sebelumnya.</li>
                <li class="mb-2">Batas waktu (expired) pengisian formulir <b>sudah habis</b>.</li>
                <li>Token keamanan URL sudah <b>tidak valid</b> atau diperbarui.</li>
            </ul>
        </div>

        <div class="d-flex justify-content-center gap-2">
            <button onclick="window.close()" style="background: #f59e0b; color: white; border: none; padding: 12px 30px; border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); transition: all 0.2s;">
                <i class="bi bi-x-circle"></i> Tutup Halaman
            </button>
        </div>

        <div class="mt-4 pt-3 border-top border-light">
            <small class="text-muted" style="font-size: 0.75rem;">
                Jika Anda merasa ini adalah kesalahan, silakan hubungi <a href="#" class="text-warning text-decoration-none fw-bold">Administrator</a>.
            </small>
        </div>

    </div>

</body>
</html>