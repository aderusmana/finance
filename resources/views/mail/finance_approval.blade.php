<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Permohonan Validasi Bank Garansi</title>
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
                            <h2 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #ffffff; line-height: 1.3;">Permohonan Validasi Bank Garansi</h2>
                            <p style="margin: 6px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #bfdbfe; line-height: 1.4;">Validasi Dokumen Bank Garansi & Lampiran D</p>
                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td style="padding: 30px 35px; background-color: #ffffff;">
                            <p style="margin: 0 0 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.5;">Yth. Ibu <strong>{{ $approver->name ?? 'Rita Rahayu' }}</strong> (Secretary Finance),</p>
                            <p style="margin: 0 0 25px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #475569;">
                                Terdapat pengajuan Bank Guarantee yang membutuhkan validasi dan verifikasi Bank Garansi dari Anda sebelum data masuk ke daftar aktif dan credit limit diperbarui secara otomatis.
                            </p>

                            {{-- 1. DATA DISTRIBUTOR --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                                Data Distributor
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 20px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Nama Distributor</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $submission->recommendation->customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kode PKD / Customer</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $submission->recommendation->customer->no_pkd ?? '-' }} / {{ $submission->recommendation->customer->code ?? '-' }}</td>
                                </tr>
                                @if($submission->custom_address)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Alamat Operasional</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">{{ $submission->custom_address }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Kode Formulir</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #2563eb; font-family: monospace, Arial, sans-serif; text-align: right;">{{ $submission->form_code }}</td>
                                </tr>
                            </table>

                            {{-- 2. VERIFIKASI BANK GARANSI & LIMIT --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 20px; margin-bottom: 12px;">
                                Rincian Bank Garansi
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 25px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Nomor Bank Garansi</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; font-family: monospace, Arial, sans-serif; color: #0f172a; text-align: right;">{{ $submission->bg_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Nominal BG</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #16a34a; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($submission->bg_nominal ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Tanggal Expired</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #dc2626; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">
                                        {{ $submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->format('d F Y') : '-' }}
                                    </td>
                                </tr>
                                @if($submission->recommendation)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%; font-family: 'Segoe UI', Arial, sans-serif;">Updated Credit Limit</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1d4ed8; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($submission->recommendation->credit_limit_updated, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                            </table>

                            {{-- 3. TAUTAN DOKUMEN --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 25px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 15px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 10px;">
                                            Dokumen Pendukung:
                                        </div>
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;">
                                            <tr>
                                                @if($submission->warkat_file_path)
                                                <td bgcolor="#eff6ff" style="border-radius: 6px; border: 1px solid #bfdbfe; padding: 0; background-color: #eff6ff;">
                                                    <a href="{{ asset($submission->warkat_file_path) }}" target="_blank" style="display: inline-block; padding: 8px 16px; font-family: 'Segoe UI', Arial, sans-serif; color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 600;">
                                                        📄 Buka Scan Bank Garansi
                                                    </a>
                                                </td>
                                                @endif
                                                @if($submission->warkat_file_path && $submission->signed_document_path)
                                                <td width="10" style="width: 10px;">&nbsp;</td>
                                                @endif
                                                @if($submission->signed_document_path)
                                                <td bgcolor="#f0fdf4" style="border-radius: 6px; border: 1px solid #bbf7d0; padding: 0; background-color: #f0fdf4;">
                                                    <a href="{{ asset($submission->signed_document_path) }}" target="_blank" style="display: inline-block; padding: 8px 16px; font-family: 'Segoe UI', Arial, sans-serif; color: #16a34a; text-decoration: none; font-size: 13px; font-weight: 600;">
                                                        📑 Buka Formulir Bertandatangan
                                                    </a>
                                                </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- ACTION BUTTONS (Bulletproof Outlook Buttons) --}}
                            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                                <p style="margin: 0 0 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #64748b; font-weight: 600;">Pilih tindakan validasi di bawah ini:</p>

                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto; border-collapse: separate;">
                                    <tr>
                                        <td align="center" bgcolor="#16a34a" style="border-radius: 50px; background-color: #16a34a;">
                                            <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 22px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid #16a34a; line-height: 1.2;">
                                                ✅ Quick Approve
                                            </a>
                                        </td>
                                        <td width="10" style="width: 10px;">&nbsp;</td>
                                        <td align="center" bgcolor="#2563eb" style="border-radius: 50px; background-color: #2563eb;">
                                            <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 22px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid #2563eb; line-height: 1.2;">
                                                📝 Review / Detail Form
                                            </a>
                                        </td>
                                        <td width="10" style="width: 10px;">&nbsp;</td>
                                        <td align="center" bgcolor="#dc2626" style="border-radius: 50px; background-color: #dc2626;">
                                            <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'reject']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 22px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid #dc2626; line-height: 1.2;">
                                                ❌ Reject / Revisi
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; text-align: center; margin: 25px 0 0; line-height: 1.4;">
                                Link validasi di atas aktif selama status pengajuan masih Pending. Anda juga dapat melakukan validasi melalui dashboard web pada menu Approval Inbox.
                            </p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 16px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; line-height: 1.5;">
                            &copy; {{ date('Y') }} PT SMII - Sistem Otomasi Bank Garansi
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
