<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tautan Tidak Valid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; color: #334155; }
        .error-card { background-color: #ffffff; border: none; border-radius: 20px; padding: 4rem 3rem; text-align: center; max-width: 600px; box-shadow: 0 15px 35px rgba(239, 68, 68, 0.1); }
        .icon-circle { background-color: #fef2f2; color: #ef4444; width: 110px; height: 110px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; border: 4px solid #fee2e2; }
        .reason-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: left; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 text-center">
            <div class="error-card mx-auto">
                <div class="icon-circle">
                    <i class="ph-bold ph-warning-octagon" style="font-size: 4.5rem;"></i>
                </div>
                <h1 class="fw-bold mb-3" style="color: #0f172a;">Tautan Tidak Valid</h1>
                <p class="fs-5 mb-4" style="color: #475569;">Maaf, tautan persetujuan ini sudah tidak berlaku. Hal ini biasanya terjadi karena:</p>
                
                <div class="reason-box mb-4">
                    <ul class="list-unstyled mb-0" style="color: #334155;">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="ph-bold ph-caret-right text-danger me-2 mt-1"></i> 
                            <span>Pengajuan ini sudah pernah disetujui atau ditolak sebelumnya.</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="ph-bold ph-caret-right text-danger me-2 mt-1"></i> 
                            <span>Token autentikasi dari email Anda telah kedaluwarsa.</span>
                        </li>
                    </ul>
                </div>
                
                <p class="fs-6 text-muted mb-0 border-top pt-4" style="border-color: #e2e8f0 !important;">
                    Silakan periksa kembali kotak masuk email Anda untuk tautan terbaru atau hubungi administrator sistem.
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>