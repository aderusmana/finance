<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pemberitahuan: Lengkapi Data Bank Garansi</title>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, th, p, a, li, span, h1, h2, h3, h4, h5, h6 {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 25px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="650">
                <tr>
                <td align="center" valign="top" width="650">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 650px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    {{-- HEADER --}}
                    <tr>
                        <td bgcolor="#d97706" style="background-color: #d97706; background: linear-gradient(135deg, #0f172a 0%, #d97706 100%); padding: 30px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #ffffff; line-height: 1.3;">Tindakan Diperlukan: Lengkapi Data BG</h2>
                            <p style="margin: 6px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #fef3c7; line-height: 1.4;">Divisi Sales & Marketing (Tim Sales)</p>
                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td style="padding: 30px 35px; background-color: #ffffff;">
                            <p style="margin: 0 0 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.5;">Halo <strong>{{ $salesUser->name ?? 'Tim Sales' }}</strong>,</p>
                            <p style="margin: 0 0 25px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #475569;">
                                Dokumen konfirmasi Bank Garansi yang diunggah oleh distributor telah diverifikasi oleh <strong>Admin-RTM</strong>.
                                Selanjutnya, mohon kesediaan tim Sales untuk <strong>melengkapi data Bank Garansi</strong> (Nomor BG per bank, Tanggal Jatuh Tempo / Expired Date, penyesuaian Nominal jika ada, dan Scan Dokumen Bank Garansi Asli / Warkat) agar proses dapat diajukan kepada <strong>Bu Rita (Secretary Finance)</strong> untuk validasi.
                            </p>

                            {{-- 1. DATA DISTRIBUTOR --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                                Informasi Pengajuan Bank Garansi
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 20px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Nama Distributor</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kode PKD / Form Code</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $customer->no_pkd ?? '-' }} / {{ $submission->form_code }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kota / Wilayah</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $customer->city ?? '-' }} ({{ $customer->area ?? '-' }})</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Status Saat Ini</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #d97706; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">Menunggu Pengisian Data oleh Sales</td>
                                </tr>
                            </table>

                            {{-- 2. PETUNJUK PENGISIAN --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; margin-bottom: 25px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 14px 18px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        <strong style="color: #92400e; font-size: 13px; display: block; margin-bottom: 6px;">Hal-hal yang perlu diisi oleh Sales:</strong>
                                        <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #78350f; line-height: 1.6;">
                                            <li><strong>Nomor BG:</strong> Masukkan nomor Bank Garansi resmi untuk setiap bank penerbit.</li>
                                            <li><strong>Nominal BG:</strong> Pastikan nominal per bank telah sesuai dengan warkat fisik.</li>
                                            <li><strong>Expired Date:</strong> Masukkan tanggal jatuh tempo warkat Bank Garansi.</li>
                                            <li><strong>Upload File Warkat:</strong> Unggah scan asli warkat Bank Garansi fisik (PDF/JPG/PNG).</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            {{-- CALL TO ACTION (Bulletproof Outlook Button) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0; border-collapse: collapse;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            <tr>
                                                <td align="center" bgcolor="#d97706" style="border-radius: 8px; background-color: #d97706;">
                                                    <a href="{{ route('bg-submissions.index') }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 14px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 8px; border: 1px solid #d97706; line-height: 1.2;">
                                                        Buka Submission Center & Lengkapi Data BG &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #94a3b8; text-align: center; margin: 25px 0 0; line-height: 1.4;">
                                Setelah data disimpan dan diajukan, pengajuan akan langsung masuk ke Inbox Validasi Secretary Finance (Bu Rita).
                            </p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 18px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; line-height: 1.5;">
                            Email dibuat secara otomatis oleh Sistem Bank Garansi Finance. Mohon tidak membalas email ini secara langsung.
                        </td>
                    </tr>

                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>

</body>
</html>
