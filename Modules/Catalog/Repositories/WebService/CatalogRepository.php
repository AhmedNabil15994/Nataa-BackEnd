<?php

namespace Modules\Catalog\Repositories\WebService;

use Modules\Catalog\Entities\Category;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\VendorProduct;
use Modules\Catalog\Traits\CatalogTrait;
use Modules\Variation\Entities\Option;
use Modules\Variation\Entities\ProductVariant;
use Modules\Vendor\Entities\Vendor;

class CatalogRepository
{
    use CatalogTrait;

    protected $category;
    protected $product;
    protected $vendor;
    protected $prd;
    protected $prdVariant;
    protected $option;
    protected $defaultVendor;

    public function __construct(
        VendorProduct $product,
        Product $prd,
        Category $category,
        Vendor $vendor,
        ProductVariant $prdVariant,
        Option $option
    ) {
        $this->category = $category;
        $this->product = $product;
        $this->vendor = $vendor;
        $this->prd = $prd;
        $this->prdVariant = $prdVariant;
        $this->option = $option;

        $this->defaultVendor = app('vendorObject') ?? null;
    }

    public function getLatestNCategories($request)
    {
        $categories = $this->buildCategoriesTree($request, true);
        $count = $request->categories_count ?? 8;
        return $categories/* ->where('show_in_home', 1) */->orderBy('sort', 'asc')->take($count)->get();
    }

    public function getAllCategories($request)
    {
        /* $showInHome = null;
        if (!is_null($request->show_in_home)) {
            $showInHome = filter_var($request->show_in_home, FILTER_VALIDATE_BOOLEAN);
        } */
        $categories = $this->buildCategoriesTree($request, false);
        $categories = $categories->orderBy('sort', 'asc');
        if (!empty($request->categories_count)) {
            $categories = $categories->take($request->categories_count);
        }

        return $categories->get();
    }

    public function getAllMainCategories($request = null, $vendorId = null)
    {
        $query = $this->category->active()->mainCategories();
        if (request()->route()->getName() == 'api.vendors.get_one_vendor') {
            $query = $query->where('show_in_home', 0);
        }
        $query = $query->whereHas('products', function ($query) use ($vendorId) {
            $query->active();
            if (!is_null($vendorId)) {
                $query->where('vendor_id', $vendorId);
            }
        });
        $query = $query->orderBy('sort', 'asc');
        return $query->get();
    }

    public function getFilterOptions($request)
    {
        return $this->option->active()
            ->with(['values' => function ($query) {
                $query->active();
            }])
            ->activeInFilter()
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getAutoCompleteProducts($request)
    {
        $products = $this->prd->active();
        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }
        return $products->orderBy('id', 'DESC')->get();
    }

    public function getProductsByCategory($request)
    {
        $allCats = $this->getAllSubCategoryIds($request->category_id);
        array_push($allCats, intval($request->category_id));


        $day = lcfirst(date('D'));
        $time = date('H:i');

        $optionsValues = isset($request->options_values) && !empty($request->options_values) ? array_values($request->options_values) : [];
        $products = $this->prd->active()
            ->whereHas('availability_times',function ($q) use ($day,$time){
                $q->where('day',$day)->whereTime('time_from','<=',$time)->whereTime('time_to','>=',$time);
            })->orWhereDoesntHave('availability_times')
            ->with([
                'offer' => function ($query) {
                    $query->active()->unexpired()->started();
                },
            ])
            ->with(['variants' => function ($q) {
                $q->with(['offer' => function ($q) {
                    $q->active()->unexpired()->started();
                }]);
            }]);

        if (!is_null($this->defaultVendor)) {
            $products = $products->where('vendor_id', $this->defaultVendor->id);
        }

        if (count($optionsValues) > 0) {
            $products = $products->whereHas('variantValues', function ($query) use ($optionsValues) {
                $query->whereIn('option_value_id', $optionsValues);
            });
        }

        if ($request->state_id) {
            $products->whereHas('vendor.deliveryCharge', function ($query) use ($request) {
                $query->active()->where('state_id', $request->state_id);
            });
        }

        if ($request->category_id) {
            $products->whereHas('categories', function ($query) use ($allCats) {
                $query->whereIn('product_categories.category_id', $allCats);
            });
        }

        if ($request['low_price'] && $request['high_price']) {
            $products->whereBetween('price', [$request['low_price'], $request['high_price']]);
        }

        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }

