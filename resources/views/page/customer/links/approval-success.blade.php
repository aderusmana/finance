<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Action Processed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background-color: #f1f5f9; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); overflow: hidden; max-width: 500px; width: 90%; background: white; }

        .card-header { padding: 30px 20px; text-align: center; border-bottom: none; }
        .header-approve { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; }
        .header-reject { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); color: white; }
        .header-review { background: linear-gradient(135deg, #0e7490 0%, #06b6d4 100%); color: white; }

        .icon-circle { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 32px; backdrop-filter: blur(5px); }

        .card-body { padding: 40px 30px; text-align: center; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 25px 0; text-align: left; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { color: #64748b; font-weight: 500; }
        .info-value { color: #0f172a; font-weight: 600; }

        .btn-close-tab { background-color: #1e293b; color: white; border-radius: 50px; padding: 12px 30px; font-weight: 600; text-decoration: none; transition: all 0.3s; display: inline-block; border: none; }
        .btn-close-tab:hover { background-color: #334155; transform: translateY(-2px); color: white; }
    </style>
</head>
<body>
    <div class="card">
        @php
            $headerClass = 'header-approve';
            $icon = 'fa-check';
            $title = 'Approval Successful';

            if($action === 'reject') {
                $headerClass = 'header-reject';
                $icon = 'fa-times';
                $title = 'Request Rejected';
            } elseif($action === 'review') {
                $headerClass = 'header-review';
                $icon = 'fa-clipboard-list';
                $title = 'Review Submitted';
            }
        @endphp

        <div class="card-header {{ $headerClass }}">
            <div class="icon-circle">
                <i class="fas {{ $icon }}"></i>
            </div>
            <h2 class="m-0 fw-bold" style="font-size: 24px;">{{ $title }}</h2>
        </div>

        <div class="card-body">
            <p class="text-muted">
                Terima kasih, respon Anda telah berhasil dicatat.
                Halaman ini akan tertutup otomatis dalam <b id="countdown">3</b> detik.
            </p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Customer</span>
                    <span class="info-value">{{ $customerName }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Action Taken</span>
                    <span class="info-value" style="text-transform: capitalize;">{{ $action }}</span>
                </div>
                {{-- FIELD BARU: ROUTE TO --}}
                <div class="info-row">
                    <span class="info-label">Route To</span>
                    <span class="info-value text-primary">{{ $routeTo ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ date('d M Y H:i') }}</span>
                </div>
            </div>

            <button onclick="window.close()" class="btn-close-tab">
                Close Now
            </button>
        </div>
    </div>

    {{-- SCRIPT AUTO CLOSE --}}
    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdownEl.innerText = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                window.close();
                // Fallback jika browser memblokir window.close()
                document.body.innerHTML = "<div style='display:flex; height:100vh; justify-content:center; align-items:center; color:#64748b;'>Halaman sudah tidak aktif. Silakan tutup tab ini.</div>";
            }
        }, 1000);
    </script>
</body>
</html>
