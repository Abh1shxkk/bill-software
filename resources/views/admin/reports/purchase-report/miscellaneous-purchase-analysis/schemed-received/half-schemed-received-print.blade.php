<!DOCTYPE html>
<html>
<head>
    <title>Half Scheme Received Report</title>
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
        .text-primary { color: #007bff; }
        .text-danger { color: #dc3545; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">Print</button>
    
    <div class="header">
        <h3>HALF SCHEME RECEIVED</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
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
                <th class="text-right">Full Scheme</th>
                <th class="text-right">Half Scheme</th>
                <th class="text-right">Qty Recd</th>
                <th class="text-right">Difference</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0;
                $grandQty = 0;
                $grandDiff = 0;
            @endphp
            @forelse($items ?? [] as $item)
            @php
                $grandQty += $item->qty ?? 0;
                $grandDiff += $item->difference ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $item->bill_date ? $item->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $item->bill_no ?? '-' }}</td>
                <td>{{ $item->supplier_name ?? '-' }}</td>
                <td>{{ $item->item_name ?? '-' }}</td>
                <td>{{ $item->company_name ?? '-' }}</td>
                <td class="text-right">{{ $item->full_scheme ?? '-' }}</td>
                <td class="text-right text-primary">{{ $item->half_scheme ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0, 0) }}</td>
                <td class="text-right text-danger">{{ $item->difference ?? 0 }} (Pending)</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($grandQty, 0) }}</td>
                <td class="text-right">{{ number_format($grandDiff, 0) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
