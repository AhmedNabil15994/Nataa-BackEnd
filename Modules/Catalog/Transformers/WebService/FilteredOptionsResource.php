<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class FilteredOptionsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->translate(locale())->title,
            'option_values' => FilteredOptionValuesResource::collection($this->values),
        ];
    }
}
