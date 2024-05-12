<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Requests\NewStockRequest;

class OrderService
{
    public function createNewOrderItem(Order $order, Request $request)
    {
        $item = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'stock_id' => $request->stock_id,
            'quantity' => $request->quantity,
            'parts' => $request->parts,
            'unit_price' => $request->unit_price,
            'discount' => $request->discount,
            'discount_limit' => $request->discount_limit,
            'total_amount' => $request->total_amount,
            'expire_date' => $request->expire_date ? createDateFromExpireFormat($request->expire_date) : null
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

    public function updateOrder(Order $order, Request $request)
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

    public function updateOrderItem(OrderItem $item, Request $request)
    {
        $item->update([
            'product_id' => $request->product_id ?? $item->product_id,
            'quantity' => $request->quantity ?? $item->quantity,
            'parts' => $request->parts ?? $item->parts,
            'unit_price' => $request->unit_price ?? $item->unit_price,
            'discount' => $request->discount ?? $item->discount,
            'discount_limit' => $request->discount_limit ?? $item->discount_limit,
            'total_amount' => $request->total_amount ?? $item->total_amount,
            'expire_date' => $request->expire_date ? createDateFromExpireFormat($request->expire_date) : $item->expire_date,
        ]);
        $request->session()->flash('severity', 'success');
        $request->session()->flash('message', 'Item was updated successfully');
    }

    public function completeSupplierOrder($validated)
    {
        $stocks = $validated->stocks;
        \DB::transaction(function () use ($stocks, $validated) {
            foreach ($stocks as $stock) {
                $partsPerUnit = Product::where('id', $stock['product_id'])->pluck('parts_per_unit')->first();
                Stock::insert([
                    'code' => randomCode(6),
                    'product_id' => $stock['product_id'],
                    'supplier_id' => $stock['supplier_id'],
                    'original_quantity' => $stock['quantity'],
                    'original_parts' => $stock['parts'],
                    'available_quantity' => $stock['quantity'],
                    'available_parts' => $stock['parts'],
                    'parts_per_unit' => $partsPerUnit,
                    'expire_date' => $stock['expire_date'] ? createDateFromExpireFormat($stock['expire_date']) : null,
                    'price' => $stock['price'],
                    'discount' => $stock['discount'],
                    'discount_limit' => $stock['discount_limit']
                ]);
            }
            $order = Order::find($validated->order_id);
            $order->update([
                'completed' => true
            ]);
        });
        return $stocks;
    }

    public function completeCustomerOrder(Request $request, StockService $stockService)
    {
        //check the availability of quantity and parts
        //check if the order discount doesn't exceed the stock discount

        \DB::transaction(function () use ($request, $stockService) {
            $items = OrderItem::hydrate($request->items);
            foreach ($items as $item) {
                $stock = Stock::find($item->stock_id);
                $stockService->updateStockDueToCustomerOrder($stock, $item);
            }
            $order = Order::find($request->order_id);
            $order->update([
                'completed' => true
            ]);
        });
        return response()->json([
            'message' => 'Order completed successfully'
        ]);
    }
}
