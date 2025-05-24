<?php

namespace Modules\DriverApp\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'username' => $this->username,
            'state_id' => $this->state_id,
            'state' => optional(optional($this->state)->translate(locale()))->title,
            'city' => optional(optional(optional($this->state)->city)->translate(locale()))->title,
            'block' => $this->block,
            'building' => $this->building,
            'street' => $this->street,
            'address' => $this->address,
            'lat' => $this->lat,
            'long' => $this->long,
        ];
    }
}
