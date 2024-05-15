<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
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
            'order_id' => Order::factory(),
            'product_id' => $this->product->id,
            'quantity' => $this->faker->numberBetween(1, 10),
            'parts' => $this->faker->numberBetween(0, 2),
            'unit_price' => $this->product->price,
            'expire_date' => null,
            'stock_id' => null,
            'total_amount' => 0,
            'discount' => $this->faker->numberBetween(0, 10),
            'discount_limit' => fn(array $attributes) => $this->faker->numberBetween(0, $attributes['discount'] - 1),
        ];
    }

    public function withStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock_id' => Stock::factory()->create()->id,
            ];
        });
    }

}
