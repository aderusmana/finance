<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Expired H-60</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f4f7f6; padding: 40px 0;">
        <tr>
            <td align="center">

                <table width="600" border="0" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); max-width: 600px; width: 100%;">

                    <tr>
                        <td style="background-color: #ffffff; padding: 25px 30px; border-bottom: 1px solid #edf2f7;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left" style="vertical-align: middle;">
                                        <img src="{{ $message->embed(public_path('assets/images/logo/logoputih.png')) }}" alt="Sinar Meadow" height="80" style="display: block; border: 0;">
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <p style="margin: 0; color: #718096; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; line-height: 1.4;">
                                            PT. Sinar Meadow<br>International Indonesia
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #2c3e50; padding: 12px 30px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="left">
                                        <p style="color: #ffffff; margin: 0; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <span style="color: #e74c3c; margin-right: 6px;">●</span> System Notification
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p style="color: #bdc3c7; margin: 0; font-size: 11px;">H-60 Alert</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #2d3748; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">Halo, Firas.</p>

                            <p style="color: #4a5568; font-size: 14px; line-height: 1.6; margin-bottom: 25px;">
                                Sistem mendeteksi adanya <strong>Bank Garansi</strong> yang akan jatuh tempo dalam kurun waktu 60 hari ({{ \Carbon\Carbon::now()->addDays(60)->format('d M Y') }}).
                                <br><br>
                                Berikut adalah daftar customer beserta detail Bank Garansi yang perlu mendapatkan perhatian segera:
                            </p>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden;">
                                <thead>
                                    <tr style="background-color: #f8fafc;">
                                        <th style="padding: 12px 15px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Customer</th>
                                        <th style="padding: 12px 15px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">No. BG</th>
                                        <th style="padding: 12px 15px; text-align: right; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Nominal</th>
                                        <th style="padding: 12px 15px; text-align: right; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;">Exp Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bgs as $index => $bg)
                                    <tr style="background-color: {{ $index % 2 == 0 ? '#ffffff' : '#fcfcfc' }};">
                                        <td style="padding: 12px 15px; font-size: 13px; color: #2d3748; border-bottom: 1px solid #f1f5f9; font-weight: 600;">
                                            {{ $bg->customer->name ?? '-' }}
                                        </td>

                                        <td style="padding: 12px 15px; font-size: 13px; border-bottom: 1px solid #f1f5f9;">
                                            <span style="background-color: #ebf8ff; color: #2b6cb0; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-family: monospace; font-weight: 600;">
                                                {{ $bg->bg_number }}
                                            </span>
                                        </td>

                                        <td style="padding: 12px 15px; font-size: 13px; color: #4a5568; text-align: right; border-bottom: 1px solid #f1f5f9;">
                                            Rp {{ number_format($bg->bg_nominal, 0, ',', '.') }}
                                        </td>

                                        <td style="padding: 12px 15px; font-size: 13px; color: #e53e3e; font-weight: 700; text-align: right; border-bottom: 1px solid #f1f5f9;">
                                            {{ \Carbon\Carbon::parse($bg->exp_date)->format('d M Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="text-align: center; margin-top: 30px;">
                                <a href="{{ url('/bg-recommendations') }}" style="background-color: #3182ce; color: #ffffff; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 4px 6px rgba(49, 130, 206, 0.25);">
                                    Buka Dashboard
                                </a>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #a0aec0; font-size: 11px; margin: 0; line-height: 1.5;">
                                &copy; {{ date('Y') }} PT. Sinar Meadow International Indonesia<br>
                                Automated Notification System.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
