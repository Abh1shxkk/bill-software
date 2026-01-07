<!DOCTYPE html>
<html>
<head>
    <title>Sundry Creditors - Print</title>
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
            background-color: #c8f7c8; 
            color: #800080;
        }
        td {
            background-color: #c8f7c8;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-primary { color: #0000ff; }
        .fw-bold { font-weight: bold; }
        tfoot td {
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>SUNDRY CREDITORS</h2>
    </div>

    <div class="period">
        As On: {{ \Carbon\Carbon::parse($asOnDate)->format('d/m/Y') }} | 
        From: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}
        @if($showOpening) | Opening: Yes @endif
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 70px;">Code</th>
                <th rowspan="2">Name</th>
                <th colspan="2" class="text-center">Opening Balance</th>
                <th colspan="2" class="text-center">Closing Balance</th>
            </tr>
            <tr>
                <th class="text-center" style="width: 90px;">Debit</th>
                <th class="text-center" style="width: 90px;">Credit</th>
                <th class="text-center" style="width: 90px;">Debit</th>
                <th class="text-center" style="width: 90px;">Credit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
            <tr>
                <td>{{ $row['code'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td class="text-end">{{ $row['opening_debit'] > 0 ? number_format($row['opening_debit'], 2) : '' }}</td>
                <td class="text-end">{{ $row['opening_credit'] > 0 ? number_format($row['opening_credit'], 2) : '' }}</td>
                <td class="text-end">{{ $row['closing_debit'] > 0 ? number_format($row['closing_debit'], 2) : '' }}</td>
                <td class="text-end">{{ $row['closing_credit'] > 0 ? number_format($row['closing_credit'], 2) : '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td class="text-primary">Total Records : {{ $reportData->count() }}</td>
                <td class="text-end text-primary">Total</td>
                <td class="text-end">{{ number_format($totals['opening_debit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['opening_credit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['closing_debit'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['closing_credit'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
