@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-shopping-cart"></i> Total Orders</div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-change" style="color: #10b981;">✓ +12.5% from last month</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-dollar-sign"></i> Total Revenue</div>
                <div class="stat-value">${{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="stat-change" style="color: #10b981;">✓ +8.2% from last month</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-clock"></i> Pending Orders</div>
                <div class="stat-value">{{ $stats['pending_orders'] }}</div>
                <div class="stat-change" style="color: #f59e0b;">⚠ Need attention</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-clock"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #06b6d4;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-truck"></i> Shipped Orders</div>
                <div class="stat-value">{{ $stats['shipped_orders'] }}</div>
                <div class="stat-change" style="color: #06b6d4;">📍 In transit</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-truck"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-cube"></i> Total Products</div>
                <div class="stat-value">{{ $stats['total_products'] }}</div>
                <div class="stat-change" style="color: #8b5cf6;">Active products</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-cube"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-exclamation-triangle"></i> Low Stock</div>
                <div class="stat-value">{{ $stats['low_stock'] }}</div>
                <div class="stat-change" style="color: #ef4444;">🔴 Needs restocking</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #06b6d4;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-users"></i> Total Users</div>
                <div class="stat-value">{{ $stats['total_users'] }}</div>
                <div class="stat-change" style="color: #06b6d4;">Active customers</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-calculator"></i> Avg Order Value</div>
                <div class="stat-value">${{ number_format($stats['avg_order_value'], 2) }}</div>
                <div class="stat-change" style="color: #3b82f6;">Per transaction</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-calculator"></i></div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 24px; margin-bottom: 30px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pie-chart"></i> Order Status Distribution</h3>
            <span style="font-size: 12px; color: #94a3b8;">Total: {{ $stats['total_orders'] }} orders</span>
        </div>
        <div class="chart-container">
            <canvas id="orderStatusChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-line"></i> Revenue Trend</h3>
            <span style="font-size: 12px; color: #94a3b8;">Last 7 days</span>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Recent Orders</h3>
        <div class="card-header-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> Order #</th>
                    <th><i class="fas fa-user"></i> Customer</th>
                    <th><i class="fas fa-dollar-sign"></i> Amount</th>
                    <th><i class="fas fa-box"></i> Status</th>
                    <th><i class="fas fa-credit-card"></i> Payment</th>
                    <th><i class="fas fa-calendar"></i> Date</th>
                    <th><i class="fas fa-cog"></i> Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders as $order)
                <tr style="transition: all 0.2s ease;">
                    <td><strong style="color: #3b82f6;">#{{ $order->order_number }}</strong></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 to #06b6d4); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                {{ substr($order->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $order->user->name }}</div>
                                <div style="font-size: 12px; color: #94a3b8;">{{ $order->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><strong style="color: #10b981; font-size: 16px;">${{ number_format($order->total, 2) }}</strong></td>
                    <td>
                        @switch($order->status)
                            @case('pending')
                                <span class="badge badge-warning"><i class="fas fa-hourglass-start"></i> Pending</span>
                            @break
                            @case('processing')
                                <span class="badge badge-info"><i class="fas fa-cogs"></i> Processing</span>
                            @break
                            @case('shipped')
                                <span class="badge badge-info"><i class="fas fa-truck"></i> Shipped</span>
                            @break
                            @case('delivered')
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Delivered</span>
                            @break
                            @case('cancelled')
                                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Cancelled</span>
                            @break
                        @endswitch
                    </td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> {{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 13px;">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-eye"></i> View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-inbox"></i></div>
                            <div style="font-size: 16px; color: #cbd5e1;">No recent orders</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Orders will appear here as customers place them</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Order Status Chart
    const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($order_status)),
            datasets: [{
                data: @json(array_values($order_status)),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(147, 112, 219, 0.8)',
                ],
                borderColor: 'rgba(30, 41, 59, 0.8)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#e2e8f0',
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($revenue_data)),
            datasets: [{
                label: 'Revenue ($)',
                data: @json(array_values($revenue_data)),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#e2e8f0',
                        font: { size: 12 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#cbd5e1',
                        font: { size: 11 }
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: '#cbd5e1',
                        font: { size: 11 }
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endsection
