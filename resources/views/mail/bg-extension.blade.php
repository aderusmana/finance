<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>BG Extension Notification</title>
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
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Arial, sans-serif; color: #334155;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td bgcolor="#059669" style="background-color: #059669; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 35px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; line-height: 1.3;">Extension Request</h1>
                            <p style="color: #d1fae5; margin: 8px 0 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.4;">Bank Guarantee Extension Request</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px; background-color: #ffffff;">
                            <p style="color: #334155; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 24px; margin: 0 0 20px 0;">
                                Dear <strong>{{ $rec->customer->name ?? 'Customer' }}</strong>, <br><br>
                                We have enabled the submission access for <strong>Extension (New BG)</strong>. Please click the button below to fill in the details for the Bank Guarantee.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 25px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tr>
                                    <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 12px 18px; color: #64748b; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; width: 35%;">Request Type</td>
                                    <td style="padding: 12px 18px; color: #1e293b; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0;">EXTENSION (NEW)</td>
                                </tr>
                                <tr>
                                    <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 12px 18px; color: #64748b; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; font-weight: 700; text-transform: uppercase;">Status</td>
                                    <td style="padding: 12px 18px; color: #1e293b; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px;">
                                        <span style="background-color: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 700;">WAITING INPUT</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- BUTTON (Bulletproof Table-Cell Button) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            <tr>
                                                <td align="center" bgcolor="#10b981" style="border-radius: 8px; background-color: #10b981;">
                                                    <a href="{{ route('customer.portal.input-form', ['token' => $rec->token]) }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 14px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 8px; border: 1px solid #10b981; line-height: 1.2;">
                                                        Fill in the Extension Form
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
                            <p style="color: #94a3b8; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; margin: 0; line-height: 1.5;">
                                &copy; {{ date('Y') }} Financial System. All rights reserved.
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
