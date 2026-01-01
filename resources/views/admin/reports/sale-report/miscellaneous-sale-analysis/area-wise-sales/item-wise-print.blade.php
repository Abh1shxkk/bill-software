<!DOCTYPE html>
<html>
<head>
    <title>Area Wise Sales - Item Wise Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #8B0000; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        .area-header { background: #d0d0d0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Area Wise Sales - Item Wise Report</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Item</th>
                <th>Packing</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Free Qty</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; $currentArea = null; @endphp
            @forelse($data ?? [] as $row)
                @if($currentArea !== $row['area_name'])
                    @php $currentArea = $row['area_name']; @endphp
                    <tr class="area-header">
                        <td colspan="6">{{ $row['area_name'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="text-center">{{ ++$sno }}</td>
                    <td>{{ $row['item_name'] ?? '-' }}</td>
                    <td>{{ $row['packing'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($row['qty'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($row['free_qty'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($row['amount'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="text-right">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['free_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
