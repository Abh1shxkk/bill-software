<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Stock Details - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; background: #f5f5f5; padding: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background: #333; color: #fff; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        tfoot td { background: #333; color: #fff; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
        @media print { .print-btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print</button>
    <div class="header">
        <h2>Customer Stock Details</h2>
        <p>As On: {{ \Carbon\Carbon::parse($asOnDate)->format('d-m-Y') }}</p>
    </div>
    <div class="filters">
        <strong>Filters:</strong> As On Date: {{ \Carbon\Carbon::parse($asOnDate)->format('d-m-Y') }}
        @if($customerId ?? false) | Customer: Selected @endif
    </div>
    <table>
        <thead><tr><th class="text-center" style="width:30px;">#</th><th style="width:80px;">Customer Code</th><th>Customer Name</th><th>Item</th><th class="text-end" style="width:70px;">Qty Sold</th><th class="text-end" style="width:90px;">Value</th><th style="width:80px;">Last Sale</th></tr></thead>
        <tbody>
            @forelse($stockData ?? [] as $index => $data)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $data['customer_code'] ?? '' }}</td><td>{{ $data['customer_name'] ?? '' }}</td><td>{{ $data['item_name'] ?? '' }}</td><td class="text-end">{{ number_format($data['qty_sold'] ?? 0) }}</td><td class="text-end fw-bold">{{ number_format($data['value'] ?? 0, 2) }}</td><td>{{ $data['last_sale'] ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="7" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        @if(isset($totals))<tfoot><tr><td colspan="4" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['qty_sold'] ?? 0) }}</td><td class="text-end">{{ number_format($totals['value'] ?? 0, 2) }}</td><td></td></tr></tfoot>@endif
    </table>
    <div style="margin-top: 20px; font-size: 10px; color: #666;">Printed on: {{ now()->format('d-m-Y H:i:s') }}</div>
</body>
</html>
