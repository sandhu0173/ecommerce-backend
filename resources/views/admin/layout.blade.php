<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.8);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: rgba(15, 23, 42, 0.98);
            border-right: 1px solid rgba(148, 163, 184, 0.15);
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .sidebar-header {
            padding: 24px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .sidebar-header-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6 to #06b6d4);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .sidebar-header h1 {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa to #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-nav-group {
            margin-bottom: 15px;
        }

        .sidebar-nav-label {
            padding: 0 20px 12px;
            font-size: 11px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            margin: 0 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-nav a:hover {
            color: #fff;
            background: rgba(59, 130, 246, 0.1);
            border-left-color: #3b82f6;
        }

        .sidebar-nav a.active {
            color: #fff;
            background: rgba(59, 130, 246, 0.15);
            border-left-color: #3b82f6;
        }

        .sidebar-nav i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .main-wrapper {
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f8fafc;
        }

        @media (max-width: 768px) {
            .main-wrapper {
                margin-left: 0;
            }
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
            min-width: 0;
        }

        .topbar-toggle {
            display: none;
            background: none;
            border: none;
            color: #cbd5e1;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 8px;
            margin: -8px;
        }

        .topbar-toggle:hover {
            color: #fff;
        }

        @media (max-width: 768px) {
            .topbar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .topbar-title {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            text-align: right;
        }

        .user-info p:first-child {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .user-info p:last-child {
            color: #64748b;
            font-size: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background: linear-gradient(135deg, #3b82f6 to #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #94a3b8;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid transparent;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 13px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 12px;
            color: #10b981;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 0;
            border-bottom: none;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            border: 1px solid #3b82f6;
        }

        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.15);
            border-color: rgba(148, 163, 184, 0.3);
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: 1px solid #ef4444;
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            color: #1e293b;
        }

        .table thead {
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-success {
            background: #d1fae5;
            color: #059669;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .alert-success {
            background: #d1fae5;
            border-color: #10b981;
            color: #059669;
        }

        .alert-error {
            background: #fee2e2;
            border-color: #dc2626;
            color: #b91c1c;
        }

        .alert i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #1e293b;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Color-coded stat card left borders */
        .stat-card:nth-child(1) {
            border-left-color: #3b82f6;
        }

        .stat-card:nth-child(2) {
            border-left-color: #10b981;
        }

        .stat-card:nth-child(3) {
            border-left-color: #06b6d4;
        }

        .stat-card:nth-child(4) {
            border-left-color: #8b5cf6;
        }

        .stat-card:nth-child(5) {
            border-left-color: #f59e0b;
        }

        .stat-card:nth-child(6) {
            border-left-color: #ec4899;
        }

        .stat-card:nth-child(7) {
            border-left-color: #ef4444;
        }

        .stat-card:nth-child(8) {
            border-left-color: #3b82f6;
        }

        /* Pagination styling */
        .pagination {
            text-align: center;
            padding: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 6px;
            background: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.2);
            text-decoration: none;
        }

        .pagination a:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .pagination .active {
            background: rgba(59, 130, 246, 0.3);
            color: #fff;
            border-color: #3b82f6;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-logo">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1>TailAdmin</h1>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-nav-group">
                <div class="sidebar-nav-label">MENU</div>
                <a href="{{ route('admin.dashboard') }}" class="@if(Route::currentRouteName() === 'admin.dashboard') active @endif">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>
            </div>

            <div class="sidebar-nav-group">
                <div class="sidebar-nav-label">PRODUCTS & ORDERS</div>
                <a href="{{ route('admin.products.index') }}" class="@if(str_starts_with(Route::currentRouteName(), 'admin.products')) active @endif">
                    <i class="fas fa-box"></i>
                    Products
                </a>
                <a href="{{ route('admin.orders.index') }}" class="@if(str_starts_with(Route::currentRouteName(), 'admin.orders')) active @endif">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </a>
            </div>

            <div class="sidebar-nav-group">
                <div class="sidebar-nav-label">MANAGEMENT</div>
                <a href="{{ route('admin.users.index') }}" class="@if(str_starts_with(Route::currentRouteName(), 'admin.users')) active @endif">
                    <i class="fas fa-users"></i>
                    Users
                </a>
                <a href="{{ route('admin.reports.index') }}" class="@if(str_starts_with(Route::currentRouteName(), 'admin.reports')) active @endif">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
            </div>

            <div class="sidebar-nav-group" style="margin-top: auto; border-top: 1px solid rgba(148, 163, 184, 0.1); padding-top: 15px;">
                <div class="sidebar-nav-label">OTHER</div>
                <a href="/">
                    <i class="fas fa-home"></i>
                    Back to Store
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="topbar-title">@yield('title')</h2>
            </div>

            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="user-info">
                        <p>{{ auth()->user()->name }}</p>
                        <p>Administrator</p>
                    </div>
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Errors:</strong>
                        <ul style="margin-top: 10px; margin-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-times-circle"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
