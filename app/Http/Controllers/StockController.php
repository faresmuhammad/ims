<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    //TODO: store stock

    public function store(Request $request)
    {
        $stock = Stock::create([
            'code' => randomCode(6),
            'product_id' => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'original_quantity' => $request->quantity,
            'available_quantity' => $request->quantity,
            'available_parts' => $request->parts,
            'expire_date' => $request->expire_date,
            'price' => $request->price,
            'discount' => $request->discount
        ]);
        return $stock;
    }

    /*
    'code'
    'product_id'
    'supplier_id'
    'original_quantity'
    'available_quantity'
    'available_parts'
    'sold_quantity'
    'sold_parts'
    'discount'
    'expire_date'
    'price'
    */
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
