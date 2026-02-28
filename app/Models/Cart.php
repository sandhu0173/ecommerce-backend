<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'item_count',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the user this cart belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Add product to cart.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->subtotal = $item->quantity * $item->unit_price;
            $item->save();
        } else {
            $item = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $quantity * $product->price,
                'product_data' => [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'image' => $product->image_url,
                ],
            ]);
        }

        $this->recalculate();
        return $item;
    }

    /**
     * Update product quantity in cart.
     */
    public function updateItem(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->quantity = $quantity;
            $item->subtotal = $quantity * $item->unit_price;
            $item->save();
        }

        $this->recalculate();
    }

    /**
     * Remove product from cart.
     */
    public function removeProduct(Product $product): void
    {
        $this->items()->where('product_id', $product->id)->delete();
        $this->recalculate();
    }

    /**
     * Clear all items from cart.
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->recalculate();
    }

    /**
     * Recalculate cart totals.
     */
    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $itemCount = $this->items()->sum('quantity');

        // Calculate tax (example: 10% tax)
        $tax = round($subtotal * 0.10, 2);

        // Calculate total
        $total = $subtotal + $tax + $this->shipping - $this->discount;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => max(0, $total),
            'item_count' => $itemCount,
        ]);
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->item_count === 0;
    }

    /**
     * Get cart summary.
     */
    public function getSummary(): array
    {
        return [
            'item_count' => $this->item_count,
            'subtotal' => (float)$this->subtotal,
            'tax' => (float)$this->tax,
            'shipping' => (float)$this->shipping,
            'discount' => (float)$this->discount,
            'total' => (float)$this->total,
        ];
    }
}
