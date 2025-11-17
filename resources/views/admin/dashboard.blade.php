@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .dashboard-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    }

    .stat-card {
        position: relative;
        padding: 1.5rem;
        color: white;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        margin-top: 0.5rem;
    }

    .stat-icon {
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        opacity: 0.2;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .activity-item {
        padding: 1rem;
        border-left: 3px solid transparent;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
        background: #f8f9fa;
    }

    .activity-item:hover {
        background: white;
        border-left-color: #667eea;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.125rem;
    }

    .progress-item {
        margin-bottom: 1.25rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .table-modern {
        font-size: 0.875rem;
    }

    .table-modern thead th {
        background: #f8f9fa;
        border: none;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table-modern tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 1rem;
        vertical-align: middle;
        border: none;
    }

    .badge-modern {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Welcome Section -->
    <div class="row mb-4 animate-in">
        <div class="col-12">
            <div class="dashboard-card" style="background: var(--primary-gradient); color: white; padding: 2rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-2">Welcome back, {{ auth()->user()->full_name }}! 👋</h4>
                        <p class="mb-0 opacity-75">Here's what's happening with your business today.</p>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="badge bg-white text-primary px-3 py-2">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.1s">
            <div class="dashboard-card stat-card" style="background: var(--primary-gradient);">
                <div class="stat-label">Total Sales</div>
                <div class="stat-value">{{ number_format($totalSales) }}</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($salesGrowth) }}% from last month</span>
                </div>
                <i class="fas fa-shopping-cart stat-icon"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.2s">
            <div class="dashboard-card stat-card" style="background: var(--success-gradient);">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value">{{ number_format($totalCustomers) }}</div>
                <div class="stat-trend">
                    <i class="fas fa-users"></i>
                    <span>Active Customers</span>
                </div>
                <i class="fas fa-users stat-icon"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.3s">
            <div class="dashboard-card stat-card" style="background: var(--warning-gradient);">
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ number_format($totalItems) }}</div>
                <div class="stat-trend">
                    <i class="fas fa-box-open"></i>
                    <span>In Inventory</span>
                </div>
                <i class="fas fa-box stat-icon"></i>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.4s">
            <div class="dashboard-card stat-card" style="background: var(--danger-gradient);">
                <div class="stat-label">Total Suppliers</div>
                <div class="stat-value">{{ number_format($totalSuppliers) }}</div>
                <div class="stat-trend">
                    <i class="fas fa-handshake"></i>
                    <span>Active Suppliers</span>
                </div>
                <i class="fas fa-truck stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Revenue Overview -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Sales & Purchase Trend (Last 7 Days)</h5>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Payment Status</h5>
                </div>
                <div class="chart-container" style="height: 200px;">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-success me-2"></i>Cleared</span>
                        <span class="fw-bold">{{ $paymentStatus['paid'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-circle text-warning me-2"></i>Pending</span>
                        <span class="fw-bold">{{ $paymentStatus['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-circle text-danger me-2"></i>Total Due</span>
                        <span class="fw-bold">₹{{ number_format($paymentStatus['total_due'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison Chart -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Sales vs Purchases (Last 6 Months)</h5>
                </div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Activity -->
    <div class="row g-3 mb-4">
        <!-- Recent Sales -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Recent Sales Transactions</h5>
                    <a href="{{ route('admin.sale.invoices') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Salesman</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="text-decoration-none fw-bold text-primary">
                                        #{{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            {{ strtoupper(substr($sale->customer->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <span>{{ $sale->customer->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ $sale->salesman->name ?? 'N/A' }}</td>
                                <td class="fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="btn btn-sm btn-outline-primary btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No recent sales found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Top Items -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="chart-card mb-3">
                <div class="chart-header">
                    <h5 class="chart-title">Recent Activity</h5>
                </div>
                @forelse($recentActivities as $activity)
                <div class="activity-item">
                    <div class="d-flex align-items-start">
                        <div class="activity-icon bg-{{ $activity['color'] }} text-white me-3">
                            <i class="fas {{ $activity['icon'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-1">{{ $activity['title'] }}</div>
                            <p class="text-muted small mb-1">{{ $activity['description'] }}</p>
                            <small class="text-muted">{{ $activity['time'] }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-3">No recent activity</p>
                @endforelse
            </div>

            <!-- Top Selling Items -->
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Top Selling Items</h5>
                </div>
                @forelse($topItems as $index => $item)
                <div class="progress-item">
                    <div class="progress-label">
                        <span class="fw-medium">{{ $item->name }}</span>
                        <span class="text-muted">{{ number_format($item->total_quantity) }} units</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ ($item->total_quantity / $topItems->max('total_quantity')) * 100 }}%; 
                                    background: {{ ['#667eea', '#11998e', '#f093fb', '#4facfe', '#fa709a'][$index % 5] }};">
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-3">No sales data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Customers & Low Stock -->
    <div class="row g-3 mb-4">
        <!-- Top Customers -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Top Customers</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total Sales</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-success text-white me-2">
                                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                                        </div>
                                        <span>{{ $customer->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $customer->total_sales }}</td>
                                <td class="fw-bold">₹{{ number_format($customer->total_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No customer data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">Low Stock Alert</h5>
                    <span class="badge bg-danger">{{ count($lowStockItems) }} Items</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current Stock</th>
                                <th>Min Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockItems as $item)
                            <tr>
                                <td class="fw-medium">{{ $item->name }}</td>
                                <td>{{ $item->current_stock }}</td>
                                <td>{{ $item->minimum_stock }}</td>
                                <td>
                                    <span class="badge-modern bg-danger text-white">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Low
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-success py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p class="mb-0">All items are well stocked!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // Revenue Chart (Last 7 Days)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [
                {
                    label: 'Sales',
                    data: @json($revenueData['sales']),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Purchases',
                    data: @json($revenueData['purchases']),
                    borderColor: '#11998e',
                    backgroundColor: 'rgba(17, 153, 142, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#11998e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Payment Status Doughnut Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentChart = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cleared', 'Pending'],
            datasets: [{
                data: [{{ $paymentStatus['paid'] }}, {{ $paymentStatus['pending'] }}],
                backgroundColor: ['#28a745', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8
                }
            },
            cutout: '70%'
        }
    });

    // Monthly Comparison Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyComparison['labels']),
            datasets: [
                {
                    label: 'Sales',
                    data: @json($monthlyComparison['sales']),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false
                },
                {
                    label: 'Purchases',
                    data: @json($monthlyComparison['purchases']),
                    backgroundColor: 'rgba(17, 153, 142, 0.8)',
                    borderRadius: 8,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection