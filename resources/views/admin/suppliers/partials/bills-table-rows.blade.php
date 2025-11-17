@foreach($bills as $bill)
  <tr>
    <td>{{ $loop->iteration }}</td>
    <td>PB / {{ $bill->trn_no }}</td>
    <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d-M-y') }}</td>
    <td>{{ $bill->supplier->name ?? 'N/A' }}</td>
    <td>{{ $bill->supplier->address ?? 'N/A' }}</td>
    <td class="text-end">{{ number_format($bill->net_amount, 2) }}</td>
  </tr>
@endforeach
