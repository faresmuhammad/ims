<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $value = [];
        if (!is_null($this->value_integer))
            $value = ['value' => $this->value_integer];
        if (!is_null($this->value_date))
            $value = ['value' => $this->value_date];
        if (!is_null($this->value_start_date))
            $value = ['value' => ['start' => $this->value_start_date, 'end' => $this->value_end_date]];
        return [
                'key' => $this->key,
                'category' => $this->category
            ] + $value;
    }
}
