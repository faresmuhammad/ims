<?php

namespace App\Services;

class StatisticsService
{
    //todo: total revenue with calendar filtration
    //revenue is the sum of profit
    //get all order items of customer order with their stock data also order items of return order for opposite effect on revenue
    //for each customer order item
    //ex: item-> sell: 100, q: 1, p: 0, d: 0, total: 100 | stock-> sell: 100, d: 10
    //revenue = 100 - 100 * (1 - 0.1) = 10 = [item total - stock sell * (1 - discount fraction)]
    //ex: item-> sell: 100, q: 2, p: 0, d: 0, total: 200 | stock-> sell: 100, d: 10
    //revenue = 200 - 100 * 0.9 * 2 = 20 = [item total - stock sell * (1 - discount fraction) * q]
    //ex: item-> sell: 100, q: 0, p: 1, d: 0, total: 33.3 | stock-> sell: 100, d: 10
    //revenue = 33.3 - (100/3) * 0.9 * 1 = 3.3 = [item total - (stock sell / parts per unit) * (1 - discount fraction) * p]
    //ex: item-> sell: 100, q: 2, p: 1, d: 0, total: 233.3 | stock-> sell: 100, d: 10
    //revenue = 233.3 - (100 * 2 + (100/3) * 1) * 0.9 = 23.3 = [item total - (stock sell * q + (stock sell / parts per unit) * p) * (1 - discount fraction)]

    public function calculateTotalRevenue($items)
    {
        $revenue = 0;
        foreach ($items as $item) {
            $stock = $item->stock;
            $discountPercent = $stock->discount / 100;
            $revenue += $item->total_amount - ($stock->price * $item->quantity + ($stock->price / $stock->parts_per_unit) * $item->parts) * (1 - $discountPercent);
        }
        return $revenue;
    }

    //todo: total finished orders with calendar filtration
    //todo: expected revenue with calendar filtration
}
