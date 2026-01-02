<!DOCTYPE html>
<html>
<head>
    <title>Free Scheme Received Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #c2185b; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #343a40; color: #fff; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #28a745; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">Print</button>
    
    <div class="header">
        <h3>FREE SCHEME RECEIVED</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        <p>Report Type: {{ $reportType == 'D' ? 'Detailed' : 'Summary' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th>Bill Date</th>
                <th>Bill No</th>
                <th>Supplier Name</th>
                <th>Item Name</th>
                <th>Company</th>
                <th class="text-right">Pur. Qty</th>
                <th class="text-right">Free Qty</th>
                <th class="text-right">Scheme %</th>
                <th class="text-right">Free Value</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0;
                $grandQty = 0;
                $grandFree = 0;
                $grandValue = 0;
            @endphp
            @forelse($items ?? [] as $item)
            @php
                $grandQty += $item->qty ?? 0;
                $grandFree += $item->free_qty ?? 0;
                $grandValue += $item->free_value ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->bill_no ?? '-' }}</td>
                <td>{{ $item->supplier_name ?? '-' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->company_name ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0, 0) }}</td>
                <td class="text-right text-success">{{ number_format($item->free_qty ?? 0, 0) }}</td>
                <td class="text-right">{{ $item->scheme_percent ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->free_value ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($grandQty, 0) }}</td>
                <td class="text-right">{{ number_format($grandFree, 0) }}</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($grandValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
