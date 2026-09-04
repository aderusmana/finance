<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Informasi Sinkronisasi Credit Limit</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                NOTIFIKASI IT: CREDIT LIMIT UPDATED
                            </h1>
                            <p style="color: #93c5fd; margin: 6px 0 0 0; font-size: 13px;">Proses Otomatis Background - Hanya Informasi (No Action Required)</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 35px 40px; color: #334155; font-size: 15px; line-height: 1.6;">
                            <p style="margin-top: 0;">Halo Tim IT,</p>

                            <p>Pemberitahuan bahwa verifikasi Bank Guarantee dan Lampiran D untuk distributor di bawah ini telah disetujui oleh <strong>{{ $validatorName ?? 'Secretary Finance (Bu Rita)' }}</strong>.</p>

                            <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 14px 18px; margin: 20px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 13px; color: #065f46; font-weight: 600;">
                                    ✓ Perhitungan dan pembaruan Credit Limit telah diproses secara otomatis di background ke database customer. Tim IT TIDAK PERLU melakukan pengecekan manual atau tindakan verifikasi lanjutan.
                                </p>
                            </div>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <tr style="background-color: #f8fafc;">
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; width: 40%; border-bottom: 1px solid #e2e8f0;">Nama Customer</td>
                                    <td style="padding: 10px 15px; font-weight: 700; font-size: 14px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Kode PKD / Akun</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $customer->no_pkd ?? '-' }} / {{ $customer->code ?? '-' }}</td>
                                </tr>
                                <tr style="background-color: #f8fafc;">
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Kode Formulir Submission</td>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #2563eb; font-family: monospace; border-bottom: 1px solid #e2e8f0;">{{ $submission->form_code }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Nomor Bank Garansi</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $submission->bg_number ?? '-' }}</td>
                                </tr>
                                <tr style="background-color: #f8fafc;">
                                    <td style="padding: 10px 15px; font-weight: 600; font-size: 13px; color: #64748b; border-bottom: 1px solid #e2e8f0;">Tanggal Expired BG</td>
                                    <td style="padding: 10px 15px; font-size: 13px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">{{ $submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr style="background-color: #eff6ff;">
                                    <td style="padding: 12px 15px; font-weight: 700; font-size: 14px; color: #1e40af;">Updated Credit Limit</td>
                                    <td style="padding: 12px 15px; font-weight: 800; font-size: 16px; color: #1e40af;">Rp {{ number_format($approvedCreditLimit, 0, ',', '.') }}</td>
                                </tr>
                            </table>

                            <p style="font-size: 12px; color: #94a3b8; margin-top: 25px;">
                                Email ini dibuat dan dikirim secara otomatis oleh Sistem Bank Garansi & Credit Limit PT SMII untuk kebutuhan pencatatan dan monitoring tim IT.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 18px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} PT SMII - Finance & IT Automation System
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
