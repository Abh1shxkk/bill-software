<!DOCTYPE html>
<html>
<head>
    <title>Purchase Book - Item Details</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 9px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #1565c0; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; }
        th { background: #1565c0; color: white; font-weight: bold; text-align: left; font-size: 8px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e3f2fd; font-weight: bold; }
        .summary { margin-bottom: 10px; }
        .summary-item { display: inline-block; margin-right: 12px; padding: 3px 6px; background: #f0f0f0; border-radius: 3px; font-size: 8px; }
        @media print { body { margin: 0; } @page { margin: 5mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Purchase Book - Item Details</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Type: {{ $purchaseTransfer == 'P' ? 'Purchase' : ($purchaseTransfer == 'T' ? 'Transfer' : 'Both') }} | 
           Tagged: {{ $taggedPartiesOnly ?? 'N' }} | Replacement: {{ $replacementReceived ?? 'N' }}</p>
    </div>

    <div class="summary">
        <span class="summary-item">Items: {{ $totals['count'] ?? 0 }}</span>
        <span class="summary-item">Qty: {{ number_format($totals['qty'] ?? 0, 2) }}</span>
        <span class="summary-item">Free: {{ number_format($totals['free_qty'] ?? 0, 2) }}</span>
        <span class="summary-item">Amount: ₹{{ number_format($totals['amount'] ?? 0, 2) }}</span>
        <span class="summary-item">Tax: ₹{{ number_format($totals['tax'] ?? 0, 2) }}</span>
        <span class="summary-item">Net: ₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">Sr.</th>
                <th style="width: 60px;">Date</th>
                <th style="width: 60px;">Bill No</th>
                <th>Supplier</th>
                <th>Item Name</th>
                <th style="width: 40px;">Pack</th>
                <th style="width: 50px;">Batch</th>
                <th style="width: 50px;">Expiry</th>
                <th class="text-right" style="width: 50px;">MRP</th>
                <th class="text-right" style="width: 50px;">Rate</th>
                <th class="text-right" style="width: 40px;">Qty</th>
                <th class="text-right" style="width: 35px;">Free</th>
                <th class="text-right" style="width: 60px;">Amount</th>
                <th class="text-right" style="width: 50px;">Tax</th>
                <th class="text-right" style="width: 65px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items ?? [] as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->transaction->bill_date ? $item->transaction->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->transaction->bill_no ?? '-' }}</td>
                <td>{{ $item->transaction->supplier->name ?? 'N/A' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->packing ?? '-' }}</td>
                <td>{{ $item->batch_no ?? '-' }}</td>
                <td>{{ $item->expiry_date ? $item->expiry_date->format('M-Y') : '-' }}</td>
                <td class="text-right">{{ number_format($item->mrp ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->free_qty ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="15" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="10">Grand Total</td>
                <td class="text-right">{{ number_format($totals['qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['free_qty'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
