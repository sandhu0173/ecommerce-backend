@extends('admin.layout')

@section('title', 'Create Product')

@section('content')
<a href="{{ route('admin.products.index') }}" class="btn btn-secondary" style="margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Back to Products</a>

<!-- Create Product Form -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle"></i> Create New Product</h3>
        <p style="font-size: 12px; color: #94a3b8; margin-top: 5px;">Add a new product to your catalog</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST">
        @csrf

        <div style="padding: 24px;">
            <!-- Basic Information Section -->
            <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid rgba(148, 163, 184, 0.1);">
                <h4 style="margin-bottom: 20px; font-size: 16px;"><i class="fas fa-info-circle"></i> Basic Information</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="name" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Product Name *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                    </div>

                    <div class="form-group">
                        <label for="slug" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Slug *</label>
                        <input type="text" id="slug" name="slug" required value="{{ old('slug') }}" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                    </div>

                    <div class="form-group">
                        <label for="sku" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">SKU *</label>
                        <input type="text" id="sku" name="sku" required value="{{ old('sku') }}" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                    </div>

                    <div class="form-group">
                        <label for="category_id" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Category</label>
                        <select id="category_id" name="category_id" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory Section -->
            <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid rgba(148, 163, 184, 0.1);">
                <h4 style="margin-bottom: 20px; font-size: 16px;"><i class="fas fa-dollar-sign"></i> Pricing & Inventory</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="price" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Selling Price *</label>
                        <div style="display: flex; align-items: center;">
                            <span style="position: absolute; padding-left: 12px; color: #94a3b8;">$</span>
                            <input type="number" id="price" name="price" step="0.01" required value="{{ old('price') }}" style="width: 100%; padding: 10px 12px 10px 28px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cost_price" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Cost Price *</label>
                        <div style="display: flex; align-items: center;">
                            <span style="position: absolute; padding-left: 12px; color: #94a3b8;">$</span>
                            <input type="number" id="cost_price" name="cost_price" step="0.01" required value="{{ old('cost_price') }}" style="width: 100%; padding: 10px 12px 10px 28px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stock" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" required value="{{ old('stock') }}" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                    </div>
                </div>
            </div>

            <!-- Status & Display Section -->
            <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid rgba(148, 163, 184, 0.1);">
                <h4 style="margin-bottom: 20px; font-size: 16px;"><i class="fas fa-toggle-on"></i> Status & Display</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="status" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Status *</label>
                        <select id="status" name="status" required style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                            <option value="active" @if(old('status') === 'active') selected @endif>Active</option>
                            <option value="inactive" @if(old('status') === 'inactive') selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #e2e8f0;">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" @if(old('is_featured')) checked @endif style="width: 18px; height: 18px; cursor: pointer;">
                            <span><i class="fas fa-star"></i> Featured Product</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Image & Description Section -->
            <div style="margin-bottom: 20px;">
                <h4 style="margin-bottom: 20px; font-size: 16px;"><i class="fas fa-image"></i> Media & Description</h4>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="image_url" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Featured Image URL</label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0;">
                </div>

                <div class="form-group">
                    <label for="description" style="font-size: 12px; text-transform: uppercase; color: #94a3b8; margin-bottom: 8px; display: block;">Description</label>
                    <textarea id="description" name="description" rows="5" style="width: 100%; padding: 10px 12px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 6px; color: #e2e8f0; font-family: inherit;">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div style="padding: 20px 24px; border-top: 1px solid rgba(148, 163, 184, 0.1); display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Create Product</button>
            <a href="{{ route('admin.products.index') }}" class="btn" style="background: rgba(148, 163, 184, 0.2); color: #e2e8f0; text-decoration: none;"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
@endsection
