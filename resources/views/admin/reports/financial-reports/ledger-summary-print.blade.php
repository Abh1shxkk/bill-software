<!DOCTYPE html>
<html>
<head>
    <title>Ledger Summary - Print</title>
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
            background-color: #d0d0d0; 
            color: #0000ff;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-purple { color: #800080; }
        .fw-bold { font-weight: bold; }
        tfoot td {
            font-weight: bold;
            background-color: #d0d0d0;
            color: #800080;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>LEDGER SUMMARY</h2>
    </div>

    <div class="period">
        Period: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
        | Type: {{ $ledgerType === 'C' ? 'Customer' : ($ledgerType === 'S' ? 'Supplier' : 'General Ledger') }}
        @if($groupHead !== 'All') | Group: {{ $groupHead }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 70px;">CODE</th>
                <th>NAME</th>
                <th style="width: 90px;" class="text-end">OPENING</th>
                <th style="width: 35px;" class="text-center">DrCr</th>
                <th style="width: 90px;" class="text-end">DEBIT</th>
                <th style="width: 90px;" class="text-end">CREDIT</th>
                <th style="width: 90px;" class="text-end">CLOSING</th>
                <th style="width: 35px;" class="text-center">DrCr</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
            <tr>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                <td class="text-center">{{ $row['opening_type'] }}</td>
                <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
                <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
                <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
                <td class="text-center">{{ $row['closing_type'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td class="text-purple">Total Records : {{ $reportData->count() }}</td>
                <td class="text-end text-purple">Total :</td>
                <td class="text-end">{{ number_format($totals['opening'], 2) }}</td>
                <td class="text-center">{{ $totals['opening_type'] }}</td>
                <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['closing'], 2) }}</td>
                <td class="text-center">{{ $totals['closing_type'] }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
