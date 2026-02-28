<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filtering and pagination.
     *
     * Query parameters:
     * - page: Page number (default: 1)
     * - limit: Items per page (default: 20, max: 100)
     * - search: Search by name or description
     * - sort: Sort by field (name, price, rating, created_at)
     * - order: asc or desc
     * - featured: true/false
     * - status: active/inactive/archived
     * - min_price: Minimum price filter
     * - max_price: Maximum price filter
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        // Filter featured products
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Stock filter
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $allowedSorts = ['name', 'price', 'rating', 'review_count', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, in_array($order, ['asc', 'desc']) ? $order : 'desc');
        }

        // Pagination
        $limit = min($request->input('limit', 20), 100);
        $products = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'count' => $products->count(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    /**
     * Store a newly created product (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'image_url' => 'nullable|url',
            'images' => 'nullable|array',
            'status' => 'required|in:active,inactive,archived',
            'is_featured' => 'nullable|boolean',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
        ]);

        $validated['slug'] = str()->slug($validated['name']);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Display a specific product by slug or ID.
     */
    public function show(Product $product): JsonResponse
    {
        // Add related information
        $data = $product->toArray();
        $data['profit'] = $product->profit;
        $data['profit_margin'] = $product->profitMargin;
        $data['is_available'] = $product->isAvailable();
        $data['is_low_stock'] = $product->isLowStock();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update a product (Admin only).
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0.01',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'integer|min:0',
            'sku' => 'string|unique:products,sku,' . $product->id,
            'image_url' => 'nullable|url',
            'images' => 'nullable|array',
            'status' => 'in:active,inactive,archived',
            'is_featured' => 'nullable|boolean',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $product->name) {
            $validated['slug'] = str()->slug($validated['name']);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Delete a product (Admin only).
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get featured products.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 10), 50);
        $products = Product::featured()
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters',
                'data' => [],
            ]);
        }

        $products = Product::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * Get product by slug.
     */
    public function bySlug(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return $this->show($product);
    }
}
