<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sale Return Replacement Report - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 8px; background-color: #ffc4d0; }
        .header h2 { font-family: 'Times New Roman', serif; font-style: italic; margin: 0; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; font-size: 10px; }
        th { background-color: #333; color: #fff; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header"><h2>Sale Return Replacement Report</h2></div>
    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Customer</th>
                <th>Item Name</th>
                <th class="text-end">Return Qty</th>
                <th class="text-end">Replace Qty</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $sno = 1; @endphp
            @forelse($reportData ?? [] as $transaction)
                @foreach($transaction->items as $item)
                <tr>
                    <td class="text-center">{{ $sno++ }}</td>
                    <td>{{ $transaction->trn_date->format('d-M-y') }}</td>
                    <td>{{ $transaction->trn_no }}</td>
                    <td>{{ $transaction->customer->name ?? $transaction->customer_name }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-end">{{ number_format($item->qty, 2) }}</td>
                    <td class="text-end">{{ number_format($item->free_qty ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            @empty
            <tr><td colspan="8" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
