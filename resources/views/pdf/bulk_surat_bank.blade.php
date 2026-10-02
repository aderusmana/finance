<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulk Surat Bank</title>
    <style>
        @page {
            margin-top: 3cm;
            margin-bottom: 3cm;
            margin-left: 2.5cm;
            margin-right: 2.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
@foreach($dataset as $index => $data)
    <div class="{{ $index > 0 ? 'page-break' : '' }}">
        <div style="text-align: right; margin-bottom: 30px;">
            Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
        </div>

        <div style="margin-bottom: 20px;">
            Kepada Yth.<br>
            <strong>{{ strtoupper($data['bank_name'] ?? 'BANK PENJAMIN') }}</strong><br>
            {{ $data['branch_name'] ?? '' }}<br>
            Di Tempat.
        </div>

        <div style="margin-bottom: 20px;">
            Hal: <strong>Surat Pemberitahuan Jatuh Tempo Bank Garansi &amp; Perpanjangan</strong>
        </div>

        <div style="text-align: justify; margin-bottom: 20px;">
            <p>Dengan Hormat,</p>

            Sehubungan dengan akan berakhirnya masa berlaku Bank Garansi atas nama <strong>{{ strtoupper($data['customer']->name ?? '-') }}</strong>
            pada tanggal {{ \Carbon\Carbon::parse($data['expired_date'] ?? now())->locale('id')->isoFormat('D MMMM Y') }}, dengan ini Kami mohon agar Bank Garansi
            yang akan habis masa berlakunya tersebut dilakukan perpanjangan masa berlaku Bank Garansi dengan nominal sebesar
            <strong>Rp. {{ number_format($data['nominal'] ?? 0, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\DocumentHelper::terbilang($data['nominal'] ?? 0)) }} Rupiah.)</strong>
            untuk menjamin pembayaran atas pembelian produk pada PT. Sinar Meadow International Indonesia,
            dimana Bank Garansi yang akan diterbitkan dengan masa berlaku sampai dengan 1 (satu) tahun yang ditunjuk atas nama:
            <br>

            <div style="margin-left: 40px; margin-top: 10px; margin-bottom: 10px;">
                <strong>PT. Sinar Meadow International Indonesia</strong><br>
                Jalan Pulo Ayang I no. 6<br>
                Kawasan Industri Pulogadung<br>
                Jakarta Timur 13260
            </div>

            Mohon agar dapat mencantumkan Nomor Perjanjian Kerjasama Distributor (PKD) dengan nomor
            <strong>{{ $data['nomor_pkd'] ?? '-' }}</strong> di dalam Bank Garansi tersebut.

            <p style="margin-top: 15px;">Demikian Surat Permohonan ini Kami buat atas perhatian dan kerjasamanya Kami ucapkan terima kasih.</p>
        </div>

        <p>Hormat Kami,<br>
        <strong>PT. Sinar Meadow International Indonesia</strong></p>
        <br><br><br>
        <strong style="text-decoration: underline;">{{ strtoupper($data['finance_name'] ?? 'FINANCE DEPT. HEAD') }}</strong><br>
        Fin. &amp; Admin Dept. Head
    </div>
@endforeach
</body>
</html>
