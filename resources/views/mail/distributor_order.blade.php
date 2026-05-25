<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - PT Sinar Meadow International Indonesia</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="650" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 650px; width: 100%;">

                    <tr>
                        <td align="center" style="background-color: #a68831; padding: 30px 20px; border-bottom: 4px solid #856d27;">
                            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tr>
                                    <td width="60" style="text-align: right; padding-right: 15px;">
                                        <img src="{{ url('assets/images/logo/outline-smii.png') }}" alt="Logo SMII" width="65" height="65" style="display: block; width: 65px; height: 65px; border: 0; outline: none; text-decoration: none;">
                                    </td>
                                    <td style="text-align: left;">
                                        <h2 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 1px;">DELIVERY NOTE</h2>
                                        <p style="color: #fcfcfc; margin: 4px 0 0 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 500;">PT Sinar Meadow International Indonesia</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 35px;">
                            @if($type === 'sales')
                                <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>{{ $order->customerShipTo->user->name ?? 'Tim Sales' }}</strong>,</p>
                                <p style="margin: 0 0 30px 0; font-size: 15px; line-height: 1.6; color: #475569;">We inform you that the delivery document <b>Delivery Note (DN)</b> below <strong>has been downloaded</strong> by the Distributor.</p>
                            @else
                                <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>Tim {{ $order->distributor->name }}</strong>,</p>
                                <p style="margin: 0 0 30px 0; font-size: 15px; line-height: 1.6; color: #475569;">We would like to inform you that a new <b>Delivery Note (DN)</b> is ready for processing. The order details are as follows:</p>
                            @endif

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 35px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            
                                            <tr>
                                                <td width="50%" valign="top" style="padding-bottom: 20px; padding-right: 10px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">No. Delivery Note</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $order->note->delivery_order_no }}</p>
                                                </td>
                                                <td width="50%" valign="top" style="padding-bottom: 20px; padding-left: 10px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">NO PO</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $order->no_po ?? '-' }}</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="50%" valign="top" style="padding-bottom: 20px; padding-right: 10px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Customer</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $order->customer->name }}</p>
                                                </td>
                                                <td width="50%" valign="top" style="padding-bottom: 20px; padding-left: 10px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Recipient (Ship To)</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600; color: #0f172a; line-height: 1.4;">{{ $order->customerShipTo->ship_to_name }}</p>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2" width="100%" valign="top">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Delivery Date</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #0f172a;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</p>
                                                </td>
                                            </tr>
                                            
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'sales')
                                <p style="margin: 0 0 25px 0; font-size: 15px; text-align: center; color: #475569;">Please log in to the system to monitor the order status further:</p>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center;">
                                    <tr>
                                        <td style="padding-bottom: 15px;">
                                            <a href="{{ route('login') }}" style="display: inline-block; background-color: #1e293b; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 600; font-size: 15px; width: 80%; max-width: 300px; box-shadow: 0 4px 10px rgba(30, 41, 59, 0.3);">
                                                &#x1F517; See More
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <p style="margin: 0 0 25px 0; font-size: 15px; text-align: center; color: #475569;">Please click the button below to see full details:</p>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center;">
                                    <tr>
                                        <td style="padding-bottom: 15px;">
                                            <a href="{{ $urlDetail }}" style="display: inline-block; background-color: #a68831; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 600; font-size: 15px; width: 80%; max-width: 300px; box-shadow: 0 4px 10px rgba(166, 136, 49, 0.3);">
                                                &#x1F4CB; Review Order Details
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 25px 20px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia. All rights reserved.</p>
                            <p style="margin: 5px 0 0 0; font-size: 11px; color: #94a3b8;">This email was automatically generated by the system. Please do not reply to this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
