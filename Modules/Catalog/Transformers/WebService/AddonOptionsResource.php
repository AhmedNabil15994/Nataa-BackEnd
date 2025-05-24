<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class AddonOptionsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'option' => $this->translate(locale()) ? $this->translate(locale())->option : '---',
            'price' => number_format($this->price, 3),
            'default' => $this->default ? 1 : 0,
            'image' => $this->image ? url($this->image) : null,
        ];
    }
}
