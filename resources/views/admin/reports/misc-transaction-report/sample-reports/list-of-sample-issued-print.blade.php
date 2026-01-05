<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Sample Issued - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        @media print { body { margin: 0.5cm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>List of Sample Issued</h2>
        <p>From: {{ request('from_date') ? date('d-M-Y', strtotime(request('from_date'))) : '' }} To: {{ request('to_date') ? date('d-M-Y', strtotime(request('to_date'))) : '' }}</p>
    </div>
    <table>
        <thead><tr><th class="text-center">S.No</th><th>Date</th><th>Voucher No</th><th>Issued To</th><th>Item Name</th><th class="text-end">Qty</th><th>Remarks</th></tr></thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                <td>{{ $item->voucher_no ?? '' }}</td>
                <td>{{ $item->issued_to ?? '' }}</td>
                <td>{{ $item->item_name ?? '' }}</td>
                <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td>{{ $item->remarks ?? '' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
