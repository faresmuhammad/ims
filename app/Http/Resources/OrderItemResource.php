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
            // 'order' => $this->order,
            'id' => $this->id,
            'product' => $this->product,

            'quantity' => $this->quantity,
            'parts' => $this->parts,
            'discount' => $this->discount,

            'unit_price' => $this->unit_price,
            'total_amount' => $this->total_amount,
            'expire_date' => formatCarbonDate($this->expire_date),
        ];
    }
}
