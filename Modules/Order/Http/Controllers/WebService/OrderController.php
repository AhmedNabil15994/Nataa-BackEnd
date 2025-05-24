<?php

namespace Modules\Order\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Cart\Traits\CartTrait;
use Modules\Catalog\Repositories\WebService\CatalogRepository as Catalog;
use Modules\Company\Repositories\WebService\CompanyRepository as Company;
use Modules\Notification\Repositories\Dashboard\NotificationRepository as Notification;
use Modules\Notification\Traits\SendNotificationTrait;
use Modules\Order\Events\ActivityLog;
use Modules\Order\Events\DriverOrder;
use Modules\Order\Events\VendorOrder;
use Modules\Order\Http\Requests\WebService\CreateOrderRequest;
use Modules\Order\Mail\Dashboard\UpdateOrderStatusMail;
use Modules\Order\Repositories\WebService\OrderRepository as Order;
use Modules\Order\Transformers\WebService\OrderProductResource;
use Modules\Order\Transformers\WebService\OrderResource;
use Modules\Transaction\Services\MyFatoorahPaymentService;
use Modules\User\Entities\User;
use Modules\User\Repositories\WebService\AddressRepository;
use Modules\Vendor\Repositories\WebService\VendorRepository as Vendor;

class OrderController extends WebServiceController
{
    use CartTrait, SendNotificationTrait;

    protected $payment;
    protected $myFatoorahPayment;
    protected $order;
    protected $company;
    protected $catalog;
    protected $address;
    protected $vendor;
    protected $notification;

    public function __construct(
        Order $order,
        MyFatoorahPaymentService $myFatoorahPayment,
        Company $company,
        Catalog $catalog,
        AddressRepository $address,
        Vendor $vendor,
        Notification $notification
    ) {
        $this->myFatoorahPayment = $myFatoorahPayment;
        $this->order = $order;
        $this->company = $company;
        $this->catalog = $catalog;
        $this->address = $address;
        $this->vendor = $vendor;
        $this->notification = $notification;
    }

    public function createOrder(CreateOrderRequest $request)
    {
        if (auth('api')->check()) {
            $userToken = auth('api')->user()->id;
        } else {
            $userToken = $request->user_id;
        }

        // Check if address is not found
        if ($request->address_type == 'selected_address') {
            // get address by id
            $companyDeliveryFees = getCartConditionByName($userToken, 'company_delivery_fees');
            $addressId = isset($companyDeliveryFees->getAttributes()['address_id'])
            ? $companyDeliveryFees->getAttributes()['address_id']
            : null;
            $address = $this->address->findByIdWithoutAuth($addressId);
            if (!$address) {
                return $this->error(__('user::webservice.address.errors.address_not_found'), [], 422);
            }

        }

        foreach (getCartContent($userToken) as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProduct($cartProduct->id);
                if (!$product) {
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);
                }

                ### Start - Check Single Addons Selections - Validation ###
                $selectedAddons = $item->attributes->has('addonsOptions') ? $item->attributes['addonsOptions']['data'] : [];
                $addOnsCheck = $this->checkProductAddonsValidation($selectedAddons, $product);
                if (gettype($addOnsCheck) == 'string') {
                    return $this->error($addOnsCheck . ' : ' . $cartProduct->translate(locale())->title, [], 422);
                }

                ### End - Check Single Addons Selections - Validation ###

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->catalog->findOneProductVariant($cartProduct->id);
                if (!$product) {
                    return $this->error(__('cart::api.cart.product.not_found') . $cartProduct->id, [], 422);
                }

                $product->product_type = 'variation';
            }

            $checkPrdFound = $this->productFound($product, $item);
            if ($checkPrdFound) {
                return $this->error($checkPrdFound, [], 422);
            }

            $checkPrdStatus = $this->checkProductActiveStatus($product, $request);
            if ($checkPrdStatus) {
                return $this->error($checkPrdStatus, [], 422);
            }

            if (!is_null($product->qty)) {
                $checkPrdMaxQty = $this->checkMaxQty($product, $item->quantity);
                if ($checkPrdMaxQty) {
                    return $this->error($checkPrdMaxQty, [], 422);
                }

            }

