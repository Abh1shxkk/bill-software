<!DOCTYPE html>
<html>
<head>
    <title>Ledger Printing</title>
    <style>
        body { 
            font-family: 'Times New Roman', serif; 
            font-size: 11px; 
            margin: 10px; 
        }
        .ledger {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .ledger-header { 
            background-color: #ffc4d0; 
            font-style: italic; 
            padding: 8px 15px; 
            text-align: center;
            border: 1px solid #000;
            border-bottom: none;
        }
        .ledger-header h3 {
            margin: 0;
            color: #800080;
            font-size: 14px;
        }
        .ledger-info {
            padding: 5px 15px;
            border: 1px solid #000;
            border-bottom: none;
            background-color: #f5f5f5;
        }
        .ledger-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .ledger-table th, .ledger-table td { 
            border: 1px solid #000; 
            padding: 4px 6px; 
        }
        .ledger-table th { 
            background-color: #d0d0d0; 
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .opening-row {
            background-color: #ffffcc;
        }
        .closing-row {
            background-color: #ccffcc;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            .ledger { page-break-after: always; }
            .ledger:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div style="text-align: center; margin-bottom: 15px;">
        <strong>Ledger Report</strong><br>
        Period: {{ \Carbon\Carbon::parse($fromDate)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-M-Y') }}
    </div>

    @forelse($reportData as $ledger)
    <div class="ledger">
        <div class="ledger-header">
            <h3>{{ $ledger['name'] }}</h3>
        </div>
        <div class="ledger-info">
            <strong>Code:</strong> {{ $ledger['code'] }} | 
            <strong>Type:</strong> {{ $ledgerType === 'C' ? 'Customer' : 'Supplier' }}
        </div>
        
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 90px;">Date</th>
                    <th>Particulars</th>
                    <th style="width: 80px;">Voucher No</th>
                    <th style="width: 100px;">Debit (₹)</th>
                    <th style="width: 100px;">Credit (₹)</th>
                    <th style="width: 100px;">Balance (₹)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Opening Balance -->
                <tr class="opening-row">
                    <td>{{ \Carbon\Carbon::parse($fromDate)->format('d-M-Y') }}</td>
                    <td><strong>Opening Balance</strong></td>
                    <td></td>
                    <td class="text-end">{{ $ledger['opening_balance'] > 0 ? number_format($ledger['opening_balance'], 2) : '' }}</td>
                    <td class="text-end">{{ $ledger['opening_balance'] < 0 ? number_format(abs($ledger['opening_balance']), 2) : '' }}</td>
                    <td class="text-end">{{ number_format($ledger['opening_balance'], 2) }}</td>
                </tr>

                @foreach($ledger['entries'] as $entry)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d-M-Y') }}</td>
                    <td>{{ $entry['particulars'] }}</td>
                    <td>{{ $entry['voucher_no'] }}</td>
                    <td class="text-end">{{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '' }}</td>
                    <td class="text-end">{{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '' }}</td>
                    <td class="text-end">{{ number_format($entry['balance'], 2) }}</td>
                </tr>
                @endforeach

                <!-- Closing Balance -->
                <tr class="closing-row">
                    <td colspan="3" class="text-end">Closing Balance:</td>
                    <td class="text-end">{{ number_format($ledger['total_debit'], 2) }}</td>
                    <td class="text-end">{{ number_format($ledger['total_credit'], 2) }}</td>
                    <td class="text-end">{{ number_format($ledger['closing_balance'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @empty
    <div style="text-align: center; padding: 50px;">
        <h3>No ledger data found for the selected criteria</h3>
    </div>
    @endforelse

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Generated on: {{ now()->format('d-M-Y h:i A') }}
    </div>
</body>
</html>
