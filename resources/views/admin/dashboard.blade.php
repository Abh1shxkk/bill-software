@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
{{-- Flash Messages --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="card mb-3" style="background: #6366f1; color: white; border: none; border-radius: 12px;">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="mb-1 fw-bold">Welcome back, {{ auth()->user()->full_name ?? 'Admin' }}! 👋</h5>
                    <small class="opacity-75">Here's what's happening with your business today.</small>
                    @php $user = auth()->user(); @endphp
                    @if($user->licensed_to || $user->gst_no)
                    <div class="mt-2 small opacity-90">
                        @if($user->licensed_to)<span class="me-3">Licensed To: {{ $user->licensed_to }}</span>@endif
                        @if($user->gst_no)<span>GST: {{ $user->gst_no }}</span>@endif
                    </div>
                    @endif
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="small opacity-75">{{ now()->format('l, F j, Y') }}</div>
                    @if(!$user->isAdmin())
                    <span class="badge bg-light text-dark mt-1">
                        <i class="bi bi-person-badge me-1"></i>User Account
                    </span>
                    @else
                    <span class="badge bg-warning text-dark mt-1">
                        <i class="bi bi-shield-check me-1"></i>Administrator
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts -->
    <div class="card mb-3" style="border-radius: 12px;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-keyboard me-2"></i>Quick Access</h6>
                <small class="text-muted"><kbd>F1</kbd> for full list</small>
            </div>
            <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
                @if(auth()->user()->hasPermission('sale', 'view'))
                <a href="{{ route('admin.sale.transaction') }}" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-cart-check me-1"></i>Sale
                </a>
                @endif
                @if(auth()->user()->hasPermission('purchase', 'view'))
                <a href="{{ route('admin.purchase.transaction') }}" class="btn btn-info btn-sm px-3 text-white">
                    <i class="bi bi-box-seam me-1"></i>Purchase
                </a>
                @endif
                @if(auth()->user()->hasPermission('sale-return', 'view'))
                <a href="{{ route('admin.sale-return.transaction') }}" class="btn btn-warning btn-sm px-3">
                    <i class="bi bi-arrow-return-left me-1"></i>Sale Return
                </a>
                @endif
                @if(auth()->user()->hasPermission('customer-receipt', 'view'))
                <a href="{{ route('admin.customer-receipt.transaction') }}" class="btn btn-success btn-sm px-3">
                    <i class="bi bi-cash-stack me-1"></i>Receipt
                </a>
                @endif
                @if(auth()->user()->hasPermission('supplier-payment', 'view'))
                <a href="{{ route('admin.supplier-payment.transaction') }}" class="btn btn-danger btn-sm px-3">
                    <i class="bi bi-wallet2 me-1"></i>Payment
                </a>
                @endif
                @if(auth()->user()->hasPermission('items', 'view'))
                <a href="{{ route('admin.items.index') }}" class="btn btn-secondary btn-sm px-3">
                    <i class="bi bi-archive me-1"></i>Items
                </a>
                @endif
                @if(auth()->user()->hasPermission('customers', 'view'))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary btn-sm px-3">
                    <i class="bi bi-people me-1"></i>Customers
                </a>
                @endif
                @if(auth()->user()->hasPermission('suppliers', 'view'))
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-truck me-1"></i>Suppliers
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="card mb-3" style="background: #1e293b; color: white; border-radius: 12px;">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge text-warning"></i>
                    <span class="fw-bold">Today's Activity</span>
                </div>
                <div class="d-flex flex-wrap gap-4">
                    <div class="text-center">
                        <div class="fw-bold text-success">{{ $todayStats['sales'] }}</div>
                        <small class="opacity-75">Sales</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-success">₹{{ number_format($todayStats['sales_amount']) }}</div>
                        <small class="opacity-75">Sales Amt</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-info">{{ $todayStats['purchases'] }}</div>
                        <small class="opacity-75">Purchases</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-info">₹{{ number_format($todayStats['purchases_amount']) }}</div>
                        <small class="opacity-75">Purchase Amt</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #6366f1; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Total Sales</div>
                    <div class="h4 mb-1 fw-bold">{{ number_format($totalSales) }}</div>
                    <small><i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i> {{ abs($salesGrowth) }}% from last month</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #10b981; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Customers</div>
                    <div class="h4 mb-1 fw-bold">{{ number_format($totalCustomers) }}</div>
                    <small><i class="bi bi-people"></i> Active</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #f59e0b; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Items in Stock</div>
                    <div class="h4 mb-1 fw-bold">{{ number_format($totalItems) }}</div>
                    <small><i class="bi bi-box"></i> In Inventory</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100" style="background: #ef4444; color: white; border: none; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="small text-uppercase opacity-75">Suppliers</div>
                    <div class="h4 mb-1 fw-bold">{{ number_format($totalSuppliers) }}</div>
                    <small><i class="bi bi-truck"></i> Active</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales & Low Stock -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card" style="border-radius: 12px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-success"></i>Recent Sales</h6>
                    <a href="{{ route('admin.sale.invoices') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td><a href="{{ route('admin.sale.show', $sale->id) }}" class="fw-bold text-primary">#{{ $sale->invoice_no }}</a></td>
                                    <td>{{ Str::limit($sale->customer->name ?? 'N/A', 20) }}</td>
                                    <td class="fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No recent sales</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card" style="border-radius: 12px;">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Low Stock <span class="badge bg-danger ms-1">{{ count($lowStockItems) }}</span></h6>
                </div>
                <div class="card-body py-2">
                    @forelse($lowStockItems->take(5) as $item)
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="font-size: 0.85rem;">
                        <span class="text-truncate" style="max-width: 150px;">{{ $item->name }}</span>
                        <span class="badge bg-danger">{{ $item->current_stock }}</span>
                    </div>
                    @empty
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 mt-1 text-success small">All items stocked!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 1rem;
    background: #f1f5f9;
    min-height: 100vh;
}
</style>
@endsection
