<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Stock;

class StockService
{
    public function updateStockDueToCustomerOrder(Stock $stock, OrderItem $item)
    {
        $partsPerUnit = $stock->parts_per_unit;
        $stock->available_quantity -= $item->quantity;
        if ($item->parts != 0 && $stock->available_parts != 0) {
            $stock->available_parts = $stock->available_parts - $item->parts;
        }
        elseif ($item->parts != 0 && $item->parts <= $partsPerUnit && $stock->available_parts == 0) {
            $stock->available_quantity -= 1;
            $stock->available_parts = $partsPerUnit - $item->parts;
        }
        elseif ($item->parts != 0 && $item->parts > $partsPerUnit && $stock->available_parts == 0) {
            $units = (int) ($item->parts / $partsPerUnit);
            $stock->available_quantity -= $units;
            $stock->available_parts = $partsPerUnit - $item->parts - $units * $partsPerUnit;
        }

        $stock->save();
    }
    //Updating the stock
    //TODO: decrease the order quantity from the available_quantity column - Done
    //TODO: if order parts is not zero && available parts is not zero --> count down available parts by the order parts - Done
    //TODO: if order parts is not zero && order parts less than product parts per unit && available parts is zero --> count down available quantity by 1 && set available parts to the remaining parts - Done
    //TODO: if order parts is not zero && order parts greater than product parts per unit && available parts is zero --> count down available quantity by units && set available parts to the remaining parts

}
