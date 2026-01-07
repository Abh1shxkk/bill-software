<!DOCTYPE html>
<html>
<head>
    <title>Balance Sheet - Print</title>
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
        .container {
            display: flex;
            gap: 10px;
        }
        .side {
            flex: 1;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #666; 
            padding: 4px 6px; 
        }
        .liabilities th, .liabilities td {
            background-color: #87ceeb;
        }
        .assets th, .assets td {
            background-color: #ffb6c1;
        }
        th { 
            color: #800080;
            font-weight: bold;
        }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        tfoot td {
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            .container { display: table; width: 100%; }
            .side { display: table-cell; width: 50%; vertical-align: top; padding: 0 5px; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>BALANCE SHEET</h2>
    </div>

    <div class="period">
        As On: {{ \Carbon\Carbon::parse($asOnDate)->format('d/m/Y') }} | 
        From: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}
    </div>

    <div class="container">
        <!-- Liabilities Side -->
        <div class="side">
            <table class="liabilities">
                <thead>
                    <tr>
                        <th>Liabilities</th>
                        <th class="text-end" style="width: 100px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($liabilities as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No records</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td class="text-end">{{ number_format($totalLiabilities, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Assets Side -->
        <div class="side">
            <table class="assets">
                <thead>
                    <tr>
                        <th>Assets</th>
                        <th class="text-end" style="width: 100px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-end">{{ number_format($item['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No records</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td class="text-end">{{ number_format($totalAssets, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
