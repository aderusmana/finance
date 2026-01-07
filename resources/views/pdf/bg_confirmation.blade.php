<!DOCTYPE html>
<html>
<head>
    <title>Bulk Formulir Pengajuan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* CSS SAMA DENGAN SINGLE FORMULIR */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; }
        .section-title {
            background-color: #e0e0e0;
            padding: 5px 10px;
            font-weight: bold;
            border: 1px solid #000;
            margin-top: 15px;
            margin-bottom: 0;
            font-size: 13px;
        }

        .table-border td, .table-border th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .page-break { page-break-after: always; }
        .page-break:last-child { page-break-after: auto; }

        .content-wrapper { display: block; width: 100%; }
    </style>
</head>
<body>

    @foreach($dataset as $index => $data)
    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="header">
            <h2>FORMULIR PENGAJUAN BANK GARANSI</h2>
            <p>No. Ref: {{ $data['submission']->form_code }}</p>
        </div>

        {{-- BAGIAN A: IDENTITAS --}}
        <p style="margin-bottom: 5px;">Saya yang bertanda tangan di bawah ini mewakili:</p>
        <table style="margin-bottom: 15px; margin-top: 10px;">
            <tr>
                <td style="width: 25%; font-weight: bold;">Nama Perusahaan</td>
                <td style="width: 2%;">:</td>
                <td>{{ strtoupper($data['customer']->name) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Kota / Wilayah</td>
                <td>:</td>
                <td>{{ strtoupper($data['customer']->city) }} / {{ strtoupper($data['customer']->area ?? '-') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tanggal Pengajuan</td>
                <td>:</td>
                <td>{{ date('d F Y', strtotime($data['submission']->submitted_at ?? now())) }}</td>
            </tr>
        </table>

        {{-- BAGIAN B: DATA PERHITUNGAN (LAMPIRAN D) --}}
        <div class="section-title">
            A. DATA KEUANGAN & LIMIT KREDIT (LAMPIRAN D)
        </div>

        @php
            $rec = $data['rec'];
            $periods = $rec->periods;
            $periodeTxt = '-';
            if($periods && $periods->count() > 0) {
                $start = $periods->min('period_date');
                $end   = $periods->max('period_date');
                $periodeTxt = \Carbon\Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . \Carbon\Carbon::parse($end)->isoFormat('MMMM Y');
            }
        @endphp

        <table class="table-border" style="margin-bottom: 15px; margin-top: 0;">
            <tr>
                <td style="width: 5%; text-align: center;">1</td>
                <td style="width: 40%;">PERIODE</td>
                <td style="font-weight: bold;">{{ $periodeTxt }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>RATA-RATA PENJUALAN</td>
                <td>
                    Rp {{ number_format($rec->average, 0, ',', '.') }}<br>
                    <span style="font-size: 10px; font-style: italic;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->average)) }} Rupiah)</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>SYARAT PEMBAYARAN (TOP)</td>
                <td>{{ $rec->top }} Hari</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>LEAD TIME</td>
                <td>{{ $rec->lead_time }} Hari</td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>FAKTOR FLUKTUASI BULANAN</td>
                <td>{{ $rec->inflation }}%</td>
            </tr>
            <tr>
                <td style="text-align: center;">6</td>
                <td>LIMIT KREDIT</td>
                <td style="font-weight: bold;">
                    Rp {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}<br>
                    <span style="font-size: 10px; font-weight: normal; font-style: italic;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->credit_limit_updated)) }} Rupiah)</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">7</td>
                <td>NILAI BG DITETAPKAN</td>
                <td style="font-weight: bold;">
                    Rp {{ number_format($rec->set_bg, 0, ',', '.') }}<br>
                    <span style="font-size: 10px; font-weight: normal; font-style: italic;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->set_bg)) }} Rupiah)</span>
                </td>
            </tr>
        </table>

        {{-- BAGIAN C: RINCIAN BANK --}}
        <div class="section-title">
            B. RINCIAN BANK GARANSI YANG DISERAHKAN
        </div>

        <p style="margin: 5px 0 10px 0;">Mengajukan penerbitan Bank Garansi dengan rincian sebagai berikut:</p>

        <table class="table-border" style="margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="width: 20%;">Nama Bank</th>
                    <th style="width: 20%;">Cabang</th>
                    <th style="width: 30%;">Alamat / Kontak</th>
                    <th style="width: 30%; text-align: right;">Nominal (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    // Ambil BG spesifik yang dipassing dari Controller
                    $bgItem = $data['bg'];
                @endphp

                @if($bgItem)
                    @foreach($bgItem->details as $detail)
                        @php $grandTotal += $detail->nominal; @endphp
                        <tr>
                            <td>{{ $detail->bank_name }}</td>
                            <td>{{ $detail->branch_name ?? '-' }}</td>
                            <td>
                                {{ $detail->bank_address ?? '-' }}<br>
                                <small>PIC: {{ $detail->contact_person ?? '-' }}</small>
                            </td>
                            <td style="text-align: right;">{{ number_format($detail->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="4" style="text-align:center;">Data BG tidak tersedia</td></tr>
                @endif

                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; background-color: #f9f9f9;">TOTAL PENGAJUAN</td>
                    <td style="text-align: right; font-weight: bold; background-color: #f9f9f9;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: -10px; font-size: 11px;">
            <strong>Terbilang:</strong> <span style="font-style: italic;">{{ ucwords(\App\Helpers\DocumentHelper::terbilang($grandTotal)) }} Rupiah</span>
        </div>

        <p style="margin-top: 10px;">Demikian formulir ini dibuat dengan sebenar-benarnya untuk diproses lebih lanjut sesuai ketentuan yang berlaku.</p>

        {{-- TANDA TANGAN --}}
        <div style="margin-top: 15px; width: 100%;">
            <div style="width: 40%; display: inline-block; vertical-align: top;">
                <p style="margin-bottom: 5px;">Disetujui Oleh (Customer),</p>
                <p style="font-size: 10px; color: #555; margin-top:0;"><i>(Mohon bubuhkan Tanda Tangan & Stempel)</i></p>

                <div style="height: 80px; border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px; width: 80%;"></div>

                <table style="width: 100%; margin-top: 0; border-collapse: collapse;">
                    <tr>
                        <td style="width: 20%; padding: 0; border: none;">Nama</td>
                        <td style="width: 5%; padding: 0; border: none;">:</td>
                        <td style="border-bottom: 1px dotted #000; padding: 0; border: none;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 0; border: none;">Jabatan</td>
                        <td style="padding: 0; border: none;">:</td>
                        <td style="border-bottom: 1px dotted #000; padding: 0; border: none;"></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif

    @endforeach

</body>
</html>
