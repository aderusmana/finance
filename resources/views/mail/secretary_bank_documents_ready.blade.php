<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Berkas Siap TTD Basah & Pengajuan Bank</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif; color: #334155;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 25px 15px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 650px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; border-collapse: separate;">

                    {{-- HEADER --}}
                    <tr>
                        <td bgcolor="#0f766e" style="background-color: #0f766e; background: linear-gradient(135deg, #0f172a 0%, #0f766e 100%); padding: 30px; text-align: center; color: #ffffff;">
                            <h2 style="margin: 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; color: #ffffff; line-height: 1.3;">Berkas Siap TTD Basah &amp; Pengajuan Bank</h2>
                            <p style="margin: 6px 0 0; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #ccfbf1; line-height: 1.4;">Proses Pengajuan Fisik Bank Garansi</p>
                        </td>
                    </tr>

                    {{-- CONTENT --}}
                    <tr>
                        <td style="padding: 30px 35px; background-color: #ffffff;">
                            <p style="margin: 0 0 15px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 15px; color: #1e293b; line-height: 1.5;">Yth. Ibu <strong>{{ $secretary->name ?? 'Rita Rahayu' }}</strong> (Secretary Finance),</p>
                            <p style="margin: 0 0 20px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #475569;">
                                Admin RTM telah memverifikasi berkas konfirmasi distributor. Dokumen <strong>Lampiran D</strong> dan <strong>Surat Pengantar Bank</strong> telah siap diunduh untuk proses tanda tangan basah dan diajukan ke Bank Penjamin.
                            </p>

                            {{-- DATA DISTRIBUTOR --}}
                            <div style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #0f766e; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 20px; margin-bottom: 12px;">
                                Rincian Pengajuan
                            </div>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 14px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Distributor</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right;">{{ $submission->recommendation->customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode PKD / Customer</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $submission->recommendation->customer->no_pkd ?? '-' }} / {{ $submission->recommendation->customer->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode Formulir</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f766e; font-family: monospace; text-align: right;">{{ $submission->form_code }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nominal BG Diajukan</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #16a34a; text-align: right;">
                                        Rp {{ number_format($submission->bg_nominal ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if($submission->recommendation)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Limit Kredit Rekomendasi</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #2563eb; text-align: right;">
                                        Rp {{ number_format($submission->recommendation->credit_limit_updated, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                            </table>

                            {{-- PETUNJUK OPERASIONAL --}}
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 16px; margin-bottom: 25px;">
                                <div style="font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 6px;">
                                    📌 Langkah Selanjutnya:
                                </div>
                                <ol style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.6; color: #15803d;">
                                    <li>Unduh dokumen Lampiran D dan Surat Pengantar Bank melalui tombol di bawah.</li>
                                    <li>Lakukan proses tanda tangan basah dan stempel resmi pejabat Finance.</li>
                                    <li>Kirimkan berkas fisik ke Bank Penjamin terkait untuk penerbitan Sertifikat Bank Garansi.</li>
                                    <li>Setelah fisik Sertifikat Bank Garansi terbit, Admin RTM akan menginput nomor BG, tanggal expired, dan mengunggah scan sertifikat asli untuk verifikasi akhir dari Ibu Rita.</li>
                                </ol>
                            </div>

                            {{-- ACTION BUTTONS --}}
                            <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                                <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto; border-collapse: separate;">
                                    <tr>
                                        <td align="center" bgcolor="#dc2626" style="border-radius: 50px; background-color: #dc2626;">
                                            <a href="{{ route('bg-reports.download', ['id' => $submission->id, 'doc_type' => 'lampiran_d']) }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 24px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid #dc2626; line-height: 1.2;">
                                                📥 Unduh Lampiran D (PDF)
                                            </a>
                                        </td>
                                        <td width="12" style="width: 12px;">&nbsp;</td>
                                        <td align="center" bgcolor="#2563eb" style="border-radius: 50px; background-color: #2563eb;">
                                            <a href="{{ route('bg-submissions.index') }}"
                                               target="_blank"
                                               style="display: inline-block; padding: 12px 24px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 50px; border: 1px solid #2563eb; line-height: 1.2;">
                                                🌐 Dashboard Submissions
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color: #f8fafc; padding: 16px; text-align: center; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; line-height: 1.5;">
                            &copy; {{ date('Y') }} PT SMII - Sistem Otomasi Bank Garansi
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
