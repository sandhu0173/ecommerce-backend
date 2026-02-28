@extends('admin.layout')

@section('title', 'Reports & Analytics')

@section('content')
<!-- Key Metrics -->
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-dollar-sign"></i> Total Revenue</div>
                <div class="stat-value">${{ number_format($reports['total_revenue'], 2) }}</div>
                <div class="stat-change" style="color: #10b981;">✓ All time</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-dollar-sign"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-shopping-cart"></i> Total Orders</div>
                <div class="stat-value">{{ $reports['total_orders'] }}</div>
                <div class="stat-change" style="color: #3b82f6;">Orders completed</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #06b6d4;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-calculator"></i> Avg Order Value</div>
                <div class="stat-value">${{ number_format($reports['avg_order_value'], 2) }}</div>
                <div class="stat-change" style="color: #06b6d4;">Per transaction</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-calculator"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-percentage"></i> Conversion Rate</div>
                <div class="stat-value">{{ number_format($reports['conversion_rate'], 2) }}%</div>
                <div class="stat-change" style="color: #8b5cf6;">Visitor conversion</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-percentage"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-check-circle"></i> Fulfillment Rate</div>
                <div class="stat-value">{{ number_format($reports['fulfillment_rate'], 2) }}%</div>
                <div class="stat-change" style="color: #f59e0b;">Orders fulfilled</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div class="stat-card" style="border-left: 4px solid #ec4899;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <div class="stat-label"><i class="fas fa-boxes"></i> Items Sold</div>
                <div class="stat-value">{{ $reports['total_items_sold'] }}</div>
                <div class="stat-change" style="color: #ec4899;">Total units</div>
            </div>
            <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pie-chart"></i> Order Status Distribution</h3>
        </div>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-credit-card"></i> Payment Status Distribution</h3>
        </div>
        <div class="chart-container">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>
</div>

<!-- Revenue Trend -->
<div class="card" style="margin-bottom: 30px;">
    <div class="card-header">
        <h3><i class="fas fa-chart-bar"></i> Revenue Trend by Month</h3>
        <span style="font-size: 12px; color: #94a3b8;">Historical performance</span>
    </div>
    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- Top Products Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-fire"></i> Top Selling Products</h3>
        <span style="font-size: 12px; color: #94a3b8;">By units sold</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-product-hunt"></i> Product Name</th>
                    <th style="text-align: right;"><i class="fas fa-chart-line"></i> Units Sold</th>
                    <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Revenue</th>
                    <th style="text-align: center;"><i class="fas fa-star"></i> Performance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($top_products as $key => $product)
                <tr style="transition: all 0.2s ease;">
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="background: linear-gradient(135deg, #3b82f6 to #06b6d4); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                                {{ $key + 1 }}
                            </div>
                            <strong>{{ $product->name }}</strong>
                        </div>
                    </td>
                    <td style="text-align: right;">
                        <span style="background: rgba(59, 130, 246, 0.2); padding: 8px 12px; border-radius: 6px; color: #3b82f6; font-weight: 600;">
                            {{ $product->sold ?? 0 }} units
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <span style="background: rgba(16, 185, 129, 0.2); padding: 8px 12px; border-radius: 6px; color: #10b981; font-weight: 600;">
                            ${{ number_format(($product->sold ?? 0) * (floatval($product->price ?? 0)), 2) }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if(($product->sold ?? 0) > 50)
                            <span class="badge badge-success"><i class="fas fa-arrow-up"></i> Excellent</span>
                        @elseif(($product->sold ?? 0) > 20)
                            <span class="badge badge-info"><i class="fas fa-arrow-right"></i> Good</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-arrow-down"></i> Fair</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-chart-empty"></i></div>
                            <div style="font-size: 16px; color: #cbd5e1;">No sales data available</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Sales data will appear here once products are sold</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Order Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
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
                        padding: 15
                    }
                }
            }
        }
    });

    // Payment Status Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: @json(array_keys($payment_status)),
            datasets: [{
                data: @json(array_values($payment_status)),
                backgroundColor: [
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
                        padding: 15
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($revenue_by_month)),
            datasets: [{
                label: 'Monthly Revenue ($)',
                data: @json(array_values($revenue_by_month)),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: '#3b82f6',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#e2e8f0'
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#cbd5e1'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: '#cbd5e1'
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
