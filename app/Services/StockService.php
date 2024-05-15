<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Stock;

class StockService
{
    public function updateStockDueToCustomerOrder(Stock $stock, OrderItem $item)
    {
        $partsPerUnit = $stock->parts_per_unit;
        if ($item->quantity != 0 && $item->parts == 0) {
            $stock->available_quantity -= $item->quantity;
        } elseif ($item->parts != 0 && $stock->available_parts != 0) {
            $stock->available_parts = $stock->available_parts - $item->parts;
        } elseif ($item->parts != 0 && $item->parts <= $partsPerUnit && $stock->available_parts == 0) {
            $stock->available_quantity -= 1;
            $stock->available_parts = $partsPerUnit - $item->parts;
        } elseif ($item->parts != 0 && $item->parts > $partsPerUnit && $stock->available_parts == 0) {
            $units = (int)($item->parts / $partsPerUnit);
            $stock->available_quantity -= $units;
            $stock->available_parts = $partsPerUnit - ($item->parts - $units * $partsPerUnit);
        }

        $stock->save();
    }

}
