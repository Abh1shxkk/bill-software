<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Printing - From Batches</title>
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
            height: 30mm;
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
        
        .label-batch {
            font-size: 7px;
            text-align: center;
            color: #333;
        }
        
        .label-expiry {
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
            font-size: 24px;
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
        <h2>Barcode Labels - From Batches</h2>
        <small>Total Labels: {{ $reportData->sum('print_qty') }}</small>
    </div>

    <div class="labels-container">
        @foreach($reportData as $batch)
            @for($i = 0; $i < $batch->print_qty; $i++)
            <div class="label">
                <div class="label-name" title="{{ $batch->item_name }}">{{ Str::limit($batch->item_name, 25) }}</div>
                <div class="label-batch">Batch: {{ $batch->batch_no ?? 'N/A' }}</div>
                <div class="label-expiry">Exp: {{ $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->format('m/Y') : 'N/A' }}</div>
                <div class="barcode-container">
                    @if($batch->bc)
                        <div>
                            <div class="barcode">*{{ $batch->bc }}*</div>
                            <div class="barcode-text">{{ $batch->bc }}</div>
                        </div>
                    @else
                        <div>
                            <div class="barcode">*{{ str_pad($batch->id, 10, '0', STR_PAD_LEFT) }}*</div>
                            <div class="barcode-text">{{ str_pad($batch->id, 10, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    @endif
                </div>
                <div class="label-price">MRP: ₹{{ number_format($batch->mrp ?? 0, 2) }}</div>
            </div>
            @endfor
        @endforeach
    </div>

    @if($reportData->count() == 0)
    <div style="text-align: center; padding: 20px; color: #666;">
        No batches selected for printing.
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
