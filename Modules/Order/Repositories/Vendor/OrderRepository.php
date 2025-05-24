<?php

namespace Modules\Order\Repositories\Vendor;

use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderStatus;
use Modules\Order\Entities\OrderVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\Enum\Order as OrderEnums;
use Modules\Order\Traits\NewOrderNotificationTrait;

class OrderRepository
{
    use NewOrderNotificationTrait;
    protected $order;
    protected $orderVendor;

    function __construct(Order $order, OrderVendor $orderVendor)
    {
        $this->order = $order;
        $this->orderVendor = $orderVendor;
    }

    public function monthlyOrders()
    {
        $data["orders_dates"] = $this->order
            ->whereHas('orderStatus', function ($query) {
                $query->successOrderStatus();
            })
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })
            ->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m') as date"))
            ->groupBy('date')
            ->pluck('date');

        $ordersIncome = $this->order
            ->whereHas('orderStatus', function ($query) {
                $query->successOrderStatus();
            })
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })
            ->select(\DB::raw("sum(total) as profit"))
            ->groupBy(\DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->get();

        $data["profits"] = json_encode(array_pluck($ordersIncome, 'profit'));

        return $data;
    }

    public function ordersType()
    {
        $orders = $this->order
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })
            ->select("order_status_id", \DB::raw("count(id) as count"))
            ->groupBy('order_status_id')
            ->get();


        foreach ($orders as $order) {

            $status = $order->orderStatus->translate(locale())->title;
            $order->type = $status;

        }

        $data["ordersCount"] = json_encode(array_pluck($orders, 'count'));
        $data["ordersType"] = json_encode(array_pluck($orders, 'type'));

        return $data;
    }

    public function completeOrdersOld()
    {
        $orders = $this->order->whereHas('orderStatus', function ($query) {
            $query->successOrderStatus();
        })
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })->count();

        return $orders;
    }

    public function completeOrders()
    {
        $orders = $this->orderVendor->whereHas('vendor.sellers', function ($q) {
            $q->where('seller_id', auth()->user()->id);
        })->whereHas('order.orderStatus', function ($query) {
            $query->successOrderStatus();
        })->count();

        return $orders;
    }

    public function totalProfitOld()
    {
        return $this->order
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })->whereHas('orderStatus', function ($query) {
                $query->successOrderStatus();
            })->sum('total');
    }

    public function totalProfit()
    {
        return $this->orderVendor
            ->whereHas('vendor.sellers', function ($q) {
                $q->where('seller_id', auth()->user()->id);
            })->whereHas('order.orderStatus', function ($query) {
                $query->successOrderStatus();
            })->sum('subtotal');
    }

    public function totalTodayProfitOld()
    {
        return $this->order->whereHas('vendors', function ($q) {
            $q->whereHas('sellers', function ($q) {
                $q->where('seller_id', auth()->user()->id);
            });
        })->whereHas('orderStatus', function ($query) {
            $query->successOrderStatus();
        })->whereDate("created_at", \DB::raw('CURDATE()'))
            ->sum('total');
    }

    public function totalTodayProfit()
    {
        return $this->orderVendor->whereHas('vendor.sellers', function ($q) {
            $q->where('seller_id', auth()->user()->id);
        })->whereHas('order.orderStatus', function ($query) {
            $query->successOrderStatus();
        })->whereDate("created_at", \DB::raw('CURDATE()'))
            ->sum('subtotal');
    }

    public function totalMonthProfitOld()
    {
        return $this->order->whereHas('vendors', function ($q) {
            $q->whereHas('sellers', function ($q) {
                $q->where('seller_id', auth()->user()->id);
            });
        })->whereHas('orderStatus', function ($query) {
            $query->successOrderStatus();
        })->whereMonth("created_at", date("m"))
            ->whereYear("created_at", date("Y"))
            ->sum('total');
    }

    public function totalMonthProfit()
    {
        return $this->orderVendor->whereHas('vendor.sellers', function ($q) {
            $q->where('seller_id', auth()->user()->id);
        })->whereHas('order.orderStatus', function ($query) {
            $query->successOrderStatus();
        })->whereMonth("created_at", date("m"))
            ->whereYear("created_at", date("Y"))
            ->sum('subtotal');
    }

    public function totalYearProfitOld()
    {
        return $this->order->whereHas('orderStatus', function ($query) {
            $query->successOrderStatus();
        })
            ->whereYear("created_at", date("Y"))
            ->sum('total');
    }

    public function totalYearProfit()
    {
        return $this->orderVendor->whereHas('vendor.sellers', function ($q) {
            $q->where('seller_id', auth()->user()->id);
        })->whereHas('order.orderStatus', function ($query) {
            $query->successOrderStatus();
        })->whereYear("created_at", date("Y"))
            ->sum('subtotal');
    }

    public function totalProfitCommission()
    {
        return $this->orderVendor->whereHas('vendor.sellers', function ($q) {
            $q->where('seller_id', auth()->user()->id);
        })->whereHas('order.orderStatus', function ($query) {
            $query->successOrderStatus();
        })->sum('total_profit_comission');
    }

    public function getAll($order = 'id', $sort = 'desc')
    {
        $orders = $this->order
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })->orderBy($order, $sort)->get();

        return $orders;
    }

    public function findById($id)
    {
        $order = $this->order
            ->with([
                'orderProducts.product',
            ])
            ->whereHas('vendors', function ($q) {
                $q->whereHas('sellers', function ($q) {
                    $q->where('seller_id', auth()->user()->id);
                });
            })
            ->withDeleted()->find($id);

        return $order;
    }

    public function getVendorProductsByOrderId($id)
    {
        $order = $this->order->with([
            'orderProducts' => function ($query) {
                $query->whereHas('product.vendor.sellers', function ($q) {
                    $q->where('seller_id', auth()->id());
                });
            },
            'orderVariations' => function ($query) {
                $query->whereHas('variant.product.vendor.sellers', function ($q) {
                    $q->where('seller_id', auth()->id());
                });
            },
        ])->whereHas('vendors', function ($q) {
            $q->whereHas('sellers', function ($q) {
                $q->where('seller_id', auth()->id());
            });
        })->withDeleted()->find($id);
        return $order;
    }

    public function updateUnread($id)
    {
        $order = $this->findById($id);
        if (!$order)
            abort(404);

        $order->update([
            'unread' => true,
        ]);
    }

    public function updateStatus($request, $id)
    {
        $orderData = [];
        $order = $this->findById($id);
        if (!$order)
            abort(404);

        if(in_array($request['order_status'],OrderStatus::whereIn('flag',OrderEnums::VENDOR_ORDERS_CAN_UPDATE_TO)->pluck('id')->toArray())){

            $orderData = ['order_status_id' => $request['order_status']];
        }

        if (isset($request['order_notes']) && !empty($request['order_notes']))
            $orderData['order_notes'] = $request['order_notes'];

        $order->update($orderData);

        $order->refresh();
        
        if(in_array(optional($order->orderStatus)->flag,OrderEnums::DRIVER_NEW_ORDER_STATUSE_FOR_NOTIFICATION)){

            if (config('setting.other.select_shipping_provider') == 'vendor_delivery') {
                if ($order->vendors[0]->use_app_drivers == 1) {
                    $this->fireNotificationToDrivers($order);
                }
            }
        }

        return true;
    }

    public function restoreSoftDelte($model)
    {
        $model->restore();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $model = $this->findById($id);

            if ($model->trashed()):
                $model->forceDelete();
            else:
                $model->delete();
            endif;

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteSelected($request)
    {
        DB::beginTransaction();

        try {

            foreach ($request['ids'] as $id) {
                $model = $this->delete($id);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function QueryTable($request)
    {
        $query = $this->order->whereHas('vendors', function ($q) {
            $q->whereHas('sellers', function ($q) {
                $q->where('seller_id', auth()->user()->id);
            });
        });

        if ($request->input('search.value')) {
            $query = $query->where(function ($query) use ($request) {
                $query->where('id', 'like', '%' . $request->input('search.value') . '%');
            });
        }

        $query = $this->filterDataTable($query, $request);
        return $query;
    }

    public function filterDataTable($query, $request)
    {
        if (isset($request['req']['from']) && $request['req']['from'] != '')
            $query->whereDate('created_at', '>=', $request['req']['from']);

        if (isset($request['req']['to']) && $request['req']['to'] != '')
            $query->whereDate('created_at', '<=', $request['req']['to']);

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'only')
            $query->onlyDeleted();

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'with')
            $query->withDeleted();

        if (isset($request['req']['status']) && $request['req']['status'] == '1')
            $query->active();

        if (isset($request['req']['status']) && $request['req']['status'] == '0')
            $query->unactive();

        return $query;
    }

}
