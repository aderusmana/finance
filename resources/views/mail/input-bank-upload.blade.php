<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Bank Garansi</title>
</head>
<body style="margin: 0; padding: 40px 0; background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; -webkit-font-smoothing: antialiased; color: #334155;">

    @php
        $isUploadContext = isset($submission);

        // Normalisasi Data
        if ($isUploadContext) {
            $rec = $submission->recommendation;
            $token = $submission->token;
            $pageTitle = 'Konfirmasi Pengajuan BG';
            $refNumber = 'Form Code: #' . $submission->form_code;

            // Link menuju halaman Upload
            $actionUrl = route('customer.portal.upload-form', ['token' => $token]);
            $btnColor = '#ea580c'; // Orange
            $btnShadow = 'rgba(234, 88, 12, 0.2)';
            $btnText = 'Upload Dokumen Scan &rarr;';
        } else {
            $rec = $recommendation;
            $token = $rec->token;
            $pageTitle = 'Konfirmasi & Pengisian Form Bank Garansi';
            $refNumber = 'Ref: #BG-' . $rec->id . '/' . date('Y');

            // Link menuju halaman Input
            $actionUrl = route('customer.portal.input-form', ['token' => $token]);
            $btnColor = '#2563eb'; // Biru
            $btnShadow = 'rgba(37, 99, 235, 0.2)';
            $btnText = 'Lengkapi Formulir &rarr;';
        }

        $customer = $rec->customer;
    @endphp

    <div style="max-width: 680px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">

        {{-- HEADER --}}
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 40px 30px; text-align: center; color: #ffffff;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">{{ $pageTitle }}</h1>
            <p style="margin: 8px 0 0; opacity: 0.9; font-size: 14px;">{{ $refNumber }}</p>
        </div>

        <div style="padding: 40px 35px;">

            {{-- GREETING --}}
            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin-bottom: 25px; margin-top: 0;">
                Yth. <strong>{{ $customer->name ?? 'Mitra Bisnis' }}</strong>,<br><br>

                @if($isUploadContext)
                    Terima kasih, data formulir digital Anda telah berhasil kami terima.
                    Untuk memvalidasi pengajuan ini secara hukum, kami memerlukan dokumen fisik yang telah ditanda tangani.
                @else
                    Berdasarkan evaluasi kinerja penjualan terbaru dan kebijakan manajemen risiko, kami telah menyetujui pembaruan fasilitas Bank Garansi Anda. Berikut adalah hasil keputusan final manajemen:
                @endif
            </p>

            {{-- HERO SECTION (DYNAMIC) --}}
            @if($isUploadContext)
                {{-- TAMPILAN HERO: ACTION REQUIRED (ORANGE) --}}
                <div style="background-color: #fff7ed; border: 1px solid #fed7aa; border-radius: 10px; padding: 25px; margin-bottom: 35px;">
                    <h3 style="margin: 0 0 15px; color: #9a3412; font-size: 16px; font-weight: 700;">
                        ⚠️ Tindakan Diperlukan: Upload Dokumen
                    </h3>
                    <ol style="margin: 0; padding-left: 20px; font-size: 14px; color: #9a3412; line-height: 1.6;">
                        <li style="margin-bottom: 8px;"><strong>Download</strong> file PDF yang terlampir pada email ini.</li>
                        <li style="margin-bottom: 8px;"><strong>Cetak & Tanda Tangani</strong> (Basah + Stempel).</li>
                        <li style="margin-bottom: 8px;"><strong>Scan</strong> dokumen tersebut.</li>
                        <li><strong>Upload</strong> kembali melalui tombol di bawah ini.</li>
                    </ol>
                </div>
            @else
                {{-- TAMPILAN HERO: CREDIT LIMIT APPROVED (GREEN) --}}
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 30px; text-align: center; margin-bottom: 35px;">
                    <p style="margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; color: #15803d; font-weight: 700;">
                        Total Plafon Kredit (Credit Limit) Terbaru
                    </p>
                    <h1 style="margin: 10px 0 10px; font-size: 38px; color: #15803d; letter-spacing: -1px; font-weight: 800;">
                        Rp {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}
                    </h1>
                </div>
            @endif

            {{-- DATA TABLES (DITAMPILKAN DI KEDUA KONDISI) --}}
            <div style="margin-bottom: 15px; border-left: 4px solid #3b82f6; padding-left: 12px;">
                <h3 style="margin: 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">Rincian Analisa & Keputusan</h3>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 40px;">
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">Nominal BG Disetujui (Set BG)</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #1e3a8a; font-weight: 700;">
                        Rp {{ number_format($rec->set_bg, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">Rata-Rata Penjualan</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155;">
                        Rp {{ number_format($rec->average, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #475569;">System Recommended Limit</td>
                    <td style="padding: 12px 0; border-bottom: 1px solid #f1f5f9; text-align: right; color: #334155;">
                        Rp {{ number_format($rec->recommended_credit_limit, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Parameter Teknis --}}
                <tr>
                    <td colspan="2" style="padding: 20px 0 5px; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Parameter Kalkulasi
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px;">TOP / Lead Time</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px;">
                        {{ $rec->top }} Hari / {{ $rec->lead_time }} Hari
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 13px;">Inflasi / Tax</td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 13px;">
                        {{ $rec->inflation }}% / {{ ($rec->tax ? $rec->tax->value * 100 : 11) }}%
                    </td>
                </tr>
            </table>

            {{-- PERIODS TABLE --}}
            <div style="margin-bottom: 15px; border-left: 4px solid #3b82f6; padding-left: 12px;">
                <h3 style="margin: 0; color: #1e3a8a; font-size: 16px; font-weight: 700;">Riwayat Penjualan</h3>
            </div>

            <div style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 40px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th style="padding: 10px 15px; text-align: left; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Periode</th>
                            <th style="padding: 10px 15px; text-align: right; color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rec->periods as $period)
                        <tr>
                            <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; color: #334155;">
                                {{ \Carbon\Carbon::parse($period->period_date)->locale('id')->isoFormat('MMMM Y') }}
                            </td>
                            <td style="padding: 8px 15px; border-bottom: 1px solid #f1f5f9; text-align: right; font-family: Consolas, monospace; color: #334155;">
                                Rp {{ number_format($period->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="padding: 15px; text-align: center; color: #94a3b8; font-style: italic;">
                                Tidak ada rincian periode.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CTA SECTION (DYNAMIC) --}}
            <div style="text-align: center; padding: 35px 20px; background-color: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <p style="font-size: 14px; margin: 0 0 25px; color: #475569; line-height: 1.5;">
                    @if($isUploadContext)
                        Untuk melanjutkan proses, silakan upload dokumen hasil scan melalui tombol berikut:
                    @else
                        Untuk melanjutkan penerbitan BG senilai <strong>Rp {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}</strong>, mohon lengkapi detail bank penjamin:
                    @endif
                </p>

                <a href="{{ $actionUrl }}"
                   style="display: inline-block; background-color: {{ $btnColor }}; color: #ffffff; padding: 14px 35px; font-size: 16px; font-weight: 700; text-decoration: none; border-radius: 50px; box-shadow: 0 4px 10px {{ $btnShadow }};">
                    {!! $btnText !!}
                </a>

                <p style="font-size: 12px; color: #94a3b8; margin: 20px 0 0;">
                    <em>*Tautan ini bersifat rahasia. Mohon segera selesaikan proses ini.</em>
                </p>
            </div>

        </div>

        {{-- FOOTER --}}
        <div style="background-color: #1e293b; color: #94a3b8; padding: 30px; text-align: center; font-size: 12px; line-height: 1.6;">
            <p style="margin: 0 0 10px;">Email ini dikirim secara otomatis oleh Sistem Manajemen Kredit.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} <strong>PT. Nama Perusahaan Anda</strong>.<br>Jl. Contoh Alamat No. 123, Jakarta, Indonesia.</p>
        </div>

    </div>

</body>
</html>
