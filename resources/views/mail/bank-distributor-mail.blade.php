<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased;">
    
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 600px; width: 100%;">
                    
                    <tr>
                        <td align="center" style="background-color: #1e3a8a; padding: 30px 40px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; text-transform: uppercase;">PT. Sinar Meadow</h1>
                            <p style="color: #bfdbfe; margin: 5px 0 0 0; font-size: 14px;">International Indonesia</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1f2937; margin-top: 0; font-size: 20px; font-weight: 600;">{{ $title }}</h2>
                            
                            <p style="color: #4b5563; font-size: 16px; line-height: 24px; margin-top: 10px;">
                                Yth. Bapak/Ibu Pimpinan,
                            </p>
                            
                            <p style="color: #4b5563; font-size: 16px; line-height: 24px;">
                                {{ $content }}
                            </p>
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $link }}" target="_blank" style="background-color: #eab308; color: #ffffff; font-size: 16px; font-weight: bold; text-decoration: none; padding: 14px 32px; border-radius: 6px; display: inline-block; box-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);">
                                            Download Dokumen PDF
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="color: #6b7280; font-size: 13px; margin: 0; text-align: center;">
                                            Dokumen juga tersedia sebagai <strong>lampiran (attachment)</strong> pada email ini jika tombol di atas tidak berfungsi.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #4b5563; font-size: 16px; line-height: 24px; margin-top: 30px;">
                                Terima kasih atas kerjasama Anda.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 40px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
                                Jl. Pulo Ayang I No. 6, Kawasan Industri Pulogadung, Jakarta Timur 13260
                            </p>
                        </td>
                    </tr>
                </table>
                
                <div style="height: 40px; font-size: 40px; line-height: 40px;">&nbsp;</div>
            </td>
        </tr>
    </table>

</body>
</html>