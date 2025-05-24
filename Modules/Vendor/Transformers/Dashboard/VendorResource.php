<?php

namespace Modules\Vendor\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class VendorResource extends Resource
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
           'id'                 => $this->id,
//           'delivery_charges'   => count($this->deliveryCharge),
           'title'              => $this->translate(locale())->title,
           'description'        => $this->translate(locale())->description,
           'image'              => url($this->image),
           'status'             => $this->status,
           'deleted_at'         => $this->deleted_at,
           'created_at'         => date('d-m-Y' , strtotime($this->created_at)),
       ];
    }
}
