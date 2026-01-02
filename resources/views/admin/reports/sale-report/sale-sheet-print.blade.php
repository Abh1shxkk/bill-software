<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Book With Item Details - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; color: #0066cc; }
        .header p { font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 9px; }
        td { font-size: 9px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>SALE BOOK WITH ITEM DETAILS</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">#</th>
                <th style="width: 65px;">Date</th>
                <th style="width: 55px;">Bill No</th>
                <th>Customer</th>
                <th style="width: 55px;">Item Code</th>
                <th>Item Name</th>
                <th style="width: 55px;">Batch</th>
                <th class="text-end" style="width: 40px;">Qty</th>
                <th class="text-end" style="width: 40px;">Free</th>
                <th class="text-end" style="width: 55px;">Rate</th>
                <th class="text-end" style="width: 55px;">Disc</th>
                <th class="text-end" style="width: 55px;">Tax</th>
                <th class="text-end" style="width: 70px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items ?? [] as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->saleTransaction->sale_date->format('d-m-Y') }}</td>
                <td>{{ $item->saleTransaction->series ?? '' }}{{ $item->saleTransaction->invoice_no }}</td>
                <td>{{ Str::limit($item->saleTransaction->customer->name ?? 'N/A', 25) }}</td>
                <td>{{ $item->item_code }}</td>
                <td>{{ Str::limit($item->item_name, 30) }}</td>
                <td>{{ $item->batch_no ?? '' }}</td>
                <td class="text-end">{{ number_format($item->qty) }}</td>
                <td class="text-end">{{ number_format($item->free_qty ?? 0) }}</td>
                <td class="text-end">{{ number_format((float)($item->sale_rate ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($item->discount_amount ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($item->tax_amount ?? 0), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format((float)($item->net_amount ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="7" class="text-end">Grand Total ({{ $totals['items_count'] ?? 0 }} Items):</td>
                <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totals['discount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
