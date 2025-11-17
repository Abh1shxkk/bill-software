<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form - {{ $orderNo }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h3 {
            margin: 0;
            font-weight: bold;
            font-size: 14pt;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 10pt;
        }
        
        .header h4 {
            margin: 10px 0;
            text-decoration: underline;
            font-size: 12pt;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .border-dashed {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
        }
        
        th, td {
            padding: 8px;
        }
        
        th {
            text-align: left;
            font-weight: bold;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            
            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>PRABHAT MEDICINE COMPANY</h3>
        <p>SHOP NO. 10 & 18, CENTRAL PLAZA KHAIR NAGAR, MEERUT CITY</p>
        <h4>ORDER FORM</h4>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div><strong>Name :</strong> {{ strtoupper($supplier->name) }}</div>
            <div><strong>Page:</strong> 1</div>
        </div>
        <div class="info-row">
            <div><strong>Date:</strong> {{ now()->format('d/m/Y') }}</div>
            <div><strong>Order No:</strong> {{ $orderNo }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr class="border-dashed">
                <th class="text-left">ITEM</th>
                <th class="text-center">PACK</th>
                <th class="text-center">QTY</th>
                <th class="text-center">FQTY</th>
                @if($withTotal)
                <th class="text-center">COST</th>
                <th class="text-center">TOTAL</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
            @endphp
            @foreach($orders as $order)
            @php
                $itemCost = $order->item->cost ?? 0;
                $totalQty = ($order->order_qty ?? 0) + ($order->free_qty ?? 0);
                $lineTotal = $itemCost * $totalQty;
                $grandTotal += $lineTotal;
            @endphp
            <tr>
                <td class="text-left">{{ $order->item->name ?? '---' }}</td>
                <td class="text-center">{{ $order->item->packing ?? '---' }}</td>
                <td class="text-center">{{ $order->order_qty }}</td>
                <td class="text-center">{{ $order->free_qty }}</td>
                @if($withTotal)
                <td class="text-center">₹{{ number_format($itemCost, 2) }}</td>
                <td class="text-center">₹{{ number_format($lineTotal, 2) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($withTotal)
            <tr class="border-dashed">
                <td colspan="4"></td>
                <td class="text-center"><strong>GRAND TOTAL:</strong></td>
                <td class="text-center"><strong>₹{{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
            @else
            <tr class="border-dashed">
                <td colspan="4"></td>
            </tr>
            @endif
        </tfoot>
    </table>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
