<?php

namespace Modules\Company\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class DeliveryChargeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'company' => $this->translate(locale())->name,
            'state' => $this->translate(locale())->description,
            'delivery' => $this->delivery,
            'delivery_time' => $this->delivery_time,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
