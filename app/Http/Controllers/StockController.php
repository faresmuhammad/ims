<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Models\Order;
use App\Models\Stock;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function store(NewStockRequest $request, OrderService $service)
    {
        $validated = $request->safe();
        return $service->completeOrder($validated);
    }


    public function update(Stock $stock, UpdateStockRequest $request)
    {
        $validated = $request->safe();
        $stock->update([
            'original_quantity' => $validated->original_quantity,
            'available_quantity' => $validated->available_quantity,
            'original_parts' => $validated->original_parts,
            'available_parts' => $validated->available_parts,
            'expire_date' => $validated->expire_date ? createDateFromExpireFormat($validated->expire_date) : $stock->expire_date,
            'price' => $validated->price,
            'discount' => $validated->discount,
            'discount_limit' => $validated->discount_limit,
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
