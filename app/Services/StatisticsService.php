<?php

namespace App\Services;

use App\Models\Order;

class StatisticsService
{
    //todo: total revenue with calendar filtration
    //revenue is the sum of profit
    //get all order items of customer order with their stock data also order items of return order for opposite effect on revenue
    //for each customer order item
    //ex: item-> sell: 100, q: 2, p: 1, d: 0, total: 233.3 | stock-> sell: 100, d: 10
    //revenue = 233.3 - (100 * 2 + (100/3) * 1) * 0.9 = 23.3 = [item total - (stock sell * q + (stock sell / parts per unit) * p) * (1 - stock discount fraction)]

    //ex: item-> sell: 100, q: 2, p: 1, d: 5, total: 221.635 | stock-> sell: 100, d: 10
    //revenue = 221.635 - (100 * 2 + (100/3) * 1) * 0.9  = 11.6 = [item total - (stock sell * q + (stock sell / parts per unit) * p) * (1 - stock discount fraction)]

    public function calculateTotalRevenue($items)
    {
        return $this->calculateOrdersRevenue($items);
    }

    //todo: total finished orders with calendar filtration
    public function getTotalOrders()
    {
        return Order::where([
            'supplier_id' => null,
            'completed' => true
        ])->count();
    }

    //todo: expected revenue with calendar filtration
    public function calculateExpectedRevenue($items, $stocks, $bestCase = true)
    {
        $revenue = 0;
        /**best case
         * revenue is the percent of stock discount
         * first calculate revenue of the completed orders within the date range
         * calculate available stocks as order items with no discount applied
         *
         * For stock item
         * stock -> aq: 5, ap: 2, price: 100, parts per unit: 3, d: 10
         * revenue = (100 * 5 + (100/3) * 2) * 0.1 =  = (price * aq + (price / parts per unit) * ap) * d
         */
        $revenue += $this->calculateOrdersRevenue($items);

        if ($bestCase) {
            foreach ($stocks as $stock) {
                $discount = $stock->discount / 100;
                $revenue += $this->calculateCost($stock->price, $stock->available_quantity, $stock->available_parts, $stock->parts_per_unit, $discount);
            }
        } else {
            foreach ($stocks as $stock) {
                $discount = ($stock->discount - $stock->discount_limit) / 100;
                $revenue += $this->calculateCost($stock->price, $stock->available_quantity, $stock->available_parts, $stock->parts_per_unit, $discount);
            }
        }
        return $revenue;
        /**worst case
         * revenue is the percent of stock discount limit
         * first calculate revenue of the completed orders within the date range
         * calculate available stocks as order items with discount limit applied
         * For stock item
         *  stock -> aq: 5, ap: 2, price: 100, parts per unit: 3, d: 10, dl: 7
         *  revenue = (100 * 5 + (100/3) * 2) * (0.1 - 0.07) = 17 = (price * aq + (price / parts per unit) * ap) * (d - dl)
         */
    }

    private function calculateCost($price, $quantity, $parts, $partsPerUnit, $discount)
    {
        return ($price * $quantity + ($price / $partsPerUnit) * $parts) * $discount;
    }

    private function calculateOrdersRevenue($items)
    {
        $revenue = 0;
        foreach ($items as $item) {
            $stock = $item->stock;
            $discount = 1 - ($stock->discount / 100);
            $cost = $this->calculateCost($stock->price, $item->quantity, $item->parts, $stock->parts_per_unit, $discount);
            $revenue += $item->total_amount - $cost;
        }
        return $revenue;

    }
}
