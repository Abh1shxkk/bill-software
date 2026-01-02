<!DOCTYPE html>
<html>
<head>
    <title>Supplier - Bill Wise Purchase Report</title>
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
        @media print { 
            body { margin: 0; } 
            @page { margin: 10mm; } 
            .no-print { display: none; }
        }
        .print-btn { 
            position: fixed; top: 10px; right: 10px; 
            padding: 8px 16px; background: #007bff; color: #fff; 
            border: none; cursor: pointer; border-radius: 4px; 
        }
    </style>
</head>
<body onload="window.print()">
    <button class="print-btn no-print" onclick="window.print()">Print</button>
    
    <div class="header">
        <h3>Supplier - Bill Wise Purchase</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        @if($supplierName ?? false)
        <p>Supplier: {{ $supplierName }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">S.No</th>
                <th>Date</th>
                <th>Bill No</th>
                <th>Supplier Name</th>
                <th>Type</th>
                <th class="text-right">Gross Amt</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Tax Amt</th>
                <th class="text-right">Net Amount</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0;
                $grandGrossAmt = 0;
                $grandDiscount = 0;
                $grandTaxAmt = 0;
                $grandNetAmt = 0;
            @endphp
            @forelse($bills ?? [] as $bill)
            @php
                $grandGrossAmt += $bill->nt_amount ?? 0;
                $grandDiscount += $bill->dis_amount ?? 0;
                $grandTaxAmt += $bill->tax_amount ?? 0;
                $grandNetAmt += $bill->net_amount ?? 0;
            @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $bill->bill_date ? $bill->bill_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $bill->bill_no ?? '-' }}</td>
                <td>{{ $bill->supplier->name ?? 'N/A' }}</td>
                <td>{{ $bill->cash_flag == 'Y' ? 'Cash' : 'Credit' }}</td>
                <td class="text-right">{{ number_format($bill->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($bill->dis_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($bill->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($bill->net_amount ?? 0, 2) }}</td>
                <td>{{ $bill->due_date ? $bill->due_date->format('d-m-Y') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Grand Total:</td>
                <td class="text-right">{{ number_format($grandGrossAmt, 2) }}</td>
                <td class="text-right">{{ number_format($grandDiscount, 2) }}</td>
                <td class="text-right">{{ number_format($grandTaxAmt, 2) }}</td>
                <td class="text-right">{{ number_format($grandNetAmt, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
