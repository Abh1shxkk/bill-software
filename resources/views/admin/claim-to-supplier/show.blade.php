@extends('layouts.admin')
@section('title', 'Claim to Supplier Details')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center">
            <i class="bi bi-file-earmark-text me-2"></i> Claim to Supplier Transaction Details
        </h4>
        <div class="text-muted small">Complete details of claim to supplier transaction</div>
    </div>
    <div>
        <a href="{{ route('admin.claim-to-supplier.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Invoices
        </a>
        <a href="{{ route('admin.claim-to-supplier.modification') }}?claim_no={{ $transaction->claim_no }}" class="btn btn-outline-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
</div>

<!-- Transaction Header -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Transaction Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Claim No:</label>
                        <div>{{ $transaction->claim_no }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Claim Date:</label>
                        <div>{{ $transaction->claim_date ? $transaction->claim_date->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Supplier:</label>
                        <div>{{ $transaction->supplier_name ?? ($transaction->supplier->name ?? '-') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Invoice No:</label>
                        <div>{{ $transaction->invoice_no ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Invoice Date:</label>
                        <div>{{ $transaction->invoice_date ? $transaction->invoice_date->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Series:</label>
                        <div>{{ $transaction->series ?? 'CTS' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">GST VNo:</label>
                        <div>{{ $transaction->gst_vno ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Tax Flag:</label>
                        <div>{{ $transaction->tax_flag == 'Y' ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Status:</label>
                        <div>
                            <span class="badge bg-{{ $transaction->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($transaction->status ?? 'active') }}
                            </span>
                        </div>
                    </div>
                    @if($transaction->narration)
                    <div class="col-md-12">
                        <label class="fw-bold">Narration:</label>
                        <div>{{ $transaction->narration }}</div>
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
                    <span>NT Amount:</span>
                    <strong>₹{{ number_format($transaction->nt_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <strong>₹{{ number_format($transaction->dis_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Scheme:</span>
                    <strong>₹{{ number_format($transaction->scm_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax Amount:</span>
                    <strong>₹{{ number_format($transaction->tax_amount ?? 0, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>TCS Amount:</span>
                    <strong>₹{{ number_format($transaction->tcs_amount ?? 0, 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Amount:</span>
                    <strong class="text-success fs-5">₹{{ number_format($transaction->net_amount ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Claim Items</h5>
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
                        <th>HSN</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Free</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">MRP</th>
                        <th class="text-end">Dis%</th>
                        <th class="text-end">Tax%</th>
                        <th class="text-end">Net Amt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->items ?? [] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->batch_no ?? '-' }}</td>
                        <td>{{ $item->expiry_date ? $item->expiry_date->format('m/Y') : '-' }}</td>
                        <td>{{ $item->hsn_code ?? '-' }}</td>
                        <td class="text-end">{{ $item->qty }}</td>
                        <td class="text-end">{{ $item->free_qty ?? 0 }}</td>
                        <td class="text-end">₹{{ number_format($item->pur_rate ?? 0, 2) }}</td>
                        <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($item->dis_percent ?? 0, 2) }}%</td>
                        <td class="text-end">{{ number_format(($item->cgst_percent ?? 0) + ($item->sgst_percent ?? 0), 2) }}%</td>
                        <td class="text-end">₹{{ number_format($item->net_amount ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-3">No items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
