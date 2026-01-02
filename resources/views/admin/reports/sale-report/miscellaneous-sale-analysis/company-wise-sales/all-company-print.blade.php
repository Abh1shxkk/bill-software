<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><title>Company Wise Sales - All Company</title>
<style>* { margin: 0; padding: 0; box-sizing: border-box; } body { font-family: Arial, sans-serif; font-size: 11px; } .container { padding: 10px; } .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #8B0000; padding-bottom: 5px; } .header h2 { color: #8B0000; font-style: italic; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #333; padding: 4px 6px; } th { background: #f0f0f0; } .text-right { text-align: right; } .total-row { background: #e0e0e0; font-weight: bold; } @media print { body { font-size: 10px; } }</style>
</head><body><div class="container">
<div class="header"><h2>Company Wise Sales - All Company</h2><p>{{ \Carbon\Carbon::parse($dateFrom)->format('d-M-Y') }} To {{ \Carbon\Carbon::parse($dateTo)->format('d-M-Y') }}</p></div>
<table><thead><tr><th>Company</th><th class="text-right">Qty</th><th class="text-right">Free</th><th class="text-right">Amount</th></tr></thead>
<tbody>@foreach($data as $row)<tr><td>{{ $row['company_name'] }}</td><td class="text-right">{{ $row['qty'] }}</td><td class="text-right">{{ $row['free_qty'] }}</td><td class="text-right">{{ number_format($row['amount'], 2) }}</td></tr>@endforeach
<tr class="total-row"><td>Grand Total</td><td class="text-right">{{ $totals['qty'] }}</td><td class="text-right">{{ $totals['free_qty'] }}</td><td class="text-right">{{ number_format($totals['amount'], 2) }}</td></tr>
</tbody></table></div><script>window.onload=function(){window.print();}</script></body></html>
