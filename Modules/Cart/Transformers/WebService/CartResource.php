<?php

namespace Modules\Cart\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Entities\AddOn;
use Modules\Catalog\Entities\AddOnOption;
use Modules\Catalog\Entities\Product;
use Modules\Variation\Entities\ProductVariant;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => (string) $this->id,
            'qty' => $this->quantity,
            'image' => url($this->attributes->product->image),
            'product_type' => $this->attributes->product->product_type,
            'notes' => $this->attributes->notes,
        ];

        if ($this->attributes->product->product_type == 'product') {
            $result['title'] = $this->attributes->product->translate(locale())->title;
            $currentProduct = Product::find($this->attributes->product->id);
            if ($currentProduct) {
                if (!is_null($currentProduct->qty)) {
                    $result['remaining_qty'] = intval($currentProduct->qty);
                    // $result['remaining_qty'] = intval($currentProduct->qty) - intval($this->quantity);
                } else {
                    $result['remaining_qty'] = null;
                }
            } else {
                $result['remaining_qty'] = 0;
            }
        } else {
            $result['title'] = $this->attributes->product->product->translate(locale())->title;
            $result['product_options'] = CartProductOptionsResource::collection($this->attributes->product->productValues);
            $currentProduct = ProductVariant::find($this->attributes->product->id);
            if ($currentProduct) {
                if (!is_null($currentProduct->qty)) {
                    $result['remaining_qty'] = intval($currentProduct->qty);
                    // $result['remaining_qty'] = intval($currentProduct->qty) - intval($this->quantity);
                } else {
                    $result['remaining_qty'] = null;
                }
            } else {
                $result['remaining_qty'] = 0;
            }
        }

        if ($this->attributes->addonsOptions) {
            $price = floatval($this->price) - floatval($this->attributes->addonsOptions['total_amount']);
            $result['price'] = number_format($price, 3);
        } else {
            $result['price'] = number_format($this->price, 3);
        }

        $result['addons'] = $this->attributes->addonsOptions;
        if ($this->attributes->addonsOptions) {
            $result['addons']['selected_addons'] = $this->buildCustomSelectedAddons($this->attributes->addonsOptions);
        }
        return $result;
    }

    private function buildCustomSelectedAddons($cartAddonsOptions)
    {
        $result = [];
        foreach ($cartAddonsOptions['data'] as $key => $addon) {
            $addonModel = AddOn::find($addon['id']);
            if ($addonModel) {
                $result[$addon['id']]['id'] = $addon['id'];
                $result[$addon['id']]['name'] = $addonModel->name;
                $result[$addon['id']]['type'] = $addonModel->type;
                $addonOption = [];
                foreach ($addon['options'] as $k => $option) {
                    $addonOptionModel = AddOnOption::find($option);
                    if ($addonOptionModel) {
                        $addonOption = [
                            'id' => $option,
                            'option' => $addonOptionModel->option,
                            'price' => collect($cartAddonsOptions['addonsPriceObject'])->where('id', $option)->first()['amount'] ?? null,
                            'image' => $addonOptionModel->image ? url($addonOptionModel->image) : null,
                        ];
                    }
                    $result[$addon['id']]['addonOptions'][] = $addonOption;
                }
            }
        }
        return $result;
    }
}
