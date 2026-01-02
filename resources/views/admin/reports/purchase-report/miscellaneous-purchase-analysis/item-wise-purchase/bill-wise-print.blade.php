<!DOCTYPE html>
<html>
<head>
    <title>Item Wise Purchase Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #721c24; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #343a40; color: #fff; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; font-size: 9px; } @page { margin: 8mm; size: landscape; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">Print</button>
    <div class="header">
        <h3>Item Wise Purchase</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">S.No</th>
                <th>Bill Date</th>
                <th>Bill No</th>
                <th>Supplier</th>
                <th>Item Name</th>
                <th>Batch</th>
                <th>Expiry</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Free</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 0; $grandQty = 0; $grandFree = 0; $grandAmount = 0; @endphp
            @forelse($items ?? [] as $item)
            @php $grandQty += $item->qty; $grandFree += $item->free_qty; $grandAmount += $item->amount; @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $item->transaction->bill_date ? $item->transaction->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->transaction->bill_no ?? '-' }}</td>
                <td>{{ $item->transaction->supplier->name ?? '-' }}</td>
                <td>{{ $item->item_name ?? 'N/A' }}</td>
                <td>{{ $item->batch_no ?? '-' }}</td>
                <td>{{ $item->expiry_date ? $item->expiry_date->format('m/y') : '-' }}</td>
                <td class="text-right">{{ number_format($item->qty, 2) }}</td>
                <td class="text-right">{{ number_format($item->free_qty, 2) }}</td>
                <td class="text-right">{{ number_format($item->pur_rate, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($grandQty, 2) }}</td>
                <td class="text-right">{{ number_format($grandFree, 2) }}</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($grandAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
