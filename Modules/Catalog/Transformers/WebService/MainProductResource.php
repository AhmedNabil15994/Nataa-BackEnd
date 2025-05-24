<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class MainProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
           'id'                   => $this->id,
           'image'                => url($this->image),
           'title'                => $this->translate(locale())->title,
           'description'          => htmlView($this->translate(locale())->description),
           'short_description'    => $this->translate(locale())->short_description,
       ];
    }
}
