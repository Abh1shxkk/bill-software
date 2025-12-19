@extends('layouts.admin')

@section('title', 'Multi Voucher #' . $voucher->voucher_no)

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Multi Voucher #{{ $voucher->voucher_no }}</h5>
        <a href="{{ route('admin.multi-voucher.index') }}" class="btn btn-light btn-sm">Back to List</a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Date:</strong> {{ $voucher->voucher_date?->format('d-M-Y') }}</p>
                <p><strong>Voucher No:</strong> {{ $voucher->voucher_no }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Total Amount:</strong> {{ number_format($voucher->total_amount, 2) }}</p>
                <p><strong>Status:</strong> <span class="badge bg-{{ $voucher->status == 'active' ? 'success' : 'warning' }}">{{ ucfirst($voucher->status) }}</span></p>
            </div>
        </div>
        <p><strong>Narration:</strong> {{ $voucher->narration }}</p>
        
        <h6>Entries</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr><th>Date</th><th>Debit Account</th><th>Credit Account</th><th>Amount</th><th>DrSlcd</th></tr>
            </thead>
            <tbody>
                @foreach($voucher->entries as $entry)
                <tr>
                    <td>{{ $entry->entry_date?->format('d-M-Y') }}</td>
                    <td>{{ $entry->debit_account_name }} <small class="text-muted">({{ $entry->debit_account_type }})</small></td>
                    <td>{{ $entry->credit_account_name }} <small class="text-muted">({{ $entry->credit_account_type }})</small></td>
                    <td class="text-end">{{ number_format($entry->amount, 2) }}</td>
                    <td>{{ $entry->dr_slcd }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td class="text-end"><strong>{{ number_format($voucher->total_amount, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
