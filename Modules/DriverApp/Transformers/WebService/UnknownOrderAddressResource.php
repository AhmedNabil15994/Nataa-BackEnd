<?php

namespace Modules\DriverApp\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class UnknownOrderAddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'receiver_name' => $this->receiver_name,
            'receiver_mobile' => $this->receiver_mobile,
            'state_id' => $this->state_id,
            'state' => $this->state->translate(locale())->title,
            'city' => $this->state->city->translate(locale())->title,
            'lat' => $this->lat,
            'long' => $this->long,
        ];
    }
}
