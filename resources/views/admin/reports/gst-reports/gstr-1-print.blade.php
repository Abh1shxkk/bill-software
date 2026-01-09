<!DOCTYPE html>
<html>
<head>
    <title>GSTR-1 Report - Print</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #800000; font-style: italic; }
        .period { font-size: 11px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        th { background-color: #cc9966; font-weight: bold; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .section-title { background-color: #e0e0e0; font-weight: bold; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>G S T R - 1</h2>
        <div class="period">Period: {{ $reportData['period'] ?? '' }}</div>
    </div>

    <!-- Summary Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 200px;">Description</th>
                <th class="text-center" style="width: 60px;">Count</th>
                <th class="text-end" style="width: 100px;">Taxable</th>
                <th class="text-end" style="width: 80px;">IGST</th>
                <th class="text-end" style="width: 80px;">CGST</th>
                <th class="text-end" style="width: 80px;">SGST</th>
                <th class="text-end" style="width: 60px;">CESS</th>
                <th class="text-end" style="width: 100px;">Total Tax</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold">B2B (Registered)</td>
                <td class="text-center">{{ $reportData['b2b_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2b_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>B2C Large</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td class="fw-bold">B2C Small (Unregistered)</td>
                <td class="text-center">{{ $reportData['b2c_count'] ?? 0 }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_taxable'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_igst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_cgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_sgst'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_cess'] ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($reportData['b2c_total_tax'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Credit/Debit Notes (Registered)</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>Credit/Debit Notes (Unregistered)</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>Exports</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
                <td class="text-end">0.00</td>
            </tr>
            <tr>
                <td>Nil Rated/Exempted</td>
                <td class="text-center">0</td>
                <td class="text-end">0.00</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td>HSN Summary</td>
                <td class="text-center">{{ $hsnSummary->count() ?? 0 }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('taxable_value') ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('igst') ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('cgst') ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('sgst') ?? 0, 2) }}</td>
                <td class="text-end">{{ number_format($hsnSummary->sum('cess') ?? 0, 2) }}</td>
                <td class="text-end">-</td>
            </tr>
            <tr>
                <td>Documents Issued</td>
                <td class="text-center">{{ ($reportData['b2b_count'] ?? 0) + ($reportData['b2c_count'] ?? 0) }}</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
            </tr>
        </tbody>
    </table>

    @if($b2bData->count() > 0)
    <h4>B2B Invoices (Registered Dealers)</h4>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>GSTIN</th>
                <th class="text-end">Taxable</th>
                <th class="text-end">IGST</th>
                <th class="text-end">CGST</th>
                <th class="text-end">SGST</th>
                <th class="text-end">Total Tax</th>
            </tr>
        </thead>
        <tbody>
            @foreach($b2bData as $row)
            <tr>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d-m-Y') }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ $row['gstin'] }}</td>
                <td class="text-end">{{ number_format($row['taxable_value'], 2) }}</td>
                <td class="text-end">{{ number_format($row['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['total_tax'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($b2cData->count() > 0)
    <h4>B2C Invoices (Unregistered Dealers)</h4>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-end">Taxable</th>
                <th class="text-end">IGST</th>
                <th class="text-end">CGST</th>
                <th class="text-end">SGST</th>
                <th class="text-end">Total Tax</th>
            </tr>
        </thead>
        <tbody>
            @foreach($b2cData as $row)
            <tr>
                <td>{{ $row['invoice_no'] }}</td>
                <td>{{ \Carbon\Carbon::parse($row['invoice_date'])->format('d-m-Y') }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td class="text-end">{{ number_format($row['taxable_value'], 2) }}</td>
                <td class="text-end">{{ number_format($row['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['total_tax'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($hsnSummary->count() > 0)
    <h4>HSN Summary</h4>
    <table>
        <thead>
            <tr>
                <th>HSN Code</th>
                <th>Description</th>
                <th>UQC</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total Value</th>
                <th class="text-end">Taxable</th>
                <th class="text-end">IGST</th>
                <th class="text-end">CGST</th>
                <th class="text-end">SGST</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hsnSummary as $row)
            <tr>
                <td>{{ $row['hsn_code'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['uqc'] }}</td>
                <td class="text-end">{{ number_format($row['total_qty'], 2) }}</td>
                <td class="text-end">{{ number_format($row['total_value'], 2) }}</td>
                <td class="text-end">{{ number_format($row['taxable_value'], 2) }}</td>
                <td class="text-end">{{ number_format($row['igst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['cgst'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sgst'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
