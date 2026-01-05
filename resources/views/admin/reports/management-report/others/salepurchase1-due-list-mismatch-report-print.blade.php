<!DOCTYPE html>
<html>
<head>
    <title>Sale/Purchase Due List Mismatch Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #e0e0e0; padding: 10px; }
        .header h3 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 3px 6px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-danger { color: #dc3545; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>{{ ($request->type ?? 'C') == 'C' ? 'Customer' : 'Supplier' }} Due List Mismatch Report</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">S.No</th>
                <th style="width: 70px;">DATE</th>
                <th style="width: 80px;">TRN.NO</th>
                <th style="width: 60px;">CODE</th>
                <th>PARTY NAME</th>
                <th class="text-end" style="width: 90px;">TRN. AMT.</th>
                <th class="text-end" style="width: 90px;">ADJ. AMT.</th>
                <th class="text-end" style="width: 90px;">O/S AMT.</th>
                <th class="text-end" style="width: 90px;">DUE AMT.</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalTrn = 0; $totalAdj = 0; $totalOs = 0; $totalDue = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalTrn += $row['trn_amount']; 
                $totalAdj += $row['adj_amount'];
                $totalOs += $row['os_amount'];
                $totalDue += $row['due_amount'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['trn_no'] }}</td>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td class="text-end">{{ number_format($row['trn_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['adj_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['os_amount'], 2) }}</td>
                <td class="text-end {{ $row['due_amount'] != $row['os_amount'] ? 'text-danger' : '' }}">{{ number_format($row['due_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="5" class="text-end">Totals:</td>
                <td class="text-end">{{ number_format($totalTrn, 2) }}</td>
                <td class="text-end">{{ number_format($totalAdj, 2) }}</td>
                <td class="text-end">{{ number_format($totalOs, 2) }}</td>
                <td class="text-end">{{ number_format($totalDue, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <p style="margin-top: 10px; font-size: 10px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
