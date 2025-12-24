<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Bank</title>
    <style>
        /* Margin Halaman */
        @page {
            margin-top: 2.5cm;
            margin-bottom: 2.5cm;
            margin-left: 2cm;
            margin-right: 2cm;
        }
    </style>
</head>
{{-- FONT ARIAL 11pt --}}
<body style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #000;">

    <div style="text-align: right; margin-bottom: 30px;">
        Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    <div style="margin-bottom: 20px;">
        Kepada Yth.<br>
        <strong>{{ strtoupper($bank_name) }}</strong><br>
        {{ $branch_name }}<br>
        Di Tempat.
    </div>

    <div style="font-weight: bold; margin-bottom: 20px; text-decoration: underline;">
        Hal: Surat Pemberitahuan Jatuh Tempo Bank Garansi & Perpanjangan
    </div>

    <div style="text-align: justify; margin-bottom: 30px;">
        <p>Dengan Hormat,</p>
        
        <p>Sehubungan dengan akan berakhirnya masa berlaku Bank Garansi atas nama <strong>{{ strtoupper($customer->name) }}</strong> 
        pada tanggal {{ \Carbon\Carbon::parse($expired_date)->locale('id')->isoFormat('D MMMM Y') }}, dengan ini Kami mohon agar Bank Garansi 
        yang akan habis masa berlakunya tersebut dilakukan perpanjangan masa berlaku Bank Garansi dengan nominal sebesar 
        <strong>Rp. {{ number_format($nominal, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\DocumentHelper::terbilang($nominal)) }} Rupiah.)</strong> 
        untuk menjamin pembayaran atas pembelian produk pada PT. Sinar Meadow International Indonesia, 
        dimana Bank Garansi yang akan diterbitkan dengan masa berlaku sampai dengan 1 (satu) tahun yang ditunjuk atas nama:</p>

        {{-- PERBAIKAN DI SINI: Mengubah <p> menjadi <div> dan mengatur margin agar lebih rapat --}}
        <div style="margin-left: 40px; font-weight: bold; margin-top: 5px; margin-bottom: 15px;">
            PT. Sinar Meadow International Indonesia<br>
            Jalan Pulo Ayang I no. 6<br>
            Kawasan Industri Pulogadung<br>
            Jakarta Timur 13260
        </div>

        <p>Mohon agar dapat mencantumkan Nomor Perjanjian Kerjasama Distributor (PKD) dengan nomor 
        <strong>{{ $nomor_pkd }}</strong> di dalam Bank Garansi tersebut.</p>

        <p>Demikian Surat Permohonan ini Kami buat atas perhatian dan kerjasamanya Kami ucapkan terima kasih.</p>
    </div>

    <div style="margin-top: 50px;">
        <p>Hormat Kami,<br>
        <strong>PT. Sinar Meadow International Indonesia</strong></p>
        <br><br><br><br>
        <p><strong>Edie Hirman</strong><br>
        Fin. & Admin Dept. Head</p>
    </div>
</body>
</html>