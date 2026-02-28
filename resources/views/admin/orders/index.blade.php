@extends('admin.layout')

@section('title', 'Orders Management')

@section('content')
<!-- Orders Management Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3><i class="fas fa-shopping-cart"></i> Orders Management</h3>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Manage customer orders and shipments</p>
        </div>
    </div>

    <div style="margin-bottom: 20px; padding: 20px 24px; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; border-bottom: 1px solid rgba(148, 163, 184, 0.1);">
        <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 10px;">
            <select name="status" onchange="this.form.submit()" style="flex: 1; padding: 8px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                <option value="">All Statuses</option>
                <option value="pending" @if(request('status') === 'pending') selected @endif>Pending</option>
                <option value="processing" @if(request('status') === 'processing') selected @endif>Processing</option>
                <option value="shipped" @if(request('status') === 'shipped') selected @endif>Shipped</option>
                <option value="delivered" @if(request('status') === 'delivered') selected @endif>Delivered</option>
                <option value="cancelled" @if(request('status') === 'cancelled') selected @endif>Cancelled</option>
            </select>
        </form>

        <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 10px;">
            <select name="payment_status" onchange="this.form.submit()" style="flex: 1; padding: 8px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                <option value="">All Payment Statuses</option>
                <option value="pending" @if(request('payment_status') === 'pending') selected @endif>Pending</option>
                <option value="paid" @if(request('payment_status') === 'paid') selected @endif>Paid</option>
                <option value="failed" @if(request('payment_status') === 'failed') selected @endif>Failed</option>
                <option value="refunded" @if(request('payment_status') === 'refunded') selected @endif>Refunded</option>
            </select>
        </form>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary" style="text-align: center;"><i class="fas fa-redo"></i> Reset Filters</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> Order #</th>
                    <th><i class="fas fa-user"></i> Customer</th>
                    <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Total</th>
                    <th style="text-align: center;"><i class="fas fa-box"></i> Status</th>
                    <th style="text-align: center;"><i class="fas fa-credit-card"></i> Payment</th>
                    <th style="text-align: center;"><i class="fas fa-barcode"></i> Items</th>
                    <th style="text-align: center;"><i class="fas fa-calendar"></i> Date</th>
                    <th style="text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
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
                    <td style="text-align: right;">
                        <strong style="color: #10b981; font-size: 16px;">${{ number_format($order->total, 2) }}</strong>
                    </td>
                    <td style="text-align: center;">
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
                    <td style="text-align: center;">
                        @if($order->payment_status === 'paid')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="badge badge-warning"><i class="fas fa-undo"></i> Refunded</span>
                        @else
                            <span class="badge badge-warning"><i class="fas fa-exclamation"></i> {{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span style="background: rgba(59, 130, 246, 0.2); padding: 6px 10px; border-radius: 6px; color: #3b82f6; font-weight: 600;">
                            {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <div style="font-size: 13px;">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-eye"></i> View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-inbox"></i></div>
                            <div style="font-size: 16px; color: #cbd5e1;">No orders found</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Orders will appear here as customers place them</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div style="padding: 20px; border-top: 1px solid rgba(148, 163, 184, 0.1); text-align: center;">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
