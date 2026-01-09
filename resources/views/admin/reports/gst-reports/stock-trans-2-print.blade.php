<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Trans - 2 Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 14px; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        .sub-header { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 9px; font-weight: bold; }
        .sub-header div { flex: 1; text-align: center; border: 1px solid #999; padding: 3px; background-color: #c4a060; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #c4a060; font-weight: bold; font-size: 9px; }
        td { font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 10px; border-top: 1px solid #000; padding-top: 5px; }
        .total-row { background-color: #f0f0f0; font-weight: bold; }
        .totals-section { margin-top: 10px; }
        .totals-section table { width: auto; }
        .totals-section td { padding: 2px 10px; }
        @media print {
            body { padding: 5px; }
            @page { size: landscape; margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>STOCK TRANS - 2</h2>
        <p>Month: {{ date('F', mktime(0, 0, 0, $saleMonth ?? date('n'), 1)) }} {{ $year ?? date('Y') }} | HSN: {{ $hsnType ?? 'Full' }}</p>
    </div>

    <div class="sub-header">
        <div>Opening Stock For The TAX Period</div>
        <div>Outward Supply Made</div>
        <div>Balance Closing</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">HSN</th>
                <th style="width: 150px;">Description</th>
                <th class="text-end" style="width: 60px;">Qty.</th>
                <th class="text-end" style="width: 60px;">Qty.</th>
                <th class="text-end" style="width: 80px;">Value</th>
                <th class="text-end" style="width: 60px;">CGST</th>
                <th class="text-end" style="width: 60px;">SGST</th>
                <th class="text-end" style="width: 60px;">IGST</th>
                <th class="text-end" style="width: 70px;">ITC allowed</th>
                <th class="text-end" style="width: 60px;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $item)
            <tr>
                <td>{{ $item['hsn_code'] }}</td>
                <td>{{ Str::limit($item['item_name'], 30) }}</td>
                <td class="text-end">{{ number_format($item['opening_qty'] ?? 0, 0) }}</td>
                <td class="text-end">{{ number_format($item['qty'] ?? 0, 0) }}</td>
                <td class="text-end">{{ number_format($item['value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item['cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item['sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item['igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item['itc_allowed'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item['closing_qty'] ?? 0, 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No data available</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-end"><strong>TOTAL:</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['opening_qty'] ?? 0, 0) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['qty'] ?? 0, 0) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['value'] ?? 0, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['cgst'] ?? 0, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['sgst'] ?? 0, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['igst'] ?? 0, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['itc_allowed'] ?? 0, 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($totals['closing_qty'] ?? 0, 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="totals-section">
        <table>
            <tr>
                <td><strong>Total Qty:</strong></td>
                <td>{{ number_format($totals['opening_qty'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ number_format($totals['closing_qty'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Value:</strong></td>
                <td></td>
                <td>{{ number_format($totals['value'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['value2'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['cgst'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['sgst'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['igst'] ?? 0, 2) }}</td>
                <td>{{ number_format($totals['itc_allowed'] ?? 0, 2) }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p style="font-size: 8px;">Printed on: {{ now()->format('d-M-Y h:i A') }}</p>
    </div>

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
