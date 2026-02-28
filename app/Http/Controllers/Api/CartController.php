<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart with items.
     *
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cart->id,
                'items' => $cart->items()->with('product')->get()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product' => $item->product,
                        'quantity' => $item->quantity,
                        'unit_price' => (float)$item->unit_price,
                        'subtotal' => (float)$item->subtotal,
                    ];
                }),
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Add product to cart.
     *
     * POST /api/cart/add
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if product is available
        if (!$product->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available',
            ], 400);
        }

        // Check stock
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock,
            ], 400);
        }

        $cart = $request->user()->getOrCreateCart();
        $cartItem = $cart->addProduct($product, $validated['quantity']);

        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'data' => [
                'item' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => (float)$cartItem->unit_price,
                    'subtotal' => (float)$cartItem->subtotal,
                ],
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Get cart item details.
     *
     * GET /api/cart/{itemId}
     */
    public function show(string $itemId, Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $item = $cart->items()->findOrFail($itemId);
        $item->load('product');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'unit_price' => (float)$item->unit_price,
                'subtotal' => (float)$item->subtotal,
            ],
        ]);
    }

    /**
     * Update cart item quantity.
     *
     * PUT /api/cart/{itemId}
     */
    public function update(Request $request, string $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $request->user()->getOrCreateCart();
        $item = $cart->items()->findOrFail($itemId);

        // If quantity is 0, remove the item
        if ($validated['quantity'] === 0) {
            $item->delete();
            $cart->recalculate();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => [
                    'summary' => $cart->getSummary(),
                ],
            ]);
        }

        // Check stock availability
        if ($item->product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $item->product->stock,
            ], 400);
        }

        // Update quantity
        $cart->updateItem($item, $validated['quantity']);
        $item->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated',
            'data' => [
                'item' => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float)$item->unit_price,
                    'subtotal' => (float)$item->subtotal,
                ],
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Remove item from cart.
     *
     * DELETE /api/cart/{itemId}
     */
    public function destroy(string $itemId, Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $item = $cart->items()->findOrFail($itemId);
        $item->delete();

        $cart->recalculate();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => [
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Clear entire cart.
     *
     * POST /api/cart/clear
     */
    public function clear(Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'data' => [
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Update cart settings (shipping, discount, notes).
     *
     * PUT /api/cart/settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $request->user()->getOrCreateCart();

        if (isset($validated['shipping'])) {
            $cart->shipping = $validated['shipping'];
        }

        if (isset($validated['discount'])) {
            $cart->discount = $validated['discount'];
        }

        if (isset($validated['notes'])) {
            $cart->notes = $validated['notes'];
        }

        $cart->recalculate();

        return response()->json([
            'success' => true,
            'message' => 'Cart settings updated',
            'data' => [
                'summary' => $cart->getSummary(),
            ],
        ]);
    }

    /**
     * Get cart summary.
     *
     * GET /api/cart/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();

        return response()->json([
            'success' => true,
            'data' => $cart->getSummary(),
        ]);
    }

    /**
     * Validate cart before checkout.
     *
     * POST /api/cart/validate
     */
    public function validate(Request $request): JsonResponse
    {
        $cart = $request->user()->getOrCreateCart();

        if ($cart->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 400);
        }

        // Check stock availability for all items
        $unavailableItems = [];
        foreach ($cart->items as $item) {
            if (!$item->isProductAvailable()) {
                $unavailableItems[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'available_stock' => $item->product->stock,
                    'requested_quantity' => $item->quantity,
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

        return response()->json([
            'success' => true,
            'message' => 'Cart is valid for checkout',
            'data' => $cart->getSummary(),
        ]);
    }
}
