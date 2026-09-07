<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Informasi Sinkronisasi Credit Limit</title>
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
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; color: #334155;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    {{-- Header --}}
                    <tr>
                        <td bgcolor="#1e40af" style="background-color: #1e40af; background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 30px 35px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 20px; font-weight: 700; letter-spacing: 0.5px; line-height: 1.3;">
                                NOTIFIKASI IT: CREDIT LIMIT UPDATED
                            </h1>
                            <p style="color: #93c5fd; margin: 6px 0 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; line-height: 1.4;">Proses Otomatis Background - Hanya Informasi (No Action Required)</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 35px 35px; background-color: #ffffff; color: #334155; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6;">
                            <p style="margin: 0 0 15px 0;">Halo Tim IT,</p>

                            <p style="margin: 0 0 20px 0;">Pemberitahuan bahwa verifikasi Bank Guarantee dan Lampiran D untuk distributor di bawah ini telah disetujui oleh <strong>{{ $validatorName ?? 'Secretary Finance (Bu Rita)' }}</strong>.</p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; background-color: #ecfdf5; border-left: 4px solid #10b981; border-radius: 4px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 14px 18px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        <p style="margin: 0; font-size: 13px; color: #065f46; font-weight: 600; line-height: 1.5;">
                                            ✓ Perhitungan dan pembaruan Credit Limit telah diproses secara otomatis di background ke database customer. Tim IT TIDAK PERLU melakukan pengecekan manual atau tindakan verifikasi lanjutan.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; width: 40%; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Nama Customer</td>
                                    <td style="padding: 10px 15px; font-weight: 700; font-size: 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">{{ $customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Kode PKD / Akun</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">{{ $customer->no_pkd ?? '-' }} / {{ $customer->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Kode Formulir Submission</td>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #2563eb; font-family: Consolas, monospace, Arial; border-bottom: 1px solid #e2e8f0;">{{ $submission->form_code }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Nomor Bank Garansi</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">{{ $submission->bg_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">Tanggal Expired BG</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0; font-family: 'Segoe UI', Arial, sans-serif;">{{ $submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#eff6ff" style="background-color: #eff6ff; padding: 12px 15px; font-weight: 700; font-size: 14px; color: #1e40af; font-family: 'Segoe UI', Arial, sans-serif;">Updated Credit Limit</td>
                                    <td bgcolor="#eff6ff" style="background-color: #eff6ff; padding: 12px 15px; font-weight: 800; font-size: 16px; color: #1e40af; font-family: 'Segoe UI', Arial, sans-serif;">Rp {{ number_format($approvedCreditLimit, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <p style="font-size: 12px; color: #94a3b8; margin: 25px 0 0; line-height: 1.5; font-family: 'Segoe UI', Arial, sans-serif;">
                                Email ini dibuat dan dikirim secara otomatis oleh Sistem Bank Garansi & Credit Limit PT SMII untuk kebutuhan pencatatan dan monitoring tim IT.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 18px 35px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.5;">
                                &copy; {{ date('Y') }} PT SMII - Finance & IT Automation System
                            </p>
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
