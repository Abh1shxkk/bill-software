<!DOCTYPE html>
<html>
<head>
    <title>GSTR-3B Report - Print</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11px; 
            margin: 10px; 
        }
        .header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 10px; 
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #800000;
            letter-spacing: 5px;
        }
        .period {
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 4px 6px; 
            text-align: left;
        }
        th { 
            background-color: #333; 
            color: white; 
            font-weight: bold;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .section-header { 
            background-color: #f0f0f0; 
            font-weight: bold;
        }
        .itc-header { background-color: #d1ecf1; }
        .itc-reversed { background-color: #fff3cd; }
        .net-itc { background-color: #d4edda; }
        .tax-payable { background-color: #cce5ff; }
        .cash-payable { background-color: #f8d7da; }
        .text-danger { color: #dc3545; }
        .text-success { color: #28a745; }
        .ps-4 { padding-left: 20px; }
        .summary-table td { padding: 6px 10px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>G S T R - 3 B</h2>
    </div>

    <div class="period">
        Period: {{ $reportData['period'] ?? 'N/A' }}
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2">Description</th>
                <th class="text-end" style="width: 100px;">Integrated Tax</th>
                <th class="text-end" style="width: 100px;">Central Tax</th>
                <th class="text-end" style="width: 100px;">State/UT Tax</th>
                <th class="text-end" style="width: 80px;">Cess</th>
            </tr>
        </thead>
        <tbody>
            <!-- 3.1 Outward Supplies -->
            <tr class="section-header">
                <td colspan="6">3.1 Details of Outward Supplies and inward supplies liable to reverse charge</td>
            </tr>
            <tr>
                <td width="25">(a)</td>
                <td>Outward taxable supplies (other than zero rated, nil rated and exempted)</td>
                <td class="text-end">{{ number_format($gstr['outward_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['outward_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['outward_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['outward_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>(b)</td>
                <td>Outward taxable supplies (zero rated)</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>(c)</td>
                <td>Other outward supplies (Nil rated, exempted)</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td>(d)</td>
                <td>Inward supplies (liable to reverse charge)</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>(e)</td>
                <td>Non-GST outward supplies</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>

            <!-- 4. Eligible ITC -->
            <tr class="section-header">
                <td colspan="6">4. Eligible ITC</td>
            </tr>
            <tr class="itc-header">
                <td>(A)</td>
                <td class="fw-bold">ITC Available (whether in full or part)</td>
                <td class="text-end fw-bold">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="ps-4">(1) Import of goods</td>
                <td class="text-end">0.00</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td></td>
                <td class="ps-4">(2) Import of services</td>
                <td class="text-end">0.00</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td></td>
                <td class="ps-4">(3) Inward supplies liable to reverse charge</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td></td>
                <td class="ps-4">(4) Inward supplies from ISD</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td></td>
                <td class="ps-4">(5) All other ITC</td>
                <td class="text-end">{{ number_format($gstr['itc_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($gstr['itc_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr class="itc-reversed">
                <td>(B)</td>
                <td class="fw-bold">ITC Reversed</td>
                <td class="text-end text-danger">0.00</td>
                <td class="text-end text-danger">0.00</td>
                <td class="text-end text-danger">0.00</td>
                <td class="text-end text-danger">0.00</td>
            </tr>
            <tr class="net-itc">
                <td>(C)</td>
                <td class="fw-bold">Net ITC Available (A) - (B)</td>
                <td class="text-end fw-bold">{{ number_format($gstr['net_itc_igst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['net_itc_cgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['net_itc_sgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['net_itc_cess'] ?? 0, 2) }}</td>
            </tr>

            <!-- 6. Payment of Tax -->
            <tr class="section-header">
                <td colspan="6">6. Payment of Tax</td>
            </tr>
            <tr class="tax-payable">
                <td colspan="2" class="fw-bold">Tax Payable</td>
                <td class="text-end fw-bold">{{ number_format($gstr['payable_igst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['payable_cgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['payable_sgst'] ?? 0, 2) }}</td>
                <td class="text-end fw-bold">{{ number_format($gstr['payable_cess'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">Paid through ITC</td>
                <td class="text-end text-success">{{ number_format(min($gstr['net_itc_igst'] ?? 0, $gstr['payable_igst'] ?? 0), 2) }}</td>
                <td class="text-end text-success">{{ number_format(min($gstr['net_itc_cgst'] ?? 0, $gstr['payable_cgst'] ?? 0), 2) }}</td>
                <td class="text-end text-success">{{ number_format(min($gstr['net_itc_sgst'] ?? 0, $gstr['payable_sgst'] ?? 0), 2) }}</td>
                <td class="text-end text-success">{{ number_format(min($gstr['net_itc_cess'] ?? 0, $gstr['payable_cess'] ?? 0), 2) }}</td>
            </tr>
            <tr class="cash-payable">
                <td colspan="2" class="fw-bold">Tax/Cess paid in Cash</td>
                <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_igst'] ?? 0) - ($gstr['net_itc_igst'] ?? 0)), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_cgst'] ?? 0) - ($gstr['net_itc_cgst'] ?? 0)), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_sgst'] ?? 0) - ($gstr['net_itc_sgst'] ?? 0)), 2) }}</td>
                <td class="text-end fw-bold">{{ number_format(max(0, ($gstr['payable_cess'] ?? 0) - ($gstr['net_itc_cess'] ?? 0)), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary -->
    <table class="summary-table">
        <tr>
            <td class="fw-bold" style="width: 150px;">Total Sales</td>
            <td class="text-end" style="width: 120px;">{{ number_format($gstr['total_sales'] ?? 0, 2) }}</td>
            <td class="fw-bold" style="width: 150px;">Total Sales Return</td>
            <td class="text-end text-danger" style="width: 120px;">{{ number_format($gstr['total_sales_return'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td class="fw-bold">Total Purchase</td>
            <td class="text-end">{{ number_format($gstr['total_purchase'] ?? 0, 2) }}</td>
            <td class="fw-bold">Total Purchase Return</td>
            <td class="text-end text-danger">{{ number_format($gstr['total_purchase_return'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
