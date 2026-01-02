<!DOCTYPE html>
<html>
<head>
    <title>Central Purchase with Local Value</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #1565c0; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #1565c0; color: white; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e3f2fd; font-weight: bold; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 12px; padding: 3px 6px; background: #f0f0f0; border-radius: 3px; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>CENTRAL PURCHASE WITH LOCAL VALUE</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Items: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Qty: {{ number_format($totals['qty'] ?? 0, 2) }}</span>
        <span class="summary-item">Central: ₹{{ number_format($totals['central_value'] ?? 0, 2) }}</span>
        <span class="summary-item">Local: ₹{{ number_format($totals['local_value'] ?? 0, 2) }}</span>
        <span class="summary-item">Diff: ₹{{ number_format($totals['difference'] ?? 0, 2) }}</span>
        <span class="summary-item">Savings: {{ number_format($totals['savings_percent'] ?? 0, 2) }}%</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr.</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">Bill No</th>
                <th>Supplier</th>
                <th>Item Name</th>
                <th class="text-right" style="width: 55px;">Qty</th>
                <th class="text-right" style="width: 70px;">Central Rate</th>
                <th class="text-right" style="width: 70px;">Local Rate</th>
                <th class="text-right" style="width: 80px;">Central Value</th>
                <th class="text-right" style="width: 80px;">Local Value</th>
                <th class="text-right" style="width: 70px;">Difference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items ?? [] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->bill_no ?? '-' }}</td>
                <td>{{ $item->supplier_name ?? 'N/A' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->central_rate ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->local_rate ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->central_value ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->local_value ?? 0, 2) }}</td>
                <td class="text-right {{ ($item->difference ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($item->difference ?? 0, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5">Grand Total</td>
                <td class="text-right">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td></td>
                <td></td>
                <td class="text-right">{{ number_format($totals['central_value'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['local_value'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['difference'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
