@php
if (!function_exists('numberToIndianWords')) {
    function numberToIndianWords($number) {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        if ($number == 0) return 'Zero';
        if ($number < 0) return 'Negative ' . numberToIndianWords(abs($number));
        
        $number = round($number);
        
        $crore = floor($number / 10000000);
        $lakh = floor(($number % 10000000) / 100000);
        $thousand = floor(($number % 100000) / 1000);
        $hundred = floor(($number % 1000) / 100);
        $remainder = $number % 100;
        
        $result = '';
        
        if ($crore > 0) {
            $result .= numberToIndianWords($crore) . ' Crore ';
        }
        if ($lakh > 0) {
            $result .= numberToIndianWords($lakh) . ' Lakh ';
        }
        if ($thousand > 0) {
            $result .= numberToIndianWords($thousand) . ' Thousand ';
        }
        if ($hundred > 0) {
            $result .= $ones[$hundred] . ' Hundred ';
        }
        if ($remainder > 0) {
            if ($remainder < 20) {
                $result .= $ones[$remainder];
            } else {
                $result .= $tens[floor($remainder / 10)] . ' ' . $ones[$remainder % 10];
            }
        }
        
        return trim($result);
    }
}
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $transaction->invoice_no }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            background: #e0e0e0;
            padding: 20px;
        }
        
        /* A4 Paper Container */
        .a4-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            padding: 10mm;
        }
        
        .main-border {
            border: 2px solid #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .a4-paper {
                width: 100%;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                padding: 0;
            }
            .print-btn {
                display: none !important;
            }
        }
        
        /* Top Header Area */
        .top-header {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .top-header-left {
            width: 40%;
            padding: 3px 5px;
            font-size: 9px;
        }
        .top-header-center {
            width: 30%;
            text-align: center;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px solid #000;
        }
        .top-header-right {
            width: 30%;
            border-left: 1px solid #000;
        }
        
        /* Bill Details Table inside header */
        .bill-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .bill-details-table td {
            padding: 3px 5px;
        }
        .bill-details-table tr:first-child td {
            border-bottom: 1px solid #000;
        }
        
        /* Info Section (Company | Customer) */
        .info-section {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .company-info {
            width: 50%;
            padding: 8px;
            border-right: 1px solid #000;
            font-size: 10px;
        }
        .customer-info {
            width: 50%;
            padding: 8px;
            font-size: 10px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            border: 1px solid #000;
            border-top: none;
            border-left: none;
            padding: 4px 2px;
            background: #fff;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }
        .items-table th:first-child {
            border-left: none;
        }
        .items-table th:last-child {
            border-right: none;
        }
        
        .items-table td {
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 3px 4px;
            font-size: 10px;
            vertical-align: top;
        }
        .items-table td:first-child {
            border-left: none;
        }
        .items-table td:last-child {
            border-right: none;
        }
        
        /* GST Summary */
        .gst-summary {
            border-bottom: 1px solid #000;
            font-size: 9px;
            padding: 3px 8px;
        }
        
        /* Footer Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            border: 1px solid #000;
            border-top: none;
            border-left: none;
            padding: 4px 6px;
            text-align: right;
            font-weight: bold;
            font-size: 10px;
        }
        .totals-table tr td:first-child { border-left: none; }
        .totals-table tr td:last-child { border-right: none; }
        
        .amount-words-row {
            border-bottom: 1px solid #000;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 10px;
            font-style: italic;
        }
        
        .bank-details {
            border-bottom: 1px solid #000;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .bottom-footer {
            display: flex;
        }
        .terms {
            width: 60%;
            padding: 8px;
            border-right: 1px solid #000;
            font-size: 9px;
        }
        .signature {
            width: 40%;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 60px;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            background: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>

    <div class="a4-paper">
    <div class="main-border">
        <!-- Header -->
        <div class="top-header">
            <div class="top-header-left">
                Original/Duplicate/Triplicate Copy
            </div>
            <div class="top-header-center">
                <span style="text-decoration: underline; font-size: 12px;">** TAX INVOICE ({{ $transaction->cash_flag == 'Y' ? 'CASH' : 'CREDIT' }}) **</span>
            </div>
            <div class="top-header-right">
                <table class="bill-details-table">
                    <tr>
                        <td style="width: 40%; font-weight: bold;">Bill No.</td>
                        <td style="font-weight: bold;">{{ $transaction->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Dated</td>
                        <td style="font-weight: bold;">{{ $transaction->sale_date ? $transaction->sale_date->format('d/m/Y') : '' }} <span style="float:right">Page: 1 of 1</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="company-info">
                <div class="company-name">{{ strtoupper($organization->name ?? 'COMPANY NAME') }}</div>
                <div>{{ $organization->address ?? '' }}</div>
                <div>{{ $organization->city ?? '' }}</div>
                <div>Phone : {{ $organization->phone ?? '' }}</div>
                <div style="font-weight: bold; margin-top: 3px;">D.L.No. : {{ $organization->dl_no ?? '' }} @if($organization->dl_no_1 ?? false), {{ $organization->dl_no_1 }}@endif</div>
                <div style="font-weight: bold;">GST No. : {{ $organization->gst_no ?? '' }} <span style="float: right;">State Code : {{ $organization->state_code ?? '09' }}</span></div>
                @if($organization->fssai_no ?? false)<div>FSSAI No. : {{ $organization->fssai_no }}</div>@endif
                <div style="margin-top: 3px;">
                    E-mail : {{ $organization->email ?? '' }}
                    <span style="float: right; font-weight: bold;">PAN : {{ $organization->pan_no ?? '' }}</span>
                </div>
            </div>
            <div class="customer-info">
                <div style="font-weight: bold; font-size: 13px; margin-bottom: 3px;">To, {{ strtoupper($customer->name ?? 'CUSTOMER') }}</div>
                <div style="margin-bottom: 8px;">{{ $customer->address ?? '' }}</div>
                
                <div style="font-weight: bold;">D.L. No.: {{ $customer->dl_number ?? '' }}</div>
                <div style="font-weight: bold;">GST : {{ $customer->gst_number ?? '' }} <span style="float: right;">, State Code : {{ $customer->state_code ?? '09' }}</span></div>
                <div style="font-weight: bold;">Tel : {{ $customer->mobile ?? '' }}</div>
                <div style="text-align: right; font-weight: bold; margin-top: 8px;">PAN: {{ $customer->pan_no ?? '' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px;">Sr.</th>
                    <th style="text-align: left; padding-left: 5px;">PARTICULARS</th>
                    <th style="width: 45px;">QTY.</th>
                    <th style="width: 35px;">Free</th>
                    <th style="width: 40px;">PACK</th>
                    <th style="width: 55px;">HSN</th>
                    <th style="width: 65px;">Batch No.</th>
                    <th style="width: 40px;">Exp.</th>
                    <th style="width: 55px;">MRP.</th>
                    <th style="width: 55px;">Rate</th>
                    <th style="width: 35px;">Scm</th>
                    <th style="width: 35px;">DIS</th>
                    <th style="width: 30px;">GST</th>
                    <th style="width: 70px;">Net Amt.</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalQty = 0; 
                    $itemsCount = count($transaction->items);
                @endphp
                @foreach($transaction->items as $index => $item)
                @php $totalQty += $item->qty ?? 0; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-right">{{ number_format($item->qty ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->free_qty ?? '-' }}</td>
                    <td class="text-center">{{ $item->packing ?? '1*1' }}</td>
                    <td class="text-center">{{ $item->hsn_code ?? '' }}</td>
                    <td class="text-center">{{ $item->batch_no ?? '' }}</td>
                    <td class="text-center">{{ $item->expiry_date ?? '' }}</td>
                    <td class="text-right">{{ number_format($item->mrp ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->sale_rate ?? 0, 2) }}</td>
                    <td class="text-center">{{ $item->scheme ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->discount_percent ?? 0, 2) }}</td>
                    <td class="text-center">{{ number_format(($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0), 0) }}</td>
                    <td class="text-right">{{ number_format($item->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- GST Summary -->
        @php
            $cgst = ($transaction->tax_amount ?? 0) / 2;
            $sgst = ($transaction->tax_amount ?? 0) / 2;
            $taxableAmount = ($transaction->nt_amount ?? 0) - ($transaction->dis_amount ?? 0);
        @endphp
        <div class="gst-summary">
            SGST : &lt;0%&gt; 0.00 &lt;2.5%&gt; {{ number_format($sgst, 2) }} on {{ number_format($taxableAmount, 2) }} &lt;6%&gt; 0.00 on 0.00 &lt;9%&gt; 0.00 on 0.00 &lt;14%&gt; 0.00 on 0.00
            <br>
            CGST : &lt;0%&gt; 0.00 &lt;2.5%&gt; {{ number_format($cgst, 2) }} on {{ number_format($taxableAmount, 2) }} &lt;6%&gt; 0.00 on 0.00 &lt;9%&gt; 0.00 on 0.00 &lt;14%&gt; 0.00 on 0.00
        </div>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td style="text-align: left;">No of Items : {{ $itemsCount }}</td>
                <td>Gross Amt</td>
                <td>{{ number_format($transaction->nt_amount ?? 0, 2) }}</td>
                <td>Scm. Amt</td>
                <td>{{ number_format($transaction->scm_amount ?? 0, 2) }}</td>
                <td>Disc. Amt</td>
                <td>{{ number_format($transaction->dis_amount ?? 0, 2) }}</td>
                <td>Taxable Amt.</td>
                <td>{{ number_format($taxableAmount, 2) }}</td>
                <td>CGST Amt</td>
                <td>{{ number_format($cgst, 2) }}</td>
                <td>SGST Amt</td>
                <td>{{ number_format($sgst, 2) }}</td>
                <td>IGST Amt</td>
                <td>0.00</td>
                <td>Inv. Amt.</td>
                <td style="background: #ccc; font-size: 12px;">{{ number_format($transaction->net_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">Total :</td>
                <td>{{ number_format($transaction->nt_amount ?? 0, 2) }}</td>
                <td colspan="12"></td>
                <td>R/Off</td>
                <td style="background: #ccc;"></td>
            </tr>
        </table>
        
        <div class="amount-words-row">
            Rupees: {{ ucwords(numberToIndianWords($transaction->net_amount ?? 0)) }} Only
            <span style="float: right">E.&.O.E.</span>
        </div>
        
        <div class="bank-details">
            Bank : {{ strtoupper($organization->bank ?? 'BANK NAME') }}, Branch :{{ strtoupper($organization->branch ?? 'BRANCH') }}, A/c No.:{{ $organization->account_no ?? '' }}, IFSC :{{ $organization->ifsc_code ?? '' }}
        </div>
        
        <div class="bottom-footer">
            <div class="terms">
                <u>Terms & Conditions :-</u><br>
                All disputes are subject to Meerut Jurisdiction.<br>
                Prices of Medicines are inclusive of all taxes.<br><br>
                IRN NO:76bcada1e89ae357eabb25e139292b2f7fb5dc3c0ac2c4c9d4408357d4f330<br><br>
                ACK NO.14261920389168 <br>
                ACK DT:{{ date('d/m/Y') }}<br><br>
                ( EasySol, For Demo : 9319312226,7500641054 )
            </div>
            <div class="signature">
                <div>For {{ strtoupper($organization->name ?? 'COMPANY NAME') }}</div>
                <div style="margin-top: 30px;">Auth. Sign.</div>
            </div>
        </div>
    </div>
    </div>
    
    <script>
        @if($autoPrint ?? false)
            window.onload = function() {
                window.print();
            }
        @endif
    </script>
</body>
</html>
