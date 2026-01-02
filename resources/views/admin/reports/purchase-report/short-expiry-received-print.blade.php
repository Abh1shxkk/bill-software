<!DOCTYPE html>
<html>
<head>
    <title>Short Expiry Received</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #f4a460; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #f4a460; color: white; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #ffa07a; font-weight: bold; }
        .danger-row { background: #f8d7da; }
        .warning-row { background: #fff3cd; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #17a2b8; color: white; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 15px; padding: 4px 8px; background: #f0f0f0; border-radius: 3px; font-size: 9px; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>SHORT EXPIRY RECEIVED</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Items expiring within {{ $noOfMonths ?? 6 }} months | Date Type: {{ ($dateType ?? 'B') == 'B' ? 'Bill Date' : 'Receive Date' }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Total Items: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Total Qty: {{ number_format($totals['qty'] ?? 0, 2) }}</span>
        <span class="summary-item">Total Amount: ₹{{ number_format($totals['amount'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">Sr.</th>
                <th style="width: 70px;">Recv Date</th>
                <th style="width: 70px;">Bill No</th>
                <th>Supplier</th>
                <th>Item Name</th>
                <th style="width: 60px;">Batch</th>
                <th style="width: 60px;">Expiry</th>
                <th class="text-center" style="width: 60px;">Days Left</th>
                <th class="text-right" style="width: 55px;">Qty</th>
                <th class="text-right" style="width: 60px;">Rate</th>
                <th class="text-right" style="width: 70px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shortExpiry ?? [] as $index => $item)
            <tr class="{{ $item->days_left <= 30 ? 'danger-row' : ($item->days_left <= 90 ? 'warning-row' : '') }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->received_date ? $item->received_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->bill_no }}</td>
                <td>{{ $item->supplier_name }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->batch_no ?? '-' }}</td>
                <td>{{ $item->expiry_date ? $item->expiry_date->format('M-Y') : '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $item->days_left <= 30 ? 'badge-danger' : ($item->days_left <= 90 ? 'badge-warning' : 'badge-info') }}">
                        {{ $item->days_left }} days
                    </span>
                </td>
                <td class="text-right">{{ number_format($item->qty, 2) }}</td>
                <td class="text-right">{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No short expiry items found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8">Grand Total</td>
                <td class="text-right">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td></td>
                <td class="text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
