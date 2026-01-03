<!DOCTYPE html>
<html>
<head>
    <title>Ageing Analysis - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        tfoot th { background-color: #e0e0e0; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>AGEING ANALYSIS</h2>
        <p>As On: {{ request('as_on_date', date('Y-m-d')) }}</p>
    </div>

    @php
        $slab1 = request('slab1', 30);
        $slab2 = request('slab2', 60);
        $slab3 = request('slab3', 90);
        $slab4 = request('slab4', 120);
    @endphp

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Party Name</th>
                <th>Bill No</th>
                <th>Date</th>
                <th class="text-end">0-{{ $slab1 }}</th>
                <th class="text-end">{{ $slab1+1 }}-{{ $slab2 }}</th>
                <th class="text-end">{{ $slab2+1 }}-{{ $slab3 }}</th>
                <th class="text-end">{{ $slab3+1 }}-{{ $slab4 }}</th>
                <th class="text-end">{{ $slab4+1 }}+</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $totals = [0, 0, 0, 0, 0, 0]; @endphp
            @forelse($reportData as $index => $item)
            @php
                $days = $item->days_overdue ?? 0;
                $amt = $item->due_amount ?? 0;
                $col = 0;
                if ($days <= $slab1) $col = 0;
                elseif ($days <= $slab2) $col = 1;
                elseif ($days <= $slab3) $col = 2;
                elseif ($days <= $slab4) $col = 3;
                else $col = 4;
                $totals[$col] += $amt;
                $totals[5] += $amt;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->customer->name ?? '' }}</td>
                <td>{{ $item->invoice_no ?? '' }}</td>
                <td>{{ $item->sale_date ? date('d-M-y', strtotime($item->sale_date)) : '' }}</td>
                <td class="text-end">{{ $col == 0 ? number_format($amt, 2) : '' }}</td>
                <td class="text-end">{{ $col == 1 ? number_format($amt, 2) : '' }}</td>
                <td class="text-end">{{ $col == 2 ? number_format($amt, 2) : '' }}</td>
                <td class="text-end">{{ $col == 3 ? number_format($amt, 2) : '' }}</td>
                <td class="text-end">{{ $col == 4 ? number_format($amt, 2) : '' }}</td>
                <td class="text-end">{{ number_format($amt, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">No records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th class="text-end">{{ number_format($totals[0], 2) }}</th>
                <th class="text-end">{{ number_format($totals[1], 2) }}</th>
                <th class="text-end">{{ number_format($totals[2], 2) }}</th>
                <th class="text-end">{{ number_format($totals[3], 2) }}</th>
                <th class="text-end">{{ number_format($totals[4], 2) }}</th>
                <th class="text-end">{{ number_format($totals[5], 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total Records: {{ $reportData->count() }} | Printed on: {{ date('d-M-Y H:i:s') }}</p>
    </div>
</body>
</html>
