<?php

namespace Modules\Catalog\Traits;

use Cart;
use Darryldecode\Cart\CartCondition;
use Illuminate\Support\Str;
use Modules\Cart\Entities\DatabaseStorageModel;
use Modules\Catalog\Entities\AddOn;
use Modules\Catalog\Entities\AddOnOption;

trait ShoppingCartTrait
{
    protected $vendorCondition = 'vendor';
    protected $deliveryCondition = 'delivery_fees';
    protected $companyDeliveryCondition = 'company_delivery_fees';
    protected $vendorCommission = 'commission';
    protected $DiscountCoupon = 'coupon_discount';

    public function addOrUpdateCart($product, $request)
    {
        $checkQty = $this->checkQty($product);
        $vendorStatus = $this->vendorStatus($product);
        $checkMaxQty = $this->checkMaxQty($product, $request);

        if ($vendorStatus) {
            return $vendorStatus;
        }

        if ($checkQty) {
            return $checkQty;
        }

        if ($checkMaxQty) {
            return $checkMaxQty;
        }

        /*if (!$this->addCartConditions($product))
        return false;*/

        if (!$this->addOrUpdate($product, $request)) {
            return false;
        }

    }

    // CHECK IF QTY PRODUCT IN DB IS MORE THAN 0
    public function checkQty($product)
    {
        $productTitle = $product->product_type == 'product' ? $product->translate(locale())->title : $product->product->translate(locale())->title;
        if (!is_null($product->qty) && intval($product->qty) <= 0) {
            return $errors = $productTitle . ' - ' . __('catalog::frontend.products.alerts.product_qty_less_zero');
        }

        return false;
        /*return $errors = new MessageBag([
    'productCart' => __('catalog::frontend.products.alerts.product_qty_less_zero')
    ]);*/
    }

    // CHECK IF USER REQUESTED QTY MORE THAN MAXIMUAME OF PRODUCT QTY
    public function checkMaxQty($product, $request)
    {
        $productTitle = $product->product_type == 'product' ? $product->translate(locale())->title : $product->product->translate(locale())->title;
        if (!is_null($product->qty) && intval($request->qty) > intval($product->qty)) {
            return $errors = $productTitle . ' - ' . __('catalog::frontend.products.alerts.qty_more_than_max') . $product->qty;
        }

        return false;
    }

    public function vendorExist($product)
    {
        $vendor = Cart::getCondition('vendor');

        if ($vendor) {
            if (Cart::getCondition('vendor')->getType() != $product->vendor->id) {
                return $errors = __('catalog::frontend.products.alerts.vendor_not_match');
            }

        }

        return false;
    }

    /*
     * Check if vendor or pharmacy is busy
     */
    public function vendorStatus($product)
    {
        $vendor = $product->product_type == 'variation' ? $product->product->vendor : $product->vendor;
        if ($vendor) {
            if ($vendor->vendor_status_id == 4 || (config('setting.other.select_shipping_provider') == 'vendor_delivery' && !$this->isAvailableVendorWorkTime($vendor->id))) {
                return $errors = __('catalog::frontend.products.alerts.vendor_is_busy');
            }
        }
        return false;
    }

    /*
     * Check if vendor is busy
     */
    public function checkVendorStatus($product)
    {
        ### Check if vendor status is 'opened' OR 'closed'
        if ($product) {
            if ($product->product_type == 'product') {
                if ($product->vendor->vendor_status_id == 3 || $product->vendor->vendor_status_id == 4) {
                    return __('catalog::frontend.products.alerts.vendor_is_busy');
                }

            } else {
                if ($product->product->vendor->vendor_status_id == 3 || $product->product->vendor->vendor_status_id == 4) {
                    return __('catalog::frontend.products.alerts.vendor_is_busy');
                }

            }
        }

        return false;
    }

