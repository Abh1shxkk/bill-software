<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Printing - From Item</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 10px; padding: 5mm; }
        
        .no-print { margin-bottom: 10px; }
        .no-print button { padding: 5px 15px; margin-right: 5px; cursor: pointer; }
        
        .header { text-align: center; margin-bottom: 10px; padding: 8px; background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif; }
        .header h2 { margin: 0; font-size: 16px; }
        
        .labels-container {
            display: flex;
            flex-wrap: wrap;
            gap: 3mm;
            justify-content: flex-start;
        }
        
        .label {
            width: 50mm;
            height: 25mm;
            border: 1px solid #333;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        
        .label-name {
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .label-pack {
            font-size: 7px;
            text-align: center;
            color: #666;
        }
        
        .barcode-container {
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .barcode {
            font-family: 'Libre Barcode 39', 'Free 3 of 9', monospace;
            font-size: 28px;
            letter-spacing: 2px;
        }
        
        .barcode-text {
            font-size: 7px;
            text-align: center;
            font-family: monospace;
        }
        
        .label-price {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        
        .footer { margin-top: 10px; text-align: center; font-size: 9px; color: #666; }
        
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .header { display: none; }
            .footer { display: none; }
            .label { border: 1px dashed #999; }
        }
    </style>
    {{-- Include barcode font --}}
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>Barcode Labels - From Item</h2>
        <small>Total Labels: {{ $reportData->sum('print_qty') }}</small>
    </div>

    <div class="labels-container">
        @foreach($reportData as $item)
            @for($i = 0; $i < $item->print_qty; $i++)
            <div class="label">
                <div class="label-name" title="{{ $item->name }}">{{ Str::limit($item->name, 30) }}</div>
                <div class="label-pack">{{ $item->packing }}</div>
                <div class="barcode-container">
                    @if($item->bar_code)
                        <div>
                            <div class="barcode">*{{ $item->bar_code }}*</div>
                            <div class="barcode-text">{{ $item->bar_code }}</div>
                        </div>
                    @else
                        <div>
                            <div class="barcode">*{{ str_pad($item->id, 8, '0', STR_PAD_LEFT) }}*</div>
                            <div class="barcode-text">{{ str_pad($item->id, 8, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    @endif
                </div>
                <div class="label-price">MRP: ₹{{ number_format($item->mrp ?? 0, 2) }}</div>
            </div>
            @endfor
        @endforeach
    </div>

    @if($reportData->count() == 0)
    <div style="text-align: center; padding: 20px; color: #666;">
        No items selected for printing.
    </div>
    @endif

    <div class="footer">
        Generated on: {{ now()->format('d-m-Y H:i:s') }}
    </div>

    <script>
        window.onload = function() {
            @if($reportData->count() > 0)
            window.print();
            @endif
        }
    </script>
</body>
</html>
