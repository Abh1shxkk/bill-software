<!DOCTYPE html>
<html>
<head>
    <title>Cash / Cheque Collection - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #ffc4d0; padding: 10px; }
        .header h3 { margin: 0; font-family: 'Brush Script MT', cursive; font-size: 24px; color: #333; }
        .filter-info { font-size: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 8px; }
        th { background-color: #e0e0e0; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { 
            .header, th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>CASH / CHEQUE COLLECTION</h3>
        <p>From: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-01'))->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filter-info">
        @php
            $modes = ['C' => 'Cash', 'Q' => 'Cheque', 'B' => 'Both'];
        @endphp
        Mode: {{ $modes[$request->mode] ?? 'Both' }}
        @if($request->cheque_no)
            | Cheque No: {{ $request->cheque_no }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">S.No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 80px;">Receipt No</th>
                <th>Customer</th>
                <th style="width: 70px;">Mode</th>
                <th class="text-end" style="width: 90px;">Cash Amt</th>
                <th class="text-end" style="width: 90px;">Cheque Amt</th>
                <th class="text-end" style="width: 90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalCash = 0; 
                $totalCheque = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalCash += $row['cash_amount']; 
                $totalCheque += $row['cheque_amount'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['receipt_no'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ $row['mode'] }}</td>
                <td class="text-end">{{ number_format($row['cash_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cheque_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cash_amount'] + $row['cheque_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalCash, 2) }}</td>
                <td class="text-end">{{ number_format($totalCheque, 2) }}</td>
                <td class="text-end">{{ number_format($totalCash + $totalCheque, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        Printed on: {{ date('d-M-Y h:i A') }} | Total Records: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
