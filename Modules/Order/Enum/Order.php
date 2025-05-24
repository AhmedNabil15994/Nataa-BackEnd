<?php

namespace Modules\Order\Enum;

class Order
{

    /**  DRIVER FLAGS CONSTS **/
    const DRIVER_BLOCKED_CHANGE_ORDER_STATUSES_FLAGS = ["cancelled","failed","refund","delivered"];
    const DRIVER_BLOCKED_CHANGE_STATUSES_FLAGS = ["new_order"];
    const DRIVER_ORDERS_CAN_SHOW_WITHOUT_JOIN = ["success"];
    const DRIVER_ORDERS_CAN_SHOW_WITH_JOIN = ["success","pending","delivered"];
    const DRIVER_ORDERS_STATUSES_CAN_SHOW = ["success","pending","delivered"];
    const DRIVER_NEW_ORDER_STATUSE_FOR_NOTIFICATION = ["success"];
    /**  === END DRIVER FLAGS CONSTS ===== **/


    /**  VENDOR FLAGS CONSTS **/
    const VENDOR_ORDERS_CAN_UPDATE_TO = ["success","refund","cancelled"];
    /**  === END VENDOR FLAGS CONSTS ===== **/
}
