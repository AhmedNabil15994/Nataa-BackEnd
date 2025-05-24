<?php

namespace Modules\Catalog\Transformers\Vendor;

use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
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
            'title' => optional($this->translate(locale()))->title,
            'image' => url($this->image),
            'status' => $this->status,
            'price' => $this->price,
            'vendor' => optional(optional($this->vendor)->translate(locale()))->title,
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
