<!DOCTYPE html>
<html>
<head>
    <title>Day Book - Print</title>
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
            color: #800080;
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
            background-color: #e0e0e0;
        }
        td {
            background-color: #e8e8e8;
        }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        tfoot td {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>DAY BOOK</h2>
    </div>

    <div class="period">
        From: {{ \Carbon\Carbon::parse($fromDate)->format('d-M-Y') }} 
        To: {{ \Carbon\Carbon::parse($toDate)->format('d-M-Y') }}
        @if($entryType === 'D') | Double Entry @else | Single Entry @endif
        @if($voucherType) | Type: {{ $voucherType }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Vou. No.</th>
                <th style="width: 80px;">Date</th>
                <th>Account Name</th>
                <th class="text-end" style="width: 100px;">Debit</th>
                <th class="text-end" style="width: 100px;">Credit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
            <tr>
                <td>{{ $row['voucher_no'] }}</td>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-M-y') }}</td>
                <td>{{ $row['account_name'] }}</td>
                <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>Narration :</td>
                <td colspan="2">{{ $reportData->isNotEmpty() ? ($reportData->last()['narration'] ?? '') : '' }}</td>
                <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
