<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Book with Address - Print</title>
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
        <h2>Transaction Book with Address</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>
    <div class="filters">
        <strong>Filters:</strong> Date: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
    </div>
    <table>
        <thead><tr><th class="text-center" style="width:30px;">#</th><th style="width:70px;">Date</th><th style="width:80px;">Bill No</th><th>Party Name</th><th>Address</th><th style="width:90px;">Mobile</th><th class="text-end" style="width:90px;">Amount</th></tr></thead>
        <tbody>
            @forelse($sales ?? [] as $index => $sale)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $sale->sale_date->format('d-m-Y') ?? '' }}</td><td>{{ $sale->series ?? '' }}{{ $sale->invoice_no ?? '' }}</td><td>{{ $sale->customer->name ?? 'N/A' }}</td><td>{{ $sale->customer->address ?? '-' }}</td><td>{{ $sale->customer->mobile ?? '-' }}</td><td class="text-end fw-bold">{{ number_format($sale->net_amount ?? 0, 2) }}</td></tr>
            @empty
            <tr><td colspan="7" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        @if(isset($totals))<tfoot><tr><td colspan="6" class="text-end">Total:</td><td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td></tr></tfoot>@endif
    </table>
    <div style="margin-top: 20px; font-size: 10px; color: #666;">Printed on: {{ now()->format('d-m-Y H:i:s') }}</div>
</body>
</html>