    public function productFound($product, $cartProduct)
    {
        if (!$product) {
            if ($cartProduct->attributes->product->product_type == 'product') {
                return $cartProduct->attributes->product->translate(locale())->title . ' - ' .
                __('catalog::frontend.products.alerts.qty_is_not_active');
            } else {
                return $cartProduct->attributes->product->product->translate(locale())->title . ' - ' .
                __('catalog::frontend.products.alerts.qty_is_not_active');
            }
        }

        return false;
    }

    public function checkActiveStatus($product, $request)
    {
        if ($product) {

            if ($product->product_type == 'product') {

                if ($product->deleted_at != null || $product->status == 0) {
                    return $product->translate(locale())->title . ' - ' .
                    __('catalog::frontend.products.alerts.qty_is_not_active');
                }

            } else {
                if ($product->product->deleted_at != null || $product->product->status == 0 || $product->status == 0) {
                    return $product->product->translate(locale())->title . ' - ' .
                    __('catalog::frontend.products.alerts.qty_is_not_active');
                }

            }

        }
        return false;
    }

    public function checkMaxQtyInCheckout($product, $itemQty, $cartQty)
    {
        if ($product) {

            if ($itemQty > $cartQty) {
                return __('catalog::frontend.products.alerts.qty_more_than_max') . ' ' . $cartQty . ' - ' .
                $product->product_type == 'product' ? $product->translate(locale())->title : $product->product->translate(locale())->title;
            }

        }
        return false;
    }

    public function checkAddOnsMultiOptionsQty($product, $request)
    {
        $errors = [];
        $addOnsOptionIDs = \GuzzleHttp\json_decode($request->addOnsOptionIDs);
        $addOnsIDs['ids'] = [];
        $addOnsIDs['addOnsNames'] = [];
        $addOnsIDs['options'] = [];
        foreach ($addOnsOptionIDs as $k => $item) {
            $id = $item->id;
            $addOnId = AddOnOption::find($id)->add_on_id;
            $addOns = AddOn::find($addOnId);
            if ($addOns->type == 'multi' && $addOns->options_count != null) {
                if (!in_array($addOnId, $addOnsIDs['ids'])) {
                    $addOnsIDs['ids'][] = $addOnId;
                    $addOnsIDs['addOnsNames'][] = $addOns->translate(locale())->name;
                }
                if (!in_array($addOns->options_count, $addOnsIDs['options'])) {
                    $addOnsIDs['options'][] = $addOns->options_count;
                }
                $addOnsIDs['options_ids_count'][$addOnId][] = $id;
            }
        }

        if (!empty($addOnsIDs['ids'])) {
            foreach ($addOnsIDs['ids'] as $k => $id) {
                if (count($addOnsIDs['options_ids_count'][$id]) > $addOnsIDs['options'][$k]) {
                    $error = __('catalog::frontend.products.alerts.add_ons_options_qty_more_than_max') . ' ' . $addOnsIDs['options'][$k] . ' - ' . __('catalog::frontend.products.alerts.add_ons_option_name') . $addOnsIDs['addOnsNames'][$k];
                    array_push($errors, $error);
                }
            }
        }

        if (count($errors) > 0) {
            return array_values($errors);
//            return new MessageBag(array_values($errors));
        }

        return false;
    }

    public function findItemById($id)
    {
        $item = getCartContent()->get($id);
        return $item;
    }

    public function addOrUpdate($product, $request)
    {
        $item = $this->findItemById($product->product_type == 'product' ? $product->id : 'var-' . $product->id);

        if (!is_null($item)) {

            if (!$this->updateCart($product, $request)) {
                return false;
            }

        } else {

            if (!$this->add($product, $request)) {
                return false;
            }

        }
    }

