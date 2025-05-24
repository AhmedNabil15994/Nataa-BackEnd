<?php

namespace Modules\Cart\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Vendor\Traits\VendorTrait;

class CartVendorResource extends JsonResource
{
    use VendorTrait;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => url($this->image),
            'title' => $this->translate(locale())->title,
        ];
    }
}
