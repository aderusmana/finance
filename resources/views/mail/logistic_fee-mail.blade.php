<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Logistic Fee Notification</title>
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

    @php
        $headerBg = '#1e3a8a'; // Default Blue for Request
        $statusBadge = 'Menunggu Persetujuan';
        $headerTitle = 'Persetujuan Logistic Fee';

        if($type === 'completed') {
            $headerBg = '#059669'; // Green
            $statusBadge = 'Status: Completed';
            $headerTitle = 'Pengajuan Disetujui';
        } elseif ($type === 'rejected') {
            $headerBg = '#dc2626'; // Red
            $statusBadge = 'Status: Rejected';
            $headerTitle = 'Pengajuan Ditolak';
        }
    @endphp

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    {{-- HEADER --}}
                    <tr>
                        <td bgcolor="{{ $headerBg }}" style="background-color: {{ $headerBg }}; padding: 35px 30px; text-align: center;">
                            <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto 12px; border-collapse: separate;">
                                <tr>
                                    <td bgcolor="#ffffff" style="background-color: rgba(255, 255, 255, 0.2); padding: 5px 14px; border-radius: 50px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255, 255, 255, 0.4);">
                                        {{ $statusBadge }}
                                    </td>
                                </tr>
                            </table>
                            <h1 style="margin: 0; color: #ffffff; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; line-height: 1.3;">{{ $headerTitle }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px; background-color: #ffffff;">
                            @if($type === 'request')
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #334155;">Halo <strong>{{ $extraData['approverName'] ?? 'Approver' }}</strong>,</p>
                                <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">Terdapat pengajuan penyesuaian harga <strong>Logistic Fee</strong> yang membutuhkan persetujuan Anda. Berikut adalah rinciannya:</p>
                            @elseif($type === 'completed')
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #334155;">Halo,</p>
                                <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">Proses persetujuan untuk perubahan <strong>Logistic Fee</strong> telah selesai. Perubahan harga kini telah aktif di dalam sistem.</p>
                            @elseif($type === 'rejected')
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #334155;">Halo,</p>
                                <p style="margin: 0 0 20px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">Kami menginformasikan bahwa pengajuan penyesuaian <strong>Logistic Fee</strong> Anda tidak dapat disetujui untuk saat ini dan telah ditolak oleh <em>Approver</em>.</p>

                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 8px 8px 0; margin-bottom: 25px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 15px 18px; font-family: 'Segoe UI', Arial, sans-serif;">
                                            <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase;">Alasan Penolakan:</p>
                                            <p style="margin: 0; font-size: 14px; font-style: italic; color: #991b1b; line-height: 1.5;">"{{ $extraData['notes'] ?? '-' }}"</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background-color: #f8fafc; border-collapse: collapse; margin-bottom: 25px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600; font-family: 'Segoe UI', Arial, sans-serif;">Distributor</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; text-align: right; color: #0f172a; font-family: 'Segoe UI', Arial, sans-serif;">{{ $logisticData->distributor->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600; font-family: 'Segoe UI', Arial, sans-serif;">Customer</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; text-align: right; color: #0f172a; font-family: 'Segoe UI', Arial, sans-serif;">{{ $logisticData->customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600; font-family: 'Segoe UI', Arial, sans-serif;">Harga {{ $type === 'request' ? 'Saat Ini' : 'Sebelumnya' }}</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; font-weight: 600; color: #64748b; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($type === 'completed' ? ($extraData['oldFee'] ?? 0) : $logisticData->logistic_fee, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; font-size: 13px; color: #64748b; font-weight: 600; font-family: 'Segoe UI', Arial, sans-serif;">Harga {{ $type === 'completed' ? 'Disetujui' : 'Diajukan' }}</td>
                                    <td style="padding: 12px 16px; font-size: 17px; font-weight: 800; color: {{ $type === 'completed' ? '#059669' : ($type === 'rejected' ? '#dc2626' : '#ea580c') }}; text-align: right; font-family: 'Segoe UI', Arial, sans-serif;">
                                        Rp {{ number_format($type === 'completed' ? ($extraData['newFee'] ?? $logisticData->proposed_fee) : $logisticData->proposed_fee, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'completed' && !empty($extraData['notes']))
                                <p style="margin: 20px 0 8px 0; font-weight: 600; font-size: 14px; font-family: 'Segoe UI', Arial, sans-serif;">Catatan Penyetuju Terakhir:</p>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f0fdf4; border-left: 4px solid #059669; border-radius: 0 6px 6px 0; margin-bottom: 25px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 14px 16px; font-style: italic; color: #166534; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.5;">
                                            "{{ $extraData['notes'] }}"
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- BUTTON (Bulletproof Table-Cell Button) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            <tr>
                                                @if($type === 'request')
                                                    <td align="center" bgcolor="#2563eb" style="border-radius: 8px; background-color: #2563eb;">
                                                        <a href="{{ url('/logistic-fees/approval/form/' . $extraData['log']->token . '/approve_with_review') }}"
                                                           target="_blank"
                                                           style="display: inline-block; padding: 14px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 8px; border: 1px solid #2563eb; line-height: 1.2;">
                                                            Tinjau Pengajuan
                                                        </a>
                                                    </td>
                                                @else
                                                    <td align="center" bgcolor="{{ $type === 'completed' ? '#059669' : '#dc2626' }}" style="border-radius: 8px; background-color: {{ $type === 'completed' ? '#059669' : '#dc2626' }};">
                                                        <a href="{{ url('/logistic-fees') }}"
                                                           target="_blank"
                                                           style="display: inline-block; padding: 14px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 8px; border: 1px solid {{ $type === 'completed' ? '#059669' : '#dc2626' }}; line-height: 1.2;">
                                                            Buka Sistem
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; font-weight: 500; line-height: 1.5;">
                                Sistem Persetujuan Digital &copy; {{ date('Y') }} PT Sinar Meadow<br>
                                Pesan ini dikirim secara otomatis oleh sistem, mohon tidak membalas.
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
