<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'description' => $this->description,
            'price' => $this->price,
            'stock_offset' => $this->stock_offset,
            'parts_per_unit' => $this->parts_per_unit,
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),

        ];
    }
}
