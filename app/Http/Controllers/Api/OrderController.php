<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Get user's orders.
     *
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'sort' => 'nullable|in:newest,oldest,price_low,price_high',
        ]);

        $perPage = $validated['per_page'] ?? 20;
        $page = $validated['page'] ?? 1;

        $query = $request->user()->orders();

        // Filter by status
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Filter by payment status
        if (isset($validated['payment_status'])) {
            $query->where('payment_status', $validated['payment_status']);
        }

        // Sort
        if (isset($validated['sort'])) {
            match ($validated['sort']) {
                'newest' => $query->orderBy('created_at', 'desc'),
                'oldest' => $query->orderBy('created_at', 'asc'),
                'price_low' => $query->orderBy('total', 'asc'),
                'price_high' => $query->orderBy('total', 'desc'),
                default => $query->orderBy('created_at', 'desc'),
            };
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                        'subtotal' => (float)$order->subtotal,
                        'tax' => (float)$order->tax,
                        'shipping' => (float)$order->shipping,
                        'discount' => (float)$order->discount,
                        'total' => (float)$order->total,
                        'item_count' => $order->items()->sum('quantity'),
                        'created_at' => $order->created_at,
                        'shipped_at' => $order->shipped_at,
                        'delivered_at' => $order->delivered_at,
                    ];
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Create order from cart.
     *
     * POST /api/orders
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.zip' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'billing_address' => 'nullable|array',
            'billing_address.street' => 'nullable|string|max:255',
            'billing_address.city' => 'nullable|string|max:100',
            'billing_address.state' => 'nullable|string|max:100',
            'billing_address.zip' => 'nullable|string|max:20',
            'billing_address.country' => 'nullable|string|max:100',
            'shipping_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $request->user()->getOrCreateCart();

        // Check if cart is empty
        if ($cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create order from empty cart',
            ], 400);
        }

        // Validate cart before creating order
        $unavailableItems = [];
        foreach ($cart->items as $item) {
            if (!$item->isProductAvailable()) {
                $unavailableItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                ];
            } elseif ($item->product->stock < $item->quantity) {
                $unavailableItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'available_stock' => $item->product->stock,
                    'requested_quantity' => $item->quantity,
                ];
            }
        }

        if (!empty($unavailableItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Some items have insufficient stock',
                'unavailable_items' => $unavailableItems,
            ], 400);
        }

        // Create order from cart
        $order = Order::createFromCart($cart, [
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
            'shipping_method' => $validated['shipping_method'],
            'notes' => $validated['notes'],
        ]);

        // Decrement product stock
        foreach ($cart->items as $item) {
            $item->product?->decrementStock($item->quantity);
        }

        // Clear cart after order creation
        $cart->clear();

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'subtotal' => (float)$order->subtotal,
                'tax' => (float)$order->tax,
                'shipping' => (float)$order->shipping,
                'discount' => (float)$order->discount,
                'total' => (float)$order->total,
                'items' => $order->items()->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
                'shipping_address' => json_decode($order->shipping_address, true),
                'billing_address' => json_decode($order->billing_address, true),
                'created_at' => $order->created_at,
            ],
        ], 201);
    }

    /**
     * Get order details.
     *
     * GET /api/orders/{orderId}
     */
    public function show(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);
        $order->load('items.product');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'subtotal' => (float)$order->subtotal,
                'tax' => (float)$order->tax,
                'shipping' => (float)$order->shipping,
                'discount' => (float)$order->discount,
                'total' => (float)$order->total,
                'items' => $order->items()->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
                'shipping_address' => json_decode($order->shipping_address, true),
                'billing_address' => json_decode($order->billing_address, true),
                'shipping_method' => $order->shipping_method,
                'tracking_number' => $order->tracking_number,
                'payment_method' => $order->payment_method,
                'transaction_id' => $order->transaction_id,
                'notes' => $order->notes,
                'created_at' => $order->created_at,
                'shipped_at' => $order->shipped_at,
                'delivered_at' => $order->delivered_at,
            ],
        ]);
    }

    /**
     * Update order address or notes.
     *
     * PUT /api/orders/{orderId}
     */
    public function update(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);

        // Can only update pending orders
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only update pending orders',
            ], 400);
        }

        $validated = $request->validate([
            'shipping_address' => 'nullable|array',
            'shipping_address.street' => 'nullable|string|max:255',
            'shipping_address.city' => 'nullable|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.zip' => 'nullable|string|max:20',
            'shipping_address.country' => 'nullable|string|max:100',
            'billing_address' => 'nullable|array',
            'billing_address.street' => 'nullable|string|max:255',
            'billing_address.city' => 'nullable|string|max:100',
            'billing_address.state' => 'nullable|string|max:100',
            'billing_address.zip' => 'nullable|string|max:20',
            'billing_address.country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        if (isset($validated['shipping_address'])) {
            $order->shipping_address = json_encode($validated['shipping_address']);
        }

        if (isset($validated['billing_address'])) {
            $order->billing_address = json_encode($validated['billing_address']);
        }

        if (isset($validated['notes'])) {
            $order->notes = $validated['notes'];
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => [
                'shipping_address' => json_decode($order->shipping_address, true),
                'billing_address' => json_decode($order->billing_address, true),
                'notes' => $order->notes,
            ],
        ]);
    }

    /**
     * Mark order as paid.
     *
     * POST /api/orders/{orderId}/pay
     */
    public function markAsPaid(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);

        $validated = $request->validate([
            'transaction_id' => 'required|string|max:255',
            'payment_method' => 'nullable|string|max:50',
        ]);

        // Can only mark pending orders as paid
        if ($order->payment_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order payment is not pending',
            ], 400);
        }

        $order->markAsPaid(
            $validated['transaction_id'],
            $validated['payment_method'] ?? 'credit_card'
        );

        return response()->json([
            'success' => true,
            'message' => 'Order marked as paid',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'transaction_id' => $order->transaction_id,
                'payment_method' => $order->payment_method,
            ],
        ]);
    }

    /**
     * Mark order as shipped.
     *
     * POST /api/orders/{orderId}/ship
     */
    public function markAsShipped(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);

        $validated = $request->validate([
            'tracking_number' => 'nullable|string|max:255',
        ]);

        // Can only ship paid orders in processing status
        if (!$order->canBeShipped()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be shipped. Must be paid and in processing status.',
            ], 400);
        }

        $order->markAsShipped($validated['tracking_number'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Order marked as shipped',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'shipped_at' => $order->shipped_at,
            ],
        ]);
    }

    /**
     * Mark order as delivered.
     *
     * POST /api/orders/{orderId}/deliver
     */
    public function markAsDelivered(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);

        // Can only deliver shipped orders
        if ($order->status !== 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Can only deliver shipped orders',
            ], 400);
        }

        $order->markAsDelivered();

        return response()->json([
            'success' => true,
            'message' => 'Order marked as delivered',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'delivered_at' => $order->delivered_at,
            ],
        ]);
    }

    /**
     * Cancel order.
     *
     * POST /api/orders/{orderId}/cancel
     */
    public function cancel(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Can only cancel pending or processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only cancel pending or processing orders',
            ], 400);
        }

        $order->cancel($validated['reason'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'notes' => $order->notes,
            ],
        ]);
    }

    /**
     * Get order items.
     *
     * GET /api/orders/{orderId}/items
     */
    public function items(Request $request, string $orderId): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($orderId);
        $order->load('items.product');

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'items' => $order->items()->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product' => $item->product,
                        'product_name' => $item->product_name,
                        'product_sku' => $item->product_sku,
                        'quantity' => $item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
            ],
        ]);
    }
}
