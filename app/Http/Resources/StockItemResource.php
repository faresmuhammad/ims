<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->product->id,
            'name' => $this->product->name,
            'code' => $this->code,
            'price' => $this->price,
            'parts_per_unit' => $this->product->parts_per_unit,
            'stock' => [
                'id' => $this->id,
                'expire_date' => formatExpireDate($this->expire_date),
                'price' => $this->price,
                'available_quantity' => $this->available_quantity,
                'available_parts' => $this->available_parts,
            ]
        ];
    }
}
