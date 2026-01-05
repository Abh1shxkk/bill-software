<!DOCTYPE html>
<html>
<head>
    <title>HSN Wise Sale Purchase Report - Print</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; background-color: #d4edda; padding: 10px; }
        .header h3 { margin: 0; color: #155724; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; }
        th { background-color: #c3e6cb; font-weight: bold; font-size: 9px; }
        .text-center { text-align: center; }
        @media print { 
            body { margin: 0; } 
            .header { background-color: #d4edda !important; -webkit-print-color-adjust: exact; } 
            th { background-color: #c3e6cb !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>HSN Wise Sale Purchase Report</h3>
        <p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width: 130px;">GSTIN of the TaxPayer Submitting Data</th>
                <th style="width: 80px;">Product HSN</th>
                <th>Product Name</th>
                <th style="width: 120px;">Whether Product in column 4 is hand Sanitizer(Alcohol Based)-Yes/No</th>
                <th style="width: 90px;">Nature of Transaction (Sale/purchase)</th>
                <th style="width: 80px;">Inv. Date</th>
                <th style="width: 80px;">Invoice No.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData ?? [] as $row)
            <tr>
                <td>{{ $row['gstin'] }}</td>
                <td>{{ $row['hsn'] }}</td>
                <td>{{ $row['product_name'] }}</td>
                <td class="text-center">{{ $row['is_sanitizer'] }}</td>
                <td class="text-center">{{ $row['nature'] }}</td>
                <td>{{ $row['inv_date'] }}</td>
                <td>{{ $row['inv_no'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p style="margin-top: 10px; font-size: 10px; color: red;">No. Of Records: {{ count($reportData ?? []) }}</p>
</body>
</html>
