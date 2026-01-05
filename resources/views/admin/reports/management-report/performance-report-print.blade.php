<!DOCTYPE html>
<html>
<head>
    <title>Performance Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Performance Report - {{ ($request->report_type ?? 'S') == 'S' ? 'Salesman Wise' : 'Customer Wise' }}</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th>{{ ($request->report_type ?? 'S') == 'S' ? 'Salesman' : 'Customer' }} Name</th>
                <th class="text-end">Sale Amount</th>
                <th class="text-end">Return Amount</th>
                <th class="text-end">Net Amount</th>
                <th class="text-end">Bills</th>
                <th class="text-end">Avg Bill Value</th>
                <th class="text-end">Collection</th>
                <th class="text-end">Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalSale = 0; $totalReturn = 0; $totalNet = 0; 
                $totalBills = 0; $totalCollection = 0; $totalOutstanding = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalSale += $row['sale_amount']; 
                $totalReturn += $row['return_amount'];
                $totalNet += $row['net_amount'];
                $totalBills += $row['bills'];
                $totalCollection += $row['collection'];
                $totalOutstanding += $row['outstanding'];
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['name'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['sale_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['return_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['net_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ $row['bills'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($row['avg_bill_value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['collection'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['outstanding'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="2" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                <td class="text-end">{{ number_format($totalReturn, 2) }}</td>
                <td class="text-end">{{ number_format($totalNet, 2) }}</td>
                <td class="text-end">{{ $totalBills }}</td>
                <td class="text-end">{{ $totalBills > 0 ? number_format($totalNet / $totalBills, 2) : '0.00' }}</td>
                <td class="text-end">{{ number_format($totalCollection, 2) }}</td>
                <td class="text-end">{{ number_format($totalOutstanding, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top: 10px; font-size: 11px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
