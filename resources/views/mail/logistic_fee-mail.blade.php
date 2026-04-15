<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logistic Fee Notification</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; background-color: #f1f5f9;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td>
                <table width="600" align="center" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">

                    @php
                        $headerBg = '#1e3a8a'; // Default Blue for Request
                        $statusBadge = 'Menunggu Persetujuan';
                        $headerTitle = 'Persetujuan Logistic Fee';

                        if($type === 'completed') {
                            $headerBg = '#059669'; // Green
                            $statusBadge = 'Status: Completed';
                            $headerTitle = 'Pengajuan Disetujui';
                        } elseif ($type === 'rejected') {
                            $headerBg = '#dc2626'; // Red
                            $statusBadge = 'Status: Rejected';
                            $headerTitle = 'Pengajuan Ditolak';
                        }
                    @endphp

                    <tr>
                        <td style="background-color: {{ $headerBg }}; padding: 40px; text-align: center;">
                            <div style="background-color: rgba(255, 255, 255, 0.2); display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 12px; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">
                                {{ $statusBadge }}
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">{{ $headerTitle }}</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            @if($type === 'request')
                                <p style="margin-top: 0; font-size: 16px; color: #334155;">Halo <strong>{{ $extraData['approverName'] ?? 'Approver' }}</strong>,</p>
                                <p style="font-size: 16px; color: #475569;">Terdapat pengajuan penyesuaian harga <strong>Logistic Fee</strong> yang membutuhkan persetujuan Anda. Berikut adalah rinciannya:</p>
                            @elseif($type === 'completed')
                                <p style="margin-top: 0; font-size: 16px; color: #334155;">Halo,</p>
                                <p style="font-size: 16px; color: #475569;">Proses persetujuan untuk perubahan <strong>Logistic Fee</strong> telah selesai. Perubahan harga kini telah aktif di dalam sistem.</p>
                            @elseif($type === 'rejected')
                                <p style="margin-top: 0; font-size: 16px; color: #334155;">Halo,</p>
                                <p style="font-size: 16px; color: #475569;">Kami menginformasikan bahwa pengajuan penyesuaian <strong>Logistic Fee</strong> Anda tidak dapat disetujui untuk saat ini dan telah ditolak oleh <em>Approver</em>.</p>

                                <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 0 12px 12px 0; padding: 20px; margin: 25px 0;">
                                    <p style="margin: 0 0 8px 0; font-size: 13px; font-weight: 700; color: #dc2626; text-transform: uppercase;">Alasan Penolakan:</p>
                                    <p style="margin: 0; font-size: 15px; font-style: italic; color: #991b1b;">"{{ $extraData['notes'] ?? '-' }}"</p>
                                </div>
                            @endif

                            <table width="100%" style="border-collapse: collapse; background-color: #f8fafc; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; margin-top: 20px;">
                                <tr>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Distributor</td>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; text-align: right; color: #0f172a;">{{ $logisticData->distributor->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Customer</td>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; text-align: right; color: #0f172a;">{{ $logisticData->customer->name ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 13px; color: #64748b; font-weight: 600;">Harga {{ $type === 'request' ? 'Saat Ini' : 'Sebelumnya' }}</td>
                                    <td style="padding: 16px; border-bottom: 1px solid #e2e8f0; font-size: 15px; font-weight: 600; color: #64748b; text-align: right;">
                                        Rp {{ number_format($type === 'completed' ? ($extraData['oldFee'] ?? 0) : $logisticData->logistic_fee, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 16px; font-size: 13px; color: #64748b; font-weight: 600;">Harga {{ $type === 'completed' ? 'Disetujui' : 'Diajukan' }}</td>
                                    <td style="padding: 16px; font-size: 18px; font-weight: 800; color: {{ $type === 'completed' ? '#059669' : ($type === 'rejected' ? '#dc2626' : '#ea580c') }}; text-align: right;">
                                        Rp {{ number_format($type === 'completed' ? ($extraData['newFee'] ?? $logisticData->proposed_fee) : $logisticData->proposed_fee, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            @if($type === 'completed' && !empty($extraData['notes']))
                                <p style="margin-bottom: 8px; font-weight: 600; font-size: 14px; margin-top: 25px;">Catatan Penyetuju Terakhir:</p>
                                <div style="background-color: #f0fdf4; border-left: 4px solid #059669; padding: 16px; font-style: italic; color: #166534;">
                                    "{{ $extraData['notes'] }}"
                                </div>
                            @endif

                            <div style="text-align: center; margin-top: 35px;">
                                @if($type === 'request')
                                    <a href="{{ url('/logistic-fees/approval/form/' . $extraData['log']->token . '/approve_with_review') }}"
                                        style="background-color: #2563eb; color: #ffffff; padding: 14px 28px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 15px; display: inline-block;">
                                        Tinjau Pengajuan
                                    </a>
                                @else
                                    <a href="{{ url('/logistic-fees') }}"
                                        style="background-color: {{ $type === 'completed' ? '#059669' : '#dc2626' }}; color: #ffffff; padding: 14px 28px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 15px; display: inline-block;">
                                        Buka Sistem
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-weight: 500;">
                                Sistem Persetujuan Digital &copy; {{ date('Y') }} PT Sinar Meadow<br>
                                Pesan ini dikirim secara otomatis oleh sistem, mohon tidak membalas.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
