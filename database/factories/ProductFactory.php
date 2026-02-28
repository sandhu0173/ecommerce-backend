<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->randomFloat(2, 9.99, 499.99);
        $costPrice = $price * $this->faker->randomFloat(1, 0.4, 0.7);

        return [
            'name' => ucfirst($name),
            'slug' => str()->slug($name),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'cost_price' => round($costPrice, 2),
            'stock' => $this->faker->numberBetween(0, 500),
            'sku' => $this->faker->unique()->bothify('SKU-####-??'),
            'image_url' => $this->faker->imageUrl(400, 400, 'products'),
            'images' => [
                $this->faker->imageUrl(800, 800, 'products'),
                $this->faker->imageUrl(800, 800, 'products'),
                $this->faker->imageUrl(800, 800, 'products'),
            ],
            'status' => $this->faker->randomElement(['active', 'inactive', 'archived']),
            'is_featured' => $this->faker->boolean(30),
            'rating' => $this->faker->numberBetween(1, 5),
            'review_count' => $this->faker->numberBetween(0, 200),
            'meta_description' => $this->faker->sentence(),
            'meta_keywords' => $this->faker->words(5),
            'category_id' => null, // Set by seeder
        ];
    }

    /**
     * Indicate that the product is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(10, 500),
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
