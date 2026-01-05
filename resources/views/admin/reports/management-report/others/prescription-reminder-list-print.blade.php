<!DOCTYPE html>
<html>
<head>
    <title>Prescription Reminder List - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #3399ff; color: #fff; font-weight: bold; }
        tbody tr:nth-child(odd) { background-color: #f0f0f0; }
        tbody tr:nth-child(even) { background-color: #fff; }
        @media print { body { margin: 0; } .header { background-color: #ffc4d0 !important; -webkit-print-color-adjust: exact; } th { background-color: #3399ff !important; -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>PRESCRIPTION LIST</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Date</th>
                <th>PartyName</th>
                <th>Item Name</th>
                <th style="width: 100px;">Pack</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td>{{ $row['pack'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 11px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
