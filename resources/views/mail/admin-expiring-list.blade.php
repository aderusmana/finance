<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Expiring BG Notification H-60</title>
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
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Arial, sans-serif; color: #2d3748;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7f6; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td bgcolor="#ffffff" style="background-color: #ffffff; padding: 25px 30px; border-bottom: 1px solid #edf2f7;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left" style="vertical-align: middle;">
                                        <img src="{{ $message->embed(public_path('assets/images/logo/logoputih.png')) }}" alt="Sinar Meadow" height="60" style="display: block; border: 0; outline: none; text-decoration: none;">
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <p style="margin: 0; color: #718096; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 700; line-height: 1.4;">
                                            PT. Sinar Meadow<br>International Indonesia
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#2c3e50" style="background-color: #2c3e50; padding: 12px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left">
                                        <p style="color: #ffffff; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <span style="color: #e74c3c; margin-right: 6px;">●</span> System Notification
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p style="color: #bdc3c7; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px;">H-60 Alert</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 30px; background-color: #ffffff;">
                            <p style="color: #2d3748; font-family: 'Segoe UI', Arial, sans-serif; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Hello, Team.</p>

                            <p style="color: #4a5568; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 0 0 25px 0;">
                                The system has detected <strong>Bank Guarantees</strong> that will expire within 60 days ({{ \Carbon\Carbon::now()->addDays(60)->format('d M Y') }}).
                                <br><br>
                                Below is the list of customers along with the Bank Guarantee details that require immediate attention:
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; margin-bottom: 30px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <thead>
                                    <tr bgcolor="#f8fafc" style="background-color: #f8fafc;">
                                        <th style="padding: 10px 12px; text-align: left; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Customer</th>
                                        <th style="padding: 10px 12px; text-align: left; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">No. BG</th>
                                        <th style="padding: 10px 12px; text-align: right; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Nominal</th>
                                        <th style="padding: 10px 12px; text-align: right; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Exp Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bgs as $index => $bg)
                                    <tr bgcolor="{{ $index % 2 == 0 ? '#ffffff' : '#fcfcfc' }}" style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#fcfcfc' }};">
                                        <td style="padding: 10px 12px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #2d3748; border-bottom: 1px solid #f1f5f9; font-weight: 600;">
                                            {{ $bg->customer->name ?? '-' }}
                                        </td>

                                        <td style="padding: 10px 12px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; border-bottom: 1px solid #f1f5f9;">
                                            <span style="background-color: #ebf8ff; color: #2b6cb0; padding: 3px 6px; border-radius: 4px; font-family: Consolas, monospace, Arial; font-weight: 600; font-size: 11px;">
                                                {{ $bg->bg_number }}
                                            </span>
                                        </td>

                                        <td style="padding: 10px 12px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #4a5568; text-align: right; border-bottom: 1px solid #f1f5f9;">
                                            Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                                        </td>

                                        <td style="padding: 10px 12px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #e53e3e; font-weight: 700; text-align: right; border-bottom: 1px solid #f1f5f9;">
                                            {{ \Carbon\Carbon::parse($bg->exp_date)->format('d M Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- BUTTON (Bulletproof Table-Cell Button) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            <tr>
                                                <td align="center" bgcolor="#3182ce" style="border-radius: 6px; background-color: #3182ce;">
                                                    <a href="{{ url('bg/bg-recommendations') }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 13px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 6px; border: 1px solid #3182ce; line-height: 1.2;">
                                                        Open Dashboard
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #a0aec0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; margin: 0; line-height: 1.5;">
                                &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia<br>
                                Automated Notification System.
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
