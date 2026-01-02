<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Sheet - {{ $dateFrom }} to {{ $dateTo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { font-size: 16px; margin-bottom: 5px; }
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
        .group-header { background-color: #fff3cd; font-weight: bold; }
        .group-footer { background-color: #f5f5f5; font-weight: bold; }
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
        .print-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>DISPATCH SHEET</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        <span><strong>Company:</strong> {{ $companyId ? ($companies->firstWhere('id', $companyId)->name ?? 'Selected') : 'All' }}</span>
        @if($remarks)<span><strong>Remarks:</strong> {{ $remarks }}</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 60px;">Item Code</th>
                <th>Item Name</th>
                <th style="width: 70px;">Packing</th>
                <th style="width: 70px;">Batch</th>
                <th class="text-end" style="width: 50px;">Qty</th>
                <th class="text-end" style="width: 50px;">Free</th>
                <th class="text-end" style="width: 60px;">Rate</th>
                <th class="text-end" style="width: 70px;">Amount</th>
                <th style="width: 60px;">Bill No</th>
                <th>Customer</th>
            </tr>
        </thead>
        <tbody>
            @php $srNo = 0; @endphp
            @foreach($groupedItems ?? [] as $companyName => $items)
                <tr class="group-header">
                    <td colspan="11">{{ $companyName ?: 'No Company' }} ({{ $items->count() }} Items, Qty: {{ number_format($items->sum('qty')) }})</td>
                </tr>
                @foreach($items as $item)
                @php $srNo++; @endphp
                <tr>
                    <td class="text-center">{{ $srNo }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->packing ?? '' }}</td>
                    <td>{{ $item->batch_no ?? '' }}</td>
                    <td class="text-end">{{ number_format($item->qty) }}</td>
                    <td class="text-end">{{ number_format($item->free_qty ?? 0) }}</td>
                    <td class="text-end">{{ number_format((float)($item->sale_rate ?? 0), 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format((float)($item->net_amount ?? 0), 2) }}</td>
                    <td>{{ $item->saleTransaction->series ?? '' }}{{ $item->saleTransaction->invoice_no ?? '' }}</td>
                    <td>{{ Str::limit($item->saleTransaction->customer->name ?? 'N/A', 25) }}</td>
                </tr>
                @endforeach
                <tr class="group-footer">
                    <td colspan="5" class="text-end">{{ $companyName ?: 'No Company' }} Total:</td>
                    <td class="text-end">{{ number_format($items->sum('qty')) }}</td>
                    <td class="text-end">{{ number_format($items->sum('free_qty')) }}</td>
                    <td></td>
                    <td class="text-end">{{ number_format($items->sum('net_amount'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="5" class="text-end">Grand Total ({{ number_format($totals['items_count'] ?? 0) }} Items):</td>
                <td class="text-end">{{ number_format($totals['qty'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
