<?php

namespace Modules\Vendor\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class VendorStatusResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
           'id'               => $this->id,
           'accepted_orders'  => $this->accepted_orders,
           'title'            => $this->translate(locale())->title,
           'label_color'      => $this->label_color,
           'deleted_at'       => $this->deleted_at,
           'created_at'       => date('d-m-Y' , strtotime($this->created_at)),
       ];
    }
}
