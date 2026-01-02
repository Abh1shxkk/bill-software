<!DOCTYPE html>
<html>
<head>
    <title>Monthly Purchase/Sale Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #dc3545; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px 8px; }
        th { background: #f8d7da; font-weight: bold; text-align: left; color: #721c24; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f5c6cb; font-weight: bold; }
        .text-danger { color: #dc3545; }
        .text-info { color: #0dcaf0; }
        .text-warning { color: #ffc107; }
        @media print { body { margin: 0; } @page { margin: 10mm; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Monthly Purchase/Sale Summary Report</h3>
        <p>Period: {{ $monthFrom ? \Carbon\Carbon::createFromFormat('Y-m', $monthFrom)->format('M-Y') : '' }} to {{ $monthTo ? \Carbon\Carbon::createFromFormat('Y-m', $monthTo)->format('M-Y') : '' }}</p>
        @if($companyId ?? false)
            <p>Company: {{ $suppliers->firstWhere('supplier_id', $companyId)->name ?? 'Selected' }}</p>
        @endif
        <p>Show DN/CN: {{ $showDnCn ?? 'Y' }} | Show Br.Exp: {{ $showBrExp ?? 'Y' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 90px;">Month</th>
                <th class="text-center" style="width: 60px;">Bills</th>
                <th class="text-right" style="width: 100px;">Purchase Amt</th>
                <th class="text-right" style="width: 90px;">Return Amt</th>
                @if(($showDnCn ?? 'Y') == 'Y')
                <th class="text-right" style="width: 85px;">DN Amt</th>
                <th class="text-right" style="width: 85px;">CN Amt</th>
                @endif
                <th class="text-right" style="width: 85px;">Tax</th>
                <th class="text-right" style="width: 100px;">Net Purchase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlySummary ?? [] as $monthName => $data)
            <tr>
                <td>{{ $monthName }}</td>
                <td class="text-center">{{ $data['bills'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($data['purchase'] ?? 0, 2) }}</td>
                <td class="text-right text-danger">{{ number_format($data['return'] ?? 0, 2) }}</td>
                @if(($showDnCn ?? 'Y') == 'Y')
                <td class="text-right text-info">{{ number_format($data['dn'] ?? 0, 2) }}</td>
                <td class="text-right text-warning">{{ number_format($data['cn'] ?? 0, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($data['tax'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($data['net'] ?? 0, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="{{ ($showDnCn ?? 'Y') == 'Y' ? 8 : 6 }}" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Grand Total</td>
                <td class="text-center">{{ $totals['bills'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($totals['purchase'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['return'] ?? 0, 2) }}</td>
                @if(($showDnCn ?? 'Y') == 'Y')
                <td class="text-right">{{ number_format($totals['dn'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['cn'] ?? 0, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['net'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
