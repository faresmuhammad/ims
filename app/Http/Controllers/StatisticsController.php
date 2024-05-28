<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function totalRevenue(Request $request, StatisticsService $service)
    {
        $items = OrderItem::whereNotNull('stock_id')->with('stock:id,price,discount,parts_per_unit')->get();
        return $service->calculateTotalRevenue($items);
    }
}
