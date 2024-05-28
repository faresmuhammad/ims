<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Stock;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index(Request $request, StatisticsService $service)
    {
        $items = OrderItem::whereNotNull('stock_id')->with('stock:id,price,discount,parts_per_unit')->get();
        $stocks = Stock::available()->get();
        return [
            'revenue' => $service->calculateTotalRevenue($items),
            'orders' => $service->getTotalOrders(),
            'expected' => $service->calculateExpectedRevenue($items,$stocks)
        ];
    }
}
