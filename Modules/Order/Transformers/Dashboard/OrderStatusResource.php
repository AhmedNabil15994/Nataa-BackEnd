<?php

namespace Modules\Order\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class OrderStatusResource extends Resource
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
            'flag' => $this->flag,
            'title' => $this->translate(locale())->title,
            /*'success_status' => $this->success_status,
            'failed_status' => $this->failed_status,*/
//            'color_label' => \GuzzleHttp\json_decode($this->color_label),
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];
    }
}
