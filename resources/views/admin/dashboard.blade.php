@extends('layouts.admin')

@section('title','Admin Dashboard')

@section('content')
<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --success-dark: #059669;
        --warning: #f59e0b;
        --warning-dark: #d97706;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --info: #3b82f6;
        --info-dark: #2563eb;
        --purple: #8b5cf6;
        --pink: #ec4899;
        --cyan: #06b6d4;
        --bg-primary: #f8fafc;
        --card-bg: rgba(255, 255, 255, 0.9);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.3);
    }

    .dashboard-container {
        background: #f1f5f9;
        min-height: 100vh;
        padding: 1.5rem;
    }

    /* Glassmorphism Card Base */
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -60%;
        right: 20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    .welcome-date {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Business Info Box - Clean Professional Look */
    .business-info-box {
        display: inline-block;
        background: rgba(0,0,0,0.15);
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.75rem;
        line-height: 1.6;
        max-width: 450px;
    }
    .business-info-box .info-row {
        display: flex;
        gap: 4px;
    }
    .business-info-box .info-label {
        min-width: 75px;
        opacity: 0.9;
    }
    .business-info-box .info-value {
        opacity: 0.95;
    }

    /* Keyboard Shortcuts Section */
    .shortcuts-section {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }

    .shortcuts-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .shortcuts-header h5 {
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1e293b;
    }

    .shortcuts-category-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        margin-top: 0.75rem;
    }

    .shortcuts-category-title:first-child {
        margin-top: 0;
    }

    .shortcut-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
        display: block;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.3);
        min-width: 110px;
        flex-shrink: 0;
    }

    /* Horizontal Scrollable Shortcuts */
    .shortcuts-scroll-container {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.5rem 0;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        /* Hide scrollbar but keep scroll functionality */
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }

    .shortcuts-scroll-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }

    .shortcut-card:hover {
        background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        transform: translateY(-2px) scale(1.02);
        color: white;
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
    }

    .shortcut-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.4rem;
        font-size: 1.2rem;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .shortcut-card:hover .shortcut-icon {
        background: rgba(255, 255, 255, 1);
        transform: scale(1.1);
    }
    
    .shortcut-card:hover .shortcut-icon i {
        color: #4f46e5 !important;
    }

    .shortcut-card .title {
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .shortcut-card kbd {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.15rem 0.35rem;
        border-radius: 4px;
        font-size: 0.6rem;
        font-family: 'Consolas', monospace;
        border: 1px solid rgba(255,255,255,0.4);
    }

    /* Stat Cards */
    .stat-card {
        padding: 1.25rem;
        border-radius: 16px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -15%;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .stat-card .icon-wrapper {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2.5rem;
        opacity: 0.15;
    }

    .stat-card .value {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0.25rem 0;
    }

    .stat-card .label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .stat-card .trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        margin-top: 0.5rem;
    }

    .stat-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%); }
    .stat-success { background: linear-gradient(135deg, var(--success) 0%, var(--cyan) 100%); }
    .stat-warning { background: linear-gradient(135deg, var(--warning) 0%, var(--pink) 100%); }
    .stat-danger { background: linear-gradient(135deg, var(--danger) 0%, var(--warning) 100%); }
    .stat-info { background: linear-gradient(135deg, var(--info) 0%, var(--primary) 100%); }

    /* Today's Stats Bar */
    .today-stats {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .today-stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .today-stat-item .icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .today-stat-item .info .value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .today-stat-item .info .label {
        font-size: 0.7rem;
        opacity: 0.7;
        text-transform: uppercase;
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .chart-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .chart-container {
        position: relative;
        height: 250px;
    }

    .chart-container-sm {
        height: 180px;
    }

    /* Tables */
    .modern-table {
        font-size: 0.8rem;
    }

    .modern-table thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        padding: 0.75rem;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .modern-table tbody tr:hover {
        background: #f8fafc;
    }

    .modern-table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
        border: none;
    }

    /* Avatar */
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.7rem;
        flex-shrink: 0;
    }

    /* Activity Items */
    .activity-item {
        padding: 0.75rem;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
        background: #f8fafc;
        border-left: 3px solid transparent;
    }

    .activity-item:hover {
        background: white;
        border-left-color: var(--primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Progress Items */
    .progress-item {
        margin-bottom: 1rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        margin-bottom: 0.35rem;
    }

    .progress-bar-custom {
        height: 6px;
        border-radius: 10px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    /* Badge */
    .badge-modern {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.7rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(20deg); }
        75% { transform: rotate(-15deg); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }

    @keyframes countUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .animate-in {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .wave-emoji {
        display: inline-block;
        animation: wave 1.5s ease-in-out infinite;
        transform-origin: 70% 70%;
    }

    .pulse-subtle {
        animation: pulse 3s ease-in-out infinite;
    }

    /* Live Clock */
    .live-clock {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'Consolas', monospace;
        display: flex;
        align-items: center;
    }

    /* Counter Animation */
    .counter-value {
        animation: countUp 0.8s ease-out;
    }

    /* Hover glow effect */
    .glow-on-hover {
        transition: all 0.3s ease;
    }

    .glow-on-hover:hover {
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    /* Shimmer loading effect */
    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    /* Quick Action Floating Button */
    .quick-action-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .quick-action-fab:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 6px 30px rgba(99, 102, 241, 0.6);
    }

    .quick-action-menu {
        position: fixed;
        bottom: 100px;
        right: 30px;
        display: none;
        flex-direction: column;
        gap: 10px;
        z-index: 999;
    }

    .quick-action-menu.active {
        display: flex;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: white;
        border-radius: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        text-decoration: none;
        color: #1e293b;
        font-size: 0.85rem;
        font-weight: 500;
        transform: translateX(100px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .quick-action-menu.active .quick-action-item {
        transform: translateX(0);
        opacity: 1;
    }

    .quick-action-menu.active .quick-action-item:nth-child(1) { transition-delay: 0.05s; }
    .quick-action-menu.active .quick-action-item:nth-child(2) { transition-delay: 0.1s; }
    .quick-action-menu.active .quick-action-item:nth-child(3) { transition-delay: 0.15s; }
    .quick-action-menu.active .quick-action-item:nth-child(4) { transition-delay: 0.2s; }

    .quick-action-item:hover {
        background: #6366f1;
        color: white;
        transform: translateX(-5px);
    }

    .quick-action-item i {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 50%;
        font-size: 0.8rem;
    }

    .quick-action-item:hover i {
        background: rgba(255,255,255,0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container { padding: 1rem; }
        .welcome-banner { padding: 1rem 1.25rem; }
        .shortcuts-section { padding: 1rem; }
        .stat-card .value { font-size: 1.5rem; }
        .today-stats { flex-direction: column; align-items: stretch; }
        .quick-action-fab { bottom: 20px; right: 20px; width: 50px; height: 50px; }
        .quick-action-menu { bottom: 85px; right: 20px; }
    }
</style>

<div class="dashboard-container">
    <!-- Welcome Banner with Business Info -->
    <div class="row mb-3">
        <div class="col-12 animate-in">
            <div class="welcome-banner">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold">
                            <span class="typing-text">Welcome back, {{ auth()->user()->full_name ?? 'Admin' }}!</span> 
                            <span class="wave-emoji">👋</span>
                        </h4>
                        <p class="mb-0 opacity-75" style="font-size: 0.9rem;">Here's what's happening with your business today.</p>
                        
                        @php $user = auth()->user(); @endphp
                        @if($user->licensed_to || $user->gst_no || $user->dl_no || $user->tin_no)
                        <div class="business-info-box mt-2">
                            @if($user->licensed_to)<div class="info-row"><span class="info-label">Licensed To</span><span class="info-value">:- {{ $user->licensed_to }}</span></div>@endif
                            @if($user->address)<div class="info-row"><span class="info-label">Address</span><span class="info-value">:- {{ $user->address }}</span></div>@endif
                            @if($user->telephone)<div class="info-row"><span class="info-label">Tel</span><span class="info-value">:- {{ $user->telephone }}</span></div>@endif
                            @if($user->tin_no)<div class="info-row"><span class="info-label">Tin No.</span><span class="info-value">:- {{ $user->tin_no }}</span></div>@endif
                            @if($user->gst_no)<div class="info-row"><span class="info-label">GST No.</span><span class="info-value">:- {{ $user->gst_no }}</span></div>@endif
                            @if($user->dl_no)<div class="info-row"><span class="info-label">DL No.</span><span class="info-value">:- {{ $user->dl_no }}</span></div>@endif
                            @if($user->dl_no_1)<div class="info-row"><span class="info-label">DL No.1</span><span class="info-value">:- {{ $user->dl_no_1 }}</span></div>@endif
                            @if($user->email)<div class="info-row"><span class="info-label">Email</span><span class="info-value">:- {{ $user->email }}</span></div>@endif
                        </div>
                        @endif
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <!-- Live Clock -->
                        <div class="live-clock d-none d-lg-flex">
                            <i class="bi bi-clock me-2"></i>
                            <span id="live-time">--:--:--</span>
                        </div>
                        <div class="welcome-date d-none d-md-block pulse-subtle">
                            <i class="bi bi-calendar3 me-2"></i>{{ now()->format('l, F j, Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Quick Access -->
    <div class="row mb-3">
        <div class="col-12 animate-in" style="animation-delay: 0.05s">
            <div class="shortcuts-section">
                <div class="shortcuts-header">
                    <h5><i class="bi bi-keyboard"></i> Quick Access Shortcuts</h5>
                    <span class="badge bg-dark" style="font-size: 0.7rem;">
                        <kbd>F1</kbd> for full list | <kbd>ESC</kbd> go back
                    </span>
                </div>
                <div class="shortcuts-scroll-container">
                    <!-- Sale -->
                    <a href="{{ route('admin.sale.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-cart-check" style="color: #10b981;"></i>
                        </div>
                        <div class="title">Sale</div>
                        <kbd>Ctrl</kbd>+<kbd>F1</kbd>
                    </a>
                    <!-- Purchase -->
                    <a href="{{ route('admin.purchase.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-box-seam" style="color: #3b82f6;"></i>
                        </div>
                        <div class="title">Purchase</div>
                        <kbd>Ctrl</kbd>+<kbd>F2</kbd>
                    </a>
                    <!-- Sale Return -->
                    <a href="{{ route('admin.sale-return.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-arrow-return-left" style="color: #f59e0b;"></i>
                        </div>
                        <div class="title">Sale Return</div>
                        <kbd>Ctrl</kbd>+<kbd>F3</kbd>
                    </a>
                    <!-- Receipt -->
                    <a href="{{ route('admin.customer-receipt.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-cash-stack" style="color: #8b5cf6;"></i>
                        </div>
                        <div class="title">Receipt</div>
                        <kbd>Ctrl</kbd>+<kbd>F5</kbd>
                    </a>
                    <!-- Payment -->
                    <a href="{{ route('admin.supplier-payment.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-wallet2" style="color: #ec4899;"></i>
                        </div>
                        <div class="title">Payment</div>
                        <kbd>Ctrl</kbd>+<kbd>F7</kbd>
                    </a>
                    <!-- Items -->
                    <a href="{{ route('admin.items.index') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-archive" style="color: #06b6d4;"></i>
                        </div>
                        <div class="title">Items</div>
                        <kbd>Ctrl</kbd>+<kbd>F12</kbd>
                    </a>
                    <!-- Customers -->
                    <a href="{{ route('admin.customers.index') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-people" style="color: #22c55e;"></i>
                        </div>
                        <div class="title">Customers</div>
                        <kbd>Ctrl</kbd>+<kbd>F11</kbd>
                    </a>
                    <!-- Suppliers -->
                    <a href="{{ route('admin.suppliers.index') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-truck" style="color: #f97316;"></i>
                        </div>
                        <div class="title">Suppliers</div>
                        <kbd>Ctrl</kbd>+<kbd>F9</kbd>
                    </a>
                    <!-- Purchase Return -->
                    <a href="{{ route('admin.purchase-return.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-arrow-return-right" style="color: #ef4444;"></i>
                        </div>
                        <div class="title">Pur. Return</div>
                        <kbd>Ctrl</kbd>+<kbd>F8</kbd>
                    </a>
                    <!-- Credit Note -->
                    <a href="{{ route('admin.credit-note.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-file-earmark-minus" style="color: #a855f7;"></i>
                        </div>
                        <div class="title">Credit Note</div>
                        <kbd>Ctrl</kbd>+<kbd>F6</kbd>
                    </a>
                    <!-- Debit Note -->
                    <a href="{{ route('admin.debit-note.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-file-earmark-plus" style="color: #fb923c;"></i>
                        </div>
                        <div class="title">Debit Note</div>
                        <kbd>Ctrl</kbd>+<kbd>F10</kbd>
                    </a>
                    <!-- Stock Adjustment -->
                    <a href="{{ route('admin.stock-adjustment.transaction') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-box-arrow-in-down" style="color: #0ea5e9;"></i>
                        </div>
                        <div class="title">Stock Adj.</div>
                        <kbd>Ctrl</kbd>+<kbd>F4</kbd>
                    </a>
                    <!-- General Ledger -->
                    <a href="{{ route('admin.general-ledger.index') }}" class="shortcut-card">
                        <div class="shortcut-icon">
                            <i class="bi bi-journal-text" style="color: #2dd4bf;"></i>
                        </div>
                        <div class="title">Ledger</div>
                        <kbd>Shift</kbd>+<kbd>Ins</kbd>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Quick Stats Bar -->
    <div class="row mb-3">
        <div class="col-12 animate-in" style="animation-delay: 0.1s">
            <div class="today-stats">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge text-warning" style="font-size: 1.5rem;"></i>
                    <span class="fw-bold">Today's Activity</span>
                </div>
                <div class="d-flex flex-wrap gap-4">
                    <div class="today-stat-item">
                        <div class="icon" style="background: rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-graph-up-arrow text-success"></i>
                        </div>
                        <div class="info">
                            <div class="value text-success">{{ $todayStats['sales'] }}</div>
                            <div class="label">Sales</div>
                        </div>
                    </div>
                    <div class="today-stat-item">
                        <div class="icon" style="background: rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-currency-rupee text-success"></i>
                        </div>
                        <div class="info">
                            <div class="value text-success">₹{{ number_format($todayStats['sales_amount']) }}</div>
                            <div class="label">Sales Amount</div>
                        </div>
                    </div>
                    <div class="today-stat-item">
                        <div class="icon" style="background: rgba(59, 130, 246, 0.2);">
                            <i class="bi bi-box-arrow-in-down text-info"></i>
                        </div>
                        <div class="info">
                            <div class="value text-info">{{ $todayStats['purchases'] }}</div>
                            <div class="label">Purchases</div>
                        </div>
                    </div>
                    <div class="today-stat-item">
                        <div class="icon" style="background: rgba(59, 130, 246, 0.2);">
                            <i class="bi bi-currency-rupee text-info"></i>
                        </div>
                        <div class="info">
                            <div class="value text-info">₹{{ number_format($todayStats['purchases_amount']) }}</div>
                            <div class="label">Purchase Amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.15s">
            <div class="stat-card stat-primary">
                <div class="label">Total Sales</div>
                <div class="value">{{ number_format($totalSales) }}</div>
                <div class="trend">
                    <i class="bi bi-arrow-{{ $salesGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($salesGrowth) }}% from last month
                </div>
                <div class="icon-wrapper"><i class="bi bi-cart3"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.2s">
            <div class="stat-card stat-success">
                <div class="label">Customers</div>
                <div class="value">{{ number_format($totalCustomers) }}</div>
                <div class="trend"><i class="bi bi-people"></i> Active</div>
                <div class="icon-wrapper"><i class="bi bi-people"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.25s">
            <div class="stat-card stat-warning">
                <div class="label">Items in Stock</div>
                <div class="value">{{ number_format($totalItems) }}</div>
                <div class="trend"><i class="bi bi-box"></i> In Inventory</div>
                <div class="icon-wrapper"><i class="bi bi-archive"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-in" style="animation-delay: 0.3s">
            <div class="stat-card stat-danger">
                <div class="label">Suppliers</div>
                <div class="value">{{ number_format($totalSuppliers) }}</div>
                <div class="trend"><i class="bi bi-truck"></i> Active</div>
                <div class="icon-wrapper"><i class="bi bi-building"></i></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-3 mb-3">
        <!-- Revenue Trend -->
        <div class="col-lg-8 animate-in" style="animation-delay: 0.35s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-graph-up me-2 text-primary"></i>Sales & Purchase Trend</h5>
                    <span class="badge bg-light text-dark">Last 7 Days</span>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Sales Pie Chart -->
        <div class="col-lg-4 animate-in" style="animation-delay: 0.4s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-pie-chart me-2 text-warning"></i>Sales by Category</h5>
                </div>
                <div class="chart-container chart-container-sm">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-2">
                    @forelse($categorySales->take(3) as $cat)
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.75rem;">
                        <span class="text-truncate" style="max-width: 150px;">{{ $cat->category }}</span>
                        <span class="fw-bold">₹{{ number_format($cat->total_amount) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0" style="font-size: 0.8rem;">No data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-3">
        <!-- Monthly Comparison -->
        <div class="col-lg-6 animate-in" style="animation-delay: 0.45s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-bar-chart me-2 text-success"></i>Monthly Comparison</h5>
                    <span class="badge bg-light text-dark">Last 6 Months</span>
                </div>
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Salesman Performance -->
        <div class="col-lg-6 animate-in" style="animation-delay: 0.5s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-person-badge me-2 text-info"></i>Salesman Performance</h5>
                    <span class="badge bg-light text-dark">This Month</span>
                </div>
                <div class="chart-container">
                    <canvas id="salesmanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status & Top Items -->
    <div class="row g-3 mb-3">
        <!-- Payment Status -->
        <div class="col-lg-4 animate-in" style="animation-delay: 0.55s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-credit-card me-2 text-danger"></i>Payment Status</h5>
                </div>
                <div class="chart-container chart-container-sm">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="mt-2">
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.8rem;">
                        <span><span class="badge bg-success me-1">&nbsp;</span>Cleared</span>
                        <span class="fw-bold">{{ $paymentStatus['paid'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.8rem;">
                        <span><span class="badge bg-warning me-1">&nbsp;</span>Pending</span>
                        <span class="fw-bold">{{ $paymentStatus['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.8rem;">
                        <span><span class="badge bg-danger me-1">&nbsp;</span>Total Due</span>
                        <span class="fw-bold text-danger">₹{{ number_format($paymentStatus['total_due'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Items -->
        <div class="col-lg-4 animate-in" style="animation-delay: 0.6s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-trophy me-2 text-warning"></i>Top Selling Items</h5>
                </div>
                @forelse($topItems as $index => $item)
                <div class="progress-item">
                    <div class="progress-label">
                        <span class="fw-medium text-truncate" style="max-width: 150px;">{{ $item->name }}</span>
                        <span class="text-muted">{{ number_format($item->total_quantity) }} units</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" 
                             style="width: {{ $topItems->max('total_quantity') > 0 ? ($item->total_quantity / $topItems->max('total_quantity')) * 100 : 0 }}%; 
                                    background: {{ ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ec4899'][$index % 5] }};"></div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-3 mb-0"><i class="bi bi-inbox me-1"></i>No sales data</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4 animate-in" style="animation-delay: 0.65s">
            <div class="chart-card h-100">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-activity me-2 text-purple"></i>Recent Activity</h5>
                </div>
                @forelse($recentActivities as $activity)
                <div class="activity-item">
                    <div class="d-flex align-items-start">
                        <div class="activity-icon bg-{{ $activity['color'] }} text-white me-2">
                            <i class="bi bi-{{ str_replace('fa-', '', $activity['icon']) }}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold" style="font-size: 0.8rem;">{{ $activity['title'] }}</div>
                            <p class="text-muted small mb-0 text-truncate">{{ $activity['description'] }}</p>
                            <small class="text-muted">{{ $activity['time'] }}</small>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-3 mb-0">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-3 mb-3">
        <!-- Recent Sales -->
        <div class="col-lg-8 animate-in" style="animation-delay: 0.7s">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-receipt me-2 text-success"></i>Recent Sales</h5>
                    <a href="{{ route('admin.sale.invoices') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye me-1"></i>View All
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="fw-bold text-primary text-decoration-none">
                                        #{{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-2">
                                            {{ strtoupper(substr($sale->customer->name ?? 'N', 0, 2)) }}
                                        </div>
                                        <span class="text-truncate" style="max-width: 120px;">{{ $sale->customer->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="fw-bold">₹{{ number_format($sale->net_amount, 2) }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M') }}</td>
                                <td>
                                    <a href="{{ route('admin.sale.show', $sale->id) }}" class="btn btn-sm btn-light">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No recent sales</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock & Top Customers -->
        <div class="col-lg-4 animate-in" style="animation-delay: 0.75s">
            <!-- Low Stock Alert -->
            <div class="chart-card mb-3">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Low Stock</h5>
                    <span class="badge bg-danger">{{ count($lowStockItems) }}</span>
                </div>
                @forelse($lowStockItems->take(4) as $item)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="font-size: 0.8rem;">
                    <span class="text-truncate" style="max-width: 140px;">{{ $item->name }}</span>
                    <span class="badge bg-danger">{{ $item->current_stock }} left</span>
                </div>
                @empty
                <div class="text-center py-3">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <p class="mb-0 mt-2 text-success" style="font-size: 0.8rem;">All stocked!</p>
                </div>
                @endforelse
            </div>

            <!-- Top Customers -->
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title"><i class="bi bi-star me-2 text-warning"></i>Top Customers</h5>
                </div>
                @forelse($topCustomers->take(4) as $customer)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" style="font-size: 0.8rem;">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-success text-white me-2" style="width: 28px; height: 28px; font-size: 0.6rem;">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                        <span class="text-truncate" style="max-width: 100px;">{{ $customer->name }}</span>
                    </div>
                    <span class="fw-bold">₹{{ number_format($customer->total_amount) }}</span>
                </div>
                @empty
                <p class="text-center text-muted py-3 mb-0">No data</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', -apple-system, sans-serif";
    Chart.defaults.color = '#64748b';

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [{
                label: 'Sales',
                data: @json($revenueData['sales']),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }, {
                label: 'Purchases',
                data: @json($revenueData['purchases']),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 15, font: { size: 11 } } },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': ₹' + ctx.parsed.y.toLocaleString()
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => '₹' + v.toLocaleString(), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json($categorySales->pluck('category')),
            datasets: [{
                data: @json($categorySales->pluck('total_amount')),
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#3b82f6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    callbacks: { label: ctx => ctx.label + ': ₹' + ctx.parsed.toLocaleString() }
                }
            },
            cutout: '65%'
        }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyComparison['labels']),
            datasets: [{
                label: 'Sales',
                data: @json($monthlyComparison['sales']),
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderRadius: 6
            }, {
                label: 'Purchases',
                data: @json($monthlyComparison['purchases']),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 15, font: { size: 11 } } },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    callbacks: { label: ctx => ctx.dataset.label + ': ₹' + ctx.parsed.y.toLocaleString() }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => '₹' + v.toLocaleString(), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // Payment Doughnut Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cleared', 'Pending'],
            datasets: [{
                data: [{{ $paymentStatus['paid'] }}, {{ $paymentStatus['pending'] }}],
                backgroundColor: ['#10b981', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '70%'
        }
    });

    // Salesman Performance Chart
    const salesmanCtx = document.getElementById('salesmanChart').getContext('2d');
    new Chart(salesmanCtx, {
        type: 'bar',
        data: {
            labels: @json($salesmanPerformance->pluck('name')),
            datasets: [{
                label: 'Sales Amount',
                data: @json($salesmanPerformance->pluck('total_amount')),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)'
                ],
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    callbacks: { label: ctx => '₹' + ctx.parsed.x.toLocaleString() }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { callback: v => '₹' + v.toLocaleString(), font: { size: 10 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // ========== LIVE CLOCK ==========
    function updateLiveClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeEl = document.getElementById('live-time');
        if (timeEl) {
            timeEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
    }
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    // ========== ANIMATED COUNTERS ==========
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-card .value');
        counters.forEach(counter => {
            counter.classList.add('counter-value');
        });
    }
    animateCounters();

    // ========== FLOATING ACTION BUTTON ==========
    const fab = document.getElementById('quick-action-fab');
    const fabMenu = document.getElementById('quick-action-menu');
    if (fab && fabMenu) {
        fab.addEventListener('click', function() {
            fabMenu.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!fab.contains(e.target) && !fabMenu.contains(e.target)) {
                fabMenu.classList.remove('active');
                fab.classList.remove('active');
            }
        });
    }
</script>

<!-- Floating Action Button for Quick Actions -->
<button id="quick-action-fab" class="quick-action-fab" title="Quick Actions">
    <i class="bi bi-plus-lg"></i>
</button>

<div id="quick-action-menu" class="quick-action-menu">
    <a href="{{ route('admin.sale.transaction') }}" class="quick-action-item">
        <i class="bi bi-cart-plus"></i>
        <span>New Sale</span>
    </a>
    <a href="{{ route('admin.purchase.transaction') }}" class="quick-action-item">
        <i class="bi bi-box-seam"></i>
        <span>New Purchase</span>
    </a>
    <a href="{{ route('admin.customer-receipt.transaction') }}" class="quick-action-item">
        <i class="bi bi-cash-stack"></i>
        <span>Receive Payment</span>
    </a>
    <a href="javascript:void(0)" onclick="openCalculator()" class="quick-action-item">
        <i class="bi bi-calculator"></i>
        <span>Calculator</span>
    </a>
</div>

<!-- Calculator Modal -->
<div id="calculator-modal" class="calculator-modal" style="display: none;">
    <div class="calculator-container">
        <div class="calculator-header">
            <h6><i class="bi bi-calculator me-2"></i>Quick Calculator</h6>
            <button type="button" class="btn-close btn-close-white btn-sm" onclick="closeCalculator()"></button>
        </div>
        <div class="calculator-body">
            <input type="text" id="calc-display" class="calc-display" readonly value="0">
            <div class="calc-buttons">
                <button class="calc-btn calc-clear" onclick="calcClear()">C</button>
                <button class="calc-btn calc-operator" onclick="calcBackspace()">⌫</button>
                <button class="calc-btn calc-operator" onclick="calcInput('%')">%</button>
                <button class="calc-btn calc-operator" onclick="calcInput('/')">÷</button>
                
                <button class="calc-btn" onclick="calcInput('7')">7</button>
                <button class="calc-btn" onclick="calcInput('8')">8</button>
                <button class="calc-btn" onclick="calcInput('9')">9</button>
                <button class="calc-btn calc-operator" onclick="calcInput('*')">×</button>
                
                <button class="calc-btn" onclick="calcInput('4')">4</button>
                <button class="calc-btn" onclick="calcInput('5')">5</button>
                <button class="calc-btn" onclick="calcInput('6')">6</button>
                <button class="calc-btn calc-operator" onclick="calcInput('-')">−</button>
                
                <button class="calc-btn" onclick="calcInput('1')">1</button>
                <button class="calc-btn" onclick="calcInput('2')">2</button>
                <button class="calc-btn" onclick="calcInput('3')">3</button>
                <button class="calc-btn calc-operator" onclick="calcInput('+')">+</button>
                
                <button class="calc-btn calc-zero" onclick="calcInput('0')">0</button>
                <button class="calc-btn" onclick="calcInput('.')">.</button>
                <button class="calc-btn calc-equals" onclick="calcEquals()">=</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Calculator Modal */
    .calculator-modal {
        position: fixed;
        bottom: 100px;
        right: 100px;
        z-index: 10001;
        animation: slideUp 0.2s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .calculator-container {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        width: 280px;
    }

    .calculator-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: rgba(255,255,255,0.1);
        color: white;
    }

    .calculator-header h6 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .calculator-body {
        padding: 16px;
    }

    .calc-display {
        width: 100%;
        background: #0f172a;
        border: none;
        color: #22d3ee;
        font-size: 2rem;
        font-family: 'Consolas', monospace;
        text-align: right;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 12px;
        font-weight: 600;
    }

    .calc-buttons {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .calc-btn {
        padding: 15px;
        font-size: 1.2rem;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #334155;
        color: white;
        font-weight: 600;
    }

    .calc-btn:hover {
        background: #475569;
        transform: scale(1.05);
    }

    .calc-btn:active {
        transform: scale(0.95);
    }

    .calc-operator {
        background: #6366f1;
    }

    .calc-operator:hover {
        background: #4f46e5;
    }

    .calc-clear {
        background: #ef4444;
    }

    .calc-clear:hover {
        background: #dc2626;
    }

    .calc-equals {
        background: #10b981;
    }

    .calc-equals:hover {
        background: #059669;
    }

    .calc-zero {
        grid-column: span 2;
    }
</style>

<script>
    let calcExpression = '';

    function openCalculator() {
        document.getElementById('calculator-modal').style.display = 'block';
        document.getElementById('quick-action-menu').classList.remove('active');
    }

    function closeCalculator() {
        document.getElementById('calculator-modal').style.display = 'none';
    }

    function calcInput(value) {
        if (calcExpression === '0' && value !== '.') {
            calcExpression = value;
        } else {
            calcExpression += value;
        }
        document.getElementById('calc-display').value = calcExpression || '0';
    }

    function calcClear() {
        calcExpression = '';
        document.getElementById('calc-display').value = '0';
    }

    function calcBackspace() {
        calcExpression = calcExpression.slice(0, -1);
        document.getElementById('calc-display').value = calcExpression || '0';
    }

    function calcEquals() {
        try {
            let result = eval(calcExpression);
            result = Math.round(result * 100) / 100; // Round to 2 decimals
            document.getElementById('calc-display').value = result;
            calcExpression = result.toString();
        } catch (e) {
            document.getElementById('calc-display').value = 'Error';
            calcExpression = '';
        }
    }

    // Close calculator on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCalculator();
        }
    });
</script>
@endsection