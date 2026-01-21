<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaction->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #000;
        }
        
        .header .invoice-type {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            padding: 4px 8px;
            border: 1px solid #000;
        }
        
        .info-label {
            font-weight: bold;
            width: 30%;
            background-color: #f0f0f0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th {
            background-color: #333;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #000;
        }
        
        .items-table td {
            padding: 6px 4px;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .totals-section {
            float: right;
            width: 300px;
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row.grand-total {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #000;
        }
        
        .total-label {
            font-weight: bold;
        }
        
        .footer {
            clear: both;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
        }
        
        .scanned-receipt {
            page-break-before: always;
            text-align: center;
            padding: 20px;
        }
        
        .scanned-receipt h2 {
            margin-bottom: 15px;
            color: #333;
        }
        
        .scanned-receipt img {
            max-width: 100%;
            max-height: 800px;
            border: 2px solid #000;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Bill Software') }}</h1>
            <div class="invoice-type">TAX INVOICE</div>
            <div>Invoice No: <strong>{{ $transaction->invoice_no }}</strong></div>
            <div>Date: {{ $transaction->sale_date->format('d-m-Y') }}</div>
        </div>
        
        <!-- Customer & Transaction Info -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-cell info-label">Customer:</div>
                <div class="info-cell">{{ $transaction->customer->name ?? 'N/A' }}</div>
                <div class="info-cell info-label">Salesman:</div>
                <div class="info-cell">{{ $transaction->salesman->name ?? 'N/A' }}</div>
            </div>
            @if($transaction->customer && $transaction->customer->address)
            <div class="info-row">
                <div class="info-cell info-label">Address:</div>
                <div class="info-cell" colspan="3">{{ $transaction->customer->address }}</div>
            </div>
            @endif
            @if($transaction->remarks)
            <div class="info-row">
                <div class="info-cell info-label">Remarks:</div>
                <div class="info-cell" colspan="3">{{ $transaction->remarks }}</div>
            </div>
            @endif
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">SR</th>
                    <th style="width: 30%;">Item Name</th>
                    <th style="width: 10%;">Batch</th>
                    <th style="width: 8%;">Exp</th>
                    <th style="width: 7%;">Qty</th>
                    <th style="width: 10%;">Rate</th>
                    <th style="width: 8%;">Disc%</th>
                    <th style="width: 10%;">MRP</th>
                    <th style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->batch_no ?? '-' }}</td>
                    <td class="text-center">{{ $item->expiry_date ? date('m/y', strtotime($item->expiry_date)) : '-' }}</td>
                    <td class="text-right">{{ $item->qty }}{{ $item->free_qty > 0 ? ' + ' . $item->free_qty : '' }}</td>
                    <td class="text-right">{{ number_format($item->sale_rate, 2) }}</td>
                    <td class="text-center">{{ $item->discount_percent ?? '0' }}</td>
                    <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                    <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span>₹ {{ number_format($transaction->nt_amount, 2) }}</span>
            </div>
            @if($transaction->dis_amount > 0)
            <div class="total-row">
                <span class="total-label">Discount:</span>
                <span>₹ {{ number_format($transaction->dis_amount, 2) }}</span>
            </div>
            @endif
            @if($transaction->tax_amount > 0)
            <div class="total-row">
                <span class="total-label">Tax (GST):</span>
                <span>₹ {{ number_format($transaction->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span class="total-label">Grand Total:</span>
                <span>₹ {{ number_format($transaction->net_amount, 2) }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice.</p>
        </div>
    </div>
    
    <!-- Scanned Receipt (if exists) -->
    @if($receiptImagePath && file_exists($receiptImagePath))
    <div class="scanned-receipt">
        <h2>Scanned Receipt - {{ $transaction->invoice_no }}</h2>
        @php
            // Convert image to base64 for PDF embedding
            $imageData = base64_encode(file_get_contents($receiptImagePath));
            $imageInfo = getimagesize($receiptImagePath);
            $mimeType = $imageInfo['mime'];
        @endphp
        <img src="data:{{ $mimeType }};base64,{{ $imageData }}" alt="Scanned Receipt">
    </div>
    @endif
</body>
</html>
