<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Delivery Order - {{ $order->note->delivery_order_no }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; letter-spacing: 1px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; color: #555; }

        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { vertical-align: top; padding: 4px 0; }
        .info-label { font-weight: bold; width: 130px; display: inline-block; }

        .item-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .item-table th, .item-table td { border: 1px solid #000; padding: 8px 10px; text-align: left; }
        .item-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }

        .text-center { text-align: center !important; }

        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
        .signature-table td { width: 33.33%; padding: 0 20px; }
        .signature-line { border-bottom: 1px solid #000; height: 70px; margin-bottom: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>DELIVERY ORDER</h1>
        <p>PT SINAR MEADOW INTERNATIONAL INDONESIA</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <div><span class="info-label">DO Number</span>: <b>{{ $order->note->delivery_order_no }}</b></div>
                <div><span class="info-label">LO Number</span>: LO-{{ str_pad($order->logistic_order_no, 4, '0', STR_PAD_LEFT) }}</div>
                <div><span class="info-label">Date</span>: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d F Y') }}</div>
                <div><span class="info-label">Distributor</span>: {{ $order->distributor->name }}</div>
            </td>
            <td width="50%">
                <div><span class="info-label">Customer</span>: {{ $order->customer->name }}</div>
                <div><span class="info-label">Ship To Code</span>: {{ $order->customerShipTo->ship_to_code }}</div>
                <div><span class="info-label">Delivery Address</span>:</div>
                <div style="padding-left: 135px; margin-top: -15px;">
                    {{ $order->customerShipTo->ship_to_name }}<br>
                    {{ $order->customerShipTo->ship_to_address_1 }}<br>
                    @if($order->customerShipTo->ship_to_address_2) {{ $order->customerShipTo->ship_to_address_2 }}<br> @endif
                    {{ $order->customerShipTo->ship_to_city }}
                </div>
            </td>
        </tr>
    </table>

    <table class="item-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Item Code</th>
                <th width="55%">Item Name / Description</th>
                <th width="20%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->order_item_code }}</td>
                <td>{{ $item->order_item_name }}</td>
                <td class="text-center"><b>{{ $item->order_quantity }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div style="margin-bottom: 10px;">Prepared By,</div>
                <div class="signature-line"></div>
                <div>( .................................... )</div>
                <div style="font-size: 11px; margin-top:3px;">Distributor Adm</div>
            </td>
            <td>
                <div style="margin-bottom: 10px;">Delivered By,</div>
                <div class="signature-line"></div>
                <div>( .................................... )</div>
                <div style="font-size: 11px; margin-top:3px;">Driver / Courier</div>
            </td>
            <td>
                <div style="margin-bottom: 10px;">Received By,</div>
                <div class="signature-line"></div>
                <div>( .................................... )</div>
                <div style="font-size: 11px; margin-top:3px;">Customer</div>
            </td>
        </tr>
    </table>

</body>
</html>
