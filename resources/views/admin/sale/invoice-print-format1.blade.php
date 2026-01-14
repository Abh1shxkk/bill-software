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
            font-size: 9px;
            line-height: 1.2;
            background: #e0e0e0;
            padding: 20px;
        }
        
        .a4-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            padding: 5mm;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .main-table {
            border: 1.5px solid #000;
        }
        
        .main-table td, .main-table th {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
        }
        
        .no-border { border: none !important; }
        .border-bottom { border-bottom: 1px solid #000 !important; }
        .border-right { border-right: 1px solid #000 !important; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
        }
        
        .invoice-title {
            font-weight: bold;
            font-size: 11px;
            text-decoration: underline;
        }
        
        .items-table th {
            background: #fff;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 2px;
            text-align: center;
            border: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        
        .items-table td {
            font-size: 9px;
            padding: 2px 3px;
            border-left: 1px solid #000;
            border-right: none;
            border-top: none;
            border-bottom: none;
        }
        
        .items-table td:last-child {
            border-right: 1px solid #000;
        }
        
        .totals-row td {
            font-weight: bold;
            font-size: 9px;
            padding: 3px 5px;
            border: 1px solid #000;
        }
        
        .grand-total {
            background: #ccc;
            font-size: 12px !important;
            font-weight: bold;
        }
        
        .footer-section {
            font-size: 8px;
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
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print</button>

    <div class="a4-paper">
        <table class="main-table">
            <!-- Row 1: Top Header -->
            <tr>
                <td colspan="5" style="width: 35%; font-size: 8px; border: none; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; vertical-align: middle;">
                    Original/Duplicate/Triplicate Copy
                </td>
                <td colspan="4" style="width: 30%; border: none; border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; text-align: center; vertical-align: middle;">
                    <span class="invoice-title">** TAX INVOICE ({{ $transaction->cash_flag == 'Y' ? 'CASH' : 'CREDIT' }}) **</span>
                </td>
                <td colspan="5" style="width: 35%; border: none; border-bottom: 1px solid #000; padding: 5px;">
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; font-weight: bold; width: 25%;">Bill No.</td>
                            <td style="border: none; font-weight: bold;">{{ $transaction->invoice_no }}</td>
                            <td style="border: none; text-align: right;" rowspan="2">Page: 1 of 1</td>
                        </tr>
                        <tr>
                            <td style="border: none; font-weight: bold;">Dated</td>
                            <td style="border: none; font-weight: bold;">{{ $transaction->sale_date ? $transaction->sale_date->format('d/m/Y') : '' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <!-- Row 2: Company & Customer Info -->
            <tr>
                <td colspan="7" style="border: none; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div class="company-name">{{ strtoupper($organization->name ?? 'COMPANY NAME') }}</div>
                    <div>{{ $organization->address ?? '' }}</div>
                    <div>{{ $organization->city ?? '' }}</div>
                    <div>Phone : {{ $organization->phone ?? '' }}</div>
                    <div class="text-bold">D.L.No. : {{ $organization->dl_no ?? '' }} @if($organization->dl_no_1 ?? false), {{ $organization->dl_no_1 }}@endif</div>
                    <div class="text-bold">GST No. : {{ $organization->gst_no ?? '' }} <span style="float: right;">State Code : {{ substr($organization->gst_no ?? '09', 0, 2) }}</span></div>
                    @if($organization->food_license ?? false)<div>FASSAI No. : {{ $organization->food_license }}</div>@endif
                    <div style="margin-top: 3px;">
                        E-mail : {{ $organization->email ?? '' }}
                        <span style="float: right;" class="text-bold">PAN : {{ $organization->pan_no ?? '' }}</span>
                    </div>
                </td>
                <td colspan="7" style="border: none; border-bottom: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div class="text-bold" style="font-size: 11px;">To, {{ strtoupper($customer->name ?? 'CUSTOMER') }}</div>
                    <div>{{ $customer->address ?? '' }}</div>
                    @if($customer->city ?? false)<div>{{ $customer->city }}</div>@endif
                    <br>
                    <div class="text-bold">D.L. No.: {{ $customer->dl_number ?? '' }} @if($customer->dl_number1 ?? false), {{ $customer->dl_number1 }}@endif</div>
                    <div class="text-bold">GST:{{ $customer->gst_number ?? '' }} <span style="float: right;">, State Code : {{ $customer->state_code ?? substr($customer->gst_number ?? '09', 0, 2) }}</span></div>
                    <div class="text-bold">Tel : {{ $customer->mobile ?? $customer->telephone_office ?? '' }}</div>
                    <div class="text-right text-bold" style="margin-top: 5px;">PAN: {{ $customer->pan_number ?? '' }}</div>
                </td>
            </tr>
            
            <!-- Row 3: Items Header -->
            <tr class="items-table">
                <th style="width: 25px;">Sr.</th>
                <th style="text-align: left; padding-left: 5px;">PARTICULARS</th>
                <th style="width: 40px;">QTY.</th>
                <th style="width: 30px;">Free</th>
                <th style="width: 35px;">PACK</th>
                <th style="width: 55px;">HSN</th>
                <th style="width: 60px;">Batch No.</th>
                <th style="width: 35px;">Exp.</th>
                <th style="width: 50px;">MRP.</th>
                <th style="width: 50px;">Rate</th>
                <th style="width: 30px;">Scm</th>
                <th style="width: 35px;">DIS</th>
                <th style="width: 28px;">GST</th>
                <th style="width: 60px;">Net Amt.</th>
            </tr>
            
            <!-- Items Rows -->
            @php 
                $totalQty = 0;
                $totalGross = 0;
                $totalDiscount = 0;
                $totalScm = 0;
                $itemsCount = count($transaction->items);
            @endphp
            @foreach($transaction->items as $index => $item)
            @php 
                $totalQty += $item->qty ?? 0;
                $totalGross += $item->gross_amount ?? $item->amount ?? 0;
            @endphp
            <tr class="items-table">
                <td class="text-center">{{ $index + 1 }}.</td>
                <td class="text-left">{{ $item->mrp ?? '' }} | {{ $item->item_name }}</td>
                <td class="text-right">{{ number_format($item->qty ?? 0, 2) }}</td>
                <td class="text-center">{{ $item->free_qty && $item->free_qty > 0 ? number_format($item->free_qty, 0) : '-' }}</td>
                <td class="text-center">{{ $item->packing ?? '1*1' }}</td>
                <td class="text-center">{{ $item->hsn_code ?? '' }}</td>
                <td class="text-center">{{ $item->batch_no ?? '' }}</td>
                <td class="text-center">{{ $item->expiry_date ?? '' }}</td>
                <td class="text-right">{{ number_format($item->mrp ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item->sale_rate ?? 0, 2) }}</td>
                <td class="text-center">{{ $item->scheme ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->discount_percent ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format($item->gst_percent ?? (($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0)), 0) }}</td>
                <td class="text-right">{{ number_format($item->net_amount ?? $item->amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
            
            <!-- GST Summary Row -->
            @php
                $cgst = 0;
                $sgst = 0;
                $taxableAmount = 0;
                foreach($transaction->items as $item) {
                    $cgst += $item->cgst_amount ?? 0;
                    $sgst += $item->sgst_amount ?? 0;
                    $taxableAmount += ($item->amount ?? 0) - ($item->tax_amount ?? 0);
                }
                if($cgst == 0 && $sgst == 0) {
                    $cgst = ($transaction->tax_amount ?? 0) / 2;
                    $sgst = ($transaction->tax_amount ?? 0) / 2;
                }
                if($taxableAmount == 0) {
                    $taxableAmount = ($transaction->nt_amount ?? 0) - ($transaction->dis_amount ?? 0);
                }
            @endphp
            <tr>
                <td colspan="14" style="font-size: 8px; padding: 2px 5px;">
                    SGST : &lt;0%&gt; 0.00 &lt;2.5%&gt; {{ number_format($sgst, 2) }} on {{ number_format($taxableAmount, 2) }} &lt;6%&gt; 0.00 on 0.00 &lt;9%&gt; 0.00 on 0.00 &lt;14%&gt; 0.00 on 0.00
                </td>
            </tr>
            <tr>
                <td colspan="14" style="font-size: 8px; padding: 2px 5px;">
                    CGST : &lt;0%&gt; 0.00 &lt;2.5%&gt; {{ number_format($cgst, 2) }} on {{ number_format($taxableAmount, 2) }} &lt;6%&gt; 0.00 on 0.00 &lt;9%&gt; 0.00 on 0.00 &lt;14%&gt; 0.00 on 0.00
                </td>
            </tr>
            
            <!-- Totals Row 1 -->
            <tr class="totals-row">
                <td colspan="2" class="text-left">No of Items : {{ $itemsCount }}</td>
                <td class="text-center">Gross Amt</td>
                <td colspan="2" class="text-right">{{ number_format($transaction->nt_amount ?? 0, 2) }}</td>
                <td class="text-center">Scm. Amt</td>
                <td class="text-right">{{ number_format($transaction->scm_amount ?? 0, 2) }}</td>
                <td class="text-center">Disc. Amt</td>
                <td class="text-right">{{ number_format($transaction->dis_amount ?? 0, 2) }}</td>
                <td class="text-center">Taxable Amt.</td>
                <td class="text-right">{{ number_format($taxableAmount, 2) }}</td>
                <td class="text-center">CGST Amt</td>
                <td class="text-right">{{ number_format($cgst, 2) }}</td>
                <td class="text-center">SGST Amt</td>
            </tr>
            <tr class="totals-row">
                <td colspan="2" class="text-right">Total :</td>
                <td class="text-right">{{ number_format($transaction->nt_amount ?? 0, 2) }}</td>
                <td colspan="2"></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ number_format($sgst, 2) }}</td>
                <td class="text-center">IGST Amt</td>
                <td class="text-right">0.00</td>
                <td class="text-center">Inv. Amt.<br>R/Off</td>
                <td colspan="2" class="grand-total text-right">{{ number_format($transaction->net_amount ?? 0, 2) }}</td>
            </tr>
            
            <!-- Amount in Words -->
            <tr>
                <td colspan="12" style="font-weight: bold; font-style: italic; padding: 3px 5px;">
                    Rupees: {{ ucwords(numberToIndianWords($transaction->net_amount ?? 0)) }} Only
                </td>
                <td colspan="2" class="text-right text-bold">E.&.O.E.</td>
            </tr>
            
            <!-- Bank Details -->
            <tr>
                <td colspan="14" style="font-size: 8px; font-weight: bold; padding: 3px 5px;">
                    Bank : {{ strtoupper($organization->bank ?? 'PUNJAB NATIONAL BANK') }}, Branch :{{ strtoupper($organization->branch ?? 'PRAHLAD NAGAR MEERUT') }}, A/c No.:{{ $organization->account_no ?? '04141131001026' }}, IFSC :{{ $organization->ifsc_code ?? 'PUNB0041410' }}
                </td>
            </tr>
            
            <!-- Footer: Terms & Signature -->
            <tr>
                <td colspan="7" class="footer-section" style="vertical-align: top; padding: 5px;">
                    <u>Terms & Conditions :-</u><br>
                    All disputes are subject to Meerut Jurisdiction.<br>
                    Prices of Medicines are inclusive of all taxes.<br><br>
                    IRN NO:76bcada1e89ae357eabb25e139292b2f7fb5dc3c0ac2c4c9d4408357d4f330<br><br>
                    ACK NO.14261920389168<br>
                    ACK DT:{{ $transaction->sale_date ? $transaction->sale_date->format('d/m/Y') : date('d/m/Y') }}<br><br>
                    ( EasySol, For Demo : 9319312226,7500641054 )
                </td>
                <td colspan="7" class="text-right" style="vertical-align: top; padding: 5px;">
                    <div class="text-bold">For {{ strtoupper($organization->name ?? 'COMPANY NAME') }}</div>
                    <br><br><br><br>
                    <div>Auth. Sign.</div>
                </td>
            </tr>
        </table>
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
