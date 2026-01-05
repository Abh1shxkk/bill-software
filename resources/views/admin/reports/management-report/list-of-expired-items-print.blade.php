<!DOCTYPE html>
<html>
<head>
    <title>List of Expired Items - Print</title>
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
        <h3>List of Expired Items</h3>
        <p>Expiry Period: {{ $request->from_expiry ?? '01/90' }} To {{ $request->to_expiry ?? date('m/y') }}</p>
    </div>

    <div class="filters">
        @if($request->company_id)<span>Company: Selected</span>@endif
        @if($request->supplier_id)<span>Supplier: Selected</span>@endif
        @if($request->location)<span>Location: {{ $request->location }}</span>@endif
        @if($request->division)<span>Division: {{ $request->division }}</span>@endif
        <span>Value on: {{ strtoupper($request->value_on ?? 'S') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">S.No</th>
                <th style="width: 30%;">Item Name</th>
                <th style="width: 18%;">Company</th>
                <th style="width: 12%;">Batch No</th>
                <th class="text-center" style="width: 8%;">Expiry</th>
                <th class="text-end" style="width: 8%;">Qty</th>
                <th class="text-end" style="width: 9%;">Rate</th>
                <th class="text-end" style="width: 10%;">Value</th>
            </tr>
        </thead>
        <tbody>
            @php $totalValue = 0; $totalQty = 0; @endphp
            @foreach($reportData ?? [] as $index => $row)
            @php 
                $totalValue += $row['value']; 
                $totalQty += $row['qty'];
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $row['item_name'] }}</td>
                <td>{{ $row['company_name'] }}</td>
                <td>{{ $row['batch_no'] }}</td>
                <td class="text-center">{{ $row['expiry_date'] }}</td>
                <td class="text-end">{{ number_format($row['qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['rate'], 2) }}</td>
                <td class="text-end">{{ number_format($row['value'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">Total:</td>
                <td class="text-end">{{ number_format($totalQty, 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($totalValue, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 15px; font-size: 9px; text-align: right;">
        Printed on: {{ now()->format('d-M-Y h:i A') }} | Total Items: {{ count($reportData ?? []) }}
    </div>
</body>
</html>
