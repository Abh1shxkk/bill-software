<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Return List - {{ $dateFrom }} to {{ $dateTo }}</title>
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
        .grand-total { background-color: #333; color: #fff; font-weight: bold; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Print</button>

    <div class="header">
        <h2>SALE RETURN LIST</h2>
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
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">SR No</th>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th>Salesman</th>
                <th class="text-end" style="width: 50px;">Items</th>
                <th class="text-end" style="width: 70px;">NT Amt</th>
                <th class="text-end" style="width: 60px;">Disc</th>
                <th class="text-end" style="width: 60px;">Tax</th>
                <th class="text-end" style="width: 80px;">Net Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returns ?? [] as $index => $return)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $return->return_date->format('d-m-Y') }}</td>
                <td>{{ $return->series ?? '' }}{{ $return->sr_no }}</td>
                <td>{{ $return->customer->code ?? '' }}</td>
                <td>{{ $return->customer->name ?? 'N/A' }}</td>
                <td>{{ $return->salesman->name ?? '' }}</td>
                <td class="text-end">{{ $return->items->sum('qty') }}</td>
                <td class="text-end">{{ number_format((float)($return->nt_amount ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($return->dis_amount ?? 0), 2) }}</td>
                <td class="text-end">{{ number_format((float)($return->tax_amount ?? 0), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format((float)($return->net_amount ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="grand-total">
                <td colspan="6" class="text-end">Grand Total ({{ $totals['count'] ?? 0 }} Returns):</td>
                <td class="text-end">{{ number_format($totals['items_count'] ?? 0) }}</td>
                <td class="text-end">{{ number_format($totals['nt_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['dis_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['tax_amount'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
