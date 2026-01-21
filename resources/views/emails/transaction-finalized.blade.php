<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Finalized</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .invoice-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .invoice-info h2 {
            margin-top: 0;
            color: #495057;
            font-size: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
        }
        .info-value {
            color: #212529;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .items-table th {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .items-table tr:hover {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 5px;
            margin-top: 25px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
        }
        .total-row.grand-total {
            border-top: 2px solid #007bff;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .receipt-section {
            margin-top: 30px;
            text-align: center;
        }
        .receipt-section img {
            max-width: 100%;
            height: auto;
            border: 2px solid #dee2e6;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Transaction Finalized</h1>
            <p style="margin: 10px 0 0 0; color: #6c757d;">Your temporary transaction has been successfully converted to an invoice</p>
        </div>

        <div class="invoice-info">
            <h2>Invoice Details</h2>
            <div class="info-row">
                <span class="info-label">Invoice Number:</span>
                <span class="info-value"><strong>{{ $transaction->invoice_no }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $transaction->sale_date->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $customer->name ?? 'N/A' }}</span>
            </div>
            @if($customer && $customer->address)
            <div class="info-row">
                <span class="info-label">Address:</span>
                <span class="info-value">{{ $customer->address }}</span>
            </div>
            @endif
            @if($transaction->salesman)
            <div class="info-row">
                <span class="info-label">Salesman:</span>
                <span class="info-value">{{ $transaction->salesman->name }}</span>
            </div>
            @endif
        </div>

        <h3 style="color: #495057; margin-top: 30px;">Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Batch</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->item_name }}</strong>
                        @if($item->item_code)
                        <br><small style="color: #6c757d;">Code: {{ $item->item_code }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $item->batch_no ?? '-' }}
                        @if($item->expiry_date)
                        <br><small style="color: #6c757d;">Exp: {{ $item->expiry_date }}</small>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->qty, 0) }}</td>
                    <td class="text-right">₹{{ number_format($item->sale_rate, 2) }}</td>
                    <td class="text-right">{{ number_format($item->discount_percent ?? 0, 2) }}%</td>
                    <td class="text-right"><strong>₹{{ number_format($item->amount, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($transaction->nt_amount ?? 0, 2) }}</span>
            </div>
            @if($transaction->dis_amount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>- ₹{{ number_format($transaction->dis_amount, 2) }}</span>
            </div>
            @endif
            @if($transaction->tax_amount > 0)
            <div class="total-row">
                <span>Tax (GST):</span>
                <span>₹{{ number_format($transaction->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>Grand Total:</span>
                <span>₹{{ number_format($transaction->net_amount, 2) }}</span>
            </div>
        </div>

        @if($transaction->receipt_path)
        <div class="receipt-section">
            <h3 style="color: #495057;">Scanned Receipt</h3>
            <p style="color: #6c757d; font-size: 14px;">The original scanned receipt is attached to this email</p>
        </div>
        @endif

        @if($transaction->remarks)
        <div style="margin-top: 30px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
            <strong style="color: #856404;">Remarks:</strong>
            <p style="margin: 5px 0 0 0; color: #856404;">{{ $transaction->remarks }}</p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p style="margin: 5px 0;">This is an automated email. Please do not reply to this message.</p>
            <p style="margin: 5px 0; font-size: 12px;">Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
