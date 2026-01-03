<!DOCTYPE html>
<html>
<head>
    <title>Due List Summary - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot th { background-color: #e0e0e0; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>DUE LIST SUMMARY</h2>
        <p>As On: {{ request('as_on_date', date('Y-m-d')) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Code</th>
                <th>Party Name</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBalance = 0; @endphp
            @forelse($reportData as $index => $item)
            @php $totalBalance += $item->balance ?? 0; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->code ?? $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-end">{{ number_format($item->balance ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totalBalance, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }} | Printed on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
</body>
</html>
