<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Surat Distributor</title>
    <style>
        @page {
            margin-top: 3cm;
            margin-bottom: 3cm;
            margin-left: 2.5cm;
            margin-right: 2.5cm;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #000;">
    <div style="text-align: right; margin-bottom: 30px;">
        Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
    </div>
    <div style="margin-bottom: 20px;">
        Kepada Yth.<br>
        <strong>{{ strtoupper($customer->name) }}</strong><br>
        {{ $customer->address1 }} {{ $customer->address2 ?? '' }}<br>
        Attn. Bp./Ibu Pimpinan
    </div>

    <div style="margin-bottom: 20px;">
        Hal: <strong>Surat Pemberitahuan Jatuh Tempo Bank Garansi & Perpanjangan</strong>
    </div>

    <div style="text-align: justify; margin-bottom: 20px;">
        <p>Dengan Hormat,</p>
        
        Sehubungan dengan akan berakhirnya masa berlaku Bank Garansi atas nama <strong>{{ strtoupper($customer->name) }}</strong> 
        pada tanggal {{ \Carbon\Carbon::parse($expired_date)->locale('id')->isoFormat('D MMMM Y') }}, dengan ini Kami mohon agar Bank Garansi 
        yang akan habis masa berlakunya tersebut dilakukan perpanjangan masa berlaku Bank Garansi dengan nominal sebesar 
        <strong>Rp. {{ number_format($nominal, 0, ',', '.') }},- ({{ ucwords(\App\Helpers\DocumentHelper::terbilang($nominal)) }} Rupiah.)</strong> 
        untuk menjamin pembayaran atas pembelian produk pada PT. Sinar Meadow International Indonesia, 
        dimana Bank Garansi yang akan diterbitkan dengan masa berlaku sampai dengan 1 (satu) tahun yang ditunjuk atas nama:
        <br>

        <div style="margin-left: 40px;">
            <strong>PT. Sinar Meadow International Indonesia</strong><br>
            Jalan Pulo Ayang I no. 6<br>
            Kawasan Industri Pulogadung<br>
            Jakarta Timur 13260
        </div>  
        
        Mohon agar dapat mencantumkan Nomor Perjanjian Kerjasama Distributor (PKD) dengan nomor 
        {{ $nomor_pkd }} di dalam Bank Garansi tersebut.
    

        <p>Demikian Surat Permohonan ini Kami buat atas perhatian dan kerjasamanya Kami ucapkan terima kasih.</p>
    </div>

    {{-- SIGNATURE --}}
        <p>Hormat Kami,<br>
        <strong>PT. Sinar Meadow International Indonesia</strong></p>
        <br><br>
        <strong style="text-decoration: underline;">Edie Hirman</strong><br>
        Fin. & Admin Dept. Head
</body>
</html>