<!DOCTYPE html>
<html>
<head>
    <title>Purchase / Return Book Item Wise</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #6f42c1; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #e6e6fa; font-weight: bold; text-align: left; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #d8bfd8; font-weight: bold; }
        .footer { margin-top: 15px; font-weight: bold; color: #6f42c1; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Purchase / Return Book Item Wise</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Local/Central: {{ $localCentral == 'B' ? 'Both' : ($localCentral == 'L' ? 'Local' : 'Central') }} | 
           Group By: {{ $groupBy == 'C' ? 'Company' : ($groupBy == 'I' ? 'Item' : 'Balance Stock') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">COMPANY</th>
                <th>ITEM NAME</th>
                <th style="width: 70px;">PACK</th>
                <th class="text-center" style="width: 80px;">PURCHASE</th>
                @if(($showValue ?? 'Y') == 'Y')
                <th class="text-right" style="width: 90px;">VALUE</th>
                @endif
                <th class="text-center" style="width: 80px;">BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items ?? [] as $item)
            <tr>
                <td>{{ $item->company_name ?? '-' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->packing ?? '-' }}</td>
                <td class="text-center">{{ number_format($item->purchase_qty ?? 0, 0) }}</td>
                @if(($showValue ?? 'Y') == 'Y')
                <td class="text-right">{{ number_format($item->purchase_value ?? 0, 2) }}</td>
                @endif
                <td class="text-center">{{ number_format($item->balance_qty ?? 0, 0) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ ($showValue ?? 'Y') == 'Y' ? 6 : 5 }}" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td class="text-center">{{ number_format($totals['purchase_qty'] ?? 0, 0) }}</td>
                @if(($showValue ?? 'Y') == 'Y')
                <td class="text-right">{{ number_format($totals['purchase_value'] ?? 0, 2) }}</td>
                @endif
                <td class="text-center">{{ number_format($totals['balance'] ?? 0, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        TOTAL PURCHASE VALUE: {{ number_format($totals['purchase_value'] ?? 0, 2) }}
    </div>
</body>
</html>
