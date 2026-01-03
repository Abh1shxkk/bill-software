<!DOCTYPE html>
<html>
<head>
    <title>Annual Stock Ledger Summary - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot td { font-weight: bold; background-color: #f5f5f5; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <h3>Annual Stock Ledger Summary</h3>
        <p>Year: {{ request('closing_year', date('Y')) }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sr.</th>
                <th>Item Name</th>
                <th>Company</th>
                <th class="text-end">Op. Qty</th>
                <th class="text-end">Op. Value</th>
                <th class="text-end">Pur. Qty</th>
                <th class="text-end">Pur. Value</th>
                <th class="text-end">Sale Qty</th>
                <th class="text-end">Sale Value</th>
                <th class="text-end">Cl. Qty</th>
                <th class="text-end">Cl. Value</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($reportData) && $reportData->count() > 0)
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['item_name'] ?? '' }}</td>
                    <td>{{ $row['company_name'] ?? '' }}</td>
                    <td class="text-end">{{ number_format($row['opening_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['opening_value'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['purchase_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['purchase_value'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['sale_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['sale_value'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['closing_qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['closing_value'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="11" class="text-center">No records found</td></tr>
            @endif
        </tbody>
        @if(isset($reportData) && $reportData->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totals['total_opening_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_opening_value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_purchase_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_purchase_value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_sale_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_sale_value'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_closing_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_closing_value'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
