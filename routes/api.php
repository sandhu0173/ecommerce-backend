<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Public Authentication Routes
 */
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

/**
 * Protected Authentication Routes
 */
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('auth.profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::delete('/tokens/{token}', [AuthController::class, 'revokeToken'])->name('auth.revoke-token');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Public Category Routes
 */
Route::prefix('categories')->group(function () {
    // Get all parent categories
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');

    // Get category tree with children
    Route::get('/tree', [CategoryController::class, 'tree'])->name('categories.tree');

    // Get category details with products
    Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Get products in category
    Route::get('/{category}/products', [CategoryController::class, 'products'])->name('categories.products');
});

/**
 * Admin/Protected Category Routes
 */
Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    // Create category
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');

    // Update category
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');

    // Delete category
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

/**
 * Public Product Routes
 */
Route::prefix('products')->group(function () {
    // Get all products with filters
    Route::get('/', [ProductController::class, 'index'])->name('products.index');

    // Get featured products
    Route::get('/featured', [ProductController::class, 'featured'])->name('products.featured');

    // Search products
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');

    // Get product by slug
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
});

/**
 * Admin/Protected Product Routes
 */
Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    // Create product
    Route::post('/', [ProductController::class, 'store'])->name('products.store');

    // Update product
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');

    // Delete product
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

/**
 * Protected Cart Routes
 */
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    // Get user's cart with items
    Route::get('/', [CartController::class, 'index'])->name('cart.index');

    // Add product to cart
    Route::post('/', [CartController::class, 'store'])->name('cart.store');

    // Specific cart action routes (before wildcard {itemId} routes)
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::put('/settings', [CartController::class, 'updateSettings'])->name('cart.update-settings');
    Route::get('/summary', [CartController::class, 'summary'])->name('cart.summary');
    Route::post('/validate', [CartController::class, 'validate'])->name('cart.validate');

    // Cart item routes (with itemId wildcard)
    Route::get('/{itemId}', [CartController::class, 'show'])->name('cart.show');
    Route::put('/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{itemId}', [CartController::class, 'destroy'])->name('cart.destroy');
});

/**
 * Protected Order Routes
 */
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    // Get user's orders
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');

    // Create order from cart
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');

    // Order action routes (before wildcard {orderId} routes)
    Route::post('/{orderId}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');
    Route::post('/{orderId}/ship', [OrderController::class, 'markAsShipped'])->name('orders.ship');
    Route::post('/{orderId}/deliver', [OrderController::class, 'markAsDelivered'])->name('orders.deliver');
    Route::post('/{orderId}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/{orderId}/items', [OrderController::class, 'items'])->name('orders.items');

    // Order detail routes
    Route::get('/{orderId}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/{orderId}', [OrderController::class, 'update'])->name('orders.update');
});