        if ($request['sort']) {
            $products->when($request['sort'] == 'a_to_z', function ($query) {
                $query->orderByTranslation('title', 'asc');
            });
            $products->when($request['sort'] == 'z_to_a', function ($query) {
                $query->orderByTranslation('title', 'desc');
            });
            $products->when($request['sort'] == 'low_to_high', function ($query) {
                $query->orderBy('price', 'asc');
            });
            $products->when($request['sort'] == 'high_to_low', function ($query) {
                $query->orderBy('price', 'desc');
            });
        } else {
            $products->orderBy('sort', 'DESC');
        }

        return $products->paginate(24);
    }

    public function getProductDetails($request, $id)
    {
        $product = $this->prd->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);
        return $product->find($id);
    }

    public function getLatestData($request)
    {
        $product = $this->prd->doesnthave('offer')->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);

        if ($request['search']) {
            $product = $this->productSearch($product, $request);
        }

        return $product->orderBy('id', 'desc')->take(10)->get();
    }

    public function getOffersData($request)
    {
        $product = $this->prd->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);

        if ($request['search']) {
            $product = $this->productSearch($product, $request);
        }

        $product = $product->whereHas('offer', function ($query) {
            $query->active()->unexpired()->started();
        });

        if (isset($request->offers_products_count) && !is_null($request->offers_products_count)) {
            $product = $product->take($request->offers_products_count ?? 10);
        }

        return $product->get();
    }

    public function findOneProduct($id)
    {
        $product = $this->prd->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, null);

        return $product->find($id);
    }

    public function findOneProductVariant($id)
    {
        $product = $this->prdVariant->active()->with([
            'offer' => function ($query) {
                $query->active()->unexpired()->started();
            },
            'productValues', 'product',
        ]);

        if (!is_null($this->defaultVendor)) {
            $product = $this->prdVariant->whereHas('product', function ($query) {
                $query->where('vendor_id', $this->defaultVendor->id);
            });
        }

        return $product->find($id);
    }

    public function getAllSubCategoriesByParent($id, $vendorId = null)
    {
        $query = $this->category->where('category_id', $id);
        if (request()->route()->getName() == 'api.vendors.get_one_vendor') {
            $query = $query->where('show_in_home', 0);
        }
        $query = $query->whereHas('products', function ($query) use ($vendorId) {
            $query->active();
            if (!is_null($vendorId)) {
                $query->where('vendor_id', $vendorId);
            }
        });
        return $query->get();
    }

    public function buildCategoriesTree($request, $showInHome = null)
    {
        $categories = $this->category->active()
            ->withCount(['products' => function ($query) use ($request) {
                $query->active();
                if (!is_null($this->defaultVendor)) {
                    $query->where('vendor_id', $this->defaultVendor->id);
                }

                if ($request->state_id) {
                    $query->whereHas('vendor.deliveryCharge', function ($query) use ($request) {
                        $query->active()->where('state_id', $request->state_id);
                    });
                }
            }]);

        $categories = $categories->with(['adverts' => function ($query) use ($request) {
            $query->active()->unexpired()->started()->orderBy('sort', 'asc');
        }]);

        if (!is_null($showInHome)) {
            $categories = $categories->where('show_in_home', $showInHome);
        }

        if ($request->with_sub_categories == 'yes') {
            $categories = $categories->with('childrenRecursive');
        }

        if ($request->get_main_categories == 'yes') {
            $categories = $categories->mainCategories();
        }

        $categories = $categories->whereHas('products', function ($query) use ($request) {
            $query->active();
            if ($request->state_id) {
                $query->whereHas('vendor.deliveryCharge', function ($query) use ($request) {
                    $query->active()->where('state_id', $request->state_id);
                });
            }
        });

        if ($request->with_products == 'yes') {
            // Get Category Products
            $categories = $categories->with([
                'products' => function ($query) use ($request) {
                    $query->active();

                    if ($request->state_id) {
                        $query->whereHas('vendor.deliveryCharge', function ($query) use ($request) {
                            $query->active()->where('state_id', $request->state_id);
                        });
                    }

                    $query = $this->returnProductRelations($query, $request);

                    if (!is_null($this->defaultVendor)) {
                        $query->where('vendor_id', $this->defaultVendor->id);
                    }

                    if ($request->state_id) {
                        $query->whereHas('vendor.deliveryCharge', function ($query) use ($request) {
                            $query->active()->where('state_id', $request->state_id);
                        });
                    }

                    if ($request['search']) {
                        $query = $this->productSearch($query, $request);
                    }

                    $query->orderBy('products.sort', 'asc');
                },
            ]);
        }

        return $categories;
    }

    public function productSearch($model, $request)
    {
        return $model->where('sku', 'like', '%' . $request['search'] . '%')
            ->orWhereHas('translations', function ($query) use ($request) {

                $query->where('title', 'like', '%' . $request['search'] . '%');
                $query->orWhere('description', 'like', '%' . $request['search'] . '%');
                $query->orWhere('slug', 'like', '%' . $request['search'] . '%');
            })->orWhereHas('searchKeywords', function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request['search'] . '%');
        });
    }

    public function returnProductRelations($model, $request)
    {
        return $model->with([
            'offer' => function ($query) {
                $query->active()->unexpired()->started();
            },
            'options',
            'images',
            'vendor',
            // 'subCategories',
            'addOns',
            'variants' => function ($q) {
                $q->with(['offer' => function ($q) {
                    $q->active()->unexpired()->started();
                }]);
            },
        ]);
    }

    public function relatedProducts($selectedProduct)
    {
        $relatedCategoriesIds = $selectedProduct->categories()->pluck('product_categories.category_id')->toArray();
        $products = $this->prd->where('id', '<>', $selectedProduct->id)->active();
        $products = $products->whereHas('categories', function ($query) use ($relatedCategoriesIds) {
            $query->whereIn('product_categories.category_id', $relatedCategoriesIds);
        });
        return $products->orderBy('id', 'desc')->take(10)->get();
    }

    public function getProductsByVendorAndCategory($id, $request = null, $categoryId = null, $withOffers = true)
    {
        $day = lcfirst(date('D'));
        $time = date('H:i');

        $products = $this->prd->active()->whereHas('availability_times',function ($q) use ($day,$time){
            $q->where('day',$day)->whereTime('time_from','<=',$time)->whereTime('time_to','>=',$time);
        })->orWhereDoesntHave('availability_times')->where('vendor_id', $id);
        if (!$withOffers) {
            $products = $products->doesnthave('offer');
        }

        if (!is_null($categoryId)) {
            $products = $products->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('product_categories.category_id', $categoryId);
            });
        }

        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }

        $products = $this->returnProductRelations($products, null);
        return $products->orderBy('sort', 'DESC')->paginate(config('core.config.api_pagination_count'));
    }

    public function getOfferProductsByVendorAndCategory($vendorId = null, $categoryId = null, $request = null, $returnType = 'all')
    {
        $day = lcfirst(date('D'));
        $time = date('H:i');

        $products = $this->prd->active()->whereHas('availability_times',function ($q) use ($day,$time){
            $q->where('day',$day)->whereTime('time_from','<=',$time)->whereTime('time_to','>=',$time);
        })->orWhereDoesntHave('availability_times');

        if (!is_null($vendorId)) {
            $products = $products->where('vendor_id', $vendorId);
        }

        $products = $products->whereHas('offer', function ($query) {
            $query->active()->unexpired()->started();
        });

        if (!is_null($categoryId)) {
            $products = $products->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('product_categories.category_id', $categoryId);
            });
        }

        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }

        $products = $this->returnProductRelations($products, null);
        $products = $products->orderBy('sort', 'asc');

        if ($returnType == 'paginated') {
            return $products->paginate(config('core.config.api_pagination_count'));
        } else {
            return $products->get();
        }

    }
}
