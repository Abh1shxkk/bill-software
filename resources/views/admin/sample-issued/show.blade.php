@extends('layouts.admin')

@section('title', 'Sample Issued - View Transaction')

@push('styles')
<style>
    .detail-card { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
    .detail-label { font-weight: 600; color: #6c757d; font-size: 11px; }
    .detail-value { font-size: 13px; color: #212529; }
    .table-items th { background: #ffb6c1; font-size: 11px; font-weight: 600; }
    .table-items td { font-size: 11px; }
    .badge-party { padding: 5px 10px; }
</style>
@endpush

@section('content')
<section class="py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="bi bi-eye me-2"></i> Sample Issued - View Transaction</h4>
                <div class="text-muted small">Transaction No: <strong>{{ $transaction->trn_no }}</strong></div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.sample-issued.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-list me-1"></i> View All
                </a>
                <a href="{{ route('admin.sample-issued.edit', $transaction->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Modify
                </a>
                <a href="{{ route('admin.sample-issued.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Transaction
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body">
                <!-- Header Details -->
                <div class="detail-card">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="detail-label">Transaction No.</div>
                            <div class="detail-value"><strong>{{ $transaction->trn_no }}</strong></div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Date</div>
                            <div class="detail-value">{{ $transaction->transaction_date->format('d-M-Y') }}</div>
                        </div>
                        <div class="col-md-1">
                            <div class="detail-label">Day</div>
                            <div class="detail-value">{{ $transaction->day_name }}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Party Type</div>
                            <div class="detail-value">
                                <span class="badge bg-info badge-party">{{ $partyTypes[$transaction->party_type] ?? $transaction->party_type }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Party Name</div>
                            <div class="detail-value"><strong>{{ $transaction->party_name ?? '-' }}</strong></div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">
                                @if($transaction->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($transaction->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transport Details -->
                <div class="detail-card">
                    <h6 class="mb-3"><i class="bi bi-truck me-2"></i>Transport Details</h6>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="detail-label">GR No.</div>
                            <div class="detail-value">{{ $transaction->gr_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">GR Date</div>
                            <div class="detail-value">{{ $transaction->gr_date ? $transaction->gr_date->format('d-M-Y') : '-' }}</div>
                        </div>
                        <div class="col-md-1">
                            <div class="detail-label">Cases</div>
                            <div class="detail-value">{{ $transaction->cases ?? 0 }}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Road Permit No.</div>
                            <div class="detail-value">{{ $transaction->road_permit_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="detail-label">Truck No.</div>
                            <div class="detail-value">{{ $transaction->truck_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Transport</div>
                            <div class="detail-value">{{ $transaction->transport ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                @if($transaction->remarks)
                <div class="detail-card">
                    <div class="detail-label">Remarks</div>
                    <div class="detail-value">{{ $transaction->remarks }}</div>
                </div>
                @endif

                <!-- Items Table -->
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Items ({{ $transaction->items->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-items mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th style="width: 70px;">Code</th>
                                        <th>Item Name</th>
                                        <th style="width: 80px;">Batch</th>
                                        <th style="width: 70px;">Expiry</th>
                                        <th style="width: 60px;">Qty</th>
                                        <th style="width: 80px;">Rate</th>
                                        <th style="width: 100px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transaction->items as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $item->item_code }}</td>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->batch_no ?? '-' }}</td>
                                        <td>{{ $item->expiry ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($item->qty, 0) }}</td>
                                        <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-end"><strong>{{ number_format($item->amount, 2) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: #fff3cd;">
                                        <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($transaction->total_qty, 0) }}</strong></td>
                                        <td></td>
                                        <td class="text-end"><strong>₹ {{ number_format($transaction->net_amount, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row mt-3">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <div class="detail-card" style="background: #e8f5e9;">
                            <div class="row">
                                <div class="col-6">
                                    <div class="detail-label">Total Quantity</div>
                                    <div class="detail-value fs-5">{{ number_format($transaction->total_qty, 0) }}</div>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="detail-label">Net Amount</div>
                                    <div class="detail-value fs-4 text-success"><strong>₹ {{ number_format($transaction->net_amount, 2) }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <a href="{{ route('admin.sample-issued.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.sample-issued.edit', $transaction->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Modify
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="cancelTransaction()">
                            <i class="bi bi-x-circle me-1"></i> Cancel Transaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function cancelTransaction() {
    if (!confirm('Are you sure you want to cancel this sample issue? Stock will be restored.')) {
        return;
    }

    fetch('{{ route("admin.sample-issued.destroy", $transaction->id) }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.sample-issued.index") }}';
        } else {
            alert(data.message || 'Error cancelling transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error cancelling transaction');
    });
}
</script>
@endpush