    public function add($product, $request)
    {
        $attributes = [
            'type' => 'simple',
            'image' => $product->image,
            'sku' => $product->sku,
            'old_price' => $product->offer ? $product->price : null,
            'product_type' => $product->product_type,
            'product' => $product,
            'notes' => $request->notes ?? null,
            // 'translation' => $product->translations,
            'vendor_id' => $product->vendor_id ?? null,
        ];

        if ($product->product_type == 'variation') {
            $productName = generateVariantProductData($product->product, $product->product->id, json_decode($request->selectedOptionsValue))['name'];
            $attributes['slug'] = Str::slug($productName);
            $attributes['selectedOptions'] = json_decode($request->selectedOptions);
            $attributes['selectedOptionsValue'] = json_decode($request->selectedOptionsValue);
        } else {
            $productName = $product->translate(locale())->title;
            $attributes['slug'] = $product->translate(locale())->slug;
        }

        $cartArr = [
            'id' => $product->product_type == 'product' ? $product->id : 'var-' . $product->id,
            'name' => $productName,
            'quantity' => $request->qty ? $request->qty : +1,
            'attributes' => $attributes,
        ];
//        $cartArr['price'] = $product->offer ? $product->offer->offer_price : $product->price;

        if ($product->offer) {
            if (!is_null($product->offer->offer_price)) {
                $cartArr['price'] = $product->offer->offer_price;
            } elseif (!is_null($product->offer->percentage)) {
                $percentageResult = (floatval($product->price) * floatVal($product->offer->percentage)) / 100;
                $cartArr['price'] = floatval($product->price) - $percentageResult;
            } else {
                $cartArr['price'] = floatval($product->price);
            }
        } else {
            $cartArr['price'] = floatval($product->price);
        }

        if (auth()->check()) {
            $addToCart = Cart::session(auth()->user()->id)->add($cartArr);
        } else {
            if (is_null(get_cookie_value(config('core.config.constants.CART_KEY')))) {
                $cartKey = Str::random(30);
                set_cookie_value(config('core.config.constants.CART_KEY'), $cartKey);
            } else {
                $cartKey = get_cookie_value(config('core.config.constants.CART_KEY'));
            }

            $addToCart = Cart::session($cartKey)->add($cartArr);
        }

        return $addToCart;
    }

