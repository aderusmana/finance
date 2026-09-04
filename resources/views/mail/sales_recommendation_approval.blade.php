<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permohonan Persetujuan Rekomendasi Bank Garansi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #1e293b;">

    <div style="max-width: 680px; margin: 25px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 30px; text-align: center; color: white;">
            <h2 style="margin:0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">Permohonan Approval Rekomendasi BG</h2>
            <p style="margin:6px 0 0; opacity:0.85; font-size: 14px;">Divisi Sales & Marketing (dep-SNM)</p>
        </div>

        {{-- CONTENT --}}
        <div style="padding: 30px 35px;">
            <p style="margin-top: 0; font-size: 15px;">Yth. Bapak <strong>{{ $approver->name ?? 'Ronal Katili' }}</strong> (Department Head Sales - dep-SNM),</p>
            <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                Admin-RTM telah menyelesaikan kalkulasi dan mengajukan permohonan <strong>Rekomendasi Bank Garansi</strong> berikut. Mohon kesediaan Bapak untuk meninjau dan memberikan persetujuan sebelum tautan formulir pendaftaran diteruskan kepada distributor.
            </p>

            {{-- 1. DATA DISTRIBUTOR --}}
            <div style="font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                Informasi Distributor
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Distributor</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right;">{{ $recommendation->customer->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode PKD / Customer</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $recommendation->customer->no_pkd ?? '-' }} / {{ $recommendation->customer->code ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kota / Wilayah</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $recommendation->customer->city ?? '-' }} ({{ $recommendation->customer->area ?? '-' }})</td>
                </tr>
            </table>

            {{-- 2. RINCIAN PERHITUNGAN & SET BG --}}
            <div style="font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 20px; margin-bottom: 12px;">
                Parameter & Nilai Rekomendasi
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 25px;">
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%;">Rata-rata Penjualan (Average Sales)</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #0f172a; text-align: right;">Rp {{ number_format($recommendation->average ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%;">Term of Payment (TOP) / Lead Time</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $recommendation->top ?? 0 }} Hari / {{ $recommendation->lead_time ?? 0 }} Hari</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%;">Estimasi Inflasi</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $recommendation->inflation ?? 0 }}%</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%;">Kalkulasi Credit Limit Sistem</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #64748b; text-align: right;">Rp {{ number_format($recommendation->rounded_credit_limit ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f0fdf4;">
                    <td style="padding: 12px 10px; border-bottom: 1px solid #bbf7d0; font-weight: 800; color: #166534; font-size: 15px;">
                        SET BANK GARANSI (Rekomendasi)
                    </td>
                    <td style="padding: 12px 10px; border-bottom: 1px solid #bbf7d0; font-weight: 800; color: #16a34a; text-align: right; font-size: 17px;">
                        Rp {{ number_format($recommendation->set_bg ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="background-color: #eff6ff;">
                    <td style="padding: 10px; border-bottom: 1px solid #bfdbfe; font-weight: 700; color: #1e40af;">
                        Updated Credit Limit
                    </td>
                    <td style="padding: 10px; border-bottom: 1px solid #bfdbfe; font-weight: 700; color: #1d4ed8; text-align: right; font-size: 15px;">
                        Rp {{ number_format($recommendation->credit_limit_updated ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @if($recommendation->notes)
                <tr>
                    <td style="padding: 8px 0; color: #64748b; vertical-align: top;">Catatan Pengajuan</td>
                    <td style="padding: 8px 0; font-style: italic; color: #475569; text-align: right;">{{ $recommendation->notes }}</td>
                </tr>
                @endif
            </table>

            {{-- 3. TOMBOL AKSI CEPAT --}}
            <div style="text-align: center; margin: 30px 0 15px;">
                <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}" 
                   style="display: inline-block; background-color: #16a34a; color: white; padding: 14px 28px; font-weight: 700; text-decoration: none; border-radius: 8px; font-size: 15px; box-shadow: 0 3px 8px rgba(22, 163, 74, 0.35); margin-right: 12px; margin-bottom: 10px;">
                    ✓ Approve Rekomendasi (Kirim ke Customer)
                </a>

                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}" 
                   style="display: inline-block; background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 14px 24px; font-weight: 600; text-decoration: none; border-radius: 8px; font-size: 14px; margin-bottom: 10px;">
                    Review / Beri Catatan Revisi
                </a>
            </div>

            <div style="background-color: #f8fafc; border-radius: 8px; padding: 12px 16px; margin-top: 20px; font-size: 12px; color: #64748b; line-height: 1.5; text-align: center;">
                <em>Catatan: Setelah Bapak menekan <strong>Approve</strong>, sistem akan langsung mengenerate token portal dan mengirimkan email konfirmasi ke pihak distributor untuk melengkapi data Bank Garansi.</em>
            </div>

        </div>

        {{-- FOOTER --}}
        <div style="background: #f8fafc; padding: 18px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
            Email ini dikirimkan otomatis oleh Sistem Bank Garansi Finance SMII. Harap tidak membalas langsung ke alamat ini.
        </div>
    </div>

</body>
</html>
