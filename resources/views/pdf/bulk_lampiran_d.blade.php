<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulk Lampiran D</title>
    <style>
        /* 1. MARGIN HALAMAN (SAMA PERSIS DENGAN SATUAN) */
        @page {
            margin: 2.5cm 2.5cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.3;
        }

        /* 2. HEADER STYLE */
        .header-container {
            text-align: left;
            margin-bottom: 30px;
        }

        .header-title {
            text-decoration: underline;
            margin: 0 0 5px 0;
            font-size: 12pt;
            font-weight: bold;
        }

        .header-sub {
            margin: 2px 0;
            font-size: 11pt;
        }

        /* 3. TABLE STYLE (BORDER TEGAS) */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-border {
            margin-bottom: 40px;
            width: 100%;
        }

        .table-border td {
            border: 1px solid #000 !important; /* Paksa border hitam */
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10pt;
        }

        /* Column Widths */
        .col-no { width: 5%; text-align: center; }
        .col-label { width: 35%; font-weight: bold; }
        .col-sep { width: 2%; text-align: center; border-left: none; border-right: none; }
        .col-val { width: 58%; border-left: none; font-weight: normal; }

        /* 4. SIGNATURE STYLE */
        .signature-table {
            margin-top: 20px;
            width: 100%;
        }

        .signature-table td {
            border: 1px solid #000 !important;
            padding: 5px;
            vertical-align: middle;
            font-size: 10pt;
            text-align: center;
            width: 33.33%;
        }

        .sign-title { font-weight: bold; height: 30px; vertical-align: middle; }
        .sign-space { height: 80px; }
        .sign-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; height: 30px; vertical-align: middle; }

        /* 5. PAGE BREAK (PENTING UNTUK BULK) */
        .page-break {
            page-break-after: always;
        }
        .page-break:last-child {
            page-break-after: auto;
        }

        .content-wrapper {
            /* Wrapper untuk menjaga layout per halaman */
            display: block;
            width: 100%;
        }
    </style>
</head>
<body>

    @foreach($dataset as $index => $data)
        <div class="content-wrapper">

            {{-- HEADER --}}
            <div class="header-container">
                <div class="header-title">LAMPIRAN D</div>
                <div class="header-sub">Perhitungan Bank Garansi</div>
                <div class="header-sub">Nomor PKD: {{ $data['nomor_pkd'] }}</div>
            </div>

            {{-- TABLE DATA --}}
            <table class="table-border">
                <tr>
                    <td class="col-no">1</td>
                    <td class="col-label">NAMA DISTRIBUTOR</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ strtoupper($data['customer']->name) }}</td>
                </tr>
                <tr>
                    <td class="col-no">2</td>
                    <td class="col-label">KOTA</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ strtoupper($data['customer']->city) }}</td>
                </tr>
                <tr>
                    <td class="col-no">3</td>
                    <td class="col-label">WILAYAH KERJA</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ strtoupper($data['customer']->area ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="col-no">4</td>
                    <td class="col-label">PERIODE</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">
                        @php
                            $periods = $data['rec']->periods;
                            $periodeTxt = '-';
                            if($periods && $periods->count() > 0) {
                                $start = $periods->min('period_date');
                                $end   = $periods->max('period_date');
                                $periodeTxt = \Carbon\Carbon::parse($start)->translatedFormat('F Y') . ' - ' . \Carbon\Carbon::parse($end)->translatedFormat('F Y');
                            }
                        @endphp
                        {{ $periodeTxt }}
                    </td>
                </tr>
                <tr>
                    <td class="col-no">5</td>
                    <td class="col-label">RATA-RATA PENJUALAN</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">
                        Rp. {{ number_format($data['rec']->average, 0, ',', '.') }}<br>
                        <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($data['rec']->average)) }} Rupiah)</i>
                    </td>
                </tr>
                <tr>
                    <td class="col-no">6</td>
                    <td class="col-label">SYARAT PEMBAYARAN</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ $data['rec']->top }} Hari</td>
                </tr>
                <tr>
                    <td class="col-no">7</td>
                    <td class="col-label">LEAD TIME</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ $data['rec']->lead_time }} Hari</td>
                </tr>
                <tr>
                    <td class="col-no">8</td>
                    <td class="col-label">FAKTOR FLUKTUASI BULANAN</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">{{ number_format($data['rec']->inflation, 2) }}%</td>
                </tr>
                <tr>
                    <td class="col-no">9</td>
                    <td class="col-label">LIMIT KREDIT</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">
                        Rp. {{ number_format($data['rec']->credit_limit_updated, 0, ',', '.') }}<br>
                        <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($data['rec']->credit_limit_updated)) }} Rupiah)</i>
                    </td>
                </tr>
                <tr>
                    <td class="col-no">10</td>
                    <td class="col-label">NILAI BG YANG DITETAPKAN</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">
                        Rp. {{ number_format($data['rec']->set_bg, 0, ',', '.') }}<br>
                        <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($data['rec']->set_bg)) }} Rupiah)</i>
                    </td>
                </tr>
                <tr>
                    <td class="col-no">11</td>
                    <td class="col-label">NILAI BG YANG DISERAHKAN</td>
                    <td class="col-sep">:</td>
                    <td class="col-val">
                        @php
                            $valDiserahkan = $data['total_bg_diserahkan'] ?? 0;
                        @endphp
                        Rp. {{ number_format($valDiserahkan, 0, ',', '.') }}<br>
                        <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($valDiserahkan)) }} Rupiah)</i>
                    </td>
                </tr>
            </table>

            {{-- SIGNATURE --}}
            <table class="signature-table">
                <tr>
                    <td class="sign-title">S&M DEPT. HEAD</td>
                    <td class="sign-title">FINANCE DEPT. HEAD</td>
                    <td class="sign-title">DISTRIBUTOR</td>
                </tr>
                <tr>
                    <td class="sign-space"></td>
                    <td class="sign-space"></td>
                    <td class="sign-space"></td>
                </tr>
                <tr>
                    <td class="sign-name">{{ isset($data['sales_name']) ? strtoupper($data['sales_name']) : '.........................' }}</td>
                    <td class="sign-name">{{ isset($data['finance_name']) ? strtoupper($data['finance_name']) : '.........................' }}</td>
                    <td class="sign-name">{{ strtoupper($data['customer']->name) }}</td>
                </tr>
            </table>

        </div>

        {{-- Page Break Logic --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>
