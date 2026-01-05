<!DOCTYPE html>
<html>
<head>
    <title>List of Returned Cheques - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; font-size: 18px; }
        .filter-info { font-size: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #c0c0c0; font-weight: bold; text-align: center; }
        .text-end { text-align: right; }
        .totals { background-color: #f0f0f0; font-weight: bold; }
        .text-danger { color: #dc3545; }
        @media print { 
            th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>List of Returned Cheques</h3>
        <p>From: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-01'))->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filter-info">
        Order By: {{ $request->order_by == 'P' ? 'Party' : 'Date' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">S.No.</th>
                <th style="width: 80px;">CHQ.DATE</th>
                <th style="width: 90px;">CHQ.NO</th>
                <th style="width: 50px;">CODE</th>
                <th>PARTY NAME</th>
                <th class="text-end" style="width: 90px;">CHQ.AMT</th>
                <th style="width: 80px;">RTD.DATE</th>
                <th class="text-end" style="width: 70px;">CHARGES</th>
                <th style="width: 100px;">REASON</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalAmount = 0; 
                $totalCharges = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalAmount += $row['amount']; 
                $totalCharges += $row['charges'] ?? 0;
            @endphp
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $row['cheque_date'] }}</td>
                <td>{{ $row['cheque_no'] }}</td>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                <td>{{ $row['return_date'] ?? '' }}</td>
                <td class="text-end">{{ number_format($row['charges'] ?? 0, 2) }}</td>
                <td>{{ $row['reason'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end text-danger">{{ number_format($totalAmount, 2) }}</td>
                <td></td>
                <td class="text-end text-danger">{{ number_format($totalCharges, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        Printed on: {{ date('d-M-Y h:i A') }} | Total Records: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
