<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get main categories
        $categories = Category::whereNull('parent_id')->get();

        if ($categories->count() > 0) {
            // Distribute products across categories
            // Create 15 active in-stock products
            Product::factory()
                ->active()
                ->inStock()
                ->count(15)
                ->make()
                ->each(function ($product) use ($categories) {
                    $product->category_id = $categories->random()->id;
                    $product->save();
                });

            // Create 5 featured products
            Product::factory()
                ->active()
                ->featured()
                ->inStock()
                ->count(5)
                ->make()
                ->each(function ($product) use ($categories) {
                    $product->category_id = $categories->random()->id;
                    $product->save();
                });

            // Create 3 out of stock products
            Product::factory()
                ->active()
                ->outOfStock()
                ->count(3)
                ->make()
                ->each(function ($product) use ($categories) {
                    $product->category_id = $categories->random()->id;
                    $product->save();
                });

            // Create 2 inactive products
            Product::factory()
                ->state(['status' => 'inactive'])
                ->count(2)
                ->make()
                ->each(function ($product) use ($categories) {
                    $product->category_id = $categories->random()->id;
                    $product->save();
                });
        } else {
            // Fallback if no categories exist (shouldn't happen)
            Product::factory()
                ->active()
                ->inStock()
                ->count(15)
                ->create();

            Product::factory()
                ->active()
                ->featured()
                ->inStock()
                ->count(5)
                ->create();

            Product::factory()
                ->active()
                ->outOfStock()
                ->count(3)
                ->create();

            Product::factory()
                ->state(['status' => 'inactive'])
                ->count(2)
                ->create();
        }
    }
}
