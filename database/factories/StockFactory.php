<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{

    private $product;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $this->product = Product::all(['id', 'price'])->random();
        return [
            'code' => randomCode(6),
            'product_id' => $this->product->id,
            'supplier_id' => Supplier::all('id')->random(),
            'original_quantity' => 5,
            'available_quantity' => 5,
            'original_parts' => 2,
            'available_parts' => 2,
            'parts_per_unit' => 3,
            'expire_date' => createDateFromExpireFormat('10/2025'),
            'price' => $this->product->price,
            'discount' => $this->faker->numberBetween(1, 10),
            'discount_limit' => fn(array $attributes) => $this->faker->numberBetween(0, $attributes['discount'] - 1),
        ];
    }
}
