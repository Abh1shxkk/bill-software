<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $listType == 'C' ? 'Customer' : 'Supplier' }} List - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .filters { margin-bottom: 10px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
        .total-row { font-weight: bold; background-color: #e0e0e0; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>{{ $listType == 'C' ? 'Customer' : 'Supplier' }} List</h2>
    </div>

    <div class="filters">
        <strong>Filters:</strong>
        @if($request->filled('tax_retail')) Tax/Retail: {{ $request->tax_retail }} | @endif
        @if($request->filled('status')) Status: {{ $request->status }} | @endif
        @if($request->filled('active_filter') && $request->active_filter != '3') 
            Active: {{ $request->active_filter == '1' ? 'Active Only' : 'Inactive Only' }} |
        @endif
        @if($request->filled('salesman') && $request->salesman != '00') Salesman: {{ $request->salesman }} | @endif
        @if($request->filled('area') && $request->area != '00') Area: {{ $request->area }} | @endif
        @if($request->filled('route') && $request->route != '00') Route: {{ $request->route }} | @endif
        @if($request->filled('state') && $request->state != '00') State: {{ $request->state }} | @endif
        <strong>Total Records: {{ $reportData->count() }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">S.No</th>
                <th style="width: 60px;">Code</th>
                <th>Name</th>
                <th>Address</th>
                <th style="width: 100px;">Mobile</th>
                <th style="width: 140px;">GST No</th>
                @if($listType == 'C')
                <th style="width: 80px;">Area</th>
                <th style="width: 80px;">Route</th>
                <th style="width: 100px;">Salesman</th>
                @endif
                <th class="text-center" style="width: 50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->address }}</td>
                <td>{{ $record->mobile }}</td>
                <td>{{ $listType == 'C' ? $record->gst_number : $record->gst_no }}</td>
                @if($listType == 'C')
                <td>{{ $record->area_name }}</td>
                <td>{{ $record->route_name }}</td>
                <td>{{ $record->sales_man_name }}</td>
                @endif
                <td class="text-center">{{ $record->status == 'A' ? 'A' : 'I' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
