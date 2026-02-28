@extends('admin.layout')

@section('title', 'Order Details')

@section('content')
<a href="{{ route('admin.orders.index') }}" class="btn btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Back to Orders</a>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-receipt"></i> Order #{{ $order->order_number }}</h3>
            <span style="font-size: 12px; color: #94a3b8;">Order details and items</span>
        </div>

        <div style="margin: 0; padding: 20px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) to rgba(6, 182, 212, 0.15)); border-radius: 8px; border-left: 4px solid #3b82f6;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;"><i class="fas fa-user-circle"></i> Customer</p>
                    <p style="font-weight: 600; color: white; font-size: 15px;">{{ $order->user->name }}</p>
                    <p style="color: #cbd5e1; font-size: 13px;">{{ $order->user->email }}</p>
                </div>
                <div>
                    <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;"><i class="fas fa-calendar-alt"></i> Order Date</p>
                    <p style="font-weight: 600; color: white; font-size: 15px;">{{ $order->created_at->format('M d, Y') }}</p>
                    <p style="color: #cbd5e1; font-size: 13px;">{{ $order->created_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid rgba(148, 163, 184, 0.1);">
            <h4 style="margin-bottom: 15px; font-size: 16px;"><i class="fas fa-box"></i> Order Items</h4>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-cube"></i> Product</th>
                            <th><i class="fas fa-barcode"></i> SKU</th>
                            <th style="text-align: center;"><i class="fas fa-calculator"></i> Qty</th>
                            <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Price</th>
                            <th style="text-align: right;"><i class="fas fa-receipt"></i> Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td><strong>{{ $item->product_name }}</strong></td>
                            <td><code style="background: rgba(59, 130, 246, 0.1); padding: 4px 8px; border-radius: 4px; color: #3b82f6;">{{ $item->product_sku }}</code></td>
                            <td style="text-align: center;"><span style="background: rgba(59, 130, 246, 0.2); padding: 4px 8px; border-radius: 4px; color: #3b82f6; font-weight: 600;">{{ $item->quantity }}</span></td>
                            <td style="text-align: right;">{{ $item->unit_price > 0 ? '$' . number_format($item->unit_price, 2) : 'N/A' }}</td>
                            <td style="text-align: right;"><strong style="color: #10b981;">${{ number_format($item->subtotal, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 25px; padding: 20px; background: rgba(30, 41, 59, 0.5); border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.1);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="font-size: 13px;">
                    <div style="margin: 10px 0; display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;"><i class="fas fa-box"></i> Subtotal:</span>
                        <strong>${{ number_format($order->subtotal, 2) }}</strong>
                    </div>
                    <div style="margin: 10px 0; display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;"><i class="fas fa-percent"></i> Tax:</span>
                        <strong>${{ number_format($order->tax, 2) }}</strong>
                    </div>
                    <div style="margin: 10px 0; display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;"><i class="fas fa-truck"></i> Shipping:</span>
                        <strong>${{ number_format($order->shipping, 2) }}</strong>
                    </div>
                    <div style="margin: 10px 0; display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;"><i class="fas fa-tag"></i> Discount:</span>
                        <strong style="color: #10b981;">-${{ number_format($order->discount, 2) }}</strong>
                    </div>
                </div>
                <div style="border-left: 2px solid rgba(148, 163, 184, 0.2); padding-left: 20px; display: flex; align-items: center; justify-content: flex-end;">
                    <div style="text-align: right;">
                        <p style="color: #94a3b8; font-size: 12px; margin-bottom: 5px;">TOTAL AMOUNT</p>
                        <p style="font-size: 24px; color: #10b981; font-weight: 700;">${{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <h4 style="margin-bottom: 15px; font-size: 16px;"><i class="fas fa-map-marker-alt"></i> Shipping Address</h4>
            @php
                $shipping = json_decode($order->shipping_address, true) ?? [];
            @endphp
            <div style="background: rgba(16, 185, 129, 0.1); padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
                <p style="margin: 6px 0; color: white;"><strong>{{ $shipping['street'] ?? 'N/A' }}</strong></p>
                <p style="margin: 6px 0; color: #cbd5e1;">{{ $shipping['city'] ?? 'N/A' }}, {{ $shipping['state'] ?? '' }} {{ $shipping['zip'] ?? '' }}</p>
                <p style="margin: 6px 0; color: #cbd5e1;">{{ $shipping['country'] ?? 'N/A' }}</p>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <h4 style="margin-bottom: 15px; font-size: 16px;"><i class="fas fa-home"></i> Billing Address</h4>
            @php
                $billing = json_decode($order->billing_address, true) ?? [];
            @endphp
            <div style="background: rgba(139, 92, 246, 0.1); padding: 20px; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                <p style="margin: 6px 0; color: white;"><strong>{{ $billing['street'] ?? 'N/A' }}</strong></p>
                <p style="margin: 6px 0; color: #cbd5e1;">{{ $billing['city'] ?? 'N/A' }}, {{ $billing['state'] ?? '' }} {{ $billing['zip'] ?? '' }}</p>
                <p style="margin: 6px 0; color: #cbd5e1;">{{ $billing['country'] ?? 'N/A' }}</p>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top: 25px;">
            <h4 style="margin-bottom: 15px; font-size: 16px;"><i class="fas fa-sticky-note"></i> Order Notes</h4>
            <div style="background: rgba(245, 158, 11, 0.1); padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <p style="color: #cbd5e1; line-height: 1.6;">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Order Status</h3>
            </div>
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                <div style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Select Status</label>
                        <select name="status" required style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                            <option value="pending" @if($order->status === 'pending') selected @endif>Pending</option>
                            <option value="processing" @if($order->status === 'processing') selected @endif>Processing</option>
                            <option value="shipped" @if($order->status === 'shipped') selected @endif>Shipped</option>
                            <option value="delivered" @if($order->status === 'delivered') selected @endif>Delivered</option>
                            <option value="cancelled" @if($order->status === 'cancelled') selected @endif>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-check"></i> Update Status</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-credit-card"></i> Payment Status</h3>
            </div>
            <div style="padding: 20px;">
                <div style="padding: 16px; background: @if($order->payment_status === 'paid') rgba(16, 185, 129, 0.1) @elseif($order->payment_status === 'failed') rgba(239, 68, 68, 0.1) @else rgba(245, 158, 11, 0.1) @endif; border-left: 4px solid @if($order->payment_status === 'paid') #10b981 @elseif($order->payment_status === 'failed') #ef4444 @else #f59e0b @endif; border-radius: 6px; margin-bottom: 15px;">
                    <p style="font-size: 11px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px;">Status</p>
                    <p style="font-size: 18px; font-weight: 600; color: @if($order->payment_status === 'paid') #10b981 @elseif($order->payment_status === 'failed') #ef4444 @else #f59e0b @endif;">
                        @if($order->payment_status === 'paid')
                            <i class="fas fa-check-circle"></i> Paid
                        @elseif($order->payment_status === 'failed')
                            <i class="fas fa-times-circle"></i> Failed
                        @else
                            <i class="fas fa-hourglass-half"></i> {{ ucfirst($order->payment_status) }}
                        @endif
                    </p>
                </div>
                @if($order->transaction_id)
                    <p style="font-size: 12px; color: #94a3b8; margin-top: 15px;">
                        <strong>Transaction ID:</strong><br>
                        <code style="background: rgba(59, 130, 246, 0.1); padding: 6px 10px; border-radius: 4px; color: #3b82f6; display: block; margin-top: 5px; word-break: break-all;">{{ $order->transaction_id }}</code>
                    </p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-box-open"></i> Shipping Details</h3>
            </div>
            <div style="padding: 20px;">
                <p style="color: #94a3b8; font-size: 12px; margin-bottom: 5px;"><strong>Method:</strong></p>
                <p style="color: white; font-weight: 600; margin-bottom: 15px;">{{ $order->shipping_method ?? 'Not specified' }}</p>

                @if($order->tracking_number)
                    <div style="padding: 12px; background: rgba(6, 182, 212, 0.1); border-left: 4px solid #06b6d4; border-radius: 6px; margin-bottom: 15px;">
                        <p style="color: #94a3b8; font-size: 11px; text-transform: uppercase; margin-bottom: 5px;"><i class="fas fa-barcode"></i> Tracking Number</p>
                        <p style="color: white; font-weight: 600; word-break: break-all;">{{ $order->tracking_number }}</p>
                    </div>
                @else
                    <form action="{{ route('admin.orders.ship', $order) }}" method="POST" style="margin-bottom: 15px;">
                        @csrf
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label style="font-size: 12px; color: #94a3b8; margin-bottom: 5px; display: block;">Tracking Number</label>
                            <input type="text" name="tracking_number" placeholder="Enter tracking number" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%;"><i class="fas fa-truck"></i> Mark as Shipped</button>
                    </form>
                @endif

                @if($order->shipped_at)
                    <div style="padding: 12px; background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; border-radius: 6px; margin-bottom: 15px;">
                        <p style="color: #10b981; font-size: 12px;"><i class="fas fa-check-circle"></i> Shipped on {{ $order->shipped_at->format('M d, Y') }} at {{ $order->shipped_at->format('h:i A') }}</p>
                    </div>
                @endif

                @if($order->status === 'shipped')
                    <form action="{{ route('admin.orders.deliver', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success" style="width: 100%;"><i class="fas fa-check"></i> Mark as Delivered</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
