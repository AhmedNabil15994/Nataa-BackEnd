<?php

namespace Modules\Vendor\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Transformers\WebService\PaginatedResource;
use Modules\Catalog\Transformers\WebService\ProductResource;
use Modules\Vendor\Traits\VendorTrait;

class VendorResource extends JsonResource
{
    use VendorTrait;

    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'image' => url($this->image),
            'title' => $this->translate(locale())->title,
            'description' => $this->translate(locale())->description,
            'lat' => $this->lat,
            'long' => $this->long,
            'vendor_categories' => VendorCategoryResource::collection($this->categories),
            'sections' => SectionResource::collection($this->sections),
        ];

        if (request()->get('with_products') == 'yes') {
            $productsCount = request()->get('with_products_count') ?? 10;
            $result['products'] = ProductResource::collection($this->products->take($productsCount));
        }

        if (isset($this->deliveryCharge) && $this->deliveryCharge->count() > 0 && !is_null($request->state_id))
            $result['delivery'] = new DeliveryChargeResource($this->deliveryCharge[0]);
        else
            $result['delivery'] = null;

        /*if (in_array(request()->route()->getName(), ['api.vendors.get_one_vendor', 'api.vendors.get_vendor_offers'])) {
            $products = $request->products;
            $request->request->remove('products');
            $result['products'] = PaginatedResource::make($products)->mapInto(ProductResource::class);
        }*/

        $result['opening_status'] = $this->checkVendorBusyStatus($this->id);
        return $result;
    }
}
