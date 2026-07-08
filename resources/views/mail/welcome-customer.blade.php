<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome to Sinar Meadow {{ $customer->name }}</title>
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
            /* Penyesuaian font di mobile agar tidak terlalu memenuhi layar */
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
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: Arial, sans-serif; background-color: #f4f4f4; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 850px; background-color: #ffffff; border: 1px solid #dddddd;">

                    <tr>
                        <td bgcolor="#a68831" style="padding: 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>

                                    <td style="vertical-align: middle; padding-left: 15px; color: #ffffff; font-family: Arial, sans-serif;">
                                        <h1 class="header-text" style="margin: 0; font-size: 28px; font-weight: bold; letter-spacing: 1px; line-height: 1.3; text-transform: uppercase;">
                                            PT. SINAR MEADOW INTERNATIONAL INDONESIA
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="mobile-padding" style="padding: 40px 50px; color: #333333; line-height: 1.8; font-size: 18px; font-family: Arial, sans-serif;">

                            <p style="margin: 0 0 25px; font-family: Arial, sans-serif; font-size: 16px; color: #666;">[Date: {{ date('d M Y') }}]</p>

                            <div style="margin: 0 0 35px; font-family: Arial, sans-serif; border-left: 6px solid #a68831; padding-left: 20px;">
                                <strong style="font-size: 20px;">{{ $customer->name }}</strong><br>
                                <span style="font-size: 18px; line-height: 1.6;">
                                    {{ $customer->address1 }}<br>
                                    {{ $customer->city }}{{ $customer->postal_code ? ', ' . $customer->postal_code : '' }}
                                </span>
                            </div>

                            <p style="margin: 30px 0; font-weight: bold; font-family: Arial, sans-serif; font-size: 26px; color: #a68831;">Welcome to Sinar Meadow</p>

                            <p class="body-text" style="margin: 0 0 20px; font-family: Arial, sans-serif; font-size: 18px;">Dear Mr. / Ms. {{ $customer->name }},</p>


                            <p class="body-text" style="margin: 0 0 20px; font-family: Arial, sans-serif; font-size: 18px;">On behalf of everyone at PT Sinar Meadow International Indonesia, we want to extend a warm welcome. We are so glad to have you as a new customer and are thrilled you've chosen us for your material needs.</p>

                            <p class="body-text" style="margin: 0 0 20px; font-family: Arial, sans-serif; font-size: 18px;">We are committed to providing you with excellent product and services. As a valued customer, your satisfaction is our top priority, and we are here to support you every step of the way.</p>

                            <p class="body-text" style="margin: 0 0 15px; font-family: Arial, sans-serif; font-size: 18px;">To help you get started, here are some helpful resources:</p>

                            <ul style="padding-left: 20px; margin-top: 15px; font-family: Arial, sans-serif;">

                                <li style="margin-bottom: 35px; font-size: 18px;">
                                    <strong style="font-size: 20px;">Your account details:</strong>
                                    <div style="margin-left: 0px; margin-top: 15px; background-color: #f9f9f9; padding: 25px; border-radius: 8px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 17px; line-height: 1.6;">
                                            <tr>
                                                <td style="width: 200px; padding: 8px 0; color: #555; font-weight: 600;">Customer ID</td>
                                                <td style="width: 20px; text-align: center; padding: 8px 0; color: #555;">:</td>
                                                <td style="padding: 8px 0; color: #333; font-weight: bold;">{{ $customer->code }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 8px 0; color: #555; font-weight: 600;">Customer Name</td>
                                                <td style="width: 20px; text-align: center; padding: 8px 0; color: #555;">:</td>
                                                <td style="padding: 8px 0; color: #333; font-weight: bold;">{{ $customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 8px 0; color: #555; font-weight: 600;">Term of Payment</td>
                                                <td style="width: 20px; text-align: center; padding: 8px 0; color: #555;">:</td>
                                                <td style="padding: 8px 0; color: #333;">{{ $customer->term_of_payment }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 8px 0; color: #555; font-weight: 600;">Credit Limit</td>
                                                <td style="width: 20px; text-align: center; padding: 8px 0; color: #555;">:</td>
                                                <td style="padding: 8px 0; color: #333;">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>

                                <li style="margin-bottom: 35px; font-size: 18px;">
                                    <strong style="font-size: 20px;">Support:</strong> <br>For any questions, you can reach our support team :

                                    <div style="margin-left: 0px; margin-top: 20px;">
                                        <div style="margin-bottom: 12px; font-weight: bold; color: #a68831; font-size: 19px;">Sales Representative</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 17px; margin-bottom: 25px; line-height: 1.6;">
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Name</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                <td style="padding: 6px 0; color: #333;">{{ $salesRep->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Email</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                <td style="padding: 6px 0; color: #333;">{{ $salesRep->email ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Contact No</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                <td style="padding: 6px 0; color: #333;">{{ $salesRep->no_telepon ?? '-'  }}</td>
                                            </tr>
                                        </table>

                                        <div style="margin-bottom: 12px; font-weight: bold; color: #a68831; font-size: 19px;">Finance Support</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 17px; line-height: 1.6;">
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Name</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                <td style="padding: 6px 0; color: #333;">{{ $managerFinance->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Email</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                <td style="padding: 6px 0; color: #333;">{{ $managerFinance->email ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="width: 200px; padding: 6px 0; color: #555;">Contact No</td>
                                                <td style="width: 20px; text-align: center; padding: 6px 0; color: #555;">:</td>
                                                 <td style="padding: 6px 0; color: #333;">{{ $managerFinance->no_telepon ?? '-'  }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>

                                <li style="margin-bottom: 25px; font-size: 18px;">
                                    <strong style="font-size: 19px;">Learn more</strong>
                                    <div style="margin-left: 0px; margin-top: 10px;">
                                        Explore our <a href="https://www.sinarmeadow.com" style="color: #a68831; text-decoration: underline; font-weight: bold;">[website/blog]</a> to learn more about another product
                                    </div>
                                </li>
                            </ul>

                            <p style="margin: 35px 0 20px; font-family: Arial, sans-serif; font-size: 18px;">We look forward to building a long-lasting relationship with you.</p>

                            <p style="margin: 0; font-family: Arial, sans-serif; font-size: 18px;">Sincerely,</p>

                            <br><br>

                            <p style="margin: 0; font-family: Arial, sans-serif; font-size: 18px;">
                                <strong style="font-size: 20px;">{{ $managerFinance->name ?? '-' }}</strong><br>
                                <span style="font-size: 16px; color: #666;">Finance Accounting & Tax Manager</span>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#a68831" style="padding: 25px; font-size: 14px; color: #ffffff; font-family: Arial, sans-serif;">
                            &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
                            Automated System Notification.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 
