<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase with Item Details - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #1565c0; padding-bottom: 10px; }
        .header h2 { color: #1565c0; font-size: 16px; margin-bottom: 5px; font-style: italic; }
        .header .date-range { font-size: 11px; color: #666; }
        .filters { margin-bottom: 10px; font-size: 9px; color: #666; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px; background: #f5f5f5; border-radius: 4px; }
        .summary-item { text-align: center; }
        .summary-item .label { font-size: 9px; color: #666; }
        .summary-item .value { font-size: 12px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 3px 5px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; font-size: 9px; }
        td { font-size: 9px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        tfoot tr { background-color: #333; color: white; font-weight: bold; }
        .print-info { text-align: right; font-size: 8px; color: #999; margin-top: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 8mm; size: landscape; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #1565c0; color: white; border: none; cursor: pointer; border-radius: 4px;">
            🖨️ Print Report
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 4px; margin-left: 5px;">
            ✕ Close
        </button>
    </div>

    <div class="header">
        <h2>PURCHASE WITH ITEM DETAILS</h2>
        <div class="date-range">
            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}
        </div>
    </div>

    <div class="filters">
        <strong>Filters:</strong>
        Type: {{ $purchaseReplacement == 'P' ? 'Purchase' : 'Replacement' }} |
        Mode: {{ $selectiveAll == 'S' ? 'Selective' : 'All' }}
        @if($supplierId && $selectiveAll == 'S')
            | Supplier: {{ $suppliers->firstWhere('supplier_id', $supplierId)->name ?? 'Selected' }}
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Bills</div>
            <div class="value">{{ number_format($totals['bills'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Items</div>
            <div class="value">{{ number_format($totals['items'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Qty</div>
            <div class="value">{{ number_format($totals['quantity'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Free Qty</div>
            <div class="value">{{ number_format($totals['free_qty'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Tax Amount</div>
            <div class="value">₹{{ number_format($totals['tax_amount'] ?? 0, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Net Amount</div>
            <div class="value">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">#</th>
                <th style="width: 60px;">Date</th>
                <th style="width: 70px;">Bill No</th>
                <th>Supplier</th>
                <th>Item Name</th>
                <th style="width: 50px;">Pack</th>
                <th style="width: 60px;">Batch</th>
                <th style="width: 45px;">Expiry</th>
                <th class="text-end" style="width: 40px;">Qty</th>
                <th class="text-end" style="width: 35px;">Free</th>
                <th class="text-end" style="width: 55px;">Rate</th>
                <th class="text-end" style="width: 55px;">MRP</th>
                <th class="text-end" style="width: 40px;">Disc%</th>
                <th class="text-end" style="width: 40px;">GST%</th>
                <th class="text-end" style="width: 65px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases ?? [] as $index => $purchase)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $purchase->bill_date ? $purchase->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $purchase->bill_no }}</td>
                <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                <td>{{ $purchase->item_name ?? '-' }}</td>
                <td>{{ $purchase->packing ?? '-' }}</td>
                <td>{{ $purchase->batch_no ?? '-' }}</td>
                <td>{{ $purchase->expiry_date ?? '-' }}</td>
                <td class="text-end">{{ number_format($purchase->quantity ?? 0) }}</td>
                <td class="text-end">{{ number_format($purchase->free_qty ?? 0) }}</td>
                <td class="text-end">{{ number_format($purchase->rate ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($purchase->mrp ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($purchase->discount_percent ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($purchase->gst_percent ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($purchase->net_amount ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="15" class="text-center" style="padding: 20px;">No records found</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($purchases ?? []) > 0)
        <tfoot>
            <tr>
                <td colspan="8" class="text-end fw-bold">Total:</td>
                <td class="text-end">{{ number_format($totals['quantity'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['free_qty'] ?? 0) }}</td>
                <td colspan="4"></td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="print-info">
        Printed on: {{ now()->format('d-m-Y H:i:s') }}
    </div>
</body>
</html>
