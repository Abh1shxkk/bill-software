@extends('layouts.admin')

@section('title', 'Sales Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sales Reports</h4>
        <div class="btn-group">
            <button type="button" class="btn btn-success btn-sm" onclick="exportReport('csv')">
                <i class="bi bi-file-earmark-excel me-1"></i>Export CSV
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="exportReport('pdf')">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.sales') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                               value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $customerId == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Salesman</label>
                        <select name="salesman_id" class="form-select form-select-sm">
                            <option value="">All Salesmen</option>
                            @foreach($salesmen as $salesman)
                                <option value="{{ $salesman->id }}" {{ $salesmanId == $salesman->id ? 'selected' : '' }}>
                                    {{ $salesman->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Sales</h6>
                            <h3 class="mb-0">₹{{ number_format($totalSales, 2) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Invoices</h6>
                            <h3 class="mb-0">{{ number_format($totalInvoices) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-receipt fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Avg Sale Value</h6>
                            <h3 class="mb-0">₹{{ number_format($avgSaleValue, 2) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up-arrow fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-dark-50">Total Tax</h6>
                            <h3 class="mb-0">₹{{ number_format($totalTax, 2) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-percent fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Daily Sales Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Sales by Salesman</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesmanChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Monthly Comparison (Last 6 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>Top 10 Customers</h6>
                </div>
                <div class="card-body">
                    <canvas id="customersChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Selling Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th class="text-end">Qty Sold</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td class="text-end">{{ number_format($item->total_qty) }}</td>
                                    <td class="text-end">₹{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Sales</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->sale_date->format('d-m-Y') }}</td>
                                    <td>{{ $sale->invoice_no }}</td>
                                    <td>{{ $sale->customer->name ?? 'N/A' }}</td>
                                    <td class="text-end">₹{{ number_format($sale->net_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent sales</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper function to format date
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        return day + '-' + month;
    }

    // Daily Sales Chart
    const dailyData = @json($dailySales);
    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(d => formatDate(d.sale_date)),
            datasets: [{
                label: 'Sales Amount',
                data: dailyData.map(d => d.total),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₹' + value.toLocaleString()
                    }
                }
            }
        }
    });

    // Salesman Pie Chart
    const salesmanData = @json($salesBySalesman);
    new Chart(document.getElementById('salesmanChart'), {
        type: 'doughnut',
        data: {
            labels: salesmanData.map(d => d.salesman?.name || 'Unknown'),
            datasets: [{
                data: salesmanData.map(d => d.total_sales),
                backgroundColor: [
                    '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                    '#20c997', '#fd7e14', '#6c757d', '#0dcaf0', '#d63384'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                }
            }
        }
    });

    // Monthly Comparison Chart
    const monthlyData = @json($monthlyData);
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => monthNames[d.month - 1] + ' ' + d.year),
            datasets: [{
                label: 'Monthly Sales',
                data: monthlyData.map(d => d.total),
                backgroundColor: '#198754'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₹' + value.toLocaleString()
                    }
                }
            }
        }
    });

    // Top Customers Chart
    const customerData = @json($topCustomers);
    new Chart(document.getElementById('customersChart'), {
        type: 'bar',
        data: {
            labels: customerData.map(d => d.customer?.name?.substring(0, 15) || 'Unknown'),
            datasets: [{
                label: 'Sales',
                data: customerData.map(d => d.total_sales),
                backgroundColor: '#6f42c1'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '₹' + value.toLocaleString()
                    }
                }
            }
        }
    });
});

function exportReport(type) {
    const params = new URLSearchParams(window.location.search);
    const url = type === 'csv' 
        ? '{{ route("admin.reports.sales.export-csv") }}?' + params.toString()
        : '{{ route("admin.reports.sales.export-pdf") }}?' + params.toString();
    window.open(url, '_blank');
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn-group, form, .card-header { display: none !important; }
    .card { border: 1px solid #ddd !important; }
}
</style>
@endpush
