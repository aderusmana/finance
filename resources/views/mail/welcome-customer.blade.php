<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome to Sinar Meadow</title>
    <style type="text/css">
        /* RESET STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: Arial, sans-serif; background-color: #f4f4f4; }

        /* RESPONSIVE STYLES */
        @media screen and (max-width: 600px) {
            .email-container { width: 100% !important; }
            .col-split { display: block !important; width: 100% !important; padding-right: 0 !important; padding-left: 0 !important; margin-bottom: 20px !important; }
            .mobile-padding { padding: 20px !important; }
        }
    </style>
    </head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #dddddd;" class="email-container">

                    <tr>
                        <td align="center" bgcolor="#1e3a8a" style="padding: 30px 20px;">
                            <h1 style="margin: 0; font-size: 24px; font-weight: bold; letter-spacing: 1px; color: #ffffff; font-family: Arial, sans-serif;">SINAR MEADOW</h1>
                            <p style="margin: 5px 0 0; font-size: 14px; opacity: 0.8; color: #ffffff; font-family: Arial, sans-serif;">International Indonesia</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px; color: #333333; line-height: 1.6; font-size: 14px; font-family: Arial, sans-serif;" class="mobile-padding">

                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">[Date: {{ date('d M Y') }}]</p>

                            <p style="margin: 0 0 20px; font-family: Arial, sans-serif;">
                                <strong>{{ $customer->name }}</strong><br>
                                {{ $customer->address1 }}<br>
                                {{ $customer->city }}{{ $customer->postal_code ? ', ' . $customer->postal_code : '' }}
                            </p>

                            <p style="margin: 20px 0; font-weight: bold; font-family: Arial, sans-serif;">Subject: Welcome to Sinar Meadow!</p>

                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">Dear Sirs,</p>
                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">On behalf of everyone at PT Sinar Meadow International Indonesia, we want to extend a warm welcome!</p>
                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">We are so glad to have you as a new customer and are thrilled you've chosen us for your material needs.</p>
                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">We are committed to providing you with excellent product and services.</p>
                            <p style="margin: 0 0 20px; font-family: Arial, sans-serif;">As a valued customer, your satisfaction is our top priority, and we are here to support you every step of the way.</p>

                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">To help you get started, here are some helpful resources:</p>

                            <ul style="padding-left: 20px; margin: 0; font-family: Arial, sans-serif;">

                                <li style="margin-bottom: 20px; font-weight: bold; font-size: 14px;">
                                    Your account details:
                                    <div style="margin-top: 5px; margin-left: 0; font-weight: normal;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="160" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">Customer ID</td>
                                                <td width="20" align="center" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">:</td>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">{{ $customer->code }}</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">Customer Name</td>
                                                <td align="center" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">:</td>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">{{ $customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">Term of Payment</td>
                                                <td align="center" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">:</td>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">{{ $customer->term_of_payment }}</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">Credit Limit</td>
                                                <td align="center" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">:</td>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">Credit Rating</td>
                                                <td align="center" valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">:</td>
                                                <td valign="top" style="padding: 2px 0; font-family: Arial, sans-serif; color: #333;">
                                                    <span style="background-color: #d1d5db; padding: 1px 6px; font-weight: bold; font-size: 12px;">N/A</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>

                                <li style="margin-bottom: 20px; font-weight: bold; font-size: 14px;">
                                    Support: <span style="font-weight: normal;">For any questions, you can reach our support team :</span>

                                    <div style="margin-top: 15px; margin-left: 0; font-weight: normal;">

                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top" width="48%" class="col-split" style="padding-right: 2%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #eeeeee;">
                                                        <tr>
                                                            <td bgcolor="#1e3a8a" style="padding: 8px 10px; color: #ffffff; font-weight: bold; font-size: 13px; font-family: Arial, sans-serif;">Sales Representative</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 15px; font-size: 13px; line-height: 1.5; font-family: Arial, sans-serif; color: #333;">
                                                                <strong>Name:</strong> {{ $salesRep->name ?? '-' }}<br>
                                                                <strong>Email:</strong> {{ $salesRep->email ?? '-' }}<br>
                                                                <strong>Contact:</strong> {{ $salesRep->phone_number ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>

                                                <td valign="top" width="48%" class="col-split" style="padding-left: 2%;">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #eeeeee;">
                                                        <tr>
                                                            <td bgcolor="#3b82f6" style="padding: 8px 10px; color: #ffffff; font-weight: bold; font-size: 13px; font-family: Arial, sans-serif;">Finance Support</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 15px; font-size: 13px; line-height: 1.5; font-family: Arial, sans-serif; color: #333;">
                                                                <strong>Name:</strong> {{ $managerFinance->name ?? 'Finance Team' }}<br>
                                                                <strong>Email:</strong> {{ $managerFinance->email ?? 'finance@sinarmeadow.com' }}<br>
                                                                <strong>Contact:</strong> {{ $managerFinance->phone_number ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>

                                <li style="margin-bottom: 15px; font-weight: bold; font-size: 14px;">
                                    Learn more
                                    <div style="margin-top: 5px; margin-left: 5px; font-weight: normal;">
                                        Explore our <a href="https://www.sinarmeadow.com" style="color: #0369a1; text-decoration: underline;">[website/blog]</a> to learn more about another product
                                    </div>
                                </li>
                            </ul>

                            <p style="margin: 30px 0 10px; font-family: Arial, sans-serif;">We look forward to building a long-lasting relationship with you.</p>

                            <p style="margin: 30px 0 5px; font-family: Arial, sans-serif;">Sincerely,</p>
                            <p style="margin: 0; font-family: Arial, sans-serif;">
                                <strong>{{ $managerFinance->name ?? 'Manager Finance' }}</strong><br>
                                Finance Accounting & Tax Manager
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#f4f4f4" style="padding: 20px; font-size: 11px; color: #999999; font-family: Arial, sans-serif;">
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
