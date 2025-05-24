<?php

namespace Modules\Area\Transformers\WebService;

use Illuminate\Http\Resources\Json\Resource;

class CountryResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'title' => $this->translate(locale())->title,
            'status' => $this->status,
        ];

        if ($request->route()->getName() == 'api.areas.countries_with_cities_and_states') {
            $result['cities'] = CityResource::collection($this->cities);
        }

        return $result;
    }
}
