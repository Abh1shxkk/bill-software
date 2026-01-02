@extends('layouts.admin')

@section('title', 'Sale Return Replacement List')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 d-flex align-items-center"><i class="bi bi-arrow-return-left me-2"></i> Sale Return Replacement List</h4>
        <div class="text-muted small">View and manage sale return replacement transactions</div>
    </div>
    <div>
        <a href="{{ route('admin.sale-return-replacement.transaction') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Transaction
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded">
    <div class="card mb-4 border-0">
        <div class="card-body">
            <!-- Search Filter -->
             <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                         <input type="text" name="search" class="form-control" placeholder="Search Trn No / Customer" value="{{ request('search') }}">
                         <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                 <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin.sale-return-replacement.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Series</th>
                    <th>Trn No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th class="text-end">Net Amt</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trn)
                <tr>
                    <td>{{ $trn->id }}</td>
                    <td>{{ $trn->series }}</td>
                    <td>{{ $trn->trn_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($trn->trn_date)->format('d/m/Y') }}</td>
                    <td>{{ $trn->customer_name }}</td>
                    <td class="text-end"><span class="badge bg-success">{{ number_format($trn->net_amt, 2) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.sale-return-replacement.show', $trn->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                        <!-- Modification link passing Trn No like Sale module does with invoice_no -->
                        <a href="#" onclick="alert('Please use Modification page and load Trn No: {{ $trn->trn_no }}')" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        
                        <form action="{{ route('admin.sale-return-replacement.destroy', $trn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
            
    <div class="card-footer bg-light">
        {{ $transactions->withQueryString()->links() }}
    </div>
</div>
@endsection
