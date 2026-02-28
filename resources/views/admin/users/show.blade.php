@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Back to Customers</a>

<!-- User Header Card -->
<div class="card" style="margin-bottom: 24px;">
    <div style="padding: 24px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) to rgba(6, 182, 212, 0.15)); border-left: 4px solid #3b82f6; border-radius: 8px 8px 0 0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 to #06b6d4); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 style="margin: 0; color: white; font-size: 24px; font-weight: 700;">{{ $user->name }}</h2>
                <p style="margin: 5px 0 0 0; color: #94a3b8; font-size: 14px;">{{ $user->email }}</p>
            </div>
        </div>
    </div>
</div>

<!-- User Information & Statistics Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 30px;">
    <div class="card">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;"><i class="fas fa-calendar-alt"></i> Member Since</p>
                    <p style="font-size: 18px; font-weight: 700; color: white; margin-bottom: 5px;">{{ $user->created_at->format('M d, Y') }}</p>
                    <p style="color: #cbd5e1; font-size: 12px;">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-user-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;"><i class="fas fa-shopping-bag"></i> Total Orders</p>
                    <p style="font-size: 32px; font-weight: 700; color: #3b82f6;">{{ $user->orders()->count() }}</p>
                    <p style="color: #cbd5e1; font-size: 12px;">All-time purchases</p>
                </div>
                <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-shopping-bag"></i></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;"><i class="fas fa-dollar-sign"></i> Total Spent</p>
                    <p style="font-size: 32px; font-weight: 700; color: #10b981;">${{ number_format($user->orders()->sum('total'), 2) }}</p>
                    <p style="color: #cbd5e1; font-size: 12px;">Lifetime value</p>
                </div>
                <div style="font-size: 32px; opacity: 0.2;"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Average Order Value Card -->
<div class="card" style="margin-bottom: 30px; background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) to rgba(59, 130, 246, 0.15));">
    <div style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;"><i class="fas fa-chart-line"></i> Average Order Value</p>
                <p style="font-size: 28px; font-weight: 700; color: #f59e0b;">${{ number_format($user->orders()->avg('total') ?? 0, 2) }}</p>
            </div>
            <div style="font-size: 48px; opacity: 0.15;"><i class="fas fa-chart-line"></i></div>
        </div>
    </div>
</div>

<!-- Order History Card -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Order History</h3>
        <span style="font-size: 12px; color: #94a3b8;">{{ $user->orders()->count() }} order{{ $user->orders()->count() !== 1 ? 's' : '' }} total</span>
    </div>

    @if($user->orders()->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> Order #</th>
                        <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Total</th>
                        <th style="text-align: center;"><i class="fas fa-box"></i> Status</th>
                        <th style="text-align: center;"><i class="fas fa-credit-card"></i> Payment</th>
                        <th style="text-align: center;"><i class="fas fa-calendar"></i> Date</th>
                        <th style="text-align: center;"><i class="fas fa-cog"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->orders()->latest()->get() as $order)
                    <tr style="transition: all 0.2s ease;">
                        <td><strong style="color: #3b82f6;">#{{ $order->order_number }}</strong></td>
                        <td style="text-align: right;"><strong style="color: #10b981; font-size: 16px;">${{ number_format($order->total, 2) }}</strong></td>
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
                            @else
                                <span class="badge badge-warning"><i class="fas fa-exclamation"></i> {{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="font-size: 13px;">{{ $order->created_at->format('M d, Y') }}</div>
                            <div style="font-size: 11px; color: #94a3b8;">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-eye"></i> View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <div class="empty-state">
                <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-inbox"></i></div>
                <div style="font-size: 16px; color: #cbd5e1;">No orders found</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">This customer hasn't placed any orders yet</div>
            </div>
        </div>
    @endif
</div>
@endsection
