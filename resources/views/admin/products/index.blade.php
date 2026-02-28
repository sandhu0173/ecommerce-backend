@extends('admin.layout')

@section('title', 'Products')

@section('content')
<!-- Products Management Card -->
<div class="card">
    <div class="card-header">
        <div>
            <h3><i class="fas fa-cube"></i> Product Management</h3>
            <p style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Manage your product catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-box"></i> Product Name</th>
                    <th><i class="fas fa-barcode"></i> SKU</th>
                    <th style="text-align: right;"><i class="fas fa-dollar-sign"></i> Price</th>
                    <th style="text-align: right;"><i class="fas fa-calculator"></i> Cost</th>
                    <th style="text-align: center;"><i class="fas fa-warehouse"></i> Stock</th>
                    <th style="text-align: right;"><i class="fas fa-percent"></i> Margin</th>
                    <th style="text-align: center;"><i class="fas fa-toggle-on"></i> Status</th>
                    <th style="text-align: center;"><i class="fas fa-star"></i> Featured</th>
                    <th style="text-align: center;"><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr style="transition: all 0.2s ease;">
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6 to #06b6d4); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                {{ strtoupper(substr($product->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong style="display: block;">{{ $product->name }}</strong>
                                <span style="font-size: 11px; color: #94a3b8;">{{ $product->id }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code style="background: rgba(59, 130, 246, 0.1); padding: 4px 8px; border-radius: 4px; color: #3b82f6;">{{ $product->sku }}</code>
                    </td>
                    <td style="text-align: right;">
                        <strong style="color: #10b981; font-size: 15px;">${{ number_format($product->price, 2) }}</strong>
                    </td>
                    <td style="text-align: right;">
                        <span style="color: #94a3b8;">${{ number_format($product->cost_price, 2) }}</span>
                    </td>
                    <td style="text-align: center;">
                        @if($product->stock > 50)
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> {{ $product->stock }}</span>
                        @elseif($product->stock > 10)
                            <span class="badge badge-info"><i class="fas fa-box"></i> {{ $product->stock }}</span>
                        @elseif($product->stock > 0)
                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> {{ $product->stock }} Low</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Out</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <span style="background: rgba(34, 197, 94, 0.2); padding: 6px 10px; border-radius: 6px; color: #22c55e; font-weight: 600;">
                            {{ number_format($product->profit_margin, 1) }}%
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if($product->status === 'active')
                            <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($product->is_featured)
                            <span class="badge badge-success" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;"><i class="fas fa-star"></i> Featured</span>
                        @else
                            <span style="color: #94a3b8; font-size: 12px;">-</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px;">
                        <div class="empty-state">
                            <div style="font-size: 48px; margin-bottom: 15px;"><i class="fas fa-cube"></i></div>
                            <div style="font-size: 16px; color: #cbd5e1;">No products found</div>
                            <div style="font-size: 12px; color: #94a3b8; margin-top: 5px;">
                                <a href="{{ route('admin.products.create') }}" style="color: #3b82f6; text-decoration: underline;">Create your first product</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div style="padding: 20px; border-top: 1px solid rgba(148, 163, 184, 0.1); text-align: center;">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
