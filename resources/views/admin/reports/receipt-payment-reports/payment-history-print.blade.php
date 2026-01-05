<!DOCTYPE html><html><head><title>Payment History - Print</title>
<style>body { font-family: Arial, sans-serif; font-size: 11px; margin: 10px; }.header { text-align: center; margin-bottom: 15px; background-color: #f5c6cb; padding: 10px; }.header h3 { margin: 0; color: #721c24; }table { width: 100%; border-collapse: collapse; }th, td { border: 1px solid #999; padding: 4px 8px; }th { background-color: #e0e0e0; }.text-end { text-align: right; }.totals { background-color: #e0e0e0; font-weight: bold; }@media print { .header, th, .totals { -webkit-print-color-adjust: exact; } }</style>
</head><body onload="window.print()">
<div class="header"><h3>Payment History</h3><p>From: {{ $request->from_date ?? date('Y-m-d') }} To: {{ $request->to_date ?? date('Y-m-d') }}</p></div>
<table><thead><tr><th>S.No</th><th>Date</th><th>Voucher No</th><th>Supplier</th><th>Mode</th><th>Cheque/Ref</th><th class="text-end">Amount</th><th>Narration</th></tr></thead>
<tbody>@php $total = 0; @endphp @foreach($reportData ?? [] as $index => $row) @php $total += $row['amount']; @endphp<tr><td>{{ $index + 1 }}</td><td>{{ $row['date'] }}</td><td>{{ $row['voucher_no'] }}</td><td>{{ $row['supplier_name'] }}</td><td>{{ $row['mode'] }}</td><td>{{ $row['cheque_ref'] }}</td><td class="text-end">{{ number_format($row['amount'], 2) }}</td><td>{{ $row['narration'] }}</td></tr>@endforeach</tbody>
<tfoot><tr class="totals"><td colspan="6" class="text-end">Total:</td><td class="text-end">{{ number_format($total, 2) }}</td><td></td></tr></tfoot></table>
</body></html>
