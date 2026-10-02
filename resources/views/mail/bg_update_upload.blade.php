<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Upload Document {{ ucfirst($type) }}</title>
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
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Segoe UI', Arial, sans-serif; color: #334155;">

    @php
        $themeColor = ($type == 'existing') ? '#4f46e5' : '#059669';
    @endphp

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7f6; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
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
                        <td bgcolor="{{ $themeColor }}" style="background-color: {{ $themeColor }}; background: linear-gradient(135deg, {{ $type == 'existing' ? '#4f46e5 0%, #4338ca' : '#059669 0%, #047857' }} 100%); padding: 35px 30px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; color: #ffffff; line-height: 1.3;">Document Confirmation {{ ucfirst($type) }}</h2>
                            <p style="margin: 8px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #f1f5f9; line-height: 1.4;">Form Code: #{{ $submission->form_code }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 30px; background-color: #ffffff;">
                            <p style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #334155; margin: 0 0 25px 0;">
                                Dear <strong>{{ $submission->recommendation->customer->name ?? 'Business Partner' }}</strong>,<br><br>

                                @if($type == 'existing')
                                    Your Bank Guarantee nominal update data has been saved.
                                @else
                                    Your Bank Guarantee extension request data has been saved.
                                @endif
                                Please download the form below, sign it, and then re-upload it.
                            </p>

                            {{-- INSTRUKSI BOX --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 8px; margin-bottom: 30px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 18px 20px; font-family: 'Segoe UI', Arial, sans-serif;">
                                        <h3 style="margin: 0 0 10px; color: #9a3412; font-size: 15px; font-weight: 700;">⚠️ Steps:</h3>
                                        <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #9a3412; line-height: 1.6;">
                                            <li style="margin-bottom: 5px;">Click the <strong>Download Form</strong> button below.</li>
                                            <li style="margin-bottom: 5px;"><strong>Print & Sign</strong> (Wet Signature + Stamp).</li>
                                            <li style="margin-bottom: 5px;"><strong>Scan</strong> the document into a PDF.</li>
                                            <li>Click the <strong>Upload Document</strong> button to send it back.</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            {{-- AREA TOMBOL ACTION (Bulletproof Outlook Buttons) --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0 25px;">
                                <tr>
                                    <td align="center">
                                        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                            {{-- 1. TOMBOL DOWNLOAD --}}
                                            <tr>
                                                <td align="center" bgcolor="#ffffff" style="border-radius: 50px; background-color: #ffffff; border: 2px solid {{ $themeColor }};">
                                                    <a href="{{ route('customer.portal.download-pdf', ['token' => $submission->token]) }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 12px 28px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: 700; color: {{ $themeColor }}; text-decoration: none; border-radius: 50px; line-height: 1.2;">
                                                        ⬇️ Download PDF Form
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="15" style="height: 15px; font-size: 15px; line-height: 15px;">&nbsp;</td>
                                            </tr>
                                            {{-- 2. TOMBOL UPLOAD --}}
                                            <tr>
                                                <td align="center" bgcolor="{{ $themeColor }}" style="border-radius: 50px; background-color: {{ $themeColor }};">
                                                    <a href="{{ route('customer.portal.upload-form', ['token' => $submission->token]) }}"
                                                       target="_blank"
                                                       style="display: inline-block; padding: 14px 34px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 700; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid {{ $themeColor }}; line-height: 1.2;">
                                                        ⬆️ Upload Signed Document
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; margin: 25px 0 0; line-height: 1.4;">
                                <em>This link is secure and exclusively for submission code: {{ $submission->form_code }}</em>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 18px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; margin: 0; line-height: 1.5;">
                                &copy; {{ date('Y') }} Financial System.
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
