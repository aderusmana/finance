<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lampiran D</title>
</head>
{{-- FONT ARIAL --}}
<body style="font-family: Arial, sans-serif; font-size: 12px; color: #000;">

    {{-- HEADER --}}
    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
        <h3 style="margin: 0; padding: 0; text-decoration: underline;">LAMPIRAN D</h3>
        <p style="margin: 5px 0;">Perhitungan Bank Garansi</p>
        <p style="margin: 5px 0;">Nomor PKD: {{ $nomor_pkd }}</p>
    </div>

    {{-- TABLE DATA --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">1</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">NAMA DISTRIBUTOR</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ strtoupper($customer->name) }}</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">2</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">KOTA</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ strtoupper($customer->city) }}</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">3</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">WILAYAH KERJA</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ strtoupper($customer->area ?? '-') }}</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">4</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">PERIODE</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">
                @php
                    $periods = $rec->periods;
                    $periodeTxt = '-';
                    if($periods->count() > 0) {
                        $start = $periods->min('period_date');
                        $end   = $periods->max('period_date');
                        $periodeTxt = \Carbon\Carbon::parse($start)->isoFormat('MMMM Y') . ' - ' . \Carbon\Carbon::parse($end)->isoFormat('MMMM Y');
                    }
                @endphp
                {{ $periodeTxt }}
            </td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">5</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">RATA-RATA PENJUALAN</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">
                Rp. {{ number_format($rec->average, 0, ',', '.') }}<br>
                <i style="font-size: 11px;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->average)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">6</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">SYARAT PEMBAYARAN</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ $rec->top }} Hari</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">7</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">LEAD TIME</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ $rec->lead_time }} Hari</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">8</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">FAKTOR FLUKTUASI BULANAN</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">{{ $rec->inflation }}%</td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">9</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">LIMIT KREDIT</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">
                Rp. {{ number_format($rec->credit_limit_updated, 0, ',', '.') }}<br>
                <i style="font-size: 11px;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->credit_limit_updated)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">10</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">NILAI BG YANG DITETAPKAN</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">
                Rp. {{ number_format($rec->set_bg, 0, ',', '.') }}<br>
                <i style="font-size: 11px;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($rec->set_bg)) }} Rupiah)</i>
            </td>
        </tr>
        <tr>
            <td style="width: 30px; text-align: center; padding: 5px; vertical-align: top;">11</td>
            <td style="width: 200px; font-weight: bold; padding: 5px; vertical-align: top;">NILAI BG YANG DISERAHKAN</td>
            <td style="width: 10px; padding: 5px; vertical-align: top;">:</td>
            <td style="padding: 5px; vertical-align: top;">
                Rp. {{ number_format($submission->bg_nominal ?? 0, 0, ',', '.') }}<br>
                <i style="font-size: 11px;">({{ ucwords(\App\Helpers\DocumentHelper::terbilang($submission->bg_nominal ?? 0)) }} Rupiah)</i>
            </td>
        </tr>
    </table>

    {{-- SIGNATURE --}}
    <table style="width: 100%; margin-top: 50px; text-align: center; border-collapse: collapse;">
        <tr>
            <td style="width: 33%; padding-bottom: 80px; vertical-align: top;">S&M DEPT. HEAD</td>
            <td style="width: 33%; padding-bottom: 80px; vertical-align: top;">FINANCE DEPT. HEAD</td>
            <td style="width: 33%; padding-bottom: 80px; vertical-align: top;">DISTRIBUTOR</td>
        </tr>
        <tr>
            <td style="font-weight: bold; text-decoration: underline;">{{ isset($sales_name) ? strtoupper($sales_name) : 'Dept Head Sales tidak terpanggil' }}</td>
            <td style="font-weight: bold; text-decoration: underline;">{{ isset($finance_name) ? strtoupper($finance_name) : 'Dept Head Finance tidak terpanggil' }}</td>
            <td style="font-weight: bold; text-decoration: underline;">{{ $customer->name }}</td>
        </tr>
    </table>
</body>
</html>
