@extends('layouts.admin')

@section('title', 'Quotations')

@section('content')
<section class="py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Quotations</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.quotation.transaction') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> New Quotation
                </a>
                <a href="{{ route('admin.quotation.modification') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square me-1"></i> Modification
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th>T.No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Remarks</th>
                                <th class="text-end">Discount %</th>
                                <th class="text-end">Net Amount</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quotation)
                            <tr>
                                <td><strong>{{ $quotation->quotation_no }}</strong></td>
                                <td>{{ $quotation->quotation_date->format('d-m-Y') }}</td>
                                <td>{{ $quotation->customer_name ?? '-' }}</td>
                                <td>{{ Str::limit($quotation->remarks, 30) }}</td>
                                <td class="text-end">{{ number_format($quotation->discount_percent, 2) }}%</td>
                                <td class="text-end">₹{{ number_format($quotation->net_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $quotation->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.quotation.show', $quotation->id) }}" class="btn btn-sm btn-info py-0 px-2" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No quotations found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $quotations->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