            $checkVendorStatus = $this->vendorStatus($product);
            if ($checkVendorStatus) {
                return $this->error($checkVendorStatus, [], 422);
            }

        }

        $order = $this->order->create($request, $userToken);
        if (!$order) {
            return $this->error('error', [], 422);
        }

        /* ### Start: UPayment Service ###
        if ($request['payment'] != 'cash') {
        $payment = $this->payment->send($order, $request['payment'], $userToken, 'api-order');
        return $this->response([
        'paymentUrl' => $payment
        ]);
        }
        ### End: UPayment Service ### */

        if ($request['payment'] == 'myfatourah' && getCartTotal($userToken) > 0) {
            $payment = $this->myFatoorahPayment->send($order, "knet", "api-order");
            if ($payment) {
                return $this->response(['paymentUrl' => $payment]);
            } else {
                return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
            }
        }

        $this->fireLog($order);
        $this->clearCart($userToken);

        return $this->response(new OrderResource($order));
    }

    /* public function webhooks(Request $request)
    {
    $this->order->updateOrder($request);
    }

    public function success(Request $request)
    {
    $order = $this->order->updateOrder($request);
    if ($order) {
    $orderDetails = $this->order->findById($request['OrderID']);
    $userToken = auth('api')->check() ? auth('api')->id() : ($request->userToken ?? null);
    if ($orderDetails) {
    $this->fireLog($orderDetails);
    // $this->clearCart($userToken);
    return $this->response(new OrderResource($orderDetails));
    } else
    return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
    }
    }

    public function failed(Request $request)
    {
    $this->order->updateOrder($request);
    return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
    } */

    public function myfatoorahSuccess(Request $request)
    {
        logger('MyFatoorah::success');
        logger($request->all());
        $response = $this->getMyFatoorahTransactionDetails($request);
        $orderCheck = $this->order->updateMyFatoorahOrder($request, $response['status'], $response['transactionsData'], $response['orderId']);
        $orderDetails = $this->order->findById($response['orderId']);
        if ($orderCheck && $orderDetails) {
            $this->fireLog($orderDetails);
            $userToken = $orderDetails->user_id ?? ($orderDetails->user_token ?? null);
            if ($userToken) {
                $this->clearCart($userToken);
            }
            return $this->response(new OrderResource($orderDetails));
        } else {
            return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
        }

    }

    public function myfatoorahFailed(Request $request)
    {
        logger('MyFatoorah::failed');
        logger($request->all());
        $response = $this->getMyFatoorahTransactionDetails($request);
        $orderCheck = $this->order->updateMyFatoorahOrder($request, $response['status'], $response['transactionsData'], $response['orderId']);
        return $this->error(__('order::frontend.orders.index.alerts.order_failed'), [], 422);
    }

    private function getMyFatoorahTransactionDetails($request)
    {
        // Get transaction details
        $response = $this->myFatoorahPayment->getTransactionDetails($request->paymentId);
        logger('Get transaction details');
        logger($response);
        $status = strtoupper($response['InvoiceStatus']);
        $orderId = $response['UserDefinedField'];
        $transactionsData = $response['InvoiceTransactions'][0] ?? [];
        return [
            'status' => $status,
            'orderId' => $orderId,
            'transactionsData' => $transactionsData,
        ];
    }

    public function userOrdersList(Request $request)
    {
        $orders = $this->order->getAllByUser();
        return $this->response(OrderResource::collection($orders));
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = $this->order->findById($id);

        if (!$order) {
            return $this->error(__('order::api.orders.validations.order_not_found'), [], 422);
        }

        $allOrderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return $this->response(OrderProductResource::collection($allOrderProducts));
    }

    public function fireLog($order)
    {
        $dashboardUrl = LaravelLocalization::localizeUrl(url(route('dashboard.orders.show', $order->id)));
        $data = [
            'id' => $order->id,
            'type' => 'orders',
            'url' => $dashboardUrl,
            'description_en' => 'New Order',
            'description_ar' => 'طلب جديد ',
        ];
        $data2 = [];

        $drivers = [];
        $driversData = [];

        if ($order->vendors) {
            foreach ($order->vendors as $k => $value) {
                $vendor = $this->vendor->findById($value->id);
                if ($vendor) {
                    foreach($vendor->companies as $company){
                        $drivers = array_merge($drivers,$company->drivers()->get()->toArray());
                    }
                    $vendorUrl = LaravelLocalization::localizeUrl(url(route('vendor.orders.show', $order->id)));
                    $data2 = [
                        'ids' => $vendor->sellers->pluck('id'),
                        'type' => 'vendor',
                        'url' => $vendorUrl,
                        'description_en' => 'New Order',
                        'description_ar' => 'طلب جديد',
                    ];
                }
            }
        }


        event(new ActivityLog($data));
        if (count($data2) > 0) {
            event(new VendorOrder($data2));
        }

        foreach($drivers as $driver){
            $driverUrl = LaravelLocalization::localizeUrl(url(route('driver.orders.show', $order->id)));
            $acceptUrl = LaravelLocalization::localizeUrl(url(route('driver.orders.accept', $order->id)));
            $driversData[] = [
                'ids' => $driver['id'],
                'type' => 'driver',
                'url' => $driverUrl,
                'accept'  => $acceptUrl,
                'description_en' => 'New Order',
                'description_ar' => 'طلب جديد',
            ];
        }
        if (count($driversData) > 0) {
            event(new DriverOrder($driversData));
        }
    }

    private function fireNotificationToDrivers($order)
    {
        // fire notification to drivers that have the same order state
        $orderStateId = optional($order->orderAddress)->state_id ?? null;
        $matchedDrivers = User::whereHas('driverStates', function ($query) use ($orderStateId) {
            $query->where('driver_state.status', 1);
            $query->where('driver_state.state_id', $orderStateId);
        })->get();

        if ($matchedDrivers->count() > 0) {
            foreach ($matchedDrivers as $key => $driver) {
                $this->sendMobileNotify($order, $driver->id, 'driver_app');
                $driverEmail = $driver->email ?? null;
                if (!is_null($driverEmail)) {
                    $this->sendEmailNotify($order, $driverEmail);
                }
            }
        }
    }

    public function sendMobileNotify($order, $userId, $googleAPIKeyType = 'main_app')
    {
        $tokens = $this->notification->getAllUserTokens($userId);
        $locale = app()->getLocale();
        if (count($tokens) > 0) {
            $data = [
                'title' => __('order::dashboard.orders.notification.new_order_title'),
                'body' => __('order::dashboard.orders.notification.new_order_body'),
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
}
