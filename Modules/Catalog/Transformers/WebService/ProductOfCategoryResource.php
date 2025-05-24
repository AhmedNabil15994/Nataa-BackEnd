<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOfCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'price' => is_null($this->price) ? 0 : $this->price,
            'sku' => $this->sku,
            'qty' => $this->qty,
            'image' => url($this->image),
            'title' => $this->translate(locale())->title,
            'offer' => new ProductOfferResource($this->offer),
            // add variations
        ];

        if ($request->route()->getName() != 'api.home') {
            $result['description'] = htmlView($this->translate(locale())->description);
            $result['short_description'] = $this->translate(locale())->short_description;
        }

        return $result;
    }
}
