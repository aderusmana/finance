<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background: #1e3a8a; padding: 25px; text-align: center; color: white; }
        .content { padding: 30px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .data-table td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; width: 40%; }
        .value { font-weight: 600; color: #333; text-align: right; }
        
        .btn-group { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1; }
        .btn { display: inline-block; padding: 12px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 5px; color: white !important; }
        .btn-approve { background-color: #166534; } /* Green */
        .btn-review { background-color: #ca8a04; } /* Yellow/Orange */
        .btn-reject { background-color: #991b1b; } /* Red */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Approval Request</h2>
            <p style="margin:5px 0 0; opacity:0.8;">Bank Garansi - Lampiran D</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $approver->name }}</strong>,</p>
            <p>Terdapat pengajuan Bank Garansi yang telah direvisi oleh Admin dan memerlukan persetujuan Anda.</p>

            {{-- 1. DATA CUSTOMER --}}
            <div class="section-title">Data Customer</div>
            <table class="data-table">
                <tr>
                    <td class="label">Nama Customer</td>
                    <td class="value">{{ $submission->recommendation->customer->name }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Customer</td>
                    <td class="value">{{ $submission->recommendation->customer->code }}</td>
                </tr>
            </table>

            {{-- 2. DATA RECOMMENDATION --}}
            <div class="section-title">Analisa Limit Kredit</div>
            <table class="data-table">
                <tr>
                    <td class="label">Limit Disetujui (Updated)</td>
                    <td class="value">Rp {{ number_format($submission->recommendation->credit_limit_updated, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Set BG</td>
                    <td class="value">Rp {{ number_format($submission->recommendation->set_bg, 0, ',', '.') }}</td>
                </tr>
            </table>

            {{-- 3. DATA BANK GARANSI (LAMPIRAN D) --}}
            <div class="section-title">Detail Bank Garansi (Lampiran D)</div>
            @php
                // Ambil BG yang terkait dengan customer ini dan status submitted
                $bg = \App\Models\BG\BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                        ->where('status', 'submitted')->latest()->with('details')->first();
            @endphp

            @if($bg)
                <table class="data-table">
                    @foreach($bg->details as $idx => $detail)
                    <tr>
                        <td colspan="2" style="background: #f8fafc; padding: 5px 10px; font-weight: bold; font-size: 12px; color: #1e3a8a;">
                            Bank {{ $idx + 1 }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Nama Bank</td>
                        <td class="value">{{ $detail->bank_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Cabang</td>
                        <td class="value">{{ $detail->branch_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nominal</td>
                        <td class="value">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="label" style="font-weight: bold; padding-top: 15px;">TOTAL PENGAJUAN</td>
                        <td class="value" style="font-weight: bold; padding-top: 15px; font-size: 16px; color: #1e3a8a;">
                            Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @else
                <p style="color: red; font-style: italic;">Data Detail BG tidak ditemukan.</p>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="btn-group">
                {{-- Quick Approve: Langsung Success --}}
                <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}" class="btn btn-approve">
                    ✅ Quick Approve
                </a>
                
                {{-- Review with Notes: Ke Form --}}
                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}" class="btn btn-review">
                    📝 Review with Notes
                </a>

                {{-- Reject: Ke Form --}}
                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'reject']) }}" class="btn btn-reject">
                    ❌ Reject
                </a>
            </div>
            
            <p style="text-align: center; font-size: 11px; color: #94a3b8; margin-top: 20px;">
                Klik tombol di atas untuk memproses. Link ini valid selama status masih Pending.
            </p>
        </div>
    </div>
</body>
</html>