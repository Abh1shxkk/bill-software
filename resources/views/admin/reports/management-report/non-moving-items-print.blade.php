<!DOCTYPE html>
<html>
<head>
    <title>Non-Moving Items - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .totals { background-color: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Non-Moving Items</h3>
        <p>Items not sold from: {{ $request->from_date ?? date('Y-m-d') }} to: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">Sr.</th>
                <th>Item Name</th>
                @if($request->tag_company == 'Y')
                <th>Company</th>
                @endif
                <th class="text-center">Last Sale Date</th>
                <th class="text-end">Days Since Sale</th>
                @if($request->with_stock)
                <th class="text-end">Current Stock</th>
                <th class="text-end">Stock Value</th>
                @endif
                @if($request->with_batch_detail)
                <th>Batch No</th>
                <th class="text-center">Expiry</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php $totalStock = 0; $totalValue = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalStock += $row['current_stock'] ?? 0; 
                $totalValue += $row['stock_value'] ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] ?? '' }}</td>
                @if($request->tag_company == 'Y')
                <td>{{ $row['company_name'] ?? '' }}</td>
                @endif
                <td class="text-center">{{ $row['last_sale_date'] ?? '' }}</td>
                <td class="text-end">{{ $row['days_since_sale'] ?? 0 }}</td>
                @if($request->with_stock)
                <td class="text-end">{{ number_format($row['current_stock'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($row['stock_value'] ?? 0, 2) }}</td>
                @endif
                @if($request->with_batch_detail)
                <td>{{ $row['batch_no'] ?? '' }}</td>
                <td class="text-center">{{ $row['expiry_date'] ?? '' }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        @if($request->with_stock)
        <tfoot>
            <tr class="totals">
                <td colspan="{{ $request->tag_company == 'Y' ? 4 : 3 }}" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalStock, 2) }}</td>
                <td class="text-end">{{ number_format($totalValue, 2) }}</td>
                @if($request->with_batch_detail)
                <td colspan="2"></td>
                @endif
            </tr>
        </tfoot>
        @endif
    </table>
    <p style="margin-top: 10px; font-size: 11px;">Total Items: {{ count($reportData ?? []) }}</p>
</body>
</html>
