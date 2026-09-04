<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review & Persetujuan Rekomendasi Bank Garansi - Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
        }
        .main-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            width: 100%;
            max-width: 720px;
        }
        .header-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);
            padding: 30px;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="main-card">
    <div class="header-bg">
        <h4 class="fw-bold mb-1">Review Rekomendasi Bank Garansi</h4>
        <p class="mb-0 opacity-75 small">Divisi Sales & Marketing (Pak Ronal Katili - dep-SNM)</p>
    </div>

    <div class="p-4 p-md-5">
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        {{-- INFO DISTRIBUTOR --}}
        <div class="card mb-4 border bg-light-subtle">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold text-dark fs-6"><i class="bi bi-building me-2 text-primary"></i>Informasi Distributor</h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nama Distributor</small>
                        <span class="fw-bold text-dark">{{ $recommendation->customer->name ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Kode PKD / Customer</small>
                        <span class="fw-semibold text-dark">{{ $recommendation->customer->no_pkd ?? '-' }} / {{ $recommendation->customer->code ?? '-' }}</span>
                    </div>
                    <div class="col-sm-12">
                        <small class="text-muted d-block">Kota & Wilayah</small>
                        <span class="text-dark">{{ $recommendation->customer->city ?? '-' }} ({{ $recommendation->customer->area ?? '-' }})</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- PARAMETER REKOMENDASI --}}
        <div class="card mb-4 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold text-dark fs-6"><i class="bi bi-calculator me-2 text-primary"></i>Kalkulasi & Rekomendasi Nilai</h6>
            </div>
            <div class="card-body p-3">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width: 50%;">Rata-rata Penjualan (Average Sales)</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($recommendation->average ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">TOP / Lead Time</td>
                        <td class="text-end fw-semibold text-dark">{{ $recommendation->top ?? 0 }} Hari / {{ $recommendation->lead_time ?? 0 }} Hari</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Estimasi Inflasi</td>
                        <td class="text-end fw-semibold text-dark">{{ $recommendation->inflation ?? 0 }}%</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kalkulasi Credit Limit Sistem</td>
                        <td class="text-end fw-semibold text-muted">Rp {{ number_format($recommendation->rounded_credit_limit ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-success">
                        <td class="fw-bold text-success py-2">SET BANK GARANSI (Rekomendasi)</td>
                        <td class="text-end fw-bold text-success fs-5 py-2">Rp {{ number_format($recommendation->set_bg ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td class="fw-bold text-primary py-2">Updated Credit Limit</td>
                        <td class="text-end fw-bold text-primary fs-6 py-2">Rp {{ number_format($recommendation->credit_limit_updated ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- FORM AKSI --}}
        <form action="{{ route('approval.submit', ['token' => $log->token]) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="notes" class="form-label small fw-semibold text-muted">Catatan / Alasan Penolakan (Wajib diisi jika memilih Reject)</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Tuliskan catatan arahan atau alasan jika menolak..."></textarea>
            </div>

            <div class="d-flex gap-3 justify-content-between">
                <button type="submit" name="action" value="reject" class="btn btn-outline-danger w-50 py-2 fw-semibold" onclick="return confirmReject()">
                    <i class="bi bi-x-circle me-1"></i> Reject (Minta Revisi)
                </button>
                <button type="submit" name="action" value="approve" class="btn btn-success w-50 py-2 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Approve & Kirim ke Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmReject() {
    const notes = document.getElementById('notes').value.trim();
    if (notes.length < 3) {
        alert('Mohon isi catatan / alasan penolakan terlebih dahulu.');
        document.getElementById('notes').focus();
        return false;
    }
    return confirm('Apakah Anda yakin ingin menolak rekomendasi ini untuk direvisi oleh Admin-RTM?');
}
</script>
</body>
</html>
