<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewStockRequest;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //TODO: store stock

    public function store(NewStockRequest $request)
    {
        $validated = $request->safe();
        $stocks = $validated->stocks;
        foreach ($stocks as $stock) {

            Stock::insert([
                'code' => randomCode(6),
                'product_id' => $stock['product_id'],
                'supplier_id' => $stock['supplier_id'],
                'original_quantity' => $stock['quantity'],
                'available_quantity' => $stock['quantity'],
                'available_parts' => $stock['parts'],
                'expire_date' => $stock['expire_date'],
                'price' => $stock['price'],
                'discount' => $stock['discount'],
                'discount_limit' => $stock['discount_limit']
            ]);
        }
        $order = Order::find($validated->order_id);
        $order->update([
            'completed' => true
        ]);
        return $stocks;
    }

    //TODO: update stock

    public function update(Stock $stock, Request $request)
    {
        $stock->update([
            'supplier_id' => $request->supplier_id,
            'original_quantity' => $request->original_quantity,
            'available_quantity' => $request->available_quantity,
            'original_parts' => $request->original_parts,
            'available_parts' => $request->available_parts,
            'expire_date' => $request->expire_date,
            'price' => $request->price,
            'discount' => $request->discount,
        ]);
        return $stock;
    }

    //TODO: check availability stock for a product
    //TODO: check the discount validity that not exceed the stock discount
    //TODO: check different prices in stocks for a product and return all prices
    //TODO: order from a stock

    public function order()
    {
        //get the data from request which is order item
        //check the availability of quantity and parts
        //check if the order discount doesn't exceed the stock discount

        //Updating the stock
        //decrease the order quantity from the available_quantity column
        //if order parts is not zero && available parts is zero --> count down available quantity by 1 && set available parts to the remaining parts
        //if order parts is not zero && available parts is not zero --> count down available parts by the order parts
    }

    //TODO: return to stock
    //TODO: get stocks by product 


    //TODO: delete stock
}
