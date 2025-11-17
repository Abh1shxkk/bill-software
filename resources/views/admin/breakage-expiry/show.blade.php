@extends('layouts.admin')
@section('title', 'Breakage/Expiry Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle me-2"></i> Breakage/Expiry Transaction Details
        </h4>
        <div class="text-muted small">Complete details of breakage/expiry transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.breakage-expiry.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Invoices
        </a>
    </div>
</div>

<!-- Transaction Header -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Transaction Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold">SR No:</label>
                        <div>{{ $transaction->sr_no }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Date:</label>
                        <div>{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Customer:</label>
                        <div>{{ $transaction->customer_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Salesman:</label>
                        <div>{{ $transaction->salesman_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Series:</label>
                        <div>{{ $transaction->series ?? 'BE' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">GST VNo:</label>
                        <div>{{ $transaction->gst_vno ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Note Type:</label>
                        <div>{{ $transaction->note_type ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">With GST:</label>
                        <div>{{ $transaction->with_gst ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Inc:</label>
                        <div>{{ $transaction->inc ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Rev Charge:</label>
                        <div>{{ $transaction->rev_charge ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Adjusted:</label>
                        <div>{{ $transaction->adjusted ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Dis/Rpl:</label>
                        <div>{{ $transaction->dis_rpl ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Breakage:</label>
                        <div>{{ $transaction->brk ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Expiry:</label>
                        <div>{{ $transaction->exp ?? 'N/A' }}</div>
                    </div>
                    @if($transaction->remarks)
                    <div class="col-md-12">
                        <label class="fw-bold">Remarks:</label>
                        <div>{{ $transaction->remarks }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-currency-rupee me-2"></i>Amount Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>MRP Value:</span>
                    <strong>₹{{ number_format($transaction->mrp_value ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Amount:</span>
                    <strong>₹{{ number_format($transaction->gross_amount, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <strong>₹{{ number_format($transaction->discount_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Scheme:</span>
                    <strong>₹{{ number_format($transaction->scheme_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax Amount:</span>
                    <strong>₹{{ number_format($transaction->tax_amount, 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Amount:</span>
                    <strong class="text-success fs-5">₹{{ number_format($transaction->net_amount, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Transaction Items</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Batch</th>
                        <th>Expiry</th>
                        <th>Type</th>
                        <th>HSN</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">MRP</th>
                        <th class="text-end">S.Rate</th>
                        <th class="text-end">Scm%</th>
                        <th class="text-end">Dis%</th>
                        <th class="text-end">Tax%</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->batch_no ?? '-' }}</td>
                        <td>{{ $item->expiry ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $item->br_ex == 'B' ? 'danger' : 'warning' }}">
                                {{ $item->br_ex == 'B' ? 'Breakage' : 'Expiry' }}
                            </span>
                        </td>
                        <td>{{ $item->hsn_code ?? '-' }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end">₹{{ number_format($item->mrp, 2) }}</td>
                        <td class="text-end">₹{{ number_format($item->s_rate ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($item->scm_percent ?? 0, 2) }}%</td>
                        <td class="text-end">{{ number_format($item->dis_percent ?? 0, 2) }}%</td>
                        <td class="text-end">{{ number_format($item->tax_percent ?? 0, 2) }}%</td>
                        <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
