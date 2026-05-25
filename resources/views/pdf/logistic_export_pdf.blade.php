<!DOCTYPE html>
<html>
<head>
    <title>Report Logistic Order</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 8pt; color: #000000; margin: 0; }
        table.main-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 10px; }
        th { padding: 8px 4px; border: 1px solid #000000; text-align: center; font-size: 7pt; color: #000000; font-weight: bold; }
        td { padding: 6px 4px; border: 1px solid #000000; word-wrap: break-word; vertical-align: middle; font-size: 7pt; color: #000000; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .nowrap { white-space: nowrap; }
        .signature-wrapper { margin-top: 50px; width: 100%; }
        .signature-box { width: 32%; display: inline-block; text-align: center; }
    </style>
</head>
<body>

    <table style="width: 100%; border: none; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <td style="border: none; padding: 0; vertical-align: middle; text-align: left;">
                <img src="{{ public_path('assets/images/logo/sinarmeadow.png') }}" style="height: 35px; margin-right: 6px; vertical-align: middle;">
                <span style="font-family: 'Arial Black', sans-serif; font-size: 15pt; font-weight: 900; color: #000000; letter-spacing: 1px; vertical-align: middle;">SINAR MEADOW</span>
            </td>
            <td style="border: none; padding: 0; vertical-align: middle; text-align: right; font-size: 8pt; color: #000000; line-height: 1.4;">
                <strong>Tanggal:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
                <strong>Dibuat Oleh:</strong> {{ Auth::user()->name }}
            </td>
        </tr>
    </table>

    <h2 style="text-align: center; text-transform: uppercase; margin-bottom: 5px; color: #0f172a; letter-spacing: 0.5px;">Report Logistic Order</h2>
    <p style="text-align: center; margin-top: 0; font-style: italic; font-size: 8.5pt; color: #212326; margin-bottom: 20px;">
        {{ request('date_from') ? 'Periode: ' . \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') : 'Semua Periode' }}
    </p>
    <p style="text-align: center; margin-top: 0; font-weight: bold; font-size: 9pt; color: #000000; margin-bottom: 20px; letter-spacing: 0.5px;">
        AP NUMBER: {{ strtoupper($apNumber) }}
    </p>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 12%;">DN NO</th>
                <th style="width: 11%;">NO PO</th> 
                <th style="width: 8%;">Delivery Date</th>
                <th style="width: 11%;">Distributor</th>
                <th style="width: 13%;">Customer</th>
                <th style="width: 11%;">Item Name</th>
                <th style="width: 8%;">Price Item</th>
                <th style="width: 3%;">Qty</th>
                <th style="width: 8%;">Total Claim</th>
                <th style="width: 9%;">Sales Value</th>
                <th style="width: 5%;">Ratio</th>
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
                    <td class="fw-bold" style="color: #0f172a;">{{ $lo->note->delivery_order_no ?? '-' }}</td>
                    <td class="fw-bold" style="color: #475569;">{{ $lo->no_po ?? '-' }}</td>
                    <td class="text-center">{{ $lo->delivery_date ? \Carbon\Carbon::parse($lo->delivery_date)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $lo->distributor->name ?? '-' }}</td>
                    <td>{{ $lo->customer->name ?? '-' }}</td>
                    <td>{{ $item->order_item_name }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($priceItem, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($salesValue, 0, ',', '.') }}</td>
                    <td class="text-center nowrap">{{ number_format($ratio, 2, '.', '') }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="color: #0f172a;">
                <td colspan="9" class="text-right" style="border-top: 1px solid #0f172a; border-bottom: 1px solid #0f172a; padding: 8px 4px;">GRAND TOTAL :</td>
                <td class="text-right nowrap" style="border-top: 1px solid #0f172a; border-bottom: 1px solid #0f172a; padding: 8px 4px;">Rp {{ number_format($totalClaim, 0, ',', '.') }}</td>
                <td class="text-right nowrap" style="border-top: 1px solid #0f172a; border-bottom: 1px solid #0f172a; padding: 8px 4px;">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
                <td class="text-center nowrap" style="border-top: 1px solid #0f172a; border-bottom: 1px solid #0f172a; padding: 8px 4px;">
                    {{ number_format($totalSales > 0 ? ($totalClaim / $totalSales) * 100 : 0, 2, '.', '') }}%
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-wrapper">
        <div class="signature-box"><p>Dibuat oleh,</p><br><br><br><p class="fw-bold">{{ Auth::user()->name }}</p></div>
        <div class="signature-box"><p>Diketahui oleh,</p><br><br><br><p class="fw-bold">Rofika</p></div>
        <div class="signature-box"><p>Disetujui oleh,</p><br><br><br><p class="fw-bold">Ronal Katili</p></div>
    </div>
</body>
</html> 
