<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Permohonan Persetujuan Rekomendasi Bank Garansi</title>
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
                        <td bgcolor="#1e40af" style="background-color: #1e40af; background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 30px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #ffffff; line-height: 1.3;">Permohonan Approval Rekomendasi BG</h2>
                            <p style="margin: 6px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #bfdbfe; line-height: 1.4;">Divisi Sales & Marketing (dep-SNM)</p>
                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td style="padding: 30px 35px; background-color: #ffffff;">
                            <p style="margin: 0 0 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.5;">Yth. Bapak <strong>{{ $approver->name ?? 'Ronal Katili' }}</strong> (Department Head Sales - dep-SNM),</p>
                            <p style="margin: 0 0 25px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #475569;">
                                Admin-RTM telah menyelesaikan kalkulasi dan mengajukan permohonan <strong>Rekomendasi Bank Garansi</strong> berikut. Mohon kesediaan Bapak untuk meninjau dan memberikan persetujuan sebelum tautan formulir pendaftaran diteruskan kepada distributor.
                            </p>

                            {{-- 1. DATA DISTRIBUTOR --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                                Informasi Distributor
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 20px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Nama Distributor</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kode PKD / Customer</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->customer->no_pkd ?? '-' }} / {{ $recommendation->customer->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kota / Wilayah</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->customer->city ?? '-' }} ({{ $recommendation->customer->area ?? '-' }})</td>
                                </tr>
                            </table>

                            {{-- 2. RINCIAN PERHITUNGAN & SET BG --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 20px; margin-bottom: 12px;">
                                Parameter & Nilai Rekomendasi
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 25px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%; font-family: 'Segoe UI', Arial, sans-serif;">Rata-rata Penjualan (Average Sales)</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #0f172a; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">Rp {{ number_format($recommendation->average ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%; font-family: 'Segoe UI', Arial, sans-serif;">Term of Payment (TOP) / Lead Time</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->top ?? 0 }} Hari / {{ $recommendation->lead_time ?? 0 }} Hari</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%; font-family: 'Segoe UI', Arial, sans-serif;">Estimasi Inflasi</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->inflation ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 45%; font-family: 'Segoe UI', Arial, sans-serif;">Kalkulasi Credit Limit Sistem</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #64748b; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">Rp {{ number_format($recommendation->rounded_credit_limit ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f0fdf4" style="background-color: #f0fdf4; padding: 12px 10px; border-bottom: 1px solid #bbf7d0; font-weight: 800; color: #166534; font-size: 15px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        SET BANK GARANSI (Rekomendasi)
                                    </td>
                                    <td bgcolor="#f0fdf4" style="background-color: #f0fdf4; padding: 12px 10px; border-bottom: 1px solid #bbf7d0; font-weight: 800; color: #16a34a; text-align: right; font-size: 17px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($recommendation->set_bg ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td bgcolor="#eff6ff" style="background-color: #eff6ff; padding: 10px; border-bottom: 1px solid #bfdbfe; font-weight: 700; color: #1e40af; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Updated Credit Limit
                                    </td>
                                    <td bgcolor="#eff6ff" style="background-color: #eff6ff; padding: 10px; border-bottom: 1px solid #bfdbfe; font-weight: 700; color: #1d4ed8; text-align: right; font-size: 15px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($recommendation->credit_limit_updated ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($recommendation->notes)
                                <tr>
                                    <td style="padding: 8px 0; color: #64748b; vertical-align: top; font-family: 'Segoe UI', Arial, sans-serif;">Catatan Pengajuan</td>
                                    <td style="padding: 8px 0; font-style: italic; color: #475569; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $recommendation->notes }}</td>
                                </tr>
                                @endif
                            </table>

                            {{-- 3. TOMBOL AKSI CEPAT (Bulletproof Outlook Buttons) --}}
                            <div style="text-align: center; margin: 30px 0 15px;">
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto; border-collapse: separate;">
                                    <tr>
                                        <td align="center" bgcolor="#16a34a" style="border-radius: 8px; background-color: #16a34a;">
                                            <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 14px 26px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 8px; border: 1px solid #16a34a; line-height: 1.2;">
                                                ✓ Approve Rekomendasi (Kirim ke Customer)
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="12" style="height: 12px; font-size: 12px; line-height: 12px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#f1f5f9" style="border-radius: 8px; background-color: #f1f5f9;">
                                            <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 24px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: 600; color: #334155; text-decoration: none; border-radius: 8px; border: 1px solid #cbd5e1; line-height: 1.2;">
                                                Review / Beri Catatan Revisi
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 8px; margin-top: 20px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 12px 16px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #64748b; line-height: 1.5; text-align: center;">
                                        <em>Catatan: Setelah Bapak menekan <strong>Approve</strong>, sistem akan langsung mengenerate token portal dan mengirimkan email konfirmasi ke pihak distributor untuk melengkapi data Bank Garansi.</em>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 18px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; line-height: 1.5;">
                            Email ini dikirimkan otomatis oleh Sistem Bank Garansi Finance SMII. Harap tidak membalas langsung ke alamat ini.
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
