<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'parent_id',
        'status',
        'sort_order',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get parent category (for nested categories).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories (for nested categories).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all products in this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope: Get active categories only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get parent categories only (no children).
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all child categories recursively.
     */
    public function getAllChildren()
    {
        return $this->children()->with('children')->get();
    }

    /**
     * Get product count in this category.
     */
    public function getProductCount(): int
    {
        return $this->products()->count();
    }

    /**
     * Get all products including subcategories.
     */
    public function getAllProducts()
    {
        $productIds = $this->products()->pluck('id');

        foreach ($this->children as $child) {
            $productIds = $productIds->merge($child->getAllProducts()->pluck('id'));
        }

        return Product::whereIn('id', $productIds)->get();
    }
}