    public function updateCart($product, $request)
    {
        if (isset($request->request_type) && $request->request_type == 'product') {

            ### Start Update Cart Attributes ###

            $attributes = [
                'type' => 'simple',
                'image' => $product->image,
                'sku' => $product->sku,
                'old_price' => $product->offer ? $product->price : null,
                'product_type' => $product->product_type,
                'product' => $product,
                'notes' => $request->notes ?? null,
                // 'translation' => $product->translations,
                'vendor_id' => $product->vendor_id ?? null,
            ];

            if ($product->product_type == 'variation') {
                $productName = generateVariantProductData($product->product, $product->product->id, json_decode($request->selectedOptionsValue))['name'];
                $attributes['slug'] = Str::slug($productName);
                $attributes['selectedOptions'] = json_decode($request->selectedOptions);
                $attributes['selectedOptionsValue'] = json_decode($request->selectedOptionsValue);
            } else {
                $productName = $product->translate(locale())->title;
                $attributes['slug'] = $product->translate(locale())->slug;
            }

            ### End Update Cart Attributes ###

            $cartArr = [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->qty ? $request->qty : +1,
                ],
                'attributes' => $attributes,
            ];

        } else {
            $cartArr = [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->qty ? $request->qty : +1,
                ],
            ];
        }

        if (auth()->check()) {
            $updateItem = Cart::session(auth()->user()->id)->update($product->product_type == 'product' ? $product->id : 'var-' . $product->id, $cartArr);
        } else {
            if (is_null(get_cookie_value(config('core.config.constants.CART_KEY')))) {
                $cartKey = Str::random(30);
                set_cookie_value(config('core.config.constants.CART_KEY'), $cartKey);
            } else {
                $cartKey = get_cookie_value(config('core.config.constants.CART_KEY'));
            }
            $updateItem = Cart::session($cartKey)->update($product->product_type == 'product' ? $product->id : 'var-' . $product->id, $cartArr);
        }

        if (!$updateItem) {
            return false;
        }

        return $updateItem;
    }

    public function addCartConditions($product)
    {
        $orderVendor = new CartCondition([
            'name' => $this->vendorCondition,
            'type' => $product->vendor->id,
            'value' => (string) $product->vendor->order_limit,
            'attributes' => [
                'fixed_delivery' => $product->vendor->fixed_delivery,
            ],
        ]);

        $commissionFromVendor = new CartCondition([
            'name' => $this->vendorCommission,
            'type' => $this->vendorCommission,
            'value' => (string) $product->vendor->commission,
            'attributes' => [
                'commission' => $product->vendor->commission,
                'fixed_commission' => $product->vendor->fixed_commission,
            ],
        ]);

        return Cart::condition([$orderVendor, $commissionFromVendor]);
    }

    public function DeliveryChargeCondition($charge, $address)
    {
        $deliveryFees = new CartCondition([
            'name' => $this->deliveryCondition,
            'type' => $this->deliveryCondition,
            'target' => 'total',
            'value' => (string) $charge ? +$charge : +Cart::getCondition('vendor')->getAttributes()['fixed_delivery'],
            'attributes' => [
                'address' => $address,
            ],
        ]);

        return Cart::condition([$deliveryFees]);
    }

    public function discountCouponCondition($coupon, $discount_value, $userToken = null)
    {
        $coupon_discount = new CartCondition([
            'name' => $this->DiscountCoupon,
            'type' => $this->DiscountCoupon,
            'target' => 'subtotal',
            // 'target' => 'total',
            'value' => (string) $discount_value * -1,
            'attributes' => [
                'coupon' => $coupon,
            ],
        ]);

        return Cart::session($userToken)->condition([$coupon_discount]);
    }

    public function saveEmptyDiscountCouponCondition($coupon, $userToken = null)
    {
        $coupon_discount = new CartCondition([
            'name' => $this->DiscountCoupon,
            'type' => $this->DiscountCoupon,
            'target' => 'subtotal',
            // 'target' => 'total',
            'value' => (string) 0,
            'attributes' => [
                'coupon' => $coupon,
            ],
        ]);

        return Cart::session($userToken)->condition([$coupon_discount]);
    }

    public function companyDeliveryChargeCondition($request, $price, $userToken = null)
    {
        $deliveryFees = new CartCondition([
            'name' => $this->companyDeliveryCondition,
            'type' => $this->companyDeliveryCondition,
            'target' => 'total',
            'value' => (string) $price,
            'attributes' => [
                'state_id' => $request->state_id,
                'address_id' => $request->address_id ?? null,
                'vendor_id' => $request->vendor_id ?? null,
            ],
        ]);

        return Cart::session($userToken)->condition([$deliveryFees]);
    }

    public function deleteProductFromCart($productId)
    {
        $userToken = $this->getCartUserToken();
        $cartItem = Cart::session($userToken)->remove($productId);

        if (count(getCartContent()) <= 0) {
            return $this->clearCart();
        }

        return $cartItem;
    }

    public function clearCart()
    {
        $userToken = $this->getCartUserToken();
        Cart::session($userToken)->clear();
        Cart::session($userToken)->clearCartConditions();

        return true;
    }

    public function searchForId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['id'] === $id) {
                return $key;
            }
        }
        return null;
    }

    public function getCartUserToken()
    {
        if (auth()->check()) {
            $userToken = auth()->user()->id;
        } else {
            $userToken = get_cookie_value(config('core.config.constants.CART_KEY'));
        }

        return $userToken;
    }

    public function updateCartKey($userToken, $newUserId)
    {
        DatabaseStorageModel::where('id', $userToken . '_cart_conditions')->update(['id' => $newUserId . '_cart_conditions']);
        DatabaseStorageModel::where('id', $userToken . '_cart_items')->update(['id' => $newUserId . '_cart_items']);
        return true;
    }

    public function removeCartConditionByType($type = '', $userToken = null)
    {
        $userCartToken = $userToken ?? $this->getCartUserToken();
        Cart::session($userCartToken)->removeConditionsByType($type);
        return true;
    }

}
