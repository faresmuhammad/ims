<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'original_quantity' => $this->original_quantity,
            'original_parts' => $this->original_parts,
            'available_quantity' => $this->available_quantity,
            'available_parts' => $this->available_parts,
            'sold_quantity' => $this->sold_quantity,
            'sold_parts' => $this->sold_parts,
            'parts_per_unit' => $this->product->parts_per_unit,
            'discount' => $this->discount,
            'discount_limit' => $this->discount_limit,
            'expire_date' => formatExpireDate($this->expire_date),
            'price' => $this->price,
            'supplier' => $this->supplier->name,
        ];
    }
}
