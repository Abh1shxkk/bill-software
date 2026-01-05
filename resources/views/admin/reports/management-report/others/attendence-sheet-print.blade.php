<!DOCTYPE html>
<html>
<head>
    <title>Attendance Sheet - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; color: #0066cc; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 3px 6px; }
        th { background-color: #e0e0e0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .summary-table { margin-top: 20px; }
        @media print { 
            body { margin: 0; } 
            .header { background-color: #ffc4d0 !important; -webkit-print-color-adjust: exact; } 
            th { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>ATTENDENCE SHEET</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">S.No</th>
                <th>User Name</th>
                <th style="width: 90px;">Date</th>
                <th style="width: 80px;">In Time</th>
                <th style="width: 80px;">Out Time</th>
                <th style="width: 70px;">Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['user_name'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td class="text-center">{{ $row['in_time'] }}</td>
                <td class="text-center">{{ $row['out_time'] }}</td>
                <td class="text-center {{ $row['status'] == 'Present' ? 'text-success' : ($row['status'] == 'Absent' ? 'text-danger' : '') }}">{{ $row['status'] }}</td>
                <td>{{ $row['remarks'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(isset($summary) && count($summary) > 0)
    <h4>Attendance Summary</h4>
    <table class="summary-table">
        <thead>
            <tr>
                <th>User</th>
                <th class="text-center">Total Days</th>
                <th class="text-center">Present</th>
                <th class="text-center">Absent</th>
                <th class="text-center">Attendance %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $row)
            <tr>
                <td>{{ $row['user_name'] }}</td>
                <td class="text-center">{{ $row['total_days'] }}</td>
                <td class="text-center text-success">{{ $row['present'] }}</td>
                <td class="text-center text-danger">{{ $row['absent'] }}</td>
                <td class="text-center">{{ number_format($row['percentage'], 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <p style="margin-top: 20px; font-size: 10px;">Total Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
