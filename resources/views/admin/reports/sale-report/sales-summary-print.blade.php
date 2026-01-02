<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Summary - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #dc3545; padding-bottom: 10px; }
        .header h2 { color: #dc3545; margin-bottom: 5px; }
        .header p { color: #666; font-size: 10px; }
        .filters { background: #f8f9fa; padding: 8px; margin-bottom: 10px; border-radius: 4px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dee2e6; padding: 4px 6px; text-align: left; }
        th { background: #343a40; color: white; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        tfoot tr { background: #343a40; color: white; font-weight: bold; }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    
    <div class="header">
        <h2>SALE SUMMARY</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="filters">
        @if($series)<span><strong>Series:</strong> {{ $series }}</span>@endif
        @if($numberFrom > 0)<span><strong>No From:</strong> {{ $numberFrom }}</span>@endif
        @if($numberTo > 0)<span><strong>To:</strong> {{ $numberTo }}</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 80px;">Date</th>
                <th style="width: 50px;">Series</th>
                <th style="width: 70px;">Bill No</th>
                <th>Party Name</th>
                <th class="text-end" style="width: 90px;">NT Amount</th>
                <th class="text-end" style="width: 70px;">Discount</th>
                <th class="text-end" style="width: 70px;">Tax</th>
                <th class="text-end" style="width: 90px;">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales ?? [] as $index => $sale)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                <td>{{ $sale->series ?? '-' }}</td>
                <td class="fw-bold">{{ $sale->invoice_no }}</td>
                <td>{{ Str::limit($sale->customer->name ?? 'N/A', 25) }}</td>
                <td class="text-end">{{ number_format($sale->nt_amount ?? 0, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($sale->dis_amount ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                <td class="text-end fw-bold text-success">{{ number_format($sale->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        @if(isset($sales) && $sales->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-end">Grand Total ({{ $sales->count() }} Bills):</td>
                <td class="text-end">{{ number_format($grandTotals['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($grandTotals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($grandTotals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($grandTotals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666; text-align: center;">
        Generated on {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
