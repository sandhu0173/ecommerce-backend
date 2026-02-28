<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main categories
        $electronics = Category::factory()->active()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and gadgets',
            'sort_order' => 1,
        ]);

        $clothing = Category::factory()->active()->create([
            'name' => 'Clothing',
            'slug' => 'clothing',
            'description' => 'Apparel and fashion items',
            'sort_order' => 2,
        ]);

        $home = Category::factory()->active()->create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home and garden products',
            'sort_order' => 3,
        ]);

        $sports = Category::factory()->active()->create([
            'name' => 'Sports & Outdoors',
            'slug' => 'sports-outdoors',
            'description' => 'Sports equipment and outdoor gear',
            'sort_order' => 4,
        ]);

        // Create subcategories for Electronics
        Category::factory()->active()->create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops-computers',
            'parent_id' => $electronics->id,
            'description' => 'Computers and laptops',
            'sort_order' => 1,
        ]);

        Category::factory()->active()->create([
            'name' => 'Smartphones & Tablets',
            'slug' => 'smartphones-tablets',
            'parent_id' => $electronics->id,
            'description' => 'Mobile devices',
            'sort_order' => 2,
        ]);

        Category::factory()->active()->create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'parent_id' => $electronics->id,
            'description' => 'Electronics accessories',
            'sort_order' => 3,
        ]);

        // Create subcategories for Clothing
        Category::factory()->active()->create([
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
            'parent_id' => $clothing->id,
            'description' => 'Men\'s apparel',
            'sort_order' => 1,
        ]);

        Category::factory()->active()->create([
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
            'parent_id' => $clothing->id,
            'description' => 'Women\'s apparel',
            'sort_order' => 2,
        ]);

        // Create additional random categories
        Category::factory()->active()->count(3)->create();
    }
}
