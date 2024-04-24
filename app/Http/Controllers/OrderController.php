<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function supplier()
    {
        return Inertia::render('Order/Supplier', [
            'suppliers' => Supplier::select('id', 'name')->get()
        ]);
    }

    public function newSupplierOrder()
    {
        $order = Order::create([
            'reference_code' => randomCode(),
            'user_id' => auth()->id(),
            'supplier_id' => null
        ]);
        // return redirect('/orders/supplier')->with('order', $order);
        return inertia('Order/Supplier',[
            'order' => $order,
            'suppliers' => Supplier::select('id', 'name')->get()
        ]);
    }

    public function update(Order $order, Request $request)
    {
        $order->update([
            'supplier_id' => $request->supplier_id,
            'total_amount' => $request->total_amount,
            'discount' => $request->discount,
            'completed' => $request->completed
        ]);
        $request->session()->flash('severity', 'success');
        $request->session()->flash('message', 'Order updated successfully');
    }

    public function getProduct($code)
    {
        $product = Product::where('code', $code)->first();

        return response()->json([
            'product' => $product
        ]);
    }
}
