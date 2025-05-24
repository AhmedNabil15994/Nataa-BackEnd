<?php

namespace Modules\Catalog\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => optional($this->translate(locale()))->title,
            'image' => url($this->image),
            'status' => $this->status,
            'price' => $this->price,
            'qty' => $this->qty,
            'sort' => $this->sort,
            'vendor' => optional(optional($this->vendor)->translate(locale()))->title,
            'deleted_at' => $this->deleted_at,
            'created_at' => date('d-m-Y', strtotime($this->created_at)),
        ];

        if ($this->categories) {
            $numItems = count($this->categories->toArray());
            $i = 0;
            $title = '';
            foreach ($this->categories as $k => $cat) {
                $title .= optional($cat->translate(locale()))->title;
                if (++$i !== $numItems) { // if it is not the last index
                    $title .= ' - ';
                }
            }
            $data['categories'] = $title;
        } else {
            $data['categories'] = '';
        }

        return $data;
    }
}
