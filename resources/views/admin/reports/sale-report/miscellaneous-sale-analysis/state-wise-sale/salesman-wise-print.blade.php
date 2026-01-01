<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>State - Sales Man Wise Sales Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .container { padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #8B0000; padding-bottom: 5px; }
        .header h2 { color: #8B0000; font-style: italic; margin-bottom: 3px; }
        .header p { font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .state-header { background-color: #d0d8ff; font-weight: bold; }
        @media print { body { font-size: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>State - Sales Man Wise Sales Report</h2>
            <p>From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        </div>
        <table>
            <thead>
                <tr><th>State</th><th>Salesman</th><th class="text-center">Bills</th><th class="text-right">Gross Amt</th><th class="text-right">Discount</th><th class="text-right">Net Amt</th></tr>
            </thead>
            <tbody>
                @php $currentState = ''; @endphp
                @foreach($data as $row)
                    @if($currentState != $row['state_name'])
                        @php $currentState = $row['state_name']; @endphp
                        <tr class="state-header"><td colspan="6">{{ $currentState }}</td></tr>
                    @endif
                    <tr>
                        <td></td>
                        <td>{{ $row['salesman_name'] }}</td>
                        <td class="text-center">{{ $row['bill_count'] }}</td>
                        <td class="text-right">{{ number_format($row['gross_amount'], 2) }}</td>
                        <td class="text-right">{{ number_format($row['discount'], 2) }}</td>
                        <td class="text-right">{{ number_format($row['net_amount'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" class="text-right">Grand Total:</td>
                    <td class="text-center">{{ $totals['bill_count'] }}</td>
                    <td class="text-right">{{ number_format($totals['gross_amount'], 2) }}</td>
                    <td class="text-right">{{ number_format($totals['discount'], 2) }}</td>
                    <td class="text-right">{{ number_format($totals['net_amount'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
