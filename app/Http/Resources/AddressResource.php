<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "street" => $this->whenNotNull($this->street),
            "village" => $this->whenNotNull($this->village),
            "district" => $this->whenNotNull($this->district),
            "city" => $this->whenNotNull($this->city),
            "province" => $this->whenNotNull($this->province),
            "state" => $this->state,
            "postal_code" => $this->whenNotNull($this->postal_code),
        ];
    }
}
