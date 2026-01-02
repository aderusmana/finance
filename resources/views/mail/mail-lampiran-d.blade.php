<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dokumen Lampiran D</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #1e3a8a; padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                Dokumen Lampiran D Siap
                            </h1>
                            <p style="color: #93c5fd; margin: 5px 0 0 0; font-size: 14px;">Bank Garansi Approval System</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px; color: #334155; font-size: 16px; line-height: 1.6;">
                            <p style="margin-top: 0;">Yth. Bapak/Ibu,</p>
                            
                            <p>Dengan ini kami informasikan bahwa proses perhitungan dan persetujuan Bank Garansi telah <strong>selesai</strong> (Completed). Dokumen <strong>Lampiran D</strong> telah diterbitkan sebagai acuan penerbitan BG.</p>
                            
                            <div style="background-color: #f8fafc; border-left: 4px solid #1e3a8a; padding: 15px 20px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #475569;">
                                    Dokumen ini berisi detail perhitungan final, limit kredit yang disetujui, dan rincian bank yang akan diterbitkan.
                                </p>
                            </div>

                            <p>Silakan unduh dokumen tersebut melalui lampiran (attachment) pada email ini, atau klik tombol di bawah untuk mengunduh melalui portal:</p>

                            {{-- Button Wrapper --}}
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        {{-- Tombol dengan Token --}}
                                        <a href="{{ route('customer.portal.download-pdf', $submission->token) }}" target="_blank" style="background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: 700; display: inline-block; font-size: 16px; box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);">
                                            Download Lampiran D
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 20px;">
                                <em>*Jika tombol di atas tidak berfungsi, Anda dapat menggunakan file PDF yang terlampir di bawah email ini.</em>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia.<br>
                                Automated System Notification. Do not reply.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>