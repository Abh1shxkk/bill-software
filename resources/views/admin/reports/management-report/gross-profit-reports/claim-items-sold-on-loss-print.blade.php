<!DOCTYPE html>
<html>
<head>
    <title>Claim Items - Sold on Loss - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #333; font-style: italic; font-family: 'Times New Roman', serif; }
        .header p { margin: 3px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px 5px; }
        th { background-color: #f0f0f0; font-weight: bold; font-size: 10px; }
        td { font-size: 10px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-danger { color: red; }
        .total-row { background-color: #e9ecef; font-weight: bold; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        .filters span { margin-right: 15px; }
        @media print {
            body { margin: 0; }
            @page { margin: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>Claim Items - Sold on Loss</h3>
        <p>Period: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-d'))->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        @if($request->company_id)<span>Company: {{ $companyName ?? 'All' }}</span>@endif
        @if($request->supplier_id)<span>Supplier: {{ $supplierName ?? 'All' }}</span>@endif
        @if($request->min_loss_percent)<span>Min Loss %: {{ $request->min_loss_percent }}</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">S.No</th>
                <th style="width: 28%;">Item Name</th>
                <th style="width: 17%;">Company</th>
                <th class="text-center" style="width: 8%;">Qty</th>
                <th class="text-end" style="width: 12%;">Sale Amt</th>
                <th class="text-end" style="width: 12%;">Cost Amt</th>
                <th class="text-end" style="width: 10%;">Loss Amt</th>
                <th class="text-end" style="width: 8%;">Loss %</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalSale = 0;
                $totalCost = 0;
                $totalLoss = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php
                $totalQty += $row['qty'];
                $totalSale += $row['sale_amount'];
                $totalCost += $row['cost_amount'];
                $totalLoss += $row['loss_amount'];
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cost_amount'], 2) }}</td>
                <td class="text-end text-danger">{{ number_format($row['loss_amount'], 2) }}</td>
                <td class="text-end text-danger">{{ number_format($row['loss_percent'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end">Total:</td>
                <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                <td class="text-end">{{ number_format($totalCost, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($totalLoss, 2) }}</td>
                <td class="text-end text-danger">{{ $totalSale > 0 ? number_format(abs($totalLoss) / $totalSale * 100, 2) : '0.00' }}%</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 9px; text-align: right;">
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
