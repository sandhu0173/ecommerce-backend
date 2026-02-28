<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a via-slate-900 to-slate-800);
            min-height: 100vh;
            color: #e2e8f0;
        }

        .header {
            padding: 20px 30px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa to #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .nav-links a:hover {
            background: rgba(59, 130, 246, 0.2);
            color: #fff;
        }

        .nav-links .admin-btn {
            background: linear-gradient(135deg, #3b82f6 to #06b6d4);
            color: white;
        }

        .nav-links .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #3b82f6 to #06b6d4);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 60px 30px;
            text-align: center;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: white;
        }

        p {
            font-size: 18px;
            color: #cbd5e1;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 60px;
            text-align: left;
        }

        .feature {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .feature h3 {
            font-size: 20px;
            margin-bottom: 12px;
            color: #60a5fa;
        }

        .feature p {
            color: #94a3b8;
            text-align: center;
            font-size: 14px;
            margin: 0;
        }

        .cta-section {
            margin-top: 60px;
            padding: 40px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
        }

        .cta-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: white;
        }

        .cta-section p {
            color: #cbd5e1;
            margin-bottom: 30px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 to #06b6d4);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: rgba(107, 114, 128, 0.3);
            color: #cbd5e1;
            border: 1px solid rgba(107, 114, 128, 0.5);
        }

        .btn-secondary:hover {
            background: rgba(107, 114, 128, 0.5);
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">📊 Admin Panel</div>
        <div class="nav-links">
            @if(auth()->check())
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="admin-btn">Go to Dashboard</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-links" style="background: none; border: none; color: #cbd5e1; cursor: pointer; padding: 10px 20px; font-size: 16px;">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endif
        </div>
    </div>

    <div class="container">
        <h1>Welcome to Admin Panel</h1>
        <p>Manage your e-commerce store with our powerful admin dashboard</p>

        <div class="features">
            <div class="feature">
                <h3>📊 Dashboard</h3>
                <p>Real-time analytics and business metrics at a glance</p>
            </div>
            <div class="feature">
                <h3>📦 Products</h3>
                <p>Manage inventory, pricing, and product information</p>
            </div>
            <div class="feature">
                <h3>🛒 Orders</h3>
                <p>Track orders, manage shipping, and payment status</p>
            </div>
            <div class="feature">
                <h3>👥 Users</h3>
                <p>Manage customers and their order history</p>
            </div>
            <div class="feature">
                <h3>📈 Reports</h3>
                <p>Comprehensive analytics and business insights</p>
            </div>
            <div class="feature">
                <h3>🔒 Secure</h3>
                <p>Admin-only access with role-based protection</p>
            </div>
        </div>

        <div class="cta-section">
            <h2>Ready to Get Started?</h2>
            <p>Access the admin panel to manage your entire e-commerce business</p>
            <div class="btn-group">
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Admin Login</a>
                    @if(!auth()->check())
                        <a href="/" class="btn btn-secondary">Back to Store</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</body>
</html>
