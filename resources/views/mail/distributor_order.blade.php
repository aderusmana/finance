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
                                        <img src="https://ui-avatars.com/api/?name=SM&background=ffffff&color=a68831&rounded=true&bold=true&size=100" alt="Logo SMII" style="display: block; width: 55px; height: 55px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
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
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>Tim {{ $order->distributor->name }}</strong>,</p>
                            <p style="margin: 0 0 30px 0; font-size: 15px; line-height: 1.6; color: #475569;">Kami informasikan bahwa terdapat dokumen pengiriman <b>Delivery Note (DO)</b> baru yang siap untuk diproses. Berikut adalah rincian pesanan tersebut:</p>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 35px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%" style="padding-bottom: 20px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">No. Delivery Note</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $order->note->delivery_order_no }}</p>
                                                </td>
                                                <td width="50%" style="padding-bottom: 20px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Customer Tujuan</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 700; color: #a68831;">{{ $order->customer->name }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Penerima (Ship To)</p>
                                                    <p style="margin: 4px 20px 0 0; font-size: 14px; font-weight: 600; color: #334155; line-height: 1.4;">{{ $order->customerShipTo->ship_to_name }}</p>
                                                </td>
                                                <td width="50%" valign="top">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Tgl. Pengiriman</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #334155;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; text-align: center; color: #475569;">Silakan klik tombol di bawah ini untuk melihat detail lengkap:</p>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center;">
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <a href="{{ $urlDetail }}" style="display: inline-block; background-color: #a68831; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 600; font-size: 15px; width: 80%; max-width: 300px; box-shadow: 0 4px 10px rgba(166, 136, 49, 0.3);">
                                            &#x1F4CB; Tinjau Detail Pesanan
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 25px 0 0 0; font-size: 12px; text-align: center; color: #94a3b8; font-style: italic;">
                                * Catatan: Tombol unduh dokumen hanya akan tersedia setelah Anda meninjau detail pesanan di portal.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 25px 20px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">&copy; {{ date('Y') }} PT Sinar Meadow International Indonesia. All rights reserved.</p>
                            <p style="margin: 5px 0 0 0; font-size: 11px; color: #94a3b8;">Email ini dihasilkan secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
