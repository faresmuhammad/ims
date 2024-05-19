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
        if ($this->relationLoaded('stocks')) {
            $stocks = $this->stocks;

            if ($stocks->count() > 1) {
                $prices = [];
                $expireDates = [];
                $meta = [];
                foreach ($stocks as $stock) {
                    $meta[] = [
                        'id' => $stock->id,
                        'available_quantity' => $stock->available_quantity,
                        'discount_limit' => $stock->discount_limit
                    ];
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
                        'meta' => $meta,
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
                        'discount_limit' => $stocks[0]->discount_limit
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
