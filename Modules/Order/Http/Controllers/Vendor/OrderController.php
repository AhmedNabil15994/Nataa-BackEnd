<?php

namespace Modules\Order\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\DataTable;
use Modules\Notification\Repositories\Dashboard\NotificationRepository as Notification;
use Modules\Notification\Traits\SendNotificationTrait as SendNotification;
use Modules\Order\Mail\Dashboard\UpdateOrderStatusMail;
use Modules\Order\Transformers\Vendor\OrderResource;
use Modules\Order\Repositories\Vendor\OrderRepository as Order;
use Modules\Order\Repositories\Vendor\OrderStatusRepository as OrderStatus;

class OrderController extends Controller
{
    use SendNotification;

    protected $notification;
    protected $status;
    protected $order;

    function __construct(Order $order, OrderStatus $status, Notification $notification)
    {
        $this->status = $status;
        $this->order = $order;
        $this->notification = $notification;
    }

    public function index()
    {
        return view('order::vendor.orders.index');
    }

    public function datatable(Request $request)
    {
        $datatable = DataTable::drawTable($request, $this->order->QueryTable($request));

        $datatable['data'] = OrderResource::collection($datatable['data']);

        return Response()->json($datatable);
    }

    public function show($id)
    {
        $order = $this->order->getVendorProductsByOrderId($id);
        if (!$order)
            abort(404);

        $this->order->updateUnread($id);
        $statuses = $this->status->getAll();
        $order->allProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::vendor.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        try {
            $update = $this->order->updateStatus($request, $id);

            if ($update) {

                ### Start Send E-mail & Push Notification To Mobile App Users ###
                $this->sendNotificationToUser($id);
                ### End Send E-mail & Push Notification To Mobile App Users ###

                return Response()->json([true, __('apps::dashboard.general.message_update_success')]);
            }

            return Response()->json([false, __('apps::dashboard.general.message_error')]);
        } catch (\PDOException $e) {
            return Response()->json([false, $e->errorInfo[2]]);
        }
    }

    public function sendNotificationToUser($id)
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
            // Send E-mail to order user
            \Mail::to($order->user->email)
                ->send(new UpdateOrderStatusMail($order));
        }

        return true;
    }
}
