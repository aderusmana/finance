<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kode OTP Portal Dokumen Distributor</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Arial, sans-serif; color: #1e293b;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); padding: 32px 30px; color: #ffffff;">
                            <h1 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">CUSTOMER PORTAL FINANCE</h1>
                            <p style="margin: 6px 0 0; font-size: 13px; color: #93c5fd;">PT. SINAR MEADOW INTERNATIONAL INDONESIA</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <p style="font-size: 15px; margin: 0 0 16px; color: #334155;">
                                Yth. Pimpinan / Tim Finance <strong>{{ $distributor->name }}</strong> (<code>{{ $distributor->code }}</code>),
                            </p>

                            <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 24px;">
                                Anda menerima email ini karena adanya permintaan akses mandiri ke <strong>Portal Dokumen Distributor</strong> (Bukti Potong Pajak, TOP Insentif, dan Penjelasan Transfer). Gunakan kode verifikasi di bawah ini untuk membuka dokumen:
                            </p>

                            <!-- OTP Box -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 24px 0;">
                                <tr>
                                    <td align="center" style="background-color: #f8fafc; border: 2px dashed #3b82f6; border-radius: 10px; padding: 22px 20px;">
                                        <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 6px;">
                                            Kode Verifikasi (OTP)
                                        </div>
                                        <div style="font-size: 34px; font-weight: 800; letter-spacing: 10px; color: #1d4ed8; font-family: 'Courier New', Courier, monospace;">
                                            {{ $otp }}
                                        </div>
                                        <div style="font-size: 12px; color: #ef4444; margin-top: 8px;">
                                            <i style="font-style: normal;">⏱️</i> Berlaku selama <strong>{{ $expiresMinutes }} menit</strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notes -->
                            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 12px 16px; border-radius: 0 6px 6px 0; margin-bottom: 24px;">
                                <p style="font-size: 12.5px; color: #1e40af; margin: 0; line-height: 1.5;">
                                    <strong>Penting:</strong> Kode verifikasi ini bersifat rahasia. Jangan pernah membagikan kode OTP ini kepada pihak mana pun untuk menjaga keamanan dokumen perusahaan Anda.
                                </p>
                            </div>

                            <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.5;">
                                Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini atau hubungi tim Finance & Tax kami.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 20px 30px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                            Email ini dikirim secara otomatis oleh Sistem Finance Portal PT. Sinar Meadow International Indonesia. Mohon untuk tidak membalas email ini secara langsung.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
