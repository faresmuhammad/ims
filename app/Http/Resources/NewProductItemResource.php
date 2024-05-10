<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewProductItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stockRelatedFields = [];
        /*
         * [
         *     'prices' => array of objects { stock_id, price, A. quantity, A. parts },
         *     'expire_dates' => array of objects { stock_id, expire_date, A. quantity, A. parts },
         * ]
         * */
        if ($this->relationLoaded('stocks')) {
            $stocks = $this->stocks;

            if ($stocks->count() > 1) {
                $prices = [];
                $expireDates = [];
                foreach ($stocks as $stock) {
                    $prices[] = [
                        'id' => $stock->id,
                        'price' => $stock->price,
                        'available_quantity' => $stock->available_quantity,
                        'available_parts' => $stock->available_parts,
                    ];
                    $expireDates[] = [
                        'id' => $stock->id,
                        'expire_date' => formatExpireDate($stock->expire_date),
                        'available_quantity' => $stock->available_quantity,
                        'available_parts' => $stock->available_parts,
                    ];
                }
                $stockRelatedFields = [
                    'stock' => [
                        'prices' => $prices,
                        'expire_dates' => $expireDates,
                    ]
                ];
            } else {
                $stockRelatedFields = [
                    'stock' => [
                        'id' => $stocks[0]->id,
                        'price' => $stocks[0]->price,
                        'expire_date' => formatExpireDate($stocks[0]->expire_date),
                        'available_quantity' => $stocks[0]->available_quantity,
                        'available_parts' => $stocks[0]->available_parts,
                    ]
                ];
            }
        }
        return [
                'id' => $this->id,
                'name' => $this->name,
                'code' => $this->code,
                'price' => $this->price,
                'parts_per_unit' => $this->parts_per_unit
            ] + $stockRelatedFields;
    }
}
