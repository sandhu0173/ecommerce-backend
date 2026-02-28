<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with hierarchy.
     */
    public function index(Request $request): JsonResponse
    {
        $withProducts = $request->boolean('include_products', false);

        // Get parent categories only (top level)
        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($withProducts) {
            $categories->load('products');
        }

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get categories as a tree structure (with children).
     */
    public function tree(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Display a specific category with its products.
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('products', 'children');

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'product_count' => $category->getProductCount(),
                'subcategory_count' => $category->children()->count(),
            ],
        ]);
    }

    /**
     * Store a newly created category (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = str()->slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    /**
     * Update a category (Admin only).
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'in:active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($validated['name']) && $validated['name'] !== $category->name) {
            $validated['slug'] = str()->slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Delete a category (Admin only).
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Get products in a category.
     */
    public function products(Category $category, Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 20), 100);
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');

        $allowedSorts = ['name', 'price', 'rating', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $products = $category->products()
            ->active()
            ->orderBy($sort, in_array($order, ['asc', 'desc']) ? $order : 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'count' => $products->count(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }
}
