<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book With Sale Return</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { color: #0066cc; font-style: italic; margin-bottom: 5px; }
        .header .date-range { font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        .return-row { background-color: #ffe6e6; }
        .sale-total { background-color: #d4edda; font-weight: bold; }
        .return-total { background-color: #f8d7da; font-weight: bold; }
        .grand-total { background-color: #ffffcc; font-weight: bold; }
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
        <h2>SALE BOOK WITH SALE RETURN</h2>
        <div class="date-range">
            From: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To: {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}
        </div>
    </div>

    @if(isset($combinedData) && $combinedData->count() > 0)
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th class="text-center" style="width: 75px;">Date</th>
                <th class="text-center" style="width: 55px;">Type</th>
                <th class="text-center" style="width: 80px;">Doc No</th>
                <th style="width: 55px;">Code</th>
                <th>Party Name</th>
                <th>Area</th>
                <th class="text-end" style="width: 90px;">Gross Amt</th>
                <th class="text-end" style="width: 75px;">Discount</th>
                <th class="text-end" style="width: 75px;">Tax</th>
                <th class="text-end" style="width: 95px;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($combinedData as $index => $row)
            <tr class="{{ $row['is_return'] ? 'return-row' : '' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                <td class="text-center {{ $row['is_return'] ? 'text-danger' : 'text-success' }}">
                    {{ $row['type'] }}
                </td>
                <td class="text-center">{{ $row['doc_no'] }}</td>
                <td>{{ $row['customer_code'] }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ $row['area'] }}</td>
                <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['gross_amount'], 2) }}</td>
                <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['dis_amount'], 2) }}</td>
                <td class="text-end {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['tax_amount'], 2) }}</td>
                <td class="text-end fw-bold {{ $row['is_return'] ? 'text-danger' : '' }}">{{ number_format($row['net_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="sale-total">
                <td colspan="7" class="text-end">Sale Total ({{ $totals['sale_count'] }} Bills):</td>
                <td colspan="3"></td>
                <td class="text-end text-success">{{ number_format($totals['sale_amount'], 2) }}</td>
            </tr>
            <tr class="return-total">
                <td colspan="7" class="text-end">Return Total ({{ $totals['return_count'] }} Bills):</td>
                <td colspan="3"></td>
                <td class="text-end text-danger">{{ number_format($totals['return_amount'], 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="7" class="text-end">NET TOTAL:</td>
                <td class="text-end">{{ number_format($totals['gross_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['dis_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Sales: {{ $totals['sale_count'] }} bills = ₹{{ number_format($totals['sale_amount'], 2) }} | 
        Returns: {{ $totals['return_count'] }} bills = ₹{{ number_format($totals['return_amount'], 2) }} | 
        Net: ₹{{ number_format($totals['net_amount'], 2) }} | 
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
    @else
    <p>No records found for the selected date range.</p>
    @endif
</body>
</html>
