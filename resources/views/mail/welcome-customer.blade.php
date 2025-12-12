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
        
        /* Custom Classes */
        .content-indent {
            margin-left: 25px;
        }
        .label-col {
            width: 130px;
            padding: 2px 0;
            color: #333;
        }
        .sep-col {
            width: 15px;
            text-align: center;
            padding: 2px 0;
            color: #333;
        }
        .val-col {
            padding: 2px 0;
            color: #333;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border: 1px solid #dddddd;" class="email-container">

                    <tr>
                        <td bgcolor="#1e3a8a" style="padding: 15px 20px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="60" style="vertical-align: middle;">
                                        <img src="https://via.placeholder.com/50x50/ffffff/1e3a8a?text=LOGO" alt="Logo" width="50" style="display: block;" />
                                    </td>
                                    <td style="vertical-align: middle; padding-left: 15px; color: #ffffff; font-family: Arial, sans-serif;">
                                        <h1 style="margin: 0; font-size: 20px; font-weight: bold; letter-spacing: 1px;">PT. SINAR MEADOW</h1>
                                        <p style="margin: 2px 0 0; font-size: 12px; opacity: 0.9;">International Indonesia</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 40px; color: #333333; line-height: 1.6; font-size: 14px; font-family: Arial, sans-serif;">

                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">[Date: {{ date('d M Y') }}]</p>

                            <p style="margin: 0 0 20px; font-family: Arial, sans-serif;">
                                <strong>{{ $customer->name }}</strong><br>
                                {{ $customer->address1 }}<br>
                                {{ $customer->city }}{{ $customer->postal_code ? ', ' . $customer->postal_code : '' }}
                            </p>

                            <p style="margin: 20px 0; font-weight: bold; font-family: Arial, sans-serif;">Subject: Welcome to Sinar Meadow!</p>

                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">Dear Sirs,</p>
                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">On behalf of everyone at PT Sinar Meadow International Indonesia, we want to extend a warm welcome! We are so glad to have you as a new customer and are thrilled you've chosen us for your material needs.</p>
                            <p style="margin: 0 0 15px; font-family: Arial, sans-serif;">We are committed to providing you with excellent product and services. As a valued customer, your satisfaction is our top priority, and we are here to support you every step of the way.</p>
                            <p style="margin: 0 0 5px; font-family: Arial, sans-serif;">To help you get started, here are some helpful resources:</p>

                            <ul style="padding-left: 15px; margin-top: 10px; font-family: Arial, sans-serif;">

                                <li style="margin-bottom: 20px; font-size: 14px;">
                                    <strong>Your account details:</strong>
                                    <div class="content-indent" style="margin-top: 5px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="label-col">Customer ID</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $customer->code }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Customer Name</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Term of Payment</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $customer->term_of_payment }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Credit Limit</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Credit Rating</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">N/A</td>
                                            </tr>
                                        </table>
                                    </div>
                                </li>

                                <li style="margin-bottom: 20px; font-size: 14px;">
                                    <strong>Support:</strong> For any questions, you can reach our support team :
                                    
                                    <div class="content-indent" style="margin-top: 10px;">
                                        
                                        <div style="margin-bottom: 10px; font-weight: bold;">Sales Representative</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="label-col">Name</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $salesRep->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Email</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $salesRep->email ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Contact No</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $salesRep->phone_number ?? '-' }}</td>
                                            </tr>
                                        </table>

                                        <div style="margin-top: 15px; margin-bottom: 10px; font-weight: bold;">Finance Support</div>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="label-col">Name</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $managerFinance->name ?? 'Finance Team' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Email</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $managerFinance->email ?? 'finance@sinarmeadow.com' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label-col">Contact No</td>
                                                <td class="sep-col">:</td>
                                                <td class="val-col">{{ $managerFinance->phone_number ?? '-' }}</td>
                                            </tr>
                                        </table>

                                    </div>
                                </li>

                                <li style="margin-bottom: 15px; font-size: 14px;">
                                    <strong>Learn more</strong>
                                    <div class="content-indent" style="margin-top: 5px;">
                                        Explore our <a href="https://www.sinarmeadow.com" style="color: #0369a1; text-decoration: underline;">[website/blog]</a> to learn more about another product
                                    </div>
                                </li>
                            </ul>

                            <p style="margin: 30px 0 30px; font-family: Arial, sans-serif;">We look forward to building a long-lasting relationship with you.</p>

                            <p style="margin: 0; font-family: Arial, sans-serif;">Sincerely,</p>
                            
                            <br><br>

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