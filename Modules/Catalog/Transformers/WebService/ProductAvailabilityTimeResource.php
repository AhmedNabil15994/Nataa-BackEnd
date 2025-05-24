<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAvailabilityTimeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'day'   => $this->day,
            'times' => $this->where('day',$this->day)->get(['id','product_id','time_from','time_to'])->toArray(),
        ];
    }
}
