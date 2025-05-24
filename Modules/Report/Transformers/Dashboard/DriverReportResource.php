<?php

namespace Modules\Report\Transformers\Dashboard;

use Illuminate\Http\Resources\Json\Resource;

class DriverReportResource extends Resource
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
            "id" => $this->id,
            'total' => $this->orders()->sum('shipping') ?? 0,
            'driver'    => $this->name,
            'company'   => $this->driver_company->name ?? '',
            'orders_count'   => count($this->orders) ?? 0,
            'created_at' => date('d-m-Y H:i', strtotime($this->created_at)),
        ];
    }
}
