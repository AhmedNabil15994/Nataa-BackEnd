<?php

namespace Modules\Order\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Modules\Core\Traits\DataTable;
use Modules\Notification\Traits\SendNotificationTrait as SendNotification;
use Modules\Order\Http\Requests\Dashboard\OrderDriverRequest;
use Modules\Order\Http\Requests\Dashboard\OrderStatusRequest;
use Modules\Order\Mail\Dashboard\UpdateOrderStatusMail;
use Modules\Order\Transformers\Dashboard\OrderResource;
use Modules\Order\Repositories\Dashboard\OrderRepository as Order;
use Modules\Order\Repositories\Dashboard\OrderStatusRepository as OrderStatus;
use Modules\Catalog\Repositories\Dashboard\ProductRepository as Product;
use Modules\Notification\Repositories\Dashboard\NotificationRepository as Notification;

class OrderController extends Controller
{
    use SendNotification;

    protected $order;
    protected $status;
    protected $notification;
    protected $product;

    function __construct(Order $order, OrderStatus $status, Notification $notification, Product $product)
    {
        $this->status = $status;
        $this->order = $order;
        $this->notification = $notification;
        $this->product = $product;
    }

    public function index()
    {
        return view('order::dashboard.orders.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->order->QueryTable($request), 'orders');

        $datatable['data'] = OrderResource::collection($datatable['data']);

        return Response()->json($datatable);
    }

    public function getAllOrders()
    {
        return view('order::dashboard.all_orders.index');
    }

    public function allOrdersDatatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->order->QueryTableAllOrders($request), 'orders');

        $datatable['data'] = OrderResource::collection($datatable['data']);

        return Response()->json($datatable);
    }

    public function create()
    {
        return view('order::dashboard.orders.create');
    }

    public function store(Request $request)
    {
        dd($request->all());
    }

    public function show($id)
    {
        $order = $this->order->findById($id);
        if (!$order)
            abort(404);

        $this->order->updateUnread($id);
        $statuses = $this->status->getAll();
        $order->allProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        $ordersRouteName = \request()->segment(3) == 'all-orders' ? 'all_orders' : 'orders';

        return view('order::dashboard.orders.show', compact('order', 'statuses', 'ordersRouteName'));
    }

    public function update(OrderDriverRequest $request, $id)
    {
        try {
            $order = $this->order->findById($id);
            if (!$order)
                return abort(404);

            $update = $this->order->updateOrderStatusAndDriver($request, $id);
            if ($update) {
                if ($request['user_id']) {
                    $updatedOrder = $this->order->findById($id);
                    ### Start Send E-mail & Push Notification To Mobile App Users & Drivers ###
                    $this->sendNotification($updatedOrder);
                    ### End Send E-mail & Push Notification To Mobile App Users & Drivers ###
                }

                return Response()->json([true, __('apps::dashboard.general.message_update_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function updateBulkOrderStatus(Request $request)
    {
        try {
            $updatedOrder = false;
            foreach ($request['ids'] as $id) {
                $updatedOrder = $this->order->updateOrderStatusAndDriver($request, $id);
                if ($updatedOrder) {

                    ### Start Send E-mail & Push Notification To Mobile App Users ###
                    $order = $this->order->findById($id);
                    if (!$order)
                        $this->sendNotification($order);
                    ### End Send E-mail & Push Notification To Mobile App Users ###

                }
            }

            if ($updatedOrder)
                return Response()->json([true, __('apps::dashboard.general.message_update_success')]);

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function destroy($id)
    {
        try {
            $delete = $this->order->delete($id);

            if ($delete) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function deletes(Request $request)
    {
        try {
            $deleteSelected = $this->order->deleteSelected($request);

            if ($deleteSelected) {
                return Response()->json([true, __('apps::dashboard.general.message_delete_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function printSelectedItems(Request $request)
    {
        try {
            if (isset($request['ids']) && !empty($request['ids'])) {
                $ids = explode(',', $request['ids']);
                $orders = $this->order->getSelectedOrdersById($ids);
                return view('order::dashboard.orders.print', compact('orders'));
            }
            // return Response()->json([false, __('apps::dashboard.general.select_at_least_one_item')]);
        } catch (\PDOException $e) {
            return redirect()->back()->withErrors($e->errorInfo[2]);
        }
    }

    public function sendNotification($order)
    {
        if (!is_null($order->user_id)) { // admin send notification to user
            $this->sendMobileNotify($order, $order->user_id);
            $userEmail = $order->user->email ?? null;
            if (!is_null($userEmail))
                $this->sendEmailNotify($order, $userEmail);
        }

        if (!is_null(optional($order->driver)->user)) { // admin send notification to driver
            $this->sendMobileNotify($order, $order->driver->user->id, 'driver_app');
            $driverEmail = $order->driver->user->email ?? null;
            if (!is_null($driverEmail))
                $this->sendEmailNotify($order, $driverEmail);
        }
        return true;
    }

    public function sendMobileNotify($order, $userId, $googleAPIKeyType = 'main_app')
    {
        $tokens = $this->notification->getAllUserTokens($userId);
        $locale = app()->getLocale();
        if (count($tokens) > 0) {
            $data = [
                'title' => __('order::dashboard.orders.notification.title'),
                'body' => __('order::dashboard.orders.notification.body') . ' - ' . $order->orderStatus->translate($locale)->title,
                'type' => 'order',
                'id' => $order->id,
            ];
            $this->send($data, $tokens, $googleAPIKeyType);
        }
        return true;
    }

    public function sendEmailNotify($order, $email)
    {
        \Mail::to($email)->send(new UpdateOrderStatusMail($order));
        return true;
    }

    /* public function sendNotificationToUser($id)
    {
        $order = $this->order->findById($id);
        if (!$order)
            abort(404);

        $tokens = $this->notification->getAllUserTokens($order->user_id);
        $locale = app()->getLocale();

        if (count($tokens) > 0) {
            $data = [
                'title' => __('order::dashboard.orders.notification.title'),
                'body' => __('order::dashboard.orders.notification.body') . ' - ' . $order->orderStatus->translate($locale)->title,
                'type' => 'order',
                'id' => $order->id,
            ];

            $this->send($data, $tokens);
        }

        if ($order->user && !is_null($order->user->email)) {
            Mail::to($order->user->email)
                ->send(new UpdateOrderStatusMail($order));
        }

        return true;
    } */
}
