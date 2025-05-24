<?php

namespace Modules\Order\Http\Controllers\FrontEnd;

use Cart;
use Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\MessageBag;
use Modules\Order\Events\ActivityLog;
use Modules\Order\Events\VendorOrder;
use Modules\Catalog\Traits\ShoppingCartTrait;

//use Modules\Transaction\Services\PaymentService;
use Modules\Transaction\Services\UPaymentService;

//use Modules\Transaction\Services\UPaymentTestService;

use Modules\Order\Http\Requests\FrontEnd\CreateOrderRequest;
use Modules\Order\Repositories\FrontEnd\OrderRepository as Order;
use Modules\Order\Notifications\FrontEnd\AdminNewOrderNotification;
use Modules\Order\Notifications\FrontEnd\UserNewOrderNotification;
use Modules\Order\Notifications\FrontEnd\VendorNewOrderNotification;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;
use Modules\Vendor\Repositories\FrontEnd\VendorRepository as Vendor;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class OrderController extends Controller
{
    use ShoppingCartTrait;

    protected $payment;
    protected $order;
    protected $product;
    protected $vendor;

    function __construct(Order $order, UPaymentService $payment, Product $product, Vendor $vendor)
    {
        $this->payment = $payment;
        $this->order = $order;
        $this->product = $product;
        $this->vendor = $vendor;
    }

    public function index()
    {
        $ordersIDs = isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')]) ? (array)\GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]) : [];

        if (auth()->user()) {
            $orders = $this->order->getAllByUser($ordersIDs);
            return view('order::frontend.orders.index', compact('orders'));
        } else {
            $orders = count($ordersIDs) > 0 ? $this->order->getAllGuestOrders($ordersIDs) : [];
            return view('order::frontend.orders.index', compact('orders'));
        }
    }

    public function invoice($id)
    {
        if (auth()->user())
            $order = $this->order->findByIdWithUserId($id);
        else
            $order = $this->order->findGuestOrderById($id);

        if (!$order)
            return abort(404);

        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.details', compact('order'));
    }

    public function reOrder($id)
    {
        $order = $this->order->findByIdWithUserId($id);
        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.re-order', compact('order'));
    }

    public function guestInvoice()
    {
        $savedID = [];
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $savedID = (array)\GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $id = count($savedID) > 0 ? $savedID[count($savedID) - 1] : 0;
        $order = $this->order->findByIdWithGuestId($id);
        if (!$order)
            abort(404);

        $order->orderProducts = $order->orderProducts->mergeRecursive($order->orderVariations);
        return view('order::frontend.orders.invoice', compact('order'))->with([
            'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success')
        ]);
    }

    public function createOrder(CreateOrderRequest $request)
    {
        //dd($request->all());
        $errors1 = [];
        $errors2 = [];
        $errors3 = [];
        $errors4 = [];

        foreach (getCartContent() as $key => $item) {

            if ($item->attributes->product->product_type == 'product') {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProduct($cartProduct->id);
                if (!$product)
                    return redirect()->back()->with(['alert' => 'danger', 'status' => __('cart::api.cart.product.not_found') . $cartProduct->id]);

                $product->product_type = 'product';
            } else {
                $cartProduct = $item->attributes->product;
                $product = $this->product->findOneProductVariant($cartProduct->id);
                if (!$product)
                    return redirect()->back()->with(['alert' => 'danger', 'status' => __('cart::api.cart.product.not_found') . $cartProduct->id]);

                $product->product_type = 'variation';
            }

            $productFound = $this->productFound($product, $item);
            if ($productFound) {
                $errors1[] = $productFound;
            }

            $activeStatus = $this->checkActiveStatus($product, $request);
            if ($activeStatus) {
                $errors2[] = $activeStatus;
            }

            if (!is_null($product->qty)) {
                $maxQtyInCheckout = $this->checkMaxQtyInCheckout($product, $item->quantity, $cartProduct->qty);
                if ($maxQtyInCheckout) {
                    $errors3[] = $maxQtyInCheckout;
                }
            }

            $vendorStatusError = $this->checkVendorStatus($product);
            if ($vendorStatusError) {
                $errors4[] = $vendorStatusError;
            }
        }

        if ($errors1 || $errors2 || $errors3 || $errors4) {
            $errors = new MessageBag([
                'productCart' => $errors1,
                'productCart2' => $errors2,
                'productCart3' => $errors3,
                'productCart4' => $errors4,
            ]);
            return redirect()->back()->with(["errors" => $errors]);
        }

        /*if ($request['payment'] != 'cash') {
            return redirect()->back()->with([
                'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.payment_not_supported_now')
            ]);
        }*/

        $order = $this->order->create($request);
        if (!$order)
            return $this->redirectToFailedPayment();

        if ($request['payment'] != 'cash') {
            return redirect()->away($this->payment->send($order, $request['payment'], auth()->check() ? auth()->id() : null, 'frontend-order'));
        }
        return $this->redirectToPaymentOrOrderPage($request, $order);
    }

    public function webhooks(Request $request)
    {
        $this->order->updateOrder($request);
    }

    public function success(Request $request)
    {
        $order = $this->order->updateOrder($request);
        return $order ? $this->redirectToPaymentOrOrderPage($request) : $this->redirectToFailedPayment();
    }

    public function failed(Request $request)
    {
        $this->order->updateOrder($request);
        return $this->redirectToFailedPayment();
    }

    public function redirectToPaymentOrOrderPage($data, $order = null)
    {
        $order = ($order == null) ? $this->order->findById($data['OrderID']) : $this->order->findById($order->id);
        $this->sendNotifications($order);
        $this->clearCart();
        return $this->redirectToInvoiceOrder($order);
    }

    public function redirectToInvoiceOrder($order)
    {
        ################# Start Store Guest Orders In Browser Cookie ######################
        if (isset($_COOKIE[config('core.config.constants.ORDERS_IDS')]) && !empty($_COOKIE[config('core.config.constants.ORDERS_IDS')])) {
            $cookieArray = (array)\GuzzleHttp\json_decode($_COOKIE[config('core.config.constants.ORDERS_IDS')]);
        }
        $cookieArray[] = $order['id'];
        setcookie(config('core.config.constants.ORDERS_IDS'), \GuzzleHttp\json_encode($cookieArray), time() + (5 * 365 * 24 * 60 * 60), '/'); // expires at 5 year
        ################# End Store Guest Orders In Browser Cookie ######################

        if (auth()->user())
            return redirect()->route('frontend.orders.invoice', $order->id)->with([
                'alert' => 'success', 'status' => __('order::frontend.orders.index.alerts.order_success')
            ]);

        return redirect()->route('frontend.orders.guest.invoice');
    }

    public function redirectToFailedPayment()
    {
        return redirect()->route('frontend.checkout.index')->with([
            'alert' => 'danger', 'status' => __('order::frontend.orders.index.alerts.order_failed')
        ]);
    }

    public function sendNotifications($order)
    {
        $this->fireLog($order);

        if ($order->orderAddress) {
            Notification::route('mail', $order->orderAddress->email)->notify(
                (new UserNewOrderNotification($order))->locale(locale())
            );
        }

        Notification::route('mail', config('setting.contact_us.email'))->notify(
            (new AdminNewOrderNotification($order))->locale(locale())
        );

        if ($order->vendors) {
            Notification::route('mail', $this->pluckVendorEmails($order))->notify(
                (new VendorNewOrderNotification($order))->locale(locale())
            );
        }
    }

    public function pluckVendorEmails($order)
    {
        foreach ($order->vendors as $k => $value) {
            $vendor = $this->vendor->findById($value->id);
            if ($vendor) {
                $emails = $vendor->sellers->pluck('email');
                return $emails;
            }
        }
        return [];
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

        if ($order->vendors) {
            foreach ($order->vendors as $k => $value) {
                $vendor = $this->vendor->findById($value->id);
                if ($vendor) {
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
    }
}
