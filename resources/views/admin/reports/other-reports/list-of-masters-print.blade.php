<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Masters - {{ $masterTypes[$selectedMaster] ?? 'Master' }} - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 10px; }
        .header { text-align: center; margin-bottom: 15px; padding: 10px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 18px; }
        .header .subtitle { font-size: 14px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 15px; text-align: center; font-size: 10px; }
        .address { font-size: 9px; color: #666; }
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
        <h2>List of Masters</h2>
        <div class="subtitle">{{ $masterTypes[$selectedMaster] ?? 'Master' }} - {{ $reportData->count() }} Records</div>
    </div>

    <table>
        <thead>
            @if($selectedMaster == 'COMPANY')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Short Name</th><th>Status</th></tr>
            @elseif($selectedMaster == 'CUSTOMER')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Area</th><th>Route</th><th>Status</th></tr>
            @elseif($selectedMaster == 'SUPPLIER')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>D/I</th><th>Status</th></tr>
            @elseif($selectedMaster == 'ITEM')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Company</th><th>Pack</th><th class="text-right">MRP</th><th>Status</th></tr>
            @elseif($selectedMaster == 'SALESMAN')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Status</th></tr>
            @elseif(in_array($selectedMaster, ['AREA', 'ROUTE', 'STATE']))
            <tr><th class="text-center" style="width:40px;">S.No</th><th>ID</th><th>Name</th><th>Alter Code</th><th>Status</th></tr>
            @elseif($selectedMaster == 'HSN')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>HSN Code</th><th>Description</th><th>GST %</th></tr>
            @elseif($selectedMaster == 'GENERAL_LEDGER')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Group</th><th>Status</th></tr>
            @elseif($selectedMaster == 'TRANSPORT')
            <tr><th class="text-center" style="width:40px;">S.No</th><th>Code</th><th>Name</th><th>Mobile</th><th>Status</th></tr>
            @endif
        </thead>
        <tbody>
            @foreach($reportData as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                @if($selectedMaster == 'COMPANY')
                <td>{{ $record->code ?? $record->id }}</td>
                <td>{{ $record->name }}@if($printAddress && $record->address)<br><span class="address">{{ $record->address }}</span>@endif</td>
                <td>{{ $record->short_name ?? '' }}</td>
                <td>{{ $record->status }}</td>
                @elseif($selectedMaster == 'CUSTOMER')
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}@if($printAddress && $record->address)<br><span class="address">{{ $record->address }}</span>@endif</td>
                <td>{{ $record->mobile }}</td>
                <td>{{ $record->area_name }}</td>
                <td>{{ $record->route_name }}</td>
                <td>{{ $record->status }}</td>
                @elseif($selectedMaster == 'SUPPLIER')
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}@if($printAddress && $record->address)<br><span class="address">{{ $record->address }}</span>@endif</td>
                <td>{{ $record->mobile }}</td>
                <td>{{ $record->direct_indirect }}</td>
                <td>{{ $record->status }}</td>
                @elseif($selectedMaster == 'ITEM')
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->company_name ?? '' }}</td>
                <td>{{ $record->pack ?? '' }}</td>
                <td class="text-right">{{ number_format($record->mrp ?? 0, 2) }}</td>
                <td>{{ $record->status }}</td>
                @elseif($selectedMaster == 'SALESMAN')
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}@if($printAddress && $record->address)<br><span class="address">{{ $record->address }}</span>@endif</td>
                <td>{{ $record->mobile }}</td>
                <td>{{ $record->status }}</td>
                @elseif(in_array($selectedMaster, ['AREA', 'ROUTE', 'STATE']))
                <td>{{ $record->id }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->alter_code ?? '' }}</td>
                <td>{{ $record->status ?? 'A' }}</td>
                @elseif($selectedMaster == 'HSN')
                <td>{{ $record->hsn_code }}</td>
                <td>{{ $record->description ?? '' }}</td>
                <td>{{ $record->gst_rate ?? '' }}%</td>
                @elseif($selectedMaster == 'GENERAL_LEDGER')
                <td>{{ $record->code }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->group_name ?? '' }}</td>
                <td>{{ $record->status }}</td>
                @elseif($selectedMaster == 'TRANSPORT')
                <td>{{ $record->code ?? $record->id }}</td>
                <td>{{ $record->name }}@if($printAddress && $record->address)<br><span class="address">{{ $record->address }}</span>@endif</td>
                <td>{{ $record->mobile ?? '' }}</td>
                <td>{{ $record->status }}</td>
                @endif
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
