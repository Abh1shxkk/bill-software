<!DOCTYPE html>
<html>
<head>
    <title>Purchase Return List</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; color: #666; font-style: italic; }
        .header p { margin: 2px 0; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 4px 6px; }
        th { background: #d3d3d3; font-weight: bold; text-align: left; font-size: 9px; }
        td { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #d3d3d3; font-weight: bold; }
        .section-header { background: #ffa07a; text-align: center; font-weight: bold; padding: 5px; }
        .section-header-blue { color: blue; }
        .section-header-red { color: red; }
        .two-col { display: flex; gap: 20px; }
        .two-col > div { flex: 1; }
        @media print { body { margin: 0; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h3>PURCHASE RETURN LIST</h3>
        <p>Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p>
        <p>Filter: {{ $adjustedFilter == 'Y' ? 'Adjusted' : ($adjustedFilter == 'N' ? 'Unadjusted' : 'All') }} | 
           Flag: {{ $flag == '1' ? 'PR' : ($flag == '2' ? 'CN' : ($flag == '3' ? 'DN' : ($flag == '4' ? 'PE' : 'ALL'))) }}</p>
    </div>

    <!-- Main Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Date</th>
                <th style="width: 70px;">Bill No.</th>
                <th style="width: 50px;">Code</th>
                <th>Party Name</th>
                <th class="text-right" style="width: 80px;">Amount</th>
                <th class="text-right" style="width: 80px;">Taxable</th>
                <th class="text-right" style="width: 60px;">Tax</th>
                <th class="text-right" style="width: 80px;">Due Amt</th>
                <th class="text-right" style="width: 80px;">Adj. Amt</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns ?? [] as $return)
            @php $adjAmt = $return->net_amount - ($return->balance_amount ?? 0); @endphp
            <tr>
                <td>{{ $return->return_date->format('d-m-Y') }}</td>
                <td>{{ $return->pr_no }}</td>
                <td>{{ $return->supplier->code ?? '' }}</td>
                <td>{{ $return->supplier->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($return->net_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($return->nt_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($return->tax_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($return->balance_amount ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($adjAmt, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No data found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">TOTAL :</td>
                <td class="text-right">{{ number_format($totals['amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['taxable'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['tax'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['due_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($totals['adj_amount'] ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Bottom Section -->
    <div class="two-col">
        <!-- Adjustment Detail -->
        <div>
            <div class="section-header section-header-red">------: Adjustment Detail :------</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">Trans.No.</th>
                        <th style="width: 100px;">Date</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments ?? [] as $adj)
                    <tr>
                        <td>{{ $adj->transaction_no ?? '-' }}</td>
                        <td>{{ $adj->adjustment_date ? $adj->adjustment_date->format('d-m-Y') : '-' }}</td>
                        <td class="text-right">{{ number_format($adj->amount ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">No adjustments</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>TOTAL :</td>
                        <td></td>
                        <td class="text-right">{{ number_format($adjustmentTotal ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Reference Detail -->
        <div>
            <div class="section-header section-header-blue">------: Reference Detail :------</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">Trn. No.</th>
                        <th style="width: 80px;">PBill No.</th>
                        <th style="width: 80px;">PBill Date</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($references ?? [] as $ref)
                    <tr>
                        <td>{{ $ref->trn_no ?? '-' }}</td>
                        <td>{{ $ref->pbill_no ?? '-' }}</td>
                        <td>{{ $ref->pbill_date ? $ref->pbill_date->format('d-m-Y') : '-' }}</td>
                        <td class="text-right">{{ number_format($ref->amount ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No references</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
