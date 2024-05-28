<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function show(string $reference_code, ?string $type = 'customer')
    {
        $order = Order::where('reference_code', $reference_code)->with('items')->first();
        if ($type == 'supplier') {
            $this->authorize('make supplier order');
            return Inertia::render('Order/Supplier', [
                'order' => $order,
                'suppliers' => Supplier::select('id', 'name')->get()
            ]);
        } elseif ($type == 'return') {
            $this->authorize('make return order');
            return Inertia::render('Order/Return', [
                'order' => $order,
            ]);
        } else {
            $this->authorize('make customer order');
            return Inertia::render('Order/Customer', [
                'order' => $order
            ]);
        }
    }


    public function newOrder(string $type)
    {
        $order = Order::create([
            'reference_code' => randomCode(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('order.show', [
            'reference_code' => $order->reference_code,
            'type' => $type
        ]);
    }

    //unused
    public function update(Order $order, Request $request, OrderService $service)
    {
        $service->updateOrder($order, $request);
    }


    public function newItem(Order $order, Request $request, OrderService $service)
    {
        return $service->createNewOrderItem($order, $request);
    }

    public function items(Order $order)
    {
        $items = $order->items;
        return OrderItemResource::collection($items);
    }

    public function updateItem(OrderItem $item, Request $request, OrderService $service)
    {
        $service->updateOrderItem($item, $request);
    }

    public function deleteItem(OrderItem $item, Request $request)
    {
        $item->delete();

    }
}
