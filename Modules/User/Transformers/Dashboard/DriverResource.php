<?php

namespace Modules\User\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class DriverResource extends Resource
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
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'image' => url($this->image),
            // 'company' => $this->company->translate(locale())->name,
            // 'vendor' => count($this->vendors) == 0 ? '---' : $this->vendors()->first()->translate(locale())->title,
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
