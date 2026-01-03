<!DOCTYPE html>
<html>
<head>
    <title>Category Wise Stock Status - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        .header p { margin: 5px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
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
        <h3>Category Wise Stock Status</h3>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Sr.</th>
                <th>Category Name</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Value</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($reportData) && $reportData->count() > 0)
                @foreach($reportData as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row['category_name'] ?? '' }}</td>
                    <td class="text-end">{{ number_format($row['qty'] ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row['value'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="4" class="text-center">No records found</td></tr>
            @endif
        </tbody>
        @if(isset($reportData) && $reportData->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totals['total_qty'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['total_value'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
