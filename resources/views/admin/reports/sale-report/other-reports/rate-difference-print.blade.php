<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Change Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { color: #0066cc; font-style: italic; margin-bottom: 5px; }
        .header .date-range { font-size: 11px; color: #333; }
        .header .filter-info { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .total-row { background-color: #ffffcc; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h2>RATE CHANGE REPORT</h2>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
        </div>
        <div class="filter-info">
            @if(isset($groupBy))
                @if($groupBy == 'I') Item Wise @elseif($groupBy == 'B') Bill Wise @else Party Wise @endif
            @endif
        </div>
    </div>

    @if(isset($reportData) && $reportData->count() > 0)
        @if($groupBy == 'I')
        <!-- Item Wise Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">Sr.</th>
                    <th>Item Name</th>
                    <th>Company</th>
                    <th class="text-end" style="width: 60px;">Qty</th>
                    <th class="text-end" style="width: 80px;">Pur. Rate</th>
                    <th class="text-end" style="width: 80px;">Sale Rate</th>
                    <th class="text-end" style="width: 80px;">Rate Diff</th>
                    <th class="text-end" style="width: 90px;">Diff Amt</th>
                    <th class="text-end" style="width: 100px;">Total Amt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    <td>{{ $row['company_name'] }}</td>
                    <td class="text-end">{{ number_format($row['qty'], 0) }}</td>
                    <td class="text-end">{{ number_format($row['purchase_rate'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['sale_rate'], 2) }}</td>
                    <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['rate_diff'], 2) }}</td>
                    <td class="text-end {{ $row['diff_amount'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['diff_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['total_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-end">TOTAL:</td>
                    <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                    <td colspan="3"></td>
                    <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($totals['total_amount'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @elseif($groupBy == 'B')
        <!-- Bill Wise Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">Sr.</th>
                    <th class="text-center" style="width: 75px;">Date</th>
                    <th style="width: 75px;">Bill No</th>
                    <th>Party</th>
                    <th>Item Name</th>
                    <th class="text-end" style="width: 50px;">Qty</th>
                    <th class="text-end" style="width: 70px;">Pur. Rate</th>
                    <th class="text-end" style="width: 70px;">Sale Rate</th>
                    <th class="text-end" style="width: 70px;">Rate Diff</th>
                    <th class="text-end" style="width: 80px;">Diff Amt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td>{{ $row['bill_no'] }}</td>
                    <td>{{ $row['party_name'] }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    <td class="text-end">{{ number_format($row['qty'], 0) }}</td>
                    <td class="text-end">{{ number_format($row['purchase_rate'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['sale_rate'], 2) }}</td>
                    <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['rate_diff'], 2) }}</td>
                    <td class="text-end {{ $row['diff_amount'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['diff_amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-end">TOTAL:</td>
                    <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                    <td colspan="3"></td>
                    <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <!-- Party Wise Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">Sr.</th>
                    <th style="width: 70px;">Party Code</th>
                    <th>Party Name</th>
                    <th class="text-end" style="width: 70px;">Total Qty</th>
                    <th class="text-end" style="width: 100px;">Purchase Value</th>
                    <th class="text-end" style="width: 100px;">Sale Value</th>
                    <th class="text-end" style="width: 100px;">Rate Diff</th>
                    <th class="text-end" style="width: 70px;">Diff %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['party_code'] }}</td>
                    <td>{{ $row['party_name'] }}</td>
                    <td class="text-end">{{ number_format($row['total_qty'], 0) }}</td>
                    <td class="text-end">{{ number_format($row['purchase_value'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['sale_value'], 2) }}</td>
                    <td class="text-end {{ $row['rate_diff'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">{{ number_format($row['rate_diff'], 2) }}</td>
                    <td class="text-end {{ $row['diff_percent'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($row['diff_percent'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-end">TOTAL:</td>
                    <td class="text-end">{{ number_format($totals['total_qty'], 0) }}</td>
                    <td colspan="2"></td>
                    <td class="text-end {{ $totals['total_diff_amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totals['total_diff_amount'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        @endif

    <div class="footer">
        Total Records: {{ $totals['count'] ?? 0 }} | 
        Total Difference: <span class="{{ ($totals['total_diff_amount'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($totals['total_diff_amount'] ?? 0, 2) }}</span> | 
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
    @else
    <p>No records found for the selected filters.</p>
    @endif
</body>
</html>
