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
            size: A5 landscape;
            margin: 5mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.1;
            background: #e0e0e0;
            padding: 20px;
        }
        
        /* A5 Landscape Paper Container */
        .a5-paper {
            width: 210mm;
            min-height: 148mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            padding: 5mm;
        }
        
        .main-border {
            border: 2px solid #000;
        }
        .w-100 { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px solid #000; }
        .border-right { border-right: 1px solid #000; }
        .border-left { border-left: 1px solid #000; }
        .border-top { border-top: 1px solid #000; }
        
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .a5-paper {
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
        
        /* Layout Grid */
        .container {
            display: flex;
            flex-direction: column;
        }
        
        /* Top Header Area */
        .top-header {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .top-header-left {
            width: 40%;
            padding: 2px 5px;
            font-size: 8px;
        }
        .top-header-center {
            width: 30%;
            text-align: center;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .top-header-right {
            width: 30%;
            display: flex;
        }
        
        /* Bill Details Table inside header */
        .bill-details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .bill-details-table td {
            border: 1px solid #000;
            border-top: none;
            border-right: none;
            border-bottom: none;
            padding: 2px;
        }
        
        /* Info Section (Company | Customer) */
        .info-section {
            display: flex;
            border-bottom: 1px solid #000;
        }
        .company-info {
            width: 50%; /* Adjusted split */
            padding: 5px;
            border-right: 1px solid #000;
        }
        .customer-info {
            width: 50%;
            padding: 5px;
        }
        
        .company-name {
            font-size: 14px;
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
            padding: 2px;
            background: #fff;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        .items-table th:last-child {
            border-right: none;
        }
        
        .items-table td {
            border-right: 1px solid #000;
            padding: 2px 4px;
            font-size: 9px;
            vertical-align: top;
        }
        .items-table td:last-child {
            border-right: none;
        }
        
        /* Fixed height for items area to push footer down and show vertical lines */
        .items-container {
            /* minimal height control via empty rows */
        }
        
        /* GST Summary */
        .gst-summary {
            border-top: 1px solid #000;
            font-size: 8px;
            padding: 2px 5px;
        }
        
        /* Footer Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #000;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: right;
            font-weight: bold;
            font-size: 9px;
        }
        /* Remove outer borders of totals table to blend */
        .totals-table tr td:first-child { border-left: none; }
        .totals-table tr td:last-child { border-right: none; }
        
        .amount-words-row {
            border-top: 1px solid #000;
            padding: 3px 5px;
            font-weight: bold;
            font-size: 9px;
            font-style: italic;
        }
        
        .bank-details {
            border-top: 1px solid #000;
            padding: 3px 5px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .bottom-footer {
            display: flex;
            border-top: 1px solid #000;
        }
        .terms {
            width: 60%;
            padding: 5px;
            border-right: 1px solid #000;
            font-size: 8px;
        }
        .signature {
            width: 40%;
            padding: 5px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 40px; /* Space for signature */
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 5px 10px;
            background: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>

    <div class="a5-paper">
    <div class="main-border">
        <!-- Header -->
        <div class="top-header">
            <div class="top-header-left">
                Original/Duplicate/Triplicate Copy
            </div>
            <div class="top-header-center">
                <span style="text-decoration: underline;">** TAX INVOICE ({{ $transaction->cash_flag == 'Y' ? 'CASH' : 'CREDIT' }}) **</span>
            </div>
            <div class="top-header-right">
                <table class="bill-details-table">
                    <tr>
                        <td style="width: 30%; font-weight: bold;">Bill No.</td>
                        <td style="width: 70%; font-weight: bold;">{{ $transaction->invoice_no }}</td>
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
                <div style="font-weight: bold; margin-top: 2px;">D.L.No. : {{ $organization->dl_no ?? '' }} @if($organization->dl_no_1 ?? false), {{ $organization->dl_no_1 }}@endif</div>
                <div style="font-weight: bold;">GST No. : {{ $organization->gst_no ?? '' }} <span style="float: right;">State Code : {{ $organization->state_code ?? '09' }}</span></div>
                @if($organization->fssai_no ?? false)<div>FSSAI No. : {{ $organization->fssai_no }}</div>@endif
                <div style="margin-top: 2px;">
                    E-mail : {{ $organization->email ?? '' }}
                    <span style="float: right; font-weight: bold;">PAN : {{ $organization->pan_no ?? '' }}</span>
                </div>
            </div>
            <div class="customer-info">
                <div style="font-weight: bold; font-size: 11px; margin-bottom: 2px;">To, {{ strtoupper($customer->name ?? 'CUSTOMER') }}</div>
                <div style="margin-bottom: 5px;">{{ $customer->address ?? '' }}</div>
                
                <div style="font-weight: bold;">D.L. No.: {{ $customer->dl_number ?? '' }}</div>
                <div style="font-weight: bold;">GST : {{ $customer->gst_number ?? '' }} <span style="float: right;">, State Code : {{ $customer->state_code ?? '09' }}</span></div>
                <div style="font-weight: bold;">Tel : {{ $customer->mobile ?? '' }}</div>
                <div style="text-align: right; font-weight: bold; margin-top: 5px;">PAN: {{ $customer->pan_no ?? '' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 25px;">Sr.</th>
                    <th style="text-align: left; padding-left: 5px;">PARTICULARS</th>
                    <th style="width: 35px;">QTY.</th>
                    <th style="width: 30px;">Free</th>
                    <th style="width: 35px;">PACK</th>
                    <th style="width: 50px;">HSN</th>
                    <th style="width: 55px;">Batch No.</th>
                    <th style="width: 35px;">Exp.</th>
                    <th style="width: 45px;">MRP.</th>
                    <th style="width: 45px;">Rate</th>
                    <th style="width: 30px;">Scm</th>
                    <th style="width: 30px;">DIS</th>
                    <th style="width: 25px;">GST</th>
                    <th style="width: 60px;">Net Amt.</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalQty = 0; 
                    $itemsCount = count($transaction->items);
                    // Calculate filler rows needed. Assuming approx 12-14 rows fit comfortably.
                    $minRows = 12;
                    $fillerRows = max(0, $minRows - $itemsCount);
                @endphp
                @foreach($transaction->items as $index => $item)
                @php $totalQty += $item->qty ?? 0; @endphp
                <tr style="height: 15px;">
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
                
                <!-- Filler Rows to maintain vertical lines -->
                @for($i = 0; $i < $fillerRows; $i++)
                <tr style="height: 15px;">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
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
                <td style="text-align: left; width: 10%;">No of Items: {{ $itemsCount }}</td>
                <td style="width: 10%; border-left: 1px solid #000;">Gross Amt</td>
                <td style="width: 10%;">{{ number_format($transaction->nt_amount ?? 0, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">Scm. Amt</td>
                <td style="width: 8%;">{{ number_format($transaction->scm_amount ?? 0, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">Disc. Amt</td>
                <td style="width: 8%;">{{ number_format($transaction->dis_amount ?? 0, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">Taxable Amt.</td>
                <td style="width: 8%;">{{ number_format($taxableAmount, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">CGST Amt</td>
                <td style="width: 8%;">{{ number_format($cgst, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">SGST Amt</td>
                <td style="width: 8%;">{{ number_format($sgst, 2) }}</td>
                <td style="width: 8%; border-left: 1px solid #000;">IGST Amt</td>
                <td style="width: 8%;">0.00</td>
                <td style="width: 10%; border-left: 1px solid #000;">Inv. Amt.</td>
                <td style="background: #ccc; width: 15%; font-size: 11px;">{{ number_format($transaction->net_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">Total :</td>
                <td>{{ number_format($transaction->nt_amount, 2) }}</td>
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
                ( EasySol, For Demo : 9319312226,7500641054 )
            </div>
            <div class="signature">
                <div>For {{ strtoupper($organization->name ?? 'COMPANY NAME') }}</div>
                <div>Auth. Sign.</div>
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
