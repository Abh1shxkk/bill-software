<!DOCTYPE html>
<html>
<head>
    <title>Supplier Wise Purchase Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #721c24; font-style: italic; }
        .header p { margin: 2px 0; font-size: 10px; }
        .report-info { margin-bottom: 10px; font-size: 10px; }
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
        <h3>Supplier Wise Purchase</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        <p>Report Type: 
            @switch($reportType)
                @case('1') Purchase @break
                @case('2') Purchase Return @break
                @case('3') Debit Note @break
                @case('4') Credit Note @break
                @case('5') Consolidated Purchase Book @break
                @default Purchase
            @endswitch
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th>Supplier Name</th>
                <th>City</th>
                <th>Mobile</th>
                <th class="text-right">Total Bills</th>
                <th class="text-right">Total Amount</th>
                <th class="text-right">Tax Amount</th>
                <th class="text-right">Net Payable</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sno = 0; 
                $grandTotalBills = 0;
                $grandTotalAmount = 0;
                $grandTaxAmount = 0;
                $grandNetPayable = 0;
            @endphp
            @forelse($suppliers ?? [] as $data)
            @php
                $grandTotalBills += $data->total_bills;
                $grandTotalAmount += $data->total_amount;
                $grandTaxAmount += $data->tax_amount;
                $grandNetPayable += $data->net_payable;
            @endphp
            <tr>
                <td class="text-center">{{ ++$sno }}</td>
                <td>{{ $data->supplier->name ?? 'N/A' }}</td>
                <td>{{ $data->supplier->address ?? '-' }}</td>
                <td>{{ $data->supplier->mobile ?? '-' }}</td>
                <td class="text-right">{{ $data->total_bills }}</td>
                <td class="text-right">{{ number_format($data->total_amount, 2) }}</td>
                <td class="text-right">{{ number_format($data->tax_amount, 2) }}</td>
                <td class="text-right">{{ number_format($data->net_payable, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Grand Total:</td>
                <td class="text-right">{{ $grandTotalBills }}</td>
                <td class="text-right">{{ number_format($grandTotalAmount, 2) }}</td>
                <td class="text-right">{{ number_format($grandTaxAmount, 2) }}</td>
                <td class="text-right">{{ number_format($grandNetPayable, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
