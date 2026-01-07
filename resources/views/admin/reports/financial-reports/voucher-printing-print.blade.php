<!DOCTYPE html>
<html>
<head>
    <title>Voucher Printing</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 12px; 
            margin: 10px; 
        }
        .voucher {
            border: 1px solid #000;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .voucher-header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 8px 15px; 
            text-align: center;
            border-bottom: 1px solid #000;
        }
        .voucher-header h3 {
            margin: 0;
            color: #800080;
            font-size: 16px;
        }
        .voucher-info {
            padding: 10px 15px;
            border-bottom: 1px solid #ccc;
        }
        .voucher-info table {
            width: 100%;
        }
        .voucher-info td {
            padding: 3px 0;
        }
        .voucher-body {
            padding: 10px 15px;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .items-table th, .items-table td { 
            border: 1px solid #999; 
            padding: 5px 8px; 
        }
        .items-table th { 
            background-color: #e0e0e0; 
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .voucher-footer {
            padding: 10px 15px;
            border-top: 1px solid #ccc;
        }
        .signature-area {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        @media print {
            body { margin: 0; }
            .voucher { page-break-after: always; }
            .voucher:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body onload="window.print()">
    @forelse($reportData as $voucher)
    <div class="voucher">
        <div class="voucher-header">
            <h3>{{ $voucher->voucher_type_label }}</h3>
        </div>
        
        <div class="voucher-info">
            <table>
                <tr>
                    <td style="width: 50%;"><strong>Voucher No:</strong> {{ $voucher->voucher_no }}</td>
                    <td style="width: 50%; text-align: right;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($voucher->voucher_date)->format('d-M-Y') }}</td>
                </tr>
            </table>
        </div>

        <div class="voucher-body">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">S.No</th>
                        <th>Account Name</th>
                        <th style="width: 120px;">Debit (₹)</th>
                        <th style="width: 120px;">Credit (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($voucher->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->account_name }}
                            @if($item->item_narration)
                            <br><small><em>{{ $item->item_narration }}</em></small>
                            @endif
                        </td>
                        <td class="text-end">{{ $item->debit_amount > 0 ? number_format($item->debit_amount, 2) : '' }}</td>
                        <td class="text-end">{{ $item->credit_amount > 0 ? number_format($item->credit_amount, 2) : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="2" class="text-end">Total:</td>
                        <td class="text-end">{{ number_format($voucher->total_debit, 2) }}</td>
                        <td class="text-end">{{ number_format($voucher->total_credit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            @if($voucher->narration)
            <div style="margin-top: 10px;">
                <strong>Narration:</strong> {{ $voucher->narration }}
            </div>
            @endif
        </div>

        <div class="voucher-footer">
            <div class="signature-area">
                <div class="signature-box">Prepared By</div>
                <div class="signature-box">Checked By</div>
                <div class="signature-box">Authorized Signatory</div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 50px;">
        <h3>No vouchers found for the selected criteria</h3>
        <p>Date Range: {{ \Carbon\Carbon::parse($fromDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-M-Y') }}</p>
    </div>
    @endforelse

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
