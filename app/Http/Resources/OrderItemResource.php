<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product' => [
                'id' => $this->product->id,
                'code' => $this->product->code,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'parts_per_unit' => $this->product->parts_per_unit,
            ],
            'quantity' => $this->quantity,
            'parts' => $this->parts,
            'discount' => $this->discount,
            'discount_limit' => $this->discount_limit,
            'unit_price' => $this->unit_price,
            'total_amount' => $this->total_amount,
            'expire_date' => formatExpireDate($this->expire_date),
            'stock_id' => $this->stock_id
        ];
    }
}
