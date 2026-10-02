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

        .dn-table td:nth-child(1),
        .dn-table th:nth-child(1) {
            width: 6%;
        }

        .dn-table td:nth-child(2),
        .dn-table th:nth-child(2) {
            width: 18%;
        }

        .dn-table td:nth-child(3),
        .dn-table th:nth-child(3) {
            width: 61%;
        }

        .dn-table td:nth-child(4),
        .dn-table th:nth-child(4) {
            width: 15%;
        }

        .kaso-table td:nth-child(1),
        .kaso-table th:nth-child(1) {
            width: 5%;
        }

        .kaso-table td:nth-child(2),
        .kaso-table th:nth-child(2) {
            width: 15%;
        }

        .kaso-table td:nth-child(3),
        .kaso-table th:nth-child(3) {
            width: 35%;
        }

        .kaso-table td:nth-child(4),
        .kaso-table th:nth-child(4) {
            width: 15%;
        }

        .kaso-table td:nth-child(5),
        .kaso-table th:nth-child(5) {
            width: 15%;
        }

        .kaso-table td:nth-child(6),
        .kaso-table th:nth-child(6) {
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

        .kaso-doc-control {
            border-collapse: collapse;
            font-size: 9px;
            width: 100%;
            max-width: 130px;
            float: right;
            margin-top: 5px;
        }

        .kaso-doc-control td {
            border: 1px solid #cbd5e1;
            padding: 3px 5px;
            color: #4a5568;
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
                                    <td>{{ \Carbon\Carbon::parse($order->delivery_date)->locale('id')->translatedFormat('d F Y') }}</td>
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

            <table class="item-table dn-table">
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
    <div class="kaso-wrapper" style="padding: 20px 0;">

        {{-- KASO Header --}}
        <table width="100%" style="margin-bottom: 10px;">
            <tr>
                <td width="15%" align="right" valign="middle" style="padding-right: 5px;">
                    <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" alt="Logo" style="width: 75px; border-radius: 5px;">
                </td>
                <td width="70%" align="center" valign="middle">
                    <div style="font-family: 'Times New Roman', Times, serif; font-size: 18px; font-weight: 800; color: #a68831; letter-spacing: 0.5px;">PT. SINAR MEADOW INTERNATIONAL INDONESIA</div>
                    <div style="font-size: 10px; color: #718096; margin-top: 4px;">Jl. Pulo Ayang I No.6, Kawasan Industri Pulogadung Jatinegara, Jakarta Timur, 13260 - Indonesia</div>
                </td>
                <td width="15%"></td>
            </tr>
        </table>

        <div style="border-top: 2px solid #a68831; margin-bottom: 2px;"></div>
        <div style="border-top: 1px solid #a68831; margin-bottom: 20px;"></div>

        {{-- Title & Document Control --}}
        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <td width="70%" align="left">
                    <h2 style="font-family: 'Times New Roman', Times, serif; font-size: 22px; font-weight: 800; color: #2d3748; margin: 0; letter-spacing: 1px;">KEY ACCOUNT SALES ORDER</h2>
                </td>
                <td align="right" valign="top" style="width: 140px;">
                    <table style="border-collapse: collapse; font-size: 9px; width: 100%; color: #000;">
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px 5px; font-weight: bold; width: 35%;">No.</td>
                            <td style="border: 1px solid #000; padding: 3px 5px;">F/C.1.3.02</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px 5px; font-weight: bold;">Revision</td>
                            <td style="border: 1px solid #000; padding: 3px 5px;">1</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px 5px; font-weight: bold;">Date</td>
                            <td style="border: 1px solid #000; padding: 3px 5px;">12 Januari 2024</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- KASO Information Fields --}}
        <table style="width: 100%; font-size: 11px; margin-bottom: 20px; border-collapse: collapse; color: #2d3748;">
            <tr>
                <td width="15%" style="padding: 2px;">From</td>
                <td width="2%" style="padding: 2px;">:</td>
                <td width="33%" style="padding: 2px;">Key Account Sales</td>
                <td width="25%" align="right" style="padding: 2px 15px 2px 5px;">Cust. Order (PO) No. :</td>
                <td width="25%" style="padding: 2px;">{{ $order->no_po ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px;">Name of PIC</td>
                <td style="padding: 2px;">:</td>
                <td style="padding: 2px;">{{ $order->customerShipTo->user->name ?? '-' }}</td>
                <td align="right" style="padding: 2px 15px 2px 5px;">Date of PO :</td>
                <td style="padding: 2px;">{{ $order->date_of_po ? \Carbon\Carbon::parse($order->date_of_po)->locale('id')->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px;">Ship to</td>
                <td style="padding: 2px;">:</td>
                <td colspan="3" style="padding: 2px;">{{ $order->customerShipTo->ship_to_name }}</td>
            </tr>
            <tr>
                <td valign="top" style="padding: 2px;">Address</td>
                <td valign="top" style="padding: 2px;">:</td>
                <td colspan="3" style="padding: 2px; line-height: 1.4;">
                    <div style="word-wrap: break-word; white-space: normal; max-width: 50%;">
                        {{ $order->customerShipTo->ship_to_address_1 }}
                        {{ $order->customerShipTo->ship_to_address_2 ? ', ' . $order->customerShipTo->ship_to_address_2 : '' }},
                        <span>{{ $order->customerShipTo->ship_to_city }}</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- KASO Items Table --}}
        <table class="item-table kaso-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Product Code</th>
                    <th>Description (Product Name)</th>
                    <!-- <th>Pack Size</th> -->
                    <th>Quantity</th>
                    <th>Delivery Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->order_item_code }}</td>
                    <td>{{ $item->order_item_name }}</td>
                    <!-- <td class="text-center">{{ $item->pack_size ?? '-' }}</td> -->
                    <td class="text-center">{{ $item->order_quantity }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($order->delivery_date)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</body>

</html>