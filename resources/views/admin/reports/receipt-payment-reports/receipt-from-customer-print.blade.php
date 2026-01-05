<!DOCTYPE html>
<html>
<head>
    <title>Receipt from Customer - Print</title>
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
        <h3>Receipt from Customer</h3>
        <p>From: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-d'))->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filter-info">
        @if($request->payment_mode && $request->payment_mode != '8')
            @php
                $modes = ['1' => 'Cash', '2' => 'Cheque', '3' => 'Adj.', '4' => 'Dis.', '5' => 'NEFT', '6' => 'RTGS', '7' => 'Wallet'];
            @endphp
            Mode: {{ $modes[$request->payment_mode] ?? 'All' }} |
        @endif
        @if($request->customer_code && $request->customer_code != '00')
            Customer: {{ $request->customer_name ?? $request->customer_code }} |
        @endif
        Order By: {{ $request->order_by ?? 'Date' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">S.No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 80px;">Receipt No</th>
                <th>Customer Name</th>
                <th style="width: 80px;">Mode</th>
                <th class="text-end" style="width: 100px;">Amount</th>
                <th>Narration</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php $totalAmount += $row['amount']; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['receipt_no'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ $row['mode'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                <td>{{ $row['narration'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        Printed on: {{ date('d-M-Y h:i A') }} | Total Records: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
