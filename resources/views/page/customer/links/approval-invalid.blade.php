<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background-color: #f8fafc; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-width: 480px; width: 90%; background: white; border-top: 6px solid #64748b; }

        .icon-container { color: #64748b; font-size: 60px; margin-bottom: 20px; }
        .error-title { color: #334155; font-weight: 700; font-size: 22px; margin-bottom: 10px; }
        .error-desc { color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 30px; }

        .btn-home { background-color: white; color: #475569; border: 2px solid #e2e8f0; padding: 10px 25px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .btn-home:hover { background-color: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="card p-5 text-center">
        <div class="icon-container">
            <i class="fas fa-link-slash"></i>
        </div>
        <h1 class="error-title">Link No Longer Valid</h1>
        <p class="error-desc">
            Maaf, tautan approval ini tidak valid atau sudah kadaluarsa.
            Hal ini mungkin terjadi karena permintaan ini sudah diproses (disetujui/ditolak) sebelumnya.
        </p>
        <div>
            <button onclick="window.close()" class="btn-home">
                Close Page
            </button>
        </div>
    </div>
</body>
</html>
