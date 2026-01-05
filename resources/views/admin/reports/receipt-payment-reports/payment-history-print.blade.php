<!DOCTYPE html>
<html>
<head>
    <title>Payment History - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 10px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 8px; }
        .header h3 { margin: 0; font-size: 16px; color: #155724; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #343a40; color: white; font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { background-color: #343a40; color: white; font-weight: bold; }
        .footer-totals { margin-top: 15px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 10px; }
        .footer-totals span { color: #dc3545; font-weight: bold; }
        @media print { 
            th, .totals { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Payment History</h3>
        <p>From: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-01'))->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th style="width: 70px;">Trn. Date</th>
                <th style="width: 60px;">Trn.No</th>
                <th class="text-end" style="width: 80px;">Amount</th>
                <th style="width: 55px;">P.Mode</th>
                <th class="text-center" style="width: 40px;">Days</th>
                <th style="width: 70px;">Bill Date</th>
                <th style="width: 60px;">Bill No</th>
                <th class="text-end" style="width: 80px;">Bill Amt</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalAmount = 0; 
                $totalCash = 0; 
                $totalCheque = 0; 
                $totalRTGS = 0; 
                $totalNEFT = 0; 
            @endphp
            @foreach($reportData ?? [] as $row)
            @php 
                $totalAmount += $row['amount'];
                if($row['mode'] == 'Cash') $totalCash += $row['amount'];
                if($row['mode'] == 'Cheque') $totalCheque += $row['amount'];
                if($row['mode'] == 'RTGS') $totalRTGS += $row['amount'];
                if($row['mode'] == 'NEFT') $totalNEFT += $row['amount'];
            @endphp
            <tr>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['party_name'] }}</td>
                <td>{{ $row['trn_date'] }}</td>
                <td>{{ $row['trn_no'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                <td>{{ $row['mode'] }}</td>
                <td class="text-center">{{ $row['days'] }}</td>
                <td>{{ $row['bill_date'] }}</td>
                <td>{{ $row['bill_no'] }}</td>
                <td class="text-end">{{ number_format($row['bill_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-totals">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; text-align: center;"><strong>Total:</strong> <span>{{ number_format($totalAmount, 2) }}</span></td>
                <td style="border: none; text-align: center;"><strong>Total Cash Amt:</strong> <span>{{ number_format($totalCash, 2) }}</span></td>
                <td style="border: none; text-align: center;"><strong>Total Chq. Amt:</strong> <span>{{ number_format($totalCheque, 2) }}</span></td>
                <td style="border: none; text-align: center;"><strong>Total RTGS Amt:</strong> <span>{{ number_format($totalRTGS, 2) }}</span></td>
                <td style="border: none; text-align: center;"><strong>Total NEFT Amt:</strong> <span>{{ number_format($totalNEFT, 2) }}</span></td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 15px; font-size: 9px;">
        Printed on: {{ date('d-M-Y h:i A') }} | Total Records: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
