<?php

namespace Modules\Slider\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'image' => url($this->image),
            'link' => $this->link,
            'title' => optional($this->translate(locale()))->title,
            'short_description' => optional($this->translate(locale()))->short_description,
        ];

        if ($this->morph_model == 'Category') {
            $result['target'] = 'categories';
        } elseif ($this->morph_model == 'Product') {
            $result['target'] = 'products';
        } else {
            $result['target'] = 'external';
        }
        return $result;
    }
}
