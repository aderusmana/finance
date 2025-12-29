<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tautan Tidak Valid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border: none; box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-radius: 12px; border-top: 5px solid #dc3545; }
        .icon-box { width: 80px; height: 80px; background-color: #fde8e8; color: #dc3545; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-5 text-center">
                <div class="icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                </div>

                <h3 class="mb-3">Tautan Tidak Valid</h3>
                <p class="text-muted mb-4">
                    Mohon maaf, halaman ini tidak dapat diakses. <br>
                    Hal ini mungkin disebabkan oleh:
                </p>

                <ul class="text-start text-muted small bg-light p-3 rounded mb-4" style="list-style-position: inside;">
                    <li class="mb-1">Anda sudah pernah mengisi dan mengirim form ini sebelumnya.</li>
                    <li class="mb-1">Tautan sudah kadaluarsa atau dinonaktifkan oleh sistem.</li>
                    <li>Token URL tidak valid.</li>
                </ul>

                <p class="small text-muted">
                    Jika Anda merasa ini adalah kesalahan atau Anda belum menerima dokumen PDF lanjutan, silakan hubungi Administrator.
                </p>

                <div class="mt-3">
                    <a href="javascript:window.close()" class="btn btn-secondary btn-sm">Tutup</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
