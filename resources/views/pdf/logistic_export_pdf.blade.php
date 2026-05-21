<!DOCTYPE html>
<html>
<head>
    <title>Report Logistic Order</title>
    <style>
        body { font-family: sans-serif; font-size: 8.5pt; color: #333; margin: 0; }
        .top-info { font-size: 8pt; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { padding: 6px 4px; border: 1px solid #ddd; text-align: center; font-size: 7.5pt; background-color: #1f6b3c; color: white; }
        td { padding: 5px 4px; border: 1px solid #ddd; word-wrap: break-word; vertical-align: middle; font-size: 7.5pt; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .nowrap { white-space: nowrap; }
        .signature-wrapper { margin-top: 40px; width: 100%; }
        .signature-box { width: 32%; display: inline-block; text-align: center; }
    </style>
</head>
<body>
    <div class="top-info">
        Tanggal: {{ now()->format('d/m/Y H:i:s') }}<br>
        Dibuat Oleh: {{ Auth::user()->name }}
    </div>

    <h3 style="text-align: center; text-transform: uppercase; margin-bottom: 5px;">Report Logistic Order</h3>
    <p style="text-align: center; margin-top: 0; font-style: italic; font-size: 9pt;">
        {{ request('date_from') ? 'Periode: '.request('date_from').' - '.request('date_to') : 'Semua Periode' }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 15%;">DN NO</th>
                <th style="width: 10%;">Delivery Date</th>
                <th style="width: 15%;">Distributor</th>
                <th style="width: 15%;">Customer</th>
                <th style="width: 13%;">Item Name</th>
                <th style="width: 9%;">Price Item</th>
                <th style="width: 4%;">Qty</th>
                <th style="width: 10%;">Total Claim</th>
                <th style="width: 10%;">Sales Value</th>
                <th style="width: 6%;">Ratio</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalClaim = 0; $totalSales = 0;
            @endphp
            @foreach($items as $index => $item)
                @php
                    $lo = $item->logisticOrder;
                    $qty = (float) $item->order_quantity;
                    $priceList = (float) $item->price_list;
                    $amount = (float) $item->order_amount;
                    $salesValue = $priceList * $qty;
                    $ratio = $salesValue > 0 ? ($amount / $salesValue) * 100 : 0;
                    $priceItem = \App\Models\Customer\DistributorCustomer::where('distributor_id', $lo->distributor_id)
                                ->where('customer_id', $lo->customer_id)
                                ->first()->logistic_fee ?? 0;

                    $totalClaim += $amount;
                    $totalSales += $salesValue;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $lo->note->delivery_order_no ?? '-' }}</td>
                    <td class="text-center">{{ $lo->delivery_date ? \Carbon\Carbon::parse($lo->delivery_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $lo->distributor->name ?? '-' }}</td>
                    <td>{{ $lo->customer->name ?? '-' }}</td>
                    <td>{{ $item->order_item_name }}</td>
                    <td class="text-right">Rp {{ number_format($priceItem, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($salesValue, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($ratio, 2, '.', '') }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #e2e8f0; color: #0f172a;">
                <td colspan="8" class="text-right">GRAND TOTAL :</td>
                <td class="text-right">Rp {{ number_format($totalClaim, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
                <td class="text-center">
                    {{ number_format($totalSales > 0 ? ($totalClaim / $totalSales) * 100 : 0, 2, '.', '') }}%
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-wrapper">
        <div class="signature-box"><p>Dibuat oleh,</p><br><br><br><p class="fw-bold" style="text-decoration: underline;">{{ Auth::user()->name }}</p></div>
        <div class="signature-box"><p>Diketahui oleh,</p><br><br><br><p class="fw-bold" style="text-decoration: underline;">Rofika</p></div>
        <div class="signature-box"><p>Disetujui oleh,</p><br><br><br><p class="fw-bold" style="text-decoration: underline;">Ronal Katili</p></div>
    </div>
</body>
</html>
