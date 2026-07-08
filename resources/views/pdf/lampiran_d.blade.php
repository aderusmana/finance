<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lampiran D</title>
    <style>
        /* Margin Halaman */
        @page {
            margin: 2.5cm 2.5cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.3;
        }

        /* Header Style - RATA KIRI */
        .header-container {
            text-align: left; /* Diubah ke Kiri */
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

        /* General Table Style */
        table {
            width: 100%;
            border-collapse: collapse; /* Garis menyatu */
        }

        /* Main Data Table */
        .table-border {
            margin-bottom: 40px;
        }

        .table-border td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10pt;
        }

        /* Column Widths */
        .col-no { width: 5%; text-align: center; }
        .col-label { width: 35%; font-weight: bold; }
        .col-sep { width: 2%; text-align: center; border-left: none; border-right: none; }
        .col-val { width: 58%; border-left: none; font-weight: normal; }

        /* Signature Table with Borders */
        .signature-table {
            margin-top: 20px;
        }

        .signature-table td {
            border: 1px solid #000; /* Border Hitam Tegas */
            padding: 5px;
            vertical-align: middle;
            font-size: 10pt;
            text-align: center;
            width: 33.33%; /* Pembagian rata 3 kolom */
        }

        .sign-title {
            font-weight: bold;
            height: 30px; /* Tinggi baris judul */
            vertical-align: middle;
        }

        .sign-space {
            height: 80px; /* Ruang kosong untuk tanda tangan */
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            height: 30px;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    {{-- HEADER (RATA KIRI) --}}
    <div class="header-container">
        <h3 class="header-title">LAMPIRAN D</h3>
        <p class="header-sub">Perhitungan Bank Garansi</p>
        <p class="header-sub">Nomor PKD: {{ $nomor_pkd }}</p>
    </div>

    {{-- TABLE CONTENT --}}
    <table class="table-border">
        <tr>
            <td class="col-no">1</td>
            <td class="col-label">NAMA DISTRIBUTOR</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ strtoupper($customer->pic) }}</td>
        </tr>
        <tr>
            <td class="col-no">2</td>
            <td class="col-label">KOTA</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ strtoupper($customer->city) }}</td>
        </tr>
        <tr>
            <td class="col-no">3</td>
            <td class="col-label">WILAYAH KERJA</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ strtoupper($customer->area ?? '-') }}</td>
        </tr>
        <tr>
            <td class="col-no">4</td>
            <td class="col-label">PERIODE</td>
            <td class="col-sep">:</td>
            <td class="col-val">
                @php
                    $periods = $rec->periods;
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
                Rp. {{ number_format($rec->average, 0, ',', '.') }}<br>
                <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->average)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td class="col-no">6</td>
            <td class="col-label">SYARAT PEMBAYARAN</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $rec->top }} Hari</td>
        </tr>
        <tr>
            <td class="col-no">7</td>
            <td class="col-label">LEAD TIME</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $rec->lead_time }} Hari</td>
        </tr>
        <tr>
            <td class="col-no">8</td>
            <td class="col-label">FAKTOR FLUKTUASI BULANAN</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ number_format($rec->inflation, 2) }}%</td>
        </tr>
        <tr>
            <td class="col-no">9</td>
            <td class="col-label">LIMIT KREDIT</td>
            <td class="col-sep">:</td>
            <td class="col-val">
                Rp. {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}<br>
                <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->credit_limit_updated)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td class="col-no">10</td>
            <td class="col-label">NILAI BG YANG DITETAPKAN</td>
            <td class="col-sep">:</td>
            <td class="col-val">
                Rp. {{ number_format($rec->set_bg, 0, ',', '.') }}<br>
                <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->set_bg)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td class="col-no">11</td>
            <td class="col-label">NILAI BG YANG DISERAHKAN</td>
            <td class="col-sep">:</td>
            <td class="col-val">
                @php
                    $nilaiDiserahkan = 0;
                    if(isset($total_bg_diserahkan) && $total_bg_diserahkan > 0) {
                        $nilaiDiserahkan = $total_bg_diserahkan;
                    } elseif(isset($bg) && $bg->bg_nominal > 0) {
                        $nilaiDiserahkan = $bg->bg_nominal;
                    }
                @endphp

                Rp. {{ number_format($nilaiDiserahkan, 0, ',', '.') }}<br>
                <i style="font-size: 9pt;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($nilaiDiserahkan)) }} Rupiah)</i>
            </td>
        </tr>
    </table>

    {{-- SIGNATURE SECTION (DENGAN BORDER) --}}
    <table class="signature-table">
        {{-- Row Judul --}}
        <tr>
            <td class="sign-title">S&M DEPT. HEAD</td>
            <td class="sign-title">FINANCE DEPT. HEAD</td>
            <td class="sign-title">DISTRIBUTOR</td>
        </tr>
        {{-- Row Kosong (Space TTD) --}}
        <tr>
            <td class="sign-space"></td>
            <td class="sign-space"></td>
            <td class="sign-space"></td>
        </tr>
        {{-- Row Nama --}}
        <tr>
            <td class="sign-name">{{ isset($sales_name) ? strtoupper($sales_name) : '.........................' }}</td>
            <td class="sign-name">{{ isset($finance_name) ? strtoupper($finance_name) : '.........................' }}</td>
            <td class="sign-name">{{ strtoupper($customer->name) }}</td>
        </tr>
    </table>

</body>
</html>
