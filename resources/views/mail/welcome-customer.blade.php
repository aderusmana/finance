<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Welcome to Sinar Meadow {{ $customer->name }}</title>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <style type="text/css">
        /* MEDIA QUERIES */
        @media screen and (max-width: 850px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .mobile-padding {
                padding: 20px !important;
            }
            .header-text {
                font-size: 24px !important;
            }
            .body-text {
                font-size: 16px !important;
            }
        }

        /* RESET STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4; color: #333333; }
    </style>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, th, p, a, li, span, h1, h2, h3, h4, h5, h6 {
            font-family: Arial, Helvetica, sans-serif !important;
        }
    </style>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif; color: #333333;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
            <td align="center" style="padding: 25px 15px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="750">
                <tr>
                <td align="center" valign="top" width="750">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="border-collapse: separate; max-width: 750px; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 4px; overflow: hidden; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td bgcolor="#a68831" style="background-color: #a68831; padding: 30px 40px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle; color: #ffffff; font-family: Arial, Helvetica, sans-serif;">
                                        <h1 class="header-text" style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: bold; letter-spacing: 1px; line-height: 1.3; text-transform: uppercase; color: #ffffff;">
                                            PT. SINAR MEADOW INTERNATIONAL INDONESIA
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="mobile-padding" style="padding: 40px; color: #333333; line-height: 1.8; font-size: 16px; font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <p style="margin: 0 0 25px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #666666;">[Date: {{ date('d M Y') }}]</p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px; border-left: 6px solid #a68831; background-color: #faf8f5; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 16px 20px; font-family: Arial, Helvetica, sans-serif;">
                                        <strong style="font-size: 18px; color: #1f2937;">{{ $customer->name }}</strong><br>
                                        <span style="font-size: 15px; line-height: 1.6; color: #4b5563;">
                                            {{ $customer->address1 }}<br>
                                            {{ $customer->city }}{{ $customer->postal_code ? ', ' . $customer->postal_code : '' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <h2 style="margin: 25px 0 20px; font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 24px; color: #a68831; line-height: 1.3;">Welcome to Sinar Meadow</h2>

                            <p class="body-text" style="margin: 0 0 16px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">Dear Mr. / Ms. {{ $customer->name }},</p>

                            <p class="body-text" style="margin: 0 0 16px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">On behalf of everyone at PT Sinar Meadow International Indonesia, we want to extend a warm welcome. We are so glad to have you as a new customer and are thrilled you've chosen us for your material needs.</p>

                            <p class="body-text" style="margin: 0 0 16px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">We are committed to providing you with excellent product and services. As a valued customer, your satisfaction is our top priority, and we are here to support you every step of the way.</p>

                            <p class="body-text" style="margin: 0 0 15px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.6; color: #333333;">To help you get started, here are some helpful resources:</p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0 30px;">
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif;">
                                        <div style="font-size: 18px; font-weight: bold; color: #1f2937; margin-bottom: 12px;">1. Your account details:</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9f9f9; border: 1px solid #eeeeee; border-radius: 6px; border-collapse: separate;">
                                            <tr>
                                                <td style="padding: 20px; font-family: Arial, Helvetica, sans-serif;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 15px; line-height: 1.6;">
                                                        <tr>
                                                            <td style="width: 180px; padding: 6px 0; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">Customer ID</td>
                                                            <td style="width: 20px; text-align: center; padding: 6px 0; color: #555555;">:</td>
                                                            <td style="padding: 6px 0; color: #333333; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">{{ $customer->code }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 180px; padding: 6px 0; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">Customer Name</td>
                                                            <td style="width: 20px; text-align: center; padding: 6px 0; color: #555555;">:</td>
                                                            <td style="padding: 6px 0; color: #333333; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">{{ $customer->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 180px; padding: 6px 0; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">Term of Payment</td>
                                                            <td style="width: 20px; text-align: center; padding: 6px 0; color: #555555;">:</td>
                                                            <td style="padding: 6px 0; color: #333333; font-family: Arial, Helvetica, sans-serif;">{{ $customer->term_of_payment }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 180px; padding: 6px 0; color: #555555; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">Credit Limit</td>
                                                            <td style="width: 20px; text-align: center; padding: 6px 0; color: #555555;">:</td>
                                                            <td style="padding: 6px 0; color: #15803d; font-weight: bold; font-family: Arial, Helvetica, sans-serif;">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 0 30px;">
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif;">
                                        <div style="font-size: 18px; font-weight: bold; color: #1f2937; margin-bottom: 6px;">2. Support:</div>
                                        <p style="margin: 0 0 15px; font-size: 15px; color: #555555;">For any questions, you can reach our support team:</p>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fcfcfc; border: 1px solid #eeeeee; border-radius: 6px; margin-bottom: 15px; border-collapse: separate;">
                                            <tr>
                                                <td style="padding: 16px 20px; font-family: Arial, Helvetica, sans-serif;">
                                                    <div style="margin-bottom: 10px; font-weight: bold; color: #a68831; font-size: 16px;">Sales Representative</div>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; line-height: 1.6;">
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Name</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">{{ $salesRep->name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Email</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-family: Arial, Helvetica, sans-serif;">{{ $salesRep->email ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Contact No</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-family: Arial, Helvetica, sans-serif;">{{ $salesRep->no_telepon ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fcfcfc; border: 1px solid #eeeeee; border-radius: 6px; border-collapse: separate;">
                                            <tr>
                                                <td style="padding: 16px 20px; font-family: Arial, Helvetica, sans-serif;">
                                                    <div style="margin-bottom: 10px; font-weight: bold; color: #a68831; font-size: 16px;">Finance Support</div>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; line-height: 1.6;">
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Name</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-weight: 600; font-family: Arial, Helvetica, sans-serif;">{{ $managerFinance->name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Email</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-family: Arial, Helvetica, sans-serif;">{{ $managerFinance->email ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 140px; padding: 4px 0; color: #555555; font-family: Arial, Helvetica, sans-serif;">Contact No</td>
                                                            <td style="width: 20px; text-align: center; padding: 4px 0; color: #555555;">:</td>
                                                            <td style="padding: 4px 0; color: #333333; font-family: Arial, Helvetica, sans-serif;">{{ $managerFinance->no_telepon ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 0 0 30px;">
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif;">
                                        <div style="font-size: 18px; font-weight: bold; color: #1f2937; margin-bottom: 6px;">3. Learn more:</div>
                                        <p style="margin: 0; font-size: 15px; color: #555555;">
                                            Explore our <a href="https://www.sinarmeadow.com" target="_blank" style="color: #a68831; text-decoration: underline; font-weight: bold;">[website/blog]</a> to learn more about our products.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 15px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: #333333;">We look forward to building a long-lasting relationship with you.</p>

                            <p style="margin: 0 0 30px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; color: #333333;">Sincerely,</p>

                            <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 16px; line-height: 1.5;">
                                <strong style="font-size: 18px; color: #1f2937;">{{ $managerFinance->name ?? '-' }}</strong><br>
                                <span style="font-size: 14px; color: #666666;">Finance Accounting & Tax Manager</span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#a68831" style="background-color: #a68831; padding: 22px; font-size: 13px; color: #ffffff; font-family: Arial, Helvetica, sans-serif; border-top: 1px solid #856d27;">
                            &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
                            Automated System Notification.
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
