<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class AddOnsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->translate(locale()) ? $this->translate(locale())->name : '---',
            'type' => $this->type,
            'options_count' => $this->options_count,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
            'addonOptions' => AddonOptionsResource::collection($this->addOnOptions),
        ];
    }
}
