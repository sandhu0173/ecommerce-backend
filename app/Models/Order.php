<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'payment_method',
        'transaction_id',
        'shipping_address',
        'billing_address',
        'shipping_method',
        'tracking_number',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user this order belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    /**
     * Create order from cart.
     */
    public static function createFromCart(Cart $cart, array $data): self
    {
        $order = self::create([
            'order_number' => self::generateOrderNumber(),
            'user_id' => $cart->user_id,
            'subtotal' => $cart->subtotal,
            'tax' => $cart->tax,
            'shipping' => $data['shipping'] ?? $cart->shipping,
            'discount' => $cart->discount,
            'total' => $cart->total,
            'shipping_address' => json_encode($data['shipping_address'] ?? []),
            'billing_address' => json_encode($data['billing_address'] ?? []),
            'shipping_method' => $data['shipping_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Create order items from cart items
        foreach ($cart->items()->with('product')->get() as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'product_sku' => $cartItem->product->sku,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->unit_price,
                'subtotal' => $cartItem->subtotal,
                'product_data' => $cartItem->product_data,
            ]);
        }

        return $order;
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(string $transactionId, string $method = 'credit_card'): void
    {
        $this->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'payment_method' => $method,
            'status' => 'processing',
        ]);
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(string $trackingNumber = null): void
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'tracking_number' => $trackingNumber,
        ]);
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Cancel order.
     */
    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => ($this->notes ?? '') . "\nCancellation reason: " . ($reason ?? 'No reason provided'),
        ]);

        // Restore product stock
        foreach ($this->items as $item) {
            $item->product?->incrementStock($item->quantity);
        }
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if order can be shipped.
     */
    public function canBeShipped(): bool
    {
        return $this->isPaid() && $this->status === 'processing';
    }

    /**
     * Get order status badge.
     */
    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get order summary.
     */
    public function getSummary(): array
    {
        return [
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal' => (float)$this->subtotal,
            'tax' => (float)$this->tax,
            'shipping' => (float)$this->shipping,
            'discount' => (float)$this->discount,
            'total' => (float)$this->total,
            'item_count' => $this->items()->sum('quantity'),
            'created_at' => $this->created_at,
        ];
    }
}
