<?php

namespace Modules\Catalog\Transformers\WebService;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Advertising\Transformers\WebService\AdvertisingResource;
use Modules\Tags\Transformers\WebService\TagsResource;
use Modules\Vendor\Traits\VendorTrait;

class ProductResource extends JsonResource
{
    use VendorTrait;

    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'sku' => $this->sku,
            'price' => $this->price,
            'origin_price' => $this->origin_price,
            'qty' => $this->qty,
            'image' => $this->image ? url($this->image) : null,
            'title' => optional($this->translate(locale()))->title,
            'description' => htmlView(optional($this->translate(locale()))->description),
            'short_description' => optional($this->translate(locale()))->short_description,
            'dimensions' => $this->shipment,
            'offer' => new ProductOfferResource($this->offer),
            'images' => ProductImagesResource::collection($this->images),
            'tags' => TagsResource::collection($this->tags),
            'products_options' => ProductOptionResource::collection($this->options),
            'variations_values' => ProductVariantResource::collection($this->variants),
            'availability_times'    => ProductAvailabilityTimeResource::collection($this->availability_times()->groupBy('day')->get()),
            'addons' => AddOnsResource::collection($this->addOns),
            'adverts' => AdvertisingResource::collection($this->adverts),
            'sharable_link' => route('frontend.products.index', optional($this->translate(locale()))->slug),

            //'categories' => $this->parentCategories->pluck('id'),
            //'sub_categories' => CategoryDetailsResource::collection($this->subCategories),
        ];

//        if ($this->offer) {
//            $offer = new ProductOfferResource($this->offer);
//            $result['price'] = isset($offer['offer_price']) ? $offer['offer_price'] : $this->price;
//            $result['origin_price'] = isset($offer['offer_price']) ? $this->price : null;
//        }
        if (auth('api')->check()) {
            $result['is_favorite'] = CheckProductInUserFavourites($this->id, auth('api')->id());
        } else {
            $result['is_favorite'] = null;
        }

        if (!is_null($this->vendor)) {
            $result['vendor'] = [
                'id' => $this->vendor->id,
                'title' => optional(optional($this->vendor)->translate(locale()))->title,
                'image' => url($this->vendor->image),
                'opening_status' => $this->checkVendorBusyStatus($this->vendor->id),
            ];
        } else {
            $result['vendor'] = null;
        }

        return $result;
    }
}
