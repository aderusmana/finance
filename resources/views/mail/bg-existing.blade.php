<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BG Update Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">Existing Update</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">Pembaruan/Perubahan Nominal Bank Garansi</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #334155; font-size: 16px; line-height: 24px; margin-bottom: 24px;">
                                Halo Tim, <br><br>
                                Terdapat pembaruan data untuk Customer <strong>Existing</strong> (Perubahan Nominal/Renewal). Data historis telah tersimpan.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 12px 20px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; width: 35%;">Customer Name</td>
                                    <td style="padding: 12px 20px; color: #1e293b; font-size: 14px; font-weight: 600; border-bottom: 1px solid #e2e8f0;">{{ $bg->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 12px 20px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">BG Number (New)</td>
                                    <td style="padding: 12px 20px; color: #1e293b; font-size: 14px; border-bottom: 1px solid #e2e8f0;">{{ $bg->bg_number }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 12px 20px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">New Nominal</td>
                                    <td style="padding: 12px 20px; color: #4f46e5; font-size: 16px; font-weight: 700; border-bottom: 1px solid #e2e8f0;">Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; padding: 12px 20px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Type</td>
                                    <td style="padding: 12px 20px; color: #1e293b; font-size: 14px;">
                                        <span style="background-color: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">EXISTING (UPDATE)</span>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-top: 32px; text-align: center;">
                                <a href="{{ url('/') }}" style="background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">View Details</a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">&copy; {{ date('Y') }} Financial System. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>