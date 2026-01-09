<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Trans - 1 Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 14px; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        .filters { margin-bottom: 10px; font-size: 9px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #cc6666; color: white; font-weight: bold; font-size: 9px; }
        td { font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; border-top: 1px solid #000; padding-top: 5px; }
        .total-row { background-color: #f0f0f0; font-weight: bold; }
        @media print {
            body { padding: 5px; }
            @page { size: landscape; margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>STOCK TRANS - 1 (GST TRANS)</h2>
        <p>As On: {{ \Carbon\Carbon::parse($asOnDate ?? now())->format('d-M-Y') }} | 
           Sale Month: {{ date('F', mktime(0, 0, 0, $saleMonth ?? date('n'), 1)) }} {{ $year ?? date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">HSN Code</th>
                <th style="width: 180px;">Item Name</th>
                <th style="width: 50px;">Pack</th>
                <th class="text-end" style="width: 60px;">Qty</th>
                <th class="text-end" style="width: 70px;">Cost Rate</th>
                <th class="text-end" style="width: 80px;">Value</th>
                <th class="text-end" style="width: 60px;">CGST</th>
                <th class="text-end" style="width: 60px;">SGST</th>
                <th class="text-end" style="width: 60px;">IGST</th>
                <th class="text-end" style="width: 60px;">Sale Qty</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalValue = 0;
                $totalCgst = 0;
                $totalSgst = 0;
                $totalIgst = 0;
                $totalSaleQty = 0;
            @endphp
            @forelse($reportData as $item)
            @php
                $totalQty += $item['qty'] ?? 0;
                $totalValue += $item['value'] ?? 0;
                $totalCgst += $item['cgst'] ?? 0;
                $totalSgst += $item['sgst'] ?? 0;
                $totalIgst += $item['igst'] ?? 0;
                $totalSaleQty += $item['sale_qty'] ?? 0;
            @endphp
            <tr>
                <td>{{ $item['hsn_code'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['pack'] }}</td>
                <td class="text-end">{{ number_format($item['qty'], 0) }}</td>
                <td class="text-end">{{ number_format($item['cost_rate'], 2) }}</td>
                <td class="text-end">{{ number_format($item['value'], 2) }}</td>
                <td class="text-end">{{ number_format($item['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($item['sale_qty'], 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No data available</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-end"><strong>{{ number_format($totalQty, 0) }}</strong></td>
                <td class="text-end">-</td>
                <td class="text-end"><strong>{{ number_format($totalValue, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totalCgst, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totalSgst, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totalIgst, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totalSaleQty, 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p><strong>Total Stock Value:</strong> ₹{{ number_format($totalStockValue ?? $totalValue, 2) }}</p>
        <p style="font-size: 8px; margin-top: 5px;">Printed on: {{ now()->format('d-M-Y h:i A') }}</p>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
