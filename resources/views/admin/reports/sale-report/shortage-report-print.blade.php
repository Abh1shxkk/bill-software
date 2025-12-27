<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shortage Report - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; color: #dc3545; font-style: italic; }
        .header p { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        .out-of-stock { background-color: #f8d7da; }
        .low-stock { background-color: #fff3cd; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>Shortage Report</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Company:</strong> {{ $companyId ? ($companies->firstWhere('id', $companyId)->name ?? 'Selected') : 'All' }}</span>
        <span><strong>Format:</strong> {{ $reportFormat == 'D' ? 'Detailed' : 'Summarized' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 60px;">Item Code</th>
                <th>Item Name</th>
                <th>Company</th>
                <th style="width: 60px;">Packing</th>
                <th class="text-end" style="width: 60px;">Sold Qty</th>
                <th class="text-end" style="width: 60px;">Stock</th>
                <th class="text-end" style="width: 60px;">Shortage</th>
                <th class="text-center" style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shortageItems ?? [] as $index => $item)
            <tr class="{{ $item['status'] === 'Out of Stock' ? 'out-of-stock' : 'low-stock' }}">
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item['item_code'] }}</td>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['company_name'] ?? '' }}</td>
                <td>{{ $item['packing'] ?? '' }}</td>
                <td class="text-end">{{ number_format($item['sold_qty']) }}</td>
                <td class="text-end">{{ number_format($item['current_stock']) }}</td>
                <td class="text-end fw-bold">{{ number_format($item['shortage_qty']) }}</td>
                <td class="text-center">{{ $item['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="5" class="text-end">Total ({{ $totals['items'] ?? 0 }} Items):</td>
                <td class="text-end">{{ number_format($totals['sold_qty'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['current_stock'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['shortage_qty'] ?? 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 10px;">
        <strong>Summary:</strong> Out of Stock: {{ $totals['out_of_stock'] ?? 0 }} | Low Stock: {{ $totals['low_stock'] ?? 0 }}
    </div>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
