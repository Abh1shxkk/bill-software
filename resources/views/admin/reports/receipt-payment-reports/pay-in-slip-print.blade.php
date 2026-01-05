<!DOCTYPE html>
<html>
<head>
    <title>Pay-In-Slip Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%); padding: 10px; }
        .header h3 { margin: 0; font-size: 18px; color: #383d41; font-style: italic; }
        .filter-info { font-size: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 8px; }
        th { background-color: #343a40; color: white; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { background-color: #343a40; color: white; font-weight: bold; }
        @media print { 
            th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>PAY - IN - SLIP REPORT</h3>
        <p>From: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-d'))->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filter-info">
        Type: {{ $request->report_type == 'S' ? 'Summary' : 'Detailed' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 90px;">Date</th>
                <th style="width: 100px;">Slip No</th>
                <th>Bank Name</th>
                <th style="width: 90px;">A/C No</th>
                <th class="text-end" style="width: 100px;">Cash Amt</th>
                <th class="text-end" style="width: 100px;">Cheque Amt</th>
                <th class="text-end" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCash = 0; $totalCheque = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalCash += $row['cash_amount']; 
                $totalCheque += $row['cheque_amount']; 
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['slip_no'] }}</td>
                <td>{{ $row['bank_name'] }}</td>
                <td>{{ $row['account_no'] }}</td>
                <td class="text-end">{{ number_format($row['cash_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cheque_amount'], 2) }}</td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($row['cash_amount'] + $row['cheque_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals">
                <td colspan="5" class="text-end">Grand Total:</td>
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
