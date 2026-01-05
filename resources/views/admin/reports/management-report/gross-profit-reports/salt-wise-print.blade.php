<!DOCTYPE html>
<html>
<head>
    <title>Salt wise Gross Profit - Print</title>
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
        <h3>Salt wise Gross Profit</h3>
        <p>Period: {{ \Carbon\Carbon::parse($request->from_date ?? date('Y-m-d'))->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($request->to_date ?? date('Y-m-d'))->format('d-M-Y') }}</p>
    </div>

    <div class="filters">
        @if($request->salt_name_1)<span>Salt 1: {{ $request->salt_name_1 }}</span>@endif
        @if($request->salt_name_2)<span>Salt 2: {{ $request->salt_name_2 }}</span>@endif
        @if($request->salt_name_3)<span>Salt 3: {{ $request->salt_name_3 }}</span>@endif
        @if($request->salt_name_4)<span>Salt 4: {{ $request->salt_name_4 }}</span>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">S.No</th>
                <th style="width: 34%;">Salt Name</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-end" style="width: 15%;">Sale Amt</th>
                <th class="text-end" style="width: 15%;">Pur Amt</th>
                <th class="text-end" style="width: 12%;">GP Amt</th>
                <th class="text-end" style="width: 8%;">GP %</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalSale = 0;
                $totalPurchase = 0;
                $totalGP = 0;
            @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php
                $totalQty += $row['qty'];
                $totalSale += $row['sale_amount'];
                $totalPurchase += $row['purchase_amount'];
                $totalGP += $row['gp_amount'];
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['salt_name'] }}</td>
                <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sale_amount'], 2) }}</td>
                <td class="text-end">{{ number_format($row['purchase_amount'], 2) }}</td>
                <td class="text-end {{ $row['gp_amount'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_amount'], 2) }}</td>
                <td class="text-end {{ $row['gp_percent'] < 0 ? 'text-danger' : '' }}">{{ number_format($row['gp_percent'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-end">Total:</td>
                <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                <td class="text-end">{{ number_format($totalSale, 2) }}</td>
                <td class="text-end">{{ number_format($totalPurchase, 2) }}</td>
                <td class="text-end {{ $totalGP < 0 ? 'text-danger' : '' }}">{{ number_format($totalGP, 2) }}</td>
                <td class="text-end">{{ $totalSale > 0 ? number_format($totalGP / $totalSale * 100, 2) : '0.00' }}%</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 9px; text-align: right;">
        Printed on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
