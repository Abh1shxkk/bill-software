<!DOCTYPE html>
<html>
<head>
    <title>Ledger Due List Mismatch Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #3399ff; color: #fff; padding: 10px; }
        .header h3 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-danger { color: #dc3545; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } .header { background-color: #3399ff !important; -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Ledger Due List Mismatch Report - {{ ($request->ledger_code ?? 'CL') == 'CL' ? 'Customer' : 'Supplier' }}</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">S.No</th>
                <th style="width: 80px;">CODE</th>
                <th>PARTY NAME</th>
                <th class="text-end" style="width: 130px;">LEDGER AMT.</th>
                <th class="text-end" style="width: 130px;">DUE LIST AMT.</th>
                <th class="text-end" style="width: 100px;">DIFF.</th>
            </tr>
        </thead>
        <tbody>
            @php $totalLedger = 0; $totalDue = 0; $totalDiff = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalLedger += $row['ledger_amount']; 
                $totalDue += $row['due_list_amount'];
                $totalDiff += $row['difference'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td class="text-end">{{ number_format($row['ledger_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['due_list_amount'], 2) }}</td>
                <td class="text-end {{ $row['difference'] != 0 ? 'text-danger' : '' }}">{{ number_format($row['difference'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="3" class="text-end">Totals:</td>
                <td class="text-end">{{ number_format($totalLedger, 2) }}</td>
                <td class="text-end">{{ number_format($totalDue, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($totalDiff, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top: 10px; font-size: 11px;">Total Mismatches: {{ count($reportData ?? []) }}</p>
</body>
</html>
