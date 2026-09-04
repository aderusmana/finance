<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Permohonan Validasi Bank Garansi</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; color: #334155; margin: 0; padding: 0;">

    <div style="max-width: 680px; margin: 25px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%); padding: 30px; text-align: center; color: white;">
            <h2 style="margin:0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">Permohonan Validasi Bank Garansi</h2>
            <p style="margin:6px 0 0; opacity:0.85; font-size: 14px;">Validasi Dokumen Bank Garansi & Lampiran D</p>
        </div>

        {{-- CONTENT --}}
        <div style="padding: 30px 35px;">
            <p style="margin-top: 0; font-size: 15px;">Yth. Ibu <strong>{{ $approver->name ?? 'Rita Rahayu' }}</strong> (Secretary Finance),</p>
            <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                Terdapat pengajuan Bank Guarantee yang membutuhkan validasi dan verifikasi Bank Garansi dari Anda sebelum data masuk ke daftar aktif dan credit limit diperbarui secara otomatis.
            </p>

            {{-- 1. DATA DISTRIBUTOR --}}
            <div style="font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 25px; margin-bottom: 12px;">
                Data Distributor
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nama Distributor</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0f172a; text-align: right;">{{ $submission->recommendation->customer->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode PKD / Customer</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $submission->recommendation->customer->no_pkd ?? '-' }} / {{ $submission->recommendation->customer->code ?? '-' }}</td>
                </tr>
                @if($submission->custom_address)
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Alamat Operasional</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #334155; text-align: right;">{{ $submission->custom_address }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Kode Formulir</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #2563eb; font-family: monospace; text-align: right;">{{ $submission->form_code }}</td>
                </tr>
            </table>

            {{-- 2. VERIFIKASI BANK GARANSI & LIMIT --}}
            <div style="font-size: 13px; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-top: 20px; margin-bottom: 12px;">
                Rincian Bank Garansi
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 25px;">
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nomor Bank Garansi</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; font-family: monospace; color: #0f172a; text-align: right;">{{ $submission->bg_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Nominal BG</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #16a34a; text-align: right;">
                        Rp {{ number_format($submission->bg_nominal ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Tanggal Expired</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #dc2626; text-align: right;">
                        {{ $submission->exp_date ? \Carbon\Carbon::parse($submission->exp_date)->format('d F Y') : '-' }}
                    </td>
                </tr>
                @if($submission->recommendation)
                <tr>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Updated Credit Limit</td>
                    <td style="padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1d4ed8; text-align: right;">
                        Rp {{ number_format($submission->recommendation->credit_limit_updated, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </table>

            {{-- 3. TAUTAN DOKUMEN --}}
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                <div style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 10px;">
                    Dokumen Pendukung:
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    @if($submission->warkat_file_path)
                        <a href="{{ asset($submission->warkat_file_path) }}" target="_blank" style="display: inline-block; padding: 8px 16px; background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; margin-right: 8px; margin-bottom: 5px;">
                            📄 Buka Scan Bank Garansi
                        </a>
                    @endif
                    @if($submission->signed_document_path)
                        <a href="{{ asset($submission->signed_document_path) }}" target="_blank" style="display: inline-block; padding: 8px 16px; background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; margin-bottom: 5px;">
                            📑 Buka Formulir Bertandatangan
                        </a>
                    @endif
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
                <p style="margin-bottom: 15px; font-size: 13px; color: #64748b; font-weight: 600;">Pilih tindakan validasi di bawah ini:</p>

                <a href="{{ route('approval.process', ['token' => $log->token, 'action' => 'approve']) }}"
                   style="display: inline-block; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 6px; color: #ffffff !important; background-color: #16a34a; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);">
                    ✅ Quick Approve
                </a>

                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'review']) }}"
                   style="display: inline-block; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 6px; color: #ffffff !important; background-color: #2563eb; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);">
                    📝 Review / Detail Form
                </a>

                <a href="{{ route('approval.form', ['token' => $log->token, 'action' => 'reject']) }}"
                   style="display: inline-block; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 6px; color: #ffffff !important; background-color: #dc2626; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);">
                    ❌ Reject / Revisi
                </a>
            </div>

            <p style="text-align: center; font-size: 12px; color: #94a3b8; margin-top: 25px;">
                Link validasi di atas aktif selama status pengajuan masih Pending. Anda juga dapat melakukan validasi melalui dashboard web pada menu Approval Inbox.
            </p>
        </div>

        {{-- FOOTER --}}
        <div style="background-color: #f8fafc; padding: 16px; text-align: center; border-top: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} PT SMII - Sistem Otomasi Bank Garansi
            </p>
        </div>
    </div>
</body>
</html>
