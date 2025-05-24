<?php

namespace Modules\Vendor\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Catalog\Repositories\WebService\CatalogRepository as Catalog;
use Modules\Catalog\Transformers\WebService\CategoryDetailsResource;
use Modules\Catalog\Transformers\WebService\PaginatedResource;
use Modules\Catalog\Transformers\WebService\ProductResource;
use Modules\Vendor\Http\Requests\WebService\RateRequest;
use Modules\Vendor\Repositories\Vendor\RateRepository as Rate;
use Modules\Vendor\Repositories\WebService\VendorRepository as Vendor;
use Modules\Vendor\Traits\UploaderTrait;
use Modules\Vendor\Transformers\WebService\DeliveryCompaniesResource;
use Modules\Vendor\Transformers\WebService\SectionResource;
use Modules\Vendor\Transformers\WebService\VendorCategoryResource;
use Modules\Vendor\Transformers\WebService\VendorResource;

class VendorController extends WebServiceController
{
    use UploaderTrait;

    protected $vendor;
    protected $rate;
    protected $catalog;

    public function __construct(Vendor $vendor, Rate $rate, Catalog $catalog)
    {
        $this->vendor = $vendor;
        $this->rate = $rate;
        $this->catalog = $catalog;
    }

    public function sections(Request $request)
    {
        $sections = $this->vendor->getAllSections($request);
        return $this->response(SectionResource::collection($sections));
    }

    public function categories(Request $request)
    {
        $categories = $this->vendor->getAllVendorsCategories($request);
        return $this->response(VendorCategoryResource::collection($categories));
    }

    public function vendors(Request $request)
    {
        $vendors = $this->vendor->getAllVendors($request);
        return $this->response(VendorResource::collection($vendors));
    }

    public function getVendorById(Request $request)
    {
        $vendor = $this->vendor->getOneVendor($request);
        $vendorObject = null;
        if ($vendor) {
            $products = $this->catalog->getProductsByVendorAndCategory($vendor->id, $request, $request->category_id ?? null, true);

            if (isset($request->category_id) && !is_null($request->category_id)) {
                $categories = $this->catalog->getAllSubCategoriesByParent($request->category_id, $vendor->id);
            } else {
                $categories = $this->catalog->getAllMainCategories($request, $vendor->id);
            }

            $vendorObject['vendor'] = new VendorResource($vendor);
            $vendorObject['categories'] = CategoryDetailsResource::collection($categories);
            $vendorObject['products'] = PaginatedResource::make($products)->mapInto(ProductResource::class);
            return $this->response($vendorObject);
        } else {
            return $this->response(null);
        }

    }

    public function getOfferProductsByVendor(Request $request)
    {
        $vendor = $this->vendor->getOneVendor($request);
        $vendorObject = null;
        if ($vendor) {
            $products = $this->catalog->getOfferProductsByVendorAndCategory($vendor->id, $request->category_id ?? null, $request, 'paginated');

            if (isset($request->category_id) && !is_null($request->category_id)) {
                $categories = $this->catalog->getAllSubCategoriesByParent($request->category_id);
            } else {
                $categories = $this->catalog->getAllMainCategories($request);
            }

            $vendorObject['vendor'] = new VendorResource($vendor);
            $vendorObject['categories'] = CategoryDetailsResource::collection($categories);
            $vendorObject['products'] = PaginatedResource::make($products)->mapInto(ProductResource::class);
            return $this->response($vendorObject);
        } else {
            return $this->response(null);
        }

    }

    /*public function deliveryCharge(Request $request)
    {
    $charge = $this->vendor->getDeliveryChargesByVendorByState($request);

    if (!$charge)
    return $this->response([]);

    return $this->response(new DeliveryChargeResource($charge));
    }*/

    /*public function sendPrescription(PrescriptionRequest $request, $id)
    {
    $vendor = $this->vendor->findById($id);

    if (isset($request->image) && !empty($request->image)) {
    $uploadPath = $this->base64($request->image, null, 'prescriptions');
    $request->merge([
    'imagePath' => env('APP_URL') . $uploadPath,
    ]);
    } else {
    $imagePath = null;
    }

    Notification::route('mail', $vendor['vendor_email'])->notify(
    (
    new PrescriptionVendordNotification($request->all())
    )->locale(locale()));

    return $this->response([]);
    }

    public function sendAsk(AskQuestionRequest $request, $id)
    {
    $vendor = $this->vendor->findById($id);

    Notification::route('mail', $vendor['vendor_email'])->notify(
    (
    new AskVendordNotification($request)
    )->locale(locale()));

    return $this->response([]);
    }*/

    public function vendorRate(RateRequest $request)
    {
        $order = $this->rate->findOrderByIdWithUserId($request->order_id);
        if ($order) {
            $rate = $this->rate->checkUserRate($request->order_id);
            if (!$rate) {
                $request->merge([
                    'vendor_id' => $order->vendor_id,
                ]);
                $createdRate = $this->rate->create($request);
                return $this->response([]);
            } else {
                return $this->error(__('vendor::webservice.rates.user_rate_before'));
            }

        } else {
            return $this->error(__('vendor::webservice.rates.user_not_have_order'));
        }

    }

    public function getVendorDeliveryCompanies(Request $request, $id)
    {
        $vendor = $this->vendor->findVendorByIdAndStateId($id, $request->state_id);
        if ($vendor) {
            $result['companies'] = DeliveryCompaniesResource::collection($vendor->companies);
            $result['vendor_fixed_delivery'] = $vendor->fixed_delivery;
            return $this->response($result);
        } else {
            return $this->error(__('vendor::webservice.companies.vendor_not_found_with_this_state'), null);
        }
    }

}
