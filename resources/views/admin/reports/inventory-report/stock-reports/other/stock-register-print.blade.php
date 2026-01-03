<!DOCTYPE html>
<html>
<head>
    <title>Stock Register - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background-color: #f5f5f5; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <h3>Stock Register</h3>
        <p>From: {{ request('from_date', date('d-m-Y')) }} To: {{ request('to_date', date('d-m-Y')) }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Date</th>
                <th>Particulars</th>
                <th>Voucher</th>
                <th class="text-end">In Qty</th>
                <th class="text-end">Out Qty</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($reportData) && $reportData->count() > 0)
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['date'] ?? '' }}</td>
                    <td>{{ $row['particulars'] ?? '' }}</td>
                    <td>{{ $row['voucher'] ?? '' }}</td>
                    <td class="text-end">{{ number_format($row['in_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['out_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['balance'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="7" class="text-center">No records found</td></tr>
            @endif
        </tbody>
    </table>
</body>
</html>
