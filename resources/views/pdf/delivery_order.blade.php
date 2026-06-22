<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Note - {{ $order->note->delivery_order_no }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');

        /* Paper Settings & PDF Margin */
        @page {
            size: a5 landscape;
            margin: 20px 30px;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: #2d3748;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* DomPDF: keep signature anchored at page bottom */
        .content {
            padding-bottom: 120px;
        }

        .signature-fixed {
            /* Diubah dari fixed menjadi absolute agar tidak duplikat ke halaman 2 */
            position: absolute;
            left: 30px;
            right: 30px;
            bottom: 20px;
        }

        /* Header styling */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #a68831;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .company-name {
            font-size: 15px;
            font-weight: 800;
            color: #a68831;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-address {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #718096;
        }

        .document-title {
            font-size: 22px;
            font-weight: 800;
            color: #2d3748;
            text-align: right;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 1px;
        }

        .document-subtitle {
            font-size: 18px;
            color: #5a616c;
            font-weight: 600;
            text-align: right;
            margin: 0;
            text-transform: uppercase;
        }

        /* Information Boxes */
        .info-container {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-box {
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 6px;
            height: 100px;
        }

        .info-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 5px;
        }

        /* Item Table */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            table-layout: fixed;
        }

        /* Header border color changed to white for clarity */
        .item-table th {
            background-color: #a68831;
            color: #ffffff;
            padding: 5px 6px;
            font-weight: 700;
            text-align: center;
            font-size: 10px;
            line-height: 1.1;
            text-transform: uppercase;
            border: 1px solid #ffffff;
        }

        .item-table td {
            padding: 2px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            line-height: 1.15;
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .item-table td:nth-child(1),
        .item-table th:nth-child(1) {
            width: 6%;
        }

        .item-table td:nth-child(2),
        .item-table th:nth-child(2) {
            width: 18%;
        }

        .item-table td:nth-child(3),
        .item-table th:nth-child(3) {
            width: 61%;
        }

        .item-table td:nth-child(4),
        .item-table th:nth-child(4) {
            width: 15%;
        }

        .item-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Signature Area */
        .signature-table {
            width: 100%;
            margin-top: 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 33.33%;
            padding: 0 15px;
            vertical-align: bottom;
            font-size: 11px;
        }

        .sign-line {
            height: 65px;
            margin-bottom: 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: 700;
        }

        /* ========================================== */
        /* KASO SPECIFIC STYLES                       */
        /* ========================================== */
        .page-break {
            page-break-before: always;
        }

        .kaso-wrapper {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #000;
            padding: 0;
        }

        .kaso-header-text {
            text-align: center;
            color: #000080; /* Navy Blue dari image */
        }

        .kaso-company {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .kaso-address {
            font-size: 11px;
            font-weight: bold;
        }

        .kaso-line-thick {
            border-top: 2px solid #000;
            margin-top: 8px;
        }

        .kaso-line-thin {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 15px;
        }

        .kaso-title {
            color: #ff0000; /* Merah sesuai image */
            font-size: 20px;
            font-style: italic;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .kaso-info-table {
            width: 100%;
            font-size: 11px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .kaso-info-table td {
            padding: 4px 2px;
            vertical-align: bottom;
        }

        .kaso-uline {
            border-bottom: 1px solid #000;
        }

        .kaso-item-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .kaso-item-table th, .kaso-item-table td {
            border: 1px solid #000;
            padding: 6px 4px;
        }

        .kaso-item-table th {
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ======================================================= --}}
    {{-- PAGE 1: DELIVERY NOTES (Tidak diubah, hanya dibungkus)  --}}
    {{-- ======================================================= --}}
    <div style="position: relative; height: 100%;">
        <div class="content">

            <table class="header-table">
                <tr>
                    <td width="9%" style="vertical-align: middle; text-align: left;">
                        <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo"
                            style="width: 65px; border-radius: 5px;">
                    </td>
                    <td width="56%" style="vertical-align: middle;">
                        <h1 class="company-name">PT Sinar Meadow International Indonesia</h1>
                        <p class="company-address">Jl. Pulo Ayang I No.6, Kawasan Industri Pulogadung<br>Jatinegara, Jakarta
                            Timur, 13260 - Indonesia</p>
                    </td>
                    <td width="35%" style="vertical-align: middle;">
                        <h2 class="document-title">Delivery Notes</h2>
                    </td>
                </tr>
            </table>

            <table class="info-container">
                <tr>
                    <td width="48%" valign="top">
                        <div class="info-box">
                            <div class="info-title">Delivery Information</div>
                            <table width="100%" cellpadding="1" cellspacing="0" style="font-size: 11px;">
                                <tr>
                                    <td width="35%" class="text-bold">Delivery No</td>
                                    <td width="5%">:</td>
                                    <td class="text-bold">{{ $order->note->delivery_order_no }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-bold">Purchase Order No.</td>
                                    <td width="5%">:</td>
                                    <td class="text-bold">{{ $order->no_po ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Customer Name</td>
                                    <td>:</td>
                                    <td>{{ $order->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>Delivery Date</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Distributor</td>
                                    <td>:</td>
                                    <td>{{ $order->distributor->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width="4%"></td>
                    <td width="48%" valign="top">
                        <div class="info-box">
                            <div class="info-title">Recipient (Delivery Address)</div>
                            <div style="font-size: 11px; line-height: 1.4; padding-top: 2px;">
                                <strong
                                    style="font-size: 13px; color: #1a202c;">{{ $order->customerShipTo->ship_to_name }}</strong><br>
                                {{ $order->customerShipTo->ship_to_address_1 }}<br>
                                @if ($order->customerShipTo->ship_to_address_2)
                                    {{ $order->customerShipTo->ship_to_address_2 }}<br>
                                @endif
                                <span class="text-bold"
                                    style="font-size: 12px;">{{ $order->customerShipTo->ship_to_city }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="item-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item Code</th>
                        <th>Item Description</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item->order_item_code }}</td>
                            <td>{{ $item->order_item_name }}</td>
                            <td class="text-center text-bold">{{ $item->order_quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="signature-fixed">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="text-bold">Sender (Distributor)</div>
                        <div class="sign-line"></div>
                        <div>Name & Signature</div>
                    </td>
                    <td>
                        <div class="text-bold">Recipient</div>
                        <div class="sign-line"></div>
                        <div>Name, Signature & Company Stamp</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    {{-- ======================================================= --}}
    {{-- PAGE 2: KEY ACCOUNT SALES ORDER (KASO)                    --}}
    {{-- ======================================================= --}}
    <div class="page-break"></div>
    <div class="kaso-wrapper">
        
        {{-- KASO Header --}}
        <div class="kaso-header-text">
            <div class="kaso-company">PT.SINAR MEADOW INTERNATIONAL INDONESIA</div>
            <div class="kaso-address">Jl.Pulo Ayang I/6, JIEP, Jakarta 13260. Telp : 4602981-5</div>
        </div>
        <div class="kaso-line-thick"></div>
        <div class="kaso-line-thin"></div>

        {{-- Title & Document Control --}}
        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <td width="70%" align="center">
                    <h2 class="kaso-title">KEY ACCOUNT SALES ORDER</h2>
                </td>
                <td width="30%" align="right" valign="top">
                    <table style="border-collapse: collapse; font-size: 10px; width: 100%; max-width: 140px;">
                        <tr><td style="border: 1px solid #000; padding: 3px;">No.F/C.1.3.02</td></tr>
                        <tr><td style="border: 1px solid #000; padding: 3px;">Revision : 1</td></tr>
                        <tr><td style="border: 1px solid #000; padding: 3px;">Date : 28 April 03</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- KASO Information Fields --}}
        <table class="kaso-info-table">
            <tr>
                <td width="12%">To</td>
                <td width="2%">:</td>
                <td width="36%" class="kaso-uline">Sales Admin</td>
                <td width="20%" align="right" style="padding-right: 5px;">Sales Order No. :</td>
                <td width="30%" class="kaso-uline">LO-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td>Attention</td>
                <td>:</td>
                <td class="kaso-uline">{{ $order->attention ?? '-' }}</td>
                <td align="right" style="padding-right: 5px;">Sales Order Date :</td>
                <td class="kaso-uline">{{ $order->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>From</td>
                <td>:</td>
                <td class="kaso-uline">Key Account Sales</td>
                <td align="right" style="padding-right: 5px;">Cust. Order (PO) No. :</td>
                <td class="kaso-uline">{{ $order->no_po ?? '-' }}</td>
            </tr>
            <tr>
                <td>Name of PIC</td>
                <td>:</td>
                <td class="kaso-uline">{{ $order->customerShipTo->user->name ?? '-' }}</td>
                <td align="right" style="padding-right: 5px;">Date of PO :</td>
                <td class="kaso-uline">{{ $order->date_of_po ? \Carbon\Carbon::parse($order->date_of_po)->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Ship to</td>
                <td>:</td>
                <td colspan="3" class="kaso-uline" style="font-weight: bold;">{{ $order->customerShipTo->ship_to_name }}</td>
            </tr>
            <tr>
                <td valign="top">Address</td>
                <td valign="top">:</td>
                <td colspan="3" class="kaso-uline">
                    {{ $order->customerShipTo->ship_to_address_1 }} 
                    {{ $order->customerShipTo->ship_to_address_2 ? ', ' . $order->customerShipTo->ship_to_address_2 : '' }},
                    {{ $order->customerShipTo->ship_to_city }}
                </td>
            </tr>
        </table>

        {{-- KASO Items Table --}}
        <table class="kaso-item-table">
            <thead>
                <tr>
                    <th width="6%">No.</th>
                    <th width="16%">Product Code</th>
                    <th width="38%">Description (Product Name)</th>
                    <th width="15%">Pack Size</th>
                    <th width="10%">Quantity</th>
                    <th width="15%">Delivery Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td align="center">{{ $index + 1 }}</td>
                        <td align="center">{{ $item->order_item_code }}</td>
                        <td>{{ $item->order_item_name }}</td>
                        <td align="center">{{ $item->pack_size ?? '-' }}</td>
                        <td align="center">{{ $item->order_quantity }}</td>
                        <td align="center">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</body>

</html>