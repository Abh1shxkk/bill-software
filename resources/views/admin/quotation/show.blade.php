@extends('layouts.admin')

@section('title', 'View Quotation - ' . $quotation->quotation_no)

@push('styles')
<style>
    .quotation-header { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    .quotation-header .label { font-weight: 600; color: #666; font-size: 12px; }
    .quotation-header .value { font-size: 14px; color: #333; }
    .items-table th { background: #90EE90; font-size: 12px; }
    .items-table td { font-size: 12px; }
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>
@endpush

@section('content')
<section class="py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Quotation: {{ $quotation->quotation_no }}</h4>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="bi bi-printer me-1"></i> Print
                </button>
                <a href="{{ route('admin.quotation.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="quotation-header">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="label">Quotation No</div>
                            <div class="value"><strong>{{ $quotation->quotation_no }}</strong></div>
                        </div>
                        <div class="col-md-3">
                            <div class="label">Date</div>
                            <div class="value">{{ $quotation->quotation_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="label">Customer</div>
                            <div class="value">{{ $quotation->customer_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="badge bg-{{ $quotation->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($quotation->remarks)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="label">Remarks</div>
                            <div class="value">{{ $quotation->remarks }}</div>
                        </div>
                    </div>
                    @endif
                    @if($quotation->terms)
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="label">Terms</div>
                            <div class="value">{{ $quotation->terms }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered items-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 80px;">Code</th>
                                <th>Item Name</th>
                                <th style="width: 80px;">Packing</th>
                                <th style="width: 100px;">Company</th>
                                <th style="width: 80px;" class="text-end">Qty</th>
                                <th style="width: 100px;" class="text-end">Rate</th>
                                <th style="width: 100px;" class="text-end">MRP</th>
                                <th style="width: 120px;" class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_code }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->packing }}</td>
                                <td>{{ $item->company_name }}</td>
                                <td class="text-end">{{ number_format($item->qty, 3) }}</td>
                                <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->mrp, 2) }}</td>
                                <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" class="text-end"><strong>Sub Total:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($quotation->items->sum('amount'), 2) }}</strong></td>
                            </tr>
                            @if($quotation->discount_percent > 0)
                            <tr>
                                <td colspan="8" class="text-end">Discount ({{ number_format($quotation->discount_percent, 2) }}%):</td>
                                <td class="text-end text-danger">-₹{{ number_format($quotation->items->sum('amount') * $quotation->discount_percent / 100, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="table-success">
                                <td colspan="8" class="text-end"><strong>Net Amount:</strong></td>
                                <td class="text-end"><strong>₹{{ number_format($quotation->net_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
