<!DOCTYPE html>
<html>
<head>
    <title>CL/SL Date Wise Ledger Summary - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { 
            body { margin: 0; } 
            .header { background-color: #ffc4d0 !important; -webkit-print-color-adjust: exact; } 
            th, .totals { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>{{ ($request->type ?? 'C') == 'C' ? 'CUSTOMER' : 'SUPPLIER' }} - DATE WISE LEDGER SUMMARY</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">S.No</th>
                <th style="width: 100px;">Date</th>
                <th class="text-end" style="width: 120px;">Opening</th>
                <th class="text-end" style="width: 120px;">Debit</th>
                <th class="text-end" style="width: 120px;">Credit</th>
                <th class="text-end" style="width: 120px;">Closing</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebit = 0; $totalCredit = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalDebit += $row['debit']; 
                $totalCredit += $row['credit'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['date'] }}</td>
                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="3" class="text-end">Totals:</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                <td class="text-end">{{ number_format(count($reportData ?? []) > 0 ? $reportData[count($reportData) - 1]['closing'] : 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top: 10px; font-size: 10px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
