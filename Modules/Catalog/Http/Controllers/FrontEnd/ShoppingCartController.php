<?php

namespace Modules\Catalog\Http\Controllers\FrontEnd;

use Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Catalog\Http\Requests\FrontEnd\CartRequest;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;

class ShoppingCartController extends Controller
{
    use ShoppingCartTrait;

    protected $product;

    function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index()
    {
        $items = getCartContent();
        return view('catalog::frontend.shopping-cart.index', compact('items'));
    }

    public function totalCart()
    {
        return getCartSubTotal();
    }

    public function headerCart()
    {
        return view('apps::frontend.layouts._cart');
    }

    public function createOrUpdate(CartRequest $request, $productSlug, $variantPrdId = null)
    {
        $data = [];
        $vendorId = null;
        if (isset($request->product_type) && $request->product_type == 'variation') {
            $product = $this->product->findVariantProductById($variantPrdId);
            if (!$product)
                return response()->json(["errors" => __('cart::api.cart.product.not_found') . $variantPrdId . ' / ' . $productSlug], 422);

            $product->product_type = 'variation';
            $routeParams = [$product->product->translate(locale())->slug, generateVariantProductData($product->product, $variantPrdId, json_decode($request->selectedOptionsValue))['slug']];
            $data['productDetailsRoute'] = route('frontend.products.index', $routeParams);
            $data['productTitle'] = generateVariantProductData($product->product, $variantPrdId, json_decode($request->selectedOptionsValue))['name'];
            $productCartId = 'var-' . $product->id;
            $vendorId = $product->product->vendor->id ?? null;
        } else {
            $product = $this->product->findBySlug($productSlug);
            if (!$product)
                return response()->json(["errors" => __('cart::api.cart.product.not_found') . $productSlug], 422);

            $product->product_type = 'product';
            $data['productDetailsRoute'] = route('frontend.products.index', [$product->translate(locale())->slug]);
            $data['productTitle'] = $product->translate(locale())->title;
            $productCartId = $product->id;
            $vendorId = $product->vendor->id ?? null;

            if (count($product->variants) > 0) {
                return response()->json(["errors" => __('catalog::frontend.cart.product_have_variations_it_cannot_be_ordered')], 422);
            }
        }

        if (!$product)
            abort(404);

        $checkProduct = is_null(getCartItemById($productCartId));

        if (isset($request->request_type) && $request->request_type == 'general_cart') {
            $request->merge(['qty' => getCartItemById($product->id) ? getCartItemById($product->id)->quantity + 1 : 1]);
        }

        if (config('setting.other.select_shipping_provider') == 'vendor_delivery') {
            if (getCartContent()->count() > 0 && !is_null($vendorId) && $vendorId != (getCartContent()->first()->attributes['vendor_id'] ?? ''))
                return response()->json(["errors" => __('catalog::frontend.products.alerts.empty_cart_firstly'), 'itemQty' => intval($request->qty) - 1], 422);
        }

        $errors = $this->addOrUpdateCart($product, $request);

        if ($errors)
            return response()->json(["errors" => $errors], 422);

        $data["total"] = number_format(getCartTotal(), 3);
        $data["subTotal"] = number_format(getCartSubTotal(), 3);
        $data["cartCount"] = count(getCartContent());
        //        $data["productPrice"] = $product->offer ? $product->offer->offer_price : $product->price;

        if ($product->offer) {
            if (!is_null($product->offer->offer_price)) {
                $data["productPrice"] = $product->offer->offer_price;
            } elseif (!is_null($product->offer->percentage)) {
                $percentageResult = (floatval($product->price) * floatVal($product->offer->percentage)) / 100;
                $data["productPrice"] = floatval($product->price) - $percentageResult;
            } else {
                $data["productPrice"] = floatval($product->price);
            }
        } else {
            $data["productPrice"] = floatval($product->price);
        }

        $data["productQuantity"] = $request->product_type == 'product' ? getCartItemById($product->id)->quantity : getCartItemById('var-' . $product->id)->quantity;
        $data["product_type"] = $request->product_type ?? '';
        $data["remainingQty"] = intval($product->qty) - intval($data["productQuantity"]);

        if ($checkProduct) {
            return response()->json(["message" => __('catalog::frontend.cart.add_successfully'), "data" => $data], 200);
        } else {
            return response()->json(["message" => __('catalog::frontend.cart.updated_successfully'), "data" => $data], 200);
        }
    }

    public function delete(Request $request, $id)
    {
        if ($request->product_type == 'product')
            $deleted = $this->deleteProductFromCart($id);
        else
            $deleted = $this->deleteProductFromCart('var-' . $id);

        if ($deleted)
            return redirect()->back()->with(['alert' => 'success', 'status' => __('catalog::frontend.cart.delete_item')]);

        return redirect()->back()->with(['alert' => 'danger', 'status' => __('catalog::frontend.cart.error_in_cart')]);
    }

    public function deleteByAjax(Request $request)
    {
        if ($request->product_type == 'product')
            $deleted = $this->deleteProductFromCart($request->id);
        else
            $deleted = $this->deleteProductFromCart('var-' . $request->id);

        if ($deleted) {
            $result["cartCount"] = count(getCartContent());
            $result["cartTotal"] = getCartSubTotal();
            return response()->json(["message" => __('catalog::frontend.cart.delete_item'), "result" => $result], 200);
        }

        return response()->json(["errors" => __('catalog::frontend.cart.error_in_cart')], 422);
    }

    public function clear(Request $request)
    {
        $cleared = $this->clearCart();

        if ($cleared)
            return redirect()->back()->with(['alert' => 'success', 'status' => __('catalog::frontend.cart.clear_cart')]);

        return redirect()->back()->with(['alert' => 'danger', 'status' => __('catalog::frontend.cart.error_in_cart')]);
    }
}
