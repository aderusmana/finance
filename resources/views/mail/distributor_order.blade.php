<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistic Order - PT Sinar Meadow International Indonesia</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="650" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); max-width: 650px; width: 100%;">

                    <tr>
                        <td align="center" style="background-color: #0f172a; padding: 35px 20px; border-bottom: 4px solid #f59e0b;">
                            <h2 style="color: #ffffff; margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 1px;">LOGISTIC ORDER</h2>
                            <p style="color: #94a3b8; margin: 8px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px;">PT Sinar Meadow International Indonesia</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 35px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #1e293b;">Halo <strong>Tim {{ $order->distributor->name }}</strong>,</p>
                            <p style="margin: 0 0 30px 0; font-size: 15px; line-height: 1.6; color: #475569;">Kami informasikan bahwa terdapat dokumen <b>Delivery Order (DO)</b> baru yang siap untuk diproses. Berikut adalah rincian ringkas pesanan tersebut:</p>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 35px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td width="50%" style="padding-bottom: 20px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">No. Logistic Order</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">LO-{{ str_pad($order->logistic_order_no, 4, '0', STR_PAD_LEFT) }}</p>
                                                </td>
                                                <td width="50%" style="padding-bottom: 20px;">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">No. Delivery Order</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">{{ $order->note->delivery_order_no }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Customer Tujuan</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #334155;">{{ $order->customer->name }}</p>
                                                </td>
                                                <td width="50%">
                                                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Tgl. Pengiriman</p>
                                                    <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #334155;">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 25px 0; font-size: 15px; text-align: center; color: #475569;">Silakan pilih tindakan di bawah ini:</p>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: center;">
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <a href="{{ $urlDetail }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 600; font-size: 15px; width: 80%; max-width: 300px; box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);">
                                            &#x1F4CB; Tinjau Detail Pesanan
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="{{ $urlDownload }}" style="display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 600; font-size: 15px; width: 80%; max-width: 300px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);">
                                            &#x2B07; Unduh Dokumen DO
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 25px 0 0 0; font-size: 12px; text-align: center; color: #94a3b8; font-style: italic;">
                                * Catatan: Tombol unduh hanya akan memberikan file jika Anda sudah meninjau detail pesanan di portal setidaknya satu kali.
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
