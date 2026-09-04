<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pemberitahuan: Lengkapi Data Bank Garansi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #1e293b;">

    <div style="max-width: 680px; margin: 25px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, #0f172a 0%, #d97706 100%); padding: 30px; text-align: center; color: white;">
            <h2 style="margin:0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">Tindakan Diperlukan: Lengkapi Data BG</h2>
            <p style="margin:6px 0 0; opacity:0.9; font-size: 14px;">Divisi Sales & Marketing (Tim Sales)</p>
        </div>

        {{-- CONTENT --}}
        <div style="padding: 30px 35px;">
            <p style="margin-top: 0; font-size: 15px;">Halo <strong>{{ $salesUser->name ?? 'Tim Sales' }}</strong>,</p>
            <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                Dokumen konfirmasi Bank Garansi yang diunggah oleh distributor telah diverifikasi oleh <strong>Admin-RTM</strong>.
                Selanjutnya, mohon kesediaan tim Sales untuk <strong>melengkapi data Bank Garansi</strong> (Nomor BG per bank, Tanggal Jatuh Tempo / Expired Date, penyesuaian Nominal jika ada, dan Scan Dokumen Bank Garansi Asli / Warkat) agar proses dapat diajukan kepada <strong>Bu Rita (Secretary Finance)</strong> untuk validasi.
            </p>

            {{-- 1. DATA DISTRIBUTOR --}}
            <div style="font-size: 13px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                Informasi Pengajuan Bank Garansi
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Distributor</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right;">{{ $customer->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode PKD / Form Code</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $customer->no_pkd ?? '-' }} / {{ $submission->form_code }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kota / Wilayah</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $customer->city ?? '-' }} ({{ $customer->area ?? '-' }})</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Status Saat Ini</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #d97706; text-align: right;">Menunggu Pengisian Data oleh Sales</td>
                </tr>
            </table>

            {{-- 2. PETUNJUK PENGISIAN --}}
            <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 18px; margin-bottom: 25px;">
                <strong style="color: #92400e; font-size: 13px; display: block; margin-bottom: 6px;">Hal-hal yang perlu diisi oleh Sales:</strong>
                <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #78350f; line-height: 1.6;">
                    <li><strong>Nomor BG:</strong> Masukkan nomor Bank Garansi resmi untuk setiap bank penerbit.</li>
                    <li><strong>Nominal BG:</strong> Pastikan nominal per bank telah sesuai dengan warkat fisik.</li>
                    <li><strong>Expired Date:</strong> Masukkan tanggal jatuh tempo warkat Bank Garansi.</li>
                    <li><strong>Upload File Warkat:</strong> Unggah scan asli warkat Bank Garansi fisik (PDF/JPG/PNG).</li>
                </ol>
            </div>

            {{-- CALL TO ACTION --}}
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('bg-submissions.index') }}"
                   style="display: inline-block; background-color: #d97706; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; padding: 14px 28px; border-radius: 8px; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3);">
                    Buka Submission Center & Lengkapi Data BG &rarr;
                </a>
            </div>

            <p style="font-size: 13px; color: #94a3b8; text-align: center; margin-top: 25px; line-height: 1.4;">
                Setelah data disimpan dan diajukan, pengajuan akan langsung masuk ke Inbox Validasi Secretary Finance (Bu Rita).
            </p>
        </div>

        {{-- FOOTER --}}
        <div style="background: #f8fafc; padding: 18px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
            Email dibuat secara otomatis oleh Sistem Bank Garansi Finance. Mohon tidak membalas email ini secara langsung.
        </div>

    </div>

</body>
</html>
