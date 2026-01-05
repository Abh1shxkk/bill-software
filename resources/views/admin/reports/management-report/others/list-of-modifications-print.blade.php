<!DOCTYPE html>
<html>
<head>
    <title>List of Modifications - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 3px 6px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        @media print { 
            body { margin: 0; } 
            .header { background-color: #ffc4d0 !important; -webkit-print-color-adjust: exact; } 
            th { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>List of Modifications</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">S.No</th>
                <th style="width: 70px;">Type</th>
                <th style="width: 90px;">Invoice No</th>
                <th style="width: 80px;">Date</th>
                <th>Party Name</th>
                <th class="text-end" style="width: 90px;">Amount</th>
                <th style="width: 70px;">Status</th>
                <th style="width: 90px;">Modified By</th>
                <th style="width: 110px;">Modified At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                <td class="{{ $row['status'] == 'Deleted' ? 'text-danger' : 'text-warning' }}">{{ $row['status'] }}</td>
                <td>{{ $row['modified_by'] }}</td>
                <td>{{ $row['modified_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
