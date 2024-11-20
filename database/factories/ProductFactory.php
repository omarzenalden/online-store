<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        // Get random existing category and brand IDs
        $categoryId = Category::all()->random()->id; // Fetch a random existing category ID
        $brandId = Brand::all()->random()->id; // Fetch a random existing brand ID

        return [
            'name' => Str::random(10),
            'description' => $this->faker->sentence(),
            'price' => random_int(1, 999),
            'quantity' => random_int(1, 999),
            'details' => json_encode([
                'color' => $this->faker->safeColorName(),
                'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            ]),
            'category_id' => $categoryId, // Use the fetched ID
            'brand_id' => $brandId, // Use the fetched ID
        ];
    }
}
