<!DOCTYPE html>
<html>
<head>
    <title>Cash Deposite / Withdrawn Report - Print</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11px; 
            margin: 10px; 
        }
        .header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 10px; 
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #000;
        }
        .period {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #999; 
            padding: 4px 6px; 
        }
        th { 
            background-color: #d0d0d0; 
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        tfoot td {
            font-weight: bold;
            background-color: #d0d0d0;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Cash {{ $transactionType === 'D' ? 'Deposite' : 'Withdrawn' }} Report</h2>
    </div>

    <div class="period">
        Period: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">S.No</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 60px;">Trn No</th>
                <th>Bank Name</th>
                <th style="width: 80px;">Cheque No</th>
                <th style="width: 100px;" class="text-end">Amount</th>
                <th>Narration</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-M-Y') }}</td>
                <td>{{ $row['transaction_no'] }}</td>
                <td>{{ $row['bank_name'] }}</td>
                <td>{{ $row['cheque_no'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                <td>{{ $row['narration'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totals['amount'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
