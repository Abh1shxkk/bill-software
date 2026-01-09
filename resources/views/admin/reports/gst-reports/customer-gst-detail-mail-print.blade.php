<!DOCTYPE html>
<html>
<head>
    <title>Customer GST Detail Mail - Print</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            margin: 20px;
            font-size: 12px;
        }
        .header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 10px; 
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #999;
        }
        .header h2 {
            margin: 0;
            color: #800000;
        }
        .period {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        table th {
            background-color: #cc9966;
            font-weight: bold;
        }
        table tbody tr {
            background-color: #ffffcc;
        }
        table tfoot tr {
            background-color: #ffcc99;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-section {
            margin-top: 15px;
            padding: 10px;
            background-color: #d4d4d4;
            border: 1px solid #999;
        }
        .summary-section span {
            margin-right: 30px;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-primary {
            color: #0d6efd;
        }
        .text-success {
            color: #198754;
        }
        @media print {
            body { margin: 0; }
            @page { margin: 1cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>Customer GST Detail Mail</h2>
    </div>
    
    <div class="period">
        Period: {{ \Carbon\Carbon::parse($fromDate)->format('d-M-y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-M-y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">ALTERCODE</th>
                <th style="width: 280px;">PARTY NAME</th>
                <th style="width: 180px;">GSTIN No.</th>
                <th class="text-end" style="width: 120px;">AMOUNT</th>
                <th style="width: 80px;">TAG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $customer)
            <tr>
                <td>{{ $customer['code'] }}</td>
                <td>{{ $customer['name'] }}</td>
                <td>{{ $customer['gst_number'] }}</td>
                <td class="text-end">{{ number_format($customer['balance'], 2) }}</td>
                <td>{{ $customer['tag'] }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    Mobile: <span class="text-danger">{{ $summary['mobile_count'] ?? 0 }}</span> | 
                    Email ID: <span class="text-primary">{{ $summary['email_count'] ?? 0 }}</span> Mail
                </td>
                <td class="text-end">Total: <span class="text-success">{{ number_format($summary['total_amount'] ?? 0, 2) }}</span></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="summary-section">
        <span>Total Records: {{ count($reportData) }}</span>
        <span>With GSTIN: {{ collect($reportData)->filter(fn($c) => !empty($c['gst_number']) && $c['gst_number'] !== '-')->count() }}</span>
        <span>Without GSTIN: {{ collect($reportData)->filter(fn($c) => empty($c['gst_number']) || $c['gst_number'] === '-')->count() }}</span>
    </div>
</body>
</html>
