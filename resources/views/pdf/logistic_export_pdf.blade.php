<!DOCTYPE html>
<html>
<head>
    <title>Report Logistic Order</title>
    <style>
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        .top-info { font-size: 8pt; margin-bottom: 15px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background-color: #166534; color: white; padding: 8px; border: 1px solid #ddd; text-align: center; }
        td { padding: 6px; border: 1px solid #ddd; word-wrap: break-word; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .signature-wrapper { margin-top: 50px; width: 100%; }
        .signature-box { width: 32%; display: inline-block; text-align: center; vertical-align: top; }
    </style>
</head>
<body>

    {{-- INFO DI ATAS JUDUL --}}
    <div class="top-info">
        Tanggal Download: {{ now()->format('d/m/Y H:i:s') }}<br>
        Dibuat Oleh: {{ Auth::user()->name }}
    </div>

    <h2 style="text-align: center; margin-bottom: 5px; text-transform: uppercase;">Report Logistic Order</h2>
    <p style="text-align: center; margin-top: 0; font-style: italic;">
        {{ request('date_from') ? 'Period: '.request('date_from').' - '.request('date_to') : 'All Dates' }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>DN NO</th>
                <th>Distributor</th>
                <th>Customer</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Total Claim</th>
                <th>Sales Value</th>
                <th style="width: 50px;">Ratio</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalClaim = 0; 
                $totalSales = 0; 
            @endphp
            @foreach($items as $index => $item)
                @php
                    $qty = $item->order_quantity ?? 0;
                    $priceList = $item->price_list ?? 0;
                    $amount = $item->order_amount ?? 0;
                    $salesValue = $priceList * $qty;
                    $ratio = $salesValue > 0 ? ($amount / $salesValue) * 100 : 0;

                    $totalClaim += $amount;
                    $totalSales += $salesValue;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->logisticOrder->note->delivery_order_no ?? '-' }}</td>
                    <td>{{ $item->logisticOrder->distributor->name ?? '-' }}</td>
                    <td>{{ $item->logisticOrder->customer->name ?? '-' }}</td>
                    <td>{{ $item->order_item_name }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($salesValue, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($ratio, 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f2f2f2;">
                <td colspan="6" class="text-right">GRAND TOTAL:</td>
                <td class="text-right">Rp {{ number_format($totalClaim, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
                <td class="text-center">
                    {{ $totalSales > 0 ? number_format(($totalClaim / $totalSales) * 100, 2) : 0 }}%
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- TANDA TANGAN 3 KOLOM --}}
    <div class="signature-wrapper">
        <div class="signature-box">
            <p>Dibuat oleh,</p>
            <br><br><br><br>
            <p class="fw-bold" style="text-decoration: underline;">{{ Auth::user()->name }}</p>
        </div>
        <div class="signature-box">
            <p>Diketahui oleh,</p>
            <br><br><br><br>
            <p class="fw-bold" style="text-decoration: underline;">Rofika</p>
        </div>
        <div class="signature-box">
            <p>Disetujui oleh,</p>
            <br><br><br><br>
            <p class="fw-bold" style="text-decoration: underline;">Ronal Katili</p>
        </div>
    </div>

</body>
</html>