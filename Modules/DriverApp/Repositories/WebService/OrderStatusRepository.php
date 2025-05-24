<?php

namespace Modules\DriverApp\Repositories\WebService;

use Modules\Order\Entities\OrderStatus;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enum\Order as OrderEnums;

class OrderStatusRepository
{
    protected $orderStatus;

    function __construct(OrderStatus $orderStatus)
    {
        $this->orderStatus = $orderStatus;
    }

    public function getAll($order = 'sort', $sort = 'asc')
    {
        $query = $this->orderStatus->where('flag', '!=', 'failed');
        if (auth('api')->user()->can('driver_access')) {
            $query = $query->whereIn('flag', OrderEnums::DRIVER_ORDERS_STATUSES_CAN_SHOW);
        }
        return $query->orderBy($order, $sort)->get();
    }

    public function getAllFinalStatus($order = 'sort', $sort = 'asc')
    {
        return $this->orderStatus->finalStatus()->orderBy($order, $sort)->get();
    }
}
