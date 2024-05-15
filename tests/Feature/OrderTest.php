<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;


    public function test_completing_supplier_order_add_stocks_to_database_and_mark_order_as_completed(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->withSupplier()->create();
        $items = OrderItem::factory()->count(2)->create([
            'order_id' => $order->id,
        ]);
        $stocks = $items->map(function ($item) use ($order) {
            return [
                'product_id' => $item->product_id,
                'supplier_id' => $order->supplier_id,
                'quantity' => $item->quantity,
                'parts' => $item->parts,
                'expire_date' => $item->expire_date ? createDateFromExpireFormat($item->expire_date) : null,
                'price' => $item->unit_price,
                'discount' => $item->discount,
                'discount_limit' => $item->discount_limit
            ];
        })->toArray();
        $response = $this->actingAs($user)->post('/order/supplier/complete', [
            'stocks' => $stocks,
            'order_id' => $order->id,
        ]);
        $order->refresh();

        $this->assertDatabaseHas('stocks', [
            'product_id' => $items->first()->product_id,
        ]);
        $this->assertEquals(1, $order->completed);
    }

    public function test_completing_customer_order_decrease_quantity_from_stock()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $stock = Stock::factory()->create();
        $items = OrderItem::factory()->count(2)->create([
            'quantity' => 2,
            'parts' => 0,
            'order_id' => $order->id,
            'stock_id' => $stock->id,
        ]);
        $response = $this->actingAs($user)->put('/order/customer/complete', [
            'items' => $items->toArray(),
            'order_id' => $order->id,
        ]);
        $stock->refresh();
        $this->assertEquals(1, $stock->available_quantity);
    }
    public function test_completing_customer_order_decrease_available_parts_from_stock()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $stock = Stock::factory()->create([
            'original_parts' => 4,
            'available_parts' => 4,
        ]);
        $items = OrderItem::factory()->count(2)->create([
            'quantity' => 0,
            'parts' => 2,
            'order_id' => $order->id,
            'stock_id' => $stock->id,
        ]);
        $response = $this->actingAs($user)->put('/order/customer/complete', [
            'items' => $items->toArray(),
            'order_id' => $order->id,
        ]);
        $stock->refresh();
        $this->assertEquals(0, $stock->available_parts);
    }
    public function test_completing_customer_order_decrease_parts_less_than_parts_per_unit_with_no_available_parts()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $stock = Stock::factory()->create([
            'available_quantity' => 5,
            'available_parts' => 0,
        ]);
        $item = OrderItem::factory()->create([
            'quantity' => 0,
            'parts' => 1,
            'order_id' => $order->id,
            'stock_id' => $stock->id,
        ]);
        $response = $this->actingAs($user)->put('/order/customer/complete', [
            'items' => [$item->toArray()],
            'order_id' => $order->id,
        ]);
        $stock->refresh();
        $this->assertEquals(4, $stock->available_quantity);
        $this->assertEquals($stock->parts_per_unit - 1, $stock->available_parts);
    }
    public function test_completing_customer_order_decrease_parts_greater_than_parts_per_unit_with_no_available_parts()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $stock = Stock::factory()->create([
            'available_quantity' => 5,
            'available_parts' => 0,
        ]);
        $item = OrderItem::factory()->create([
            'quantity' => 0,
            'parts' => 7,
            'order_id' => $order->id,
            'stock_id' => $stock->id,
        ]);
        $response = $this->actingAs($user)->put('/order/customer/complete', [
            'items' => [$item->toArray()],
            'order_id' => $order->id,
        ]);
        dump($stock->available_parts);
        $stock->refresh();
        dump($stock->available_parts);
        $this->assertEquals(3, $stock->available_quantity);
        $this->assertEquals(2, $stock->available_parts);
    }
}
