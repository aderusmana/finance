<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Delivery Note - PT Sinar Meadow International Indonesia</title>
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
            <td align="center" style="padding: 30px 15px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="650">
                <tr>
                <td align="center" valign="top" width="650">
                <![endif]-->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 650px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                    <tr>
                        <td align="center" bgcolor="#a68831" style="background-color: #a68831; padding: 30px 25px; border-bottom: 4px solid #856d27;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="65" style="text-align: right; padding-right: 15px; vertical-align: middle;">
                                        <img src="{{ url('assets/images/logo/outline-smii.png') }}" alt="Logo SMII" width="65" height="65" style="display: block; width: 65px; height: 65px; border: 0; outline: none; text-decoration: none;">
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;">
                                        <h2 style="color: #ffffff; margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 24px; font-weight: 800; letter-spacing: 1px; line-height: 1.2;">DELIVERY NOTE</h2>
                                        <p style="color: #fefce8; margin: 4px 0 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 500; line-height: 1.4;">PT Sinar Meadow International Indonesia</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 35px 35px; background-color: #ffffff;">
                            @if($type === 'sales')
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>{{ $order->customerShipTo->user->name ?? 'Tim Sales' }}</strong>,</p>
                                <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">We inform you that the delivery document <b>Delivery Note (DN)</b> below <strong>has been downloaded</strong> by the Distributor.</p>
                            @elseif($type === 'cancel')
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>Tim {{ $order->distributor->name }} & Tim Manajemen</strong>,</p>
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #dc2626;">We regret to inform you that the Delivery Note (DN) below has been <strong>CANCELED</strong>.</p>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fee2e2; border-left: 4px solid #ef4444; border-radius: 5px; margin-bottom: 25px; border-collapse: separate;">
                                    <tr>
                                        <td style="padding: 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px;">
                                            <strong style="color: #991b1b;">Cancellation Reason:</strong><br>
                                            <span style="color: #7f1d1d;">{{ $order->cancel_reason }}</span>
                                        </td>
                                    </tr>
                                </table>
                                <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #475569; font-style: italic; text-align: center; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
                                    "Please note that our team is currently reviewing and rectifying the relevant order data. A revised Delivery Note (DN) will be sent to you once the corrections are completed."
                                </p>
                            @else
                                <p style="margin: 0 0 15px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>Tim {{ $order->distributor->name }}</strong>,</p>
                                @if($order->cancel_reason)
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fef9c3; border-left: 4px solid #eab308; border-radius: 5px; margin-bottom: 20px; border-collapse: separate;">
                                        <tr>
                                            <td style="padding: 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px;">
                                                <strong style="color: #854d0e;">⚠️ Revised Order Document</strong><br>
                                                <span style="color: #a16207;">This is a revised document. Previous revision note: <br><i>"{{ $order->cancel_reason }}"</i></span>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">We would like to inform you that the <b>Revised Delivery Note (DN)</b> is ready for processing. The updated order details are as follows:</p>
                                @else
                                    <p style="margin: 0 0 25px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #475569;">We would like to inform you that a new <b>Delivery Note (DN)</b> is ready for processing. The order details are as follows:</p>
                                @endif
                            @endif

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 30px; border-collapse: separate;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="50%" valign="top" style="padding-bottom: 15px; padding-right: 10px; font-family: 'Segoe UI', Arial, sans-serif;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">No. Delivery Note</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $order->note->delivery_order_no }}</p>
                                                </td>
                                                <td width="50%" valign="top" style="padding-bottom: 15px; padding-left: 10px; font-family: 'Segoe UI', Arial, sans-serif;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">NO PO</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $order->no_po ?? '-' }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" valign="top" style="padding-bottom: 15px; padding-right: 10px; font-family: 'Segoe UI', Arial, sans-serif;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Customer</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $order->customer->name }}</p>
                                                </td>
                                                <td width="50%" valign="top" style="padding-bottom: 15px; padding-left: 10px; font-family: 'Segoe UI', Arial, sans-serif;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Recipient (Ship To)</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: 600; color: #0f172a; line-height: 1.4;">{{ $order->customerShipTo->ship_to_name }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" width="100%" valign="top" style="font-family: 'Segoe UI', Arial, sans-serif;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Delivery Date</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #0f172a;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'distributor')
                                <p style="margin: 0 0 20px 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; text-align: center; color: #475569;">Please click the button below to see full details:</p>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 25px;">
                                    <tr>
                                        <td align="center">
                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; margin: 0 auto;">
                                                <tr>
                                                    <td align="center" bgcolor="#a68831" style="border-radius: 6px; background-color: #a68831;">
                                                        <a href="{{ $urlDetail }}"
                                                           target="_blank"
                                                           style="display: inline-block; padding: 14px 32px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px; border: 1px solid #a68831; line-height: 1.2;">
                                                            📋 Review Order Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 22px 20px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #64748b; line-height: 1.4;">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia. All rights reserved.</p>
                            <p style="margin: 5px 0 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #94a3b8; line-height: 1.4;">This email was automatically generated by the system. Please do not reply to this email.</p>
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
