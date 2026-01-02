<!DOCTYPE html>
<html>
<head>
    <title>Company Wise Purchase - All Company Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #721c24; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #343a40; color: #fff; font-weight: bold; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e0e0e0; font-weight: bold; }
        @media print { body { margin: 0; } @page { margin: 10mm; } .no-print { display: none; } }
        .print-btn { position: fixed; top: 10px; right: 10px; padding: 8px 16px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">Print</button>
    
    <div class="header">
        <h3>Company Wise Purchase - All Company</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th>Company Name</th>
                <th class="text-right">Total Items</th>
                <th class="text-right">Total Qty</th>
                <th class="text-right">Free Qty</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0;
                $grandItems = 0; $grandQty = 0; $grandFree = 0;
                $grandAmount = 0; $grandTax = 0; $grandNet = 0;
            @endphp
            @forelse($companies ?? [] as $company)
            @php
                $grandItems += $company->total_items;
                $grandQty += $company->total_qty;
                $grandFree += $company->total_free_qty;
                $grandAmount += $company->total_amount;
                $grandTax += $company->total_tax;
                $grandNet += $company->total_net;
            @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $company->company_name ?? 'N/A' }}</td>
                <td class="text-right">{{ $company->total_items }}</td>
                <td class="text-right">{{ number_format($company->total_qty, 2) }}</td>
                <td class="text-right">{{ number_format($company->total_free_qty, 2) }}</td>
                <td class="text-right">{{ number_format($company->total_amount, 2) }}</td>
                <td class="text-right">{{ number_format($company->total_tax, 2) }}</td>
                <td class="text-right">{{ number_format($company->total_net, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">Grand Total:</td>
                <td class="text-right">{{ $grandItems }}</td>
                <td class="text-right">{{ number_format($grandQty, 2) }}</td>
                <td class="text-right">{{ number_format($grandFree, 2) }}</td>
                <td class="text-right">{{ number_format($grandAmount, 2) }}</td>
                <td class="text-right">{{ number_format($grandTax, 2) }}</td>
                <td class="text-right">{{ number_format($grandNet, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
