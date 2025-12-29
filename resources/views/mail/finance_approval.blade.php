<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Approval Request</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0;">

    <div style="max-width: 700px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

        {{-- HEADER --}}
        <div style="background: #1e3a8a; padding: 25px; text-align: center; color: white;">
            <h2 style="margin:0; font-size: 24px;">Approval Request</h2>
            <p style="margin:5px 0 0; opacity:0.8;">Bank Garansi - Lampiran D</p>
        </div>

        {{-- CONTENT --}}
        <div style="padding: 30px;">
            <p style="margin-top: 0;">Yth. <strong>{{ $approver->name }}</strong>,</p>
            <p>Terdapat pengajuan Bank Garansi yang telah direvisi oleh Admin dan memerlukan persetujuan Anda.</p>

            {{-- 1. DATA CUSTOMER --}}
            <div style="font-size: 14px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px;">
                Data Customer
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Customer</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">{{ $submission->recommendation->customer->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode Customer</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">{{ $submission->recommendation->customer->code }}</td>
                </tr>
            </table>

            {{-- 2. DATA RECOMMENDATION --}}
            <div style="font-size: 14px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px;">
                Analisa Limit Kredit
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Limit Disetujui (Updated)</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">
                        Rp {{ number_format($submission->recommendation->credit_limit_updated, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Set BG</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">
                        Rp {{ number_format($submission->recommendation->set_bg, 0, ',', '.') }}
                    </td>
                </tr>
            </table>

            {{-- 3. DATA BANK GARANSI (LAMPIRAN D) --}}
            <div style="font-size: 14px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px;">
                Detail Bank Garansi (Lampiran D)
            </div>

            @php
                $bg = \App\Models\BG\BankGaransi::where('customer_id', $submission->recommendation->customer_id)
                        ->where('status', 'submitted')->latest()->with('details')->first();
            @endphp

            @if($bg)
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    @foreach($bg->details as $idx => $detail)
                    <tr>
                        <td colspan="2" style="background: #f8fafc; padding: 8px 10px; font-weight: bold; font-size: 12px; color: #1e3a8a; border-bottom: 1px solid #f1f5f9;">
                            Bank {{ $idx + 1 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Bank</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">{{ $detail->bank_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Cabang</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">{{ $detail->branch_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nominal</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #333; text-align: right;">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="padding: 15px 0 8px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-weight: bold;">TOTAL PENGAJUAN</td>
                        <td style="padding: 15px 0 8px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #1e3a8a; text-align: right; font-size: 16px;">
                            Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>
            @else
                <p style="color: #ef4444; font-style: italic;">Data Detail BG tidak ditemukan.</p>
            @endif

            {{-- ACTION BUTTONS --}}
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                {{-- Quick Approve: Langsung Success --}}
                <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}"
                   style="display: inline-block; padding: 12px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 5px; color: white !important; background-color: #166534;">
                    ✅ Quick Approve
                </a>

                {{-- Review with Notes: Ke Form --}}
                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}"
                   style="display: inline-block; padding: 12px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 5px; color: white !important; background-color: #ca8a04;">
                    📝 Review with Notes
                </a>

                {{-- Reject: Ke Form --}}
                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'reject']) }}"
                   style="display: inline-block; padding: 12px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 5px; color: white !important; background-color: #991b1b;">
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
