<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'startDate' => Carbon::parse($this->start)->format('Y-m-d'),
            'startTime' => Carbon::parse($this->start)->format('g:i A'),
            'endDate' => $this->end == null ? null : Carbon::parse($this->end)->format('Y-m-d'),
            'endTime' => $this->end == null ? null : Carbon::parse($this->end)->format('g:i A'),
            'expectedAmount' => $this->expected_amount,
            'realAmount' => $this->real_amount,
            'difference' => $this->difference,
            'user' => $this->user->username,
        ];
    }
}
