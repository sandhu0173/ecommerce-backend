<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'cost_price',
        'stock',
        'sku',
        'image_url',
        'images',
        'status',
        'is_featured',
        'rating',
        'review_count',
        'meta_description',
        'meta_keywords',
        'category_id',
    ];

    protected $casts = [
        'images' => 'array',
        'meta_keywords' => 'array',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the category this product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope: Get active products only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Get products in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Calculate profit for a product
     */
    public function profit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price - ($this->cost_price ?? 0),
        );
    }

    /**
     * Get profit margin percentage
     */
    public function profitMargin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cost_price
                ? round((($this->price - $this->cost_price) / $this->price) * 100, 2)
                : 0,
        );
    }

    /**
     * Check if product is in stock
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0 && $this->status === 'active';
    }

    /**
     * Check if stock is low (less than 10)
     */
    public function isLowStock(): bool
    {
        return $this->stock < 10 && $this->stock > 0;
    }

    /**
     * Decrement stock when product is purchased
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increment stock when order is cancelled
     */
    public function incrementStock(int $quantity = 1): void
    {
        $this->increment('stock', $quantity);
    }
}
