<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') - MediBill Platform</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #0ea5e9;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #e2e8f0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
            color: white;
        }

        .sidebar-brand small {
            color: rgba(255,255,255,0.9);
            font-size: 0.75rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .nav-link {
            color: #64748b;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            color: var(--primary-color);
            background: #f1f5f9;
            border-left-color: var(--primary-color);
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-top: 1rem;
            font-weight: 600;
        }

        /* Main content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .content-wrapper {
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
        }

        .card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stats-card .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stats-card .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: #1e293b;
        }

        .stats-card .stats-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Tables */
        .table {
            color: #1e293b;
        }

        .table th {
            border-color: #e2e8f0;
            font-weight: 600;
            color: #64748b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f8fafc;
        }

        .table td {
            border-color: #e2e8f0;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #4338ca 100%);
        }

        .btn-outline-light {
            border-color: #e2e8f0;
            color: #64748b;
        }

        .btn-outline-light:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        /* Badges */
        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        .badge-expired {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        .badge-suspended {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }

        .badge-expiring {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }

        /* Forms */
        .form-control, .form-select {
            background: white;
            border-color: #e2e8f0;
            color: #1e293b;
        }

        .form-control:focus, .form-select:focus {
            background: white;
            border-color: var(--primary-color);
            color: #1e293b;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-label {
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 12px;
        }

        /* Page header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0.5rem;
        }

        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #1e293b;
        }

        /* User dropdown */
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #1e293b;
            text-decoration: none;
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .dropdown-menu {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            color: #64748b;
        }

        .dropdown-item:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        /* License key display */
        .license-key {
            font-family: 'Fira Code', monospace;
            background: #f8fafc;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
            border: 1px solid #e2e8f0;
        }

        /* Pagination */
        .pagination .page-link {
            background: white;
            border-color: #e2e8f0;
            color: #64748b;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-shield-halved me-2"></i>MediBill</h4>
            <small>Super Admin Panel</small>
        </div>
        
        <div class="sidebar-menu">
            <div class="sidebar-section">Main</div>
            <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            
            <div class="sidebar-section">Management</div>
            <a href="{{ route('superadmin.organizations.index') }}" class="nav-link {{ request()->routeIs('superadmin.organizations.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Organizations</span>
            </a>
            <a href="{{ route('superadmin.licenses.index') }}" class="nav-link {{ request()->routeIs('superadmin.licenses.*') ? 'active' : '' }}">
                <i class="fas fa-key"></i>
                <span>Licenses</span>
            </a>
            
            <div class="sidebar-section">Quick Actions</div>
            <a href="{{ route('superadmin.organizations.create') }}" class="nav-link">
                <i class="fas fa-plus-circle"></i>
                <span>New Organization</span>
            </a>
            <a href="{{ route('superadmin.licenses.create') }}" class="nav-link">
                <i class="fas fa-key"></i>
                <span>Generate License</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div>
                @yield('breadcrumb')
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-bell"></i>
                </a>
                
                <div class="dropdown user-dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->full_name ?? 'SA', 0, 2)) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ auth()->user()->full_name ?? 'Super Admin' }}</div>
                            <small class="text-muted">Super Administrator</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
