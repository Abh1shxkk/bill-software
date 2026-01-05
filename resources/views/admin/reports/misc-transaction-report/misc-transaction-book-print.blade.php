<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misc Transaction Book - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 8px; background-color: #ffc4d0; }
        .header h2 { font-family: 'Times New Roman', serif; font-style: italic; margin: 0; font-size: 16px; }
        .header p { margin: 3px 0 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #333; color: #fff; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>Misc Transaction Book</h2>
        <p>From: {{ request('from_date') ? date('d-M-Y', strtotime(request('from_date'))) : '' }} To: {{ request('to_date') ? date('d-M-Y', strtotime(request('to_date'))) : '' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 100px;">Voucher No</th>
                <th style="width: 80px;">Type</th>
                <th>Particulars</th>
                <th class="text-end" style="width: 70px;">Qty</th>
                <th class="text-end" style="width: 90px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->date ? date('d-M-y', strtotime($item->date)) : '' }}</td>
                <td>{{ $item->voucher_no ?? '' }}</td>
                <td>{{ $item->type ?? '' }}</td>
                <td>{{ $item->particulars ?? '' }}</td>
                <td class="text-end">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item->amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
