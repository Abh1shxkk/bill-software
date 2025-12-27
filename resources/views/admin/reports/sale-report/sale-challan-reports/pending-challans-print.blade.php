<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Challans - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #dc3545; padding-bottom: 5px; }
        .header h2 { font-size: 16px; margin-bottom: 3px; color: #dc3545; }
        .header p { font-size: 10px; color: #666; }
        .filters { font-size: 9px; margin-bottom: 8px; padding: 5px; background: #f5f5f5; }
        .summary { display: flex; justify-content: space-around; margin-bottom: 10px; padding: 8px; background: #fff3cd; border: 1px solid #ffc107; }
        .summary-item { text-align: center; }
        .summary-item .label { font-size: 9px; color: #666; }
        .summary-item .value { font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
        th { background: #dc3545; color: #fff; font-size: 10px; }
        td { font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        tfoot td { background: #dc3545; color: #fff; font-weight: bold; }
        .footer { margin-top: 10px; font-size: 9px; text-align: center; color: #666; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 20px; cursor: pointer;">🖨️ Print</button>
        <button onclick="window.close()" style="padding: 8px 20px; cursor: pointer; margin-left: 10px;">✖ Close</button>
    </div>

    <div class="header">
        <h2>LIST OF PENDING CHALLANS</h2>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</p>
    </div>

    <div class="filters">
        @if($customerId) Party: {{ $customers->find($customerId)->name ?? 'N/A' }} | @endif
        @if($salesmanId) Salesman: {{ $salesmen->find($salesmanId)->name ?? 'N/A' }} | @endif
        @if($routeId) Route: {{ $routes->find($routeId)->name ?? 'N/A' }} | @endif
        @if($areaId) Area: {{ $areas->find($areaId)->name ?? 'N/A' }} | @endif
        @if($flag) Flag: {{ $flag == 'C' ? 'Cash' : 'Credit' }} @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Pending Challans</div>
            <div class="value">{{ number_format($totals['count'] ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Amount</div>
            <div class="value">₹{{ number_format($totals['net_amount'] ?? 0, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">#</th>
                <th style="width: 70px;">Chaln.Date</th>
                <th style="width: 70px;">Chln.No</th>
                <th>Party Name</th>
                <th class="text-end" style="width: 90px;">Amount</th>
                <th style="width: 70px;">Inv.Date</th>
                <th style="width: 70px;">Inv.No</th>
                <th class="text-center" style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($challans ?? [] as $index => $challan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $challan->challan_date ? $challan->challan_date->format('d-m-Y') : '' }}</td>
                <td>{{ $challan->challan_no }}</td>
                <td>{{ $challan->customer->name ?? 'N/A' }}</td>
                <td class="text-end fw-bold">{{ number_format($challan->net_amount, 2) }}</td>
                <td>{{ $challan->saleTransaction ? $challan->saleTransaction->sale_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $challan->saleTransaction->invoice_no ?? '-' }}</td>
                <td class="text-center">Pending</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No pending challans found</td>
            </tr>
            @endforelse
        </tbody>
        @if(($totals['count'] ?? 0) > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-end">Total ({{ number_format($totals['count'] ?? 0) }} Challans):</td>
                <td class="text-end">{{ number_format($totals['net_amount'] ?? 0, 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Printed on: {{ now()->format('d-m-Y h:i A') }}
    </div>
</body>
</html>
