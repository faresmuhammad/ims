<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function show(string $reference_code, ?string $type = 'customer')
    {
        $order = Order::where('reference_code', $reference_code)->with('items')->first();
        if ($type == 'supplier') {
            return Inertia::render('Order/Supplier', [
                'order' => $order,
                'suppliers' => Supplier::select('id', 'name')->get()
            ]);
        } elseif ($type == 'return') {
            return $order;
        } else {
            return $order;
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

    public function update(Order $order, Request $request)
    {
        $order->update([
            'supplier_id' => $request->supplier_id ?? $order->supplier_id,
            'total_amount' => $request->total_amount ?? $order->total_amount,
            'discount' => $request->discount ?? $order->discount,
            'completed' => $request->completed ?? $order->completed
        ]);
        $request->session()->flash('severity', 'success');
        $request->session()->flash('message', 'Order updated successfully');
    }



    public function newItem(Order $order, Request $request)
    {
        $item = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'parts' => $request->parts,
            'unit_price' => $request->unit_price,
            'discount' => $request->discount,
            'total_amount' => $request->total_amount,
            'expire_date' => convertStringToDatemmyyyy($request->expire_date)
        ]);
        return response()->json([
            'item' => $item->with(['product'])->first(),
            'items' => $order->items()->with([
                'product' => function ($query) {
                    $query->select('id', 'name', 'code', 'price');
                }
            ])->get()
        ]);
    }

    public function items(Order $order)
    {
        $items = $order->items;
        return OrderItemResource::collection($items);
    }

    public function updateItem(OrderItem $item, Request $request)
    {
        $item->update([
            'product_id' => $request->product_id ?? $item->product_id,
            'quantity' => $request->quantity ?? $item->quantity,
            'parts' => $request->parts ?? $item->parts,
            'unit_price' => $request->unit_price ?? $item->unit_price,
            'discount' => $request->discount ?? $item->discount,
            'total_amount' => $request->total_amount ?? $item->total_amount,
            'expire_date' => convertStringToDatemmyyyy($request->expire_date) ?? $item->expire_date,
        ]);
        $request->session()->flash('severity', 'success');
        $request->session()->flash('message', 'Item was updated successfully');
    }

    public function deleteItem(OrderItem $item, Request $request)
    {
        $item->delete();

    }
}
