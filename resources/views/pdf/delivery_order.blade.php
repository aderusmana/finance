<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note - {{ $order->note->delivery_order_no }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');

        /* Pengaturan Kertas & Margin PDF */
        @page { size: a5 landscape; margin: 20px 30px; }

        body { font-family: 'Helvetica', sans-serif; color: #2d3748; line-height: 1.3; margin: 0; padding: 0; }

        /* Header styling */
        .header-table { width: 100%; border-bottom: 2px solid #a68831; padding-bottom: 8px; margin-bottom: 12px; }
        .company-name { font-size: 20px; font-weight: 800; color: #a68831; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .company-address { margin: 2px 0 0 0; font-size: 10px; color: #718096; }
        .document-title { font-size: 22px; font-weight: 800; color: #2d3748; text-align: right; text-transform: uppercase; margin: 0; letter-spacing: 1px; }
        .document-subtitle { font-size: 18px; color: #5a616c; font-weight: 600; text-align: right; margin: 0; text-transform: uppercase; }

        /* Information Boxes */
        .info-container { width: 100%; margin-bottom: 15px; }
        .info-box { border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; height: 85px; }
        .info-title { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #4a5568; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 5px; }

        /* Item Table (Diperbesar & Garis Header Diperjelas) */
        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        /* Warna border diubah ke #ffffff (Putih) agar pembatas antar kolom terlihat jelas */
        .item-table th { background-color: #a68831; color: #ffffff; padding: 8px; font-weight: 700; text-align: center; font-size: 12px; text-transform: uppercase; border: 1px solid #ffffff; }
        /* Font size tabel dibesarkan menjadi 12px */
        .item-table td { padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 12px; }
        .item-table tr:nth-child(even) { background-color: #f8fafc; }

        /* Signature Area */
        .signature-table { width: 100%; margin-top: 20px; text-align: center; page-break-inside: avoid; }
        .signature-table td { width: 33.33%; padding: 0 15px; vertical-align: bottom; font-size: 11px; }
        .sign-line { height: 65px; margin-bottom: 5px; }

        .text-center { text-align: center; }
        .text-bold { font-weight: 700; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="9%" style="vertical-align: middle; text-align: left;">
                <img src="https://ui-avatars.com/api/?name=SM&background=ffffff&color=a68831&rounded=true&bold=true&size=100" alt="Logo" style="width: 55px; border-radius: 5px;">
            </td>
            <td width="56%" style="vertical-align: middle;">
                <h1 class="company-name">PT Sinar Meadow International Indonesia</h1>
                <p class="company-address">Jl. Pulo Ayang I No.2-3, Kawasan Industri Pulogadung<br>Jatinegara, Jakarta Timur, 13930 - Indonesia</p>
            </td>
            <td width="35%" style="vertical-align: middle;">
                <h2 class="document-title">Surat Jalan</h2>
                <p class="document-subtitle">Delivery Note</p>
            </td>
        </tr>
    </table>

    <table class="info-container">
        <tr>
            <td width="48%" valign="top">
                <div class="info-box">
                    <div class="info-title">Informasi Pengiriman</div>
                    <table width="100%" cellpadding="1" cellspacing="0" style="font-size: 11px;">
                        <tr><td width="35%" class="text-bold">Delivery No</td><td width="5%">:</td><td class="text-bold">{{ $order->note->delivery_order_no }}</td></tr>
                        <tr><td>Nama Customer</td><td>:</td><td>{{ $order->customer->name }}</td></tr>
                        <tr><td>Tanggal Kirim</td><td>:</td><td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</td></tr>
                        <tr><td>Distributor</td><td>:</td><td>{{ $order->distributor->name }}</td></tr>
                    </table>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top">
                <div class="info-box">
                    <div class="info-title">Penerima (Alamat Tujuan)</div>
                    <div style="font-size: 11px; line-height: 1.4; padding-top: 2px;">
                        <strong style="font-size: 13px; color: #1a202c;">{{ $order->customerShipTo->ship_to_name }}</strong><br>
                        {{ $order->customerShipTo->ship_to_address_1 }}<br>
                        @if($order->customerShipTo->ship_to_address_2) {{ $order->customerShipTo->ship_to_address_2 }}<br> @endif
                        <span class="text-bold" style="font-size: 12px;">{{ $order->customerShipTo->ship_to_city }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Item</th>
                <th width="60%">Deskripsi Barang</th>
                <th width="15%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->order_item_code }}</td>
                <td>{{ $item->order_item_name }}</td>
                <td class="text-center text-bold" style="font-size: 14px;">{{ $item->order_quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div class="text-bold">Pihak Pengirim (Distributor)</div>
                <div class="sign-line"></div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div class="text-bold">Pengemudi / Kurir</div>
                <div class="sign-line"></div>
                <div>Nama & Tanda Tangan</div>
            </td>
            <td>
                <div class="text-bold">Pihak Penerima</div>
                <div class="sign-line"></div>
                <div>Nama, TTD & Cap Perusahaan</div>
            </td>
        </tr>
    </table>

</body>
</html>
