<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailing Labels - Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .no-print { padding: 10px; background: #f0f0f0; margin-bottom: 10px; }
        .labels-container { 
            display: flex; 
            flex-wrap: wrap; 
            padding: 10px;
        }
        .label {
            width: 63.5mm;
            height: 38.1mm;
            border: 1px dashed #ccc;
            padding: 5mm;
            margin: 2mm;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .label-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
        }
        .label-address {
            font-size: 10px;
            line-height: 1.4;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .label { border: none; }
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
        <span style="margin-left: 20px;">Total Labels: {{ $reportData->count() }}</span>
    </div>

    <div class="labels-container">
        @foreach($reportData as $record)
        <div class="label">
            <div class="label-name">{{ $record->name }}</div>
            <div class="label-address">
                @if($listType == 'P')
                    {{ $record->address_office }}<br>
                    @if($record->tel_office)Tel: {{ $record->tel_office }}<br>@endif
                @else
                    {{ $record->address }}<br>
                    @if(isset($record->address_line2) && $record->address_line2){{ $record->address_line2 }}<br>@endif
                    @if(isset($record->city) && $record->city){{ $record->city }} @endif
                    @if(isset($record->pin_code) && $record->pin_code)- {{ $record->pin_code }}<br>@endif
                @endif
                @if($record->mobile)Mob: {{ $record->mobile }}@endif
            </div>
        </div>
        @endforeach
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
