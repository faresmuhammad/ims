<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Models\Order;
use App\Models\Stock;
use App\Services\OrderService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function store(NewStockRequest $request, OrderService $service)
    {
        $this->authorize('make supplier order');
        $validated = $request->safe();
        return $service->completeSupplierOrder($validated);
    }


    public function update(Stock $stock, UpdateStockRequest $request)
    {
        $this->authorize('edit stock');
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
    //TODO: order from a stock

    public function order(Request $request, OrderService $service, StockService $stockService)
    {
        $this->authorize('make customer order');
        return $service->completeCustomerOrder($request, $stockService);
    }

    public function return(Request $request, OrderService $service, StockService $stockService)
    {
        $this->authorize('make return order');
        return $service->completeReturnOrder($request, $stockService);
    }

    //TODO: delete stock
}
