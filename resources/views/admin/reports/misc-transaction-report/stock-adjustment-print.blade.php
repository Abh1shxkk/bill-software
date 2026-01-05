<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Adjustment Report - Print</title>
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
    <div class="header">
        <h2>Stock Adjustment Report</h2>
        <p>From: {{ request('from_date') ? date('d-M-Y', strtotime(request('from_date'))) : '' }} To: {{ request('to_date') ? date('d-M-Y', strtotime(request('to_date'))) : '' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">S.No</th>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Item Name</th>
                <th>Batch</th>
                <th class="text-center">Type</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Rate</th>
                <th class="text-end">Amount</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                <td>{{ $item->voucher_no ?? '' }}</td>
                <td>{{ $item->item_name ?? '' }}</td>
                <td>{{ $item->batch_no ?? '' }}</td>
                <td class="text-center">{{ $item->adjustment_type ?? '' }}</td>
                <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item->rate ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
                <td>{{ $item->reason ?? '' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
