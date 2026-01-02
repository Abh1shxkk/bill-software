<!DOCTYPE html>
<html>
<head>
    <title>Day Sales Summary - Item Wise</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #8B0000; }
        .info { margin-bottom: 10px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background: #f0f0f0; text-align: left; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .total-row { background: #333; color: #fff; font-weight: bold; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>DAY SALES SUMMARY - ITEM WISE</h3>
        <div class="info">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
            @if($categoryId) | Category: {{ $categories->firstWhere('id', $categoryId)->name ?? '-' }} @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>COMPANY</th>
                <th>ITEM NAME</th>
                <th>PACK</th>
                <th class="text-end">SALE</th>
                <th class="text-end">VALUE</th>
                <th class="text-end">BAL.</th>
                <th class="text-end">PO</th>
                <th class="text-end">MRP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->company_name ?? '-' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->packing ?? '-' }}</td>
                <td class="text-end">{{ number_format($item->total_qty ?? 0) }}</td>
                <td class="text-end">{{ number_format($item->total_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($item->balance ?? 0) }}</td>
                <td class="text-end">{{ number_format($item->po ?? 0) }}</td>
                <td class="text-end">{{ number_format($item->mrp ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-end">TOTAL SALE VALUE:</td>
                <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
