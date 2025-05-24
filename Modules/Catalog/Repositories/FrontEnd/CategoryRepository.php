<?php

namespace Modules\Catalog\Repositories\FrontEnd;

use Modules\Catalog\Entities\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Entities\Product;

class CategoryRepository
{
    protected $category;
    protected $prd;
    protected $defaultVendor;

    function __construct(Category $category, Product $prd)
    {
        $this->category = $category;
        $this->prd = $prd;

        $this->defaultVendor = app('vendorObject') ?? null;
    }

    public function getHeaderCategories($order = 'sort', $sort = 'asc')
    {
        return $this->category->active()
            ->whereHas('products', function ($query) {
                $query->active();
            })
            ->orderBy($order, $sort)
//            ->where('id', '<>', '1')
            ->whereNull('category_id')
            ->get();
    }

    public function getAllActive($order = 'sort', $sort = 'asc')
    {
        // get all categories that have only active vendor products
        return $this->category
            ->active()
            ->whereHas('products', function ($query) {
                $query->active();
            })
            ->orderBy($order, $sort)
//            ->where('id', '<>', '1')
            ->get();
    }

    public function mainCategoriesOfVendorProducts($vendor, $request = null)
    {
        $categories = $this->category->mainCategories()
            ->with([
                'products' => function ($query) use ($vendor, $request) {

                    if (isset($request['search'])) {
                        $query->whereHas('translations', function ($q) use ($request) {

                            $q->where('description', 'like', '%' . $request['search'] . '%');
                            $q->orWhere('short_description', 'like', '%' . $request['search'] . '%');
                            $q->orWhere('title', 'like', '%' . $request['search'] . '%');
                            $q->orWhere('slug', 'like', '%' . $request['search'] . '%');

                        });
                    }

                    if (isset($request['sorted_by'])) {

                        if ($request['sorted_by'] == 'a_to_z')
                            $query->orderByTranslation('title', 'ASC');

                        if ($request['sorted_by'] == 'latest')
                            $query->orderBy('id', 'ASC');

                    } else {
                        $query->orderBy('id', 'ASC');
                    }

                    $query->with([
                        'addOns',
                        'offer' => function ($query) {
                            $query->active()->unexpired()->started();
                        },
                    ])->whereHas('vendor', function ($query) use ($vendor) {
                        $query->where('id', $vendor->id);
                        $query->active();
                    });/*->active();*/
                }
            ])
            ->whereHas('products', function ($query) use ($vendor) {
                $query->whereHas('vendor', function ($query) use ($vendor) {
                    $query->whereTranslation('slug', $vendor->translate(locale())->slug);
                });
            })
            ->active()
            ->orderBy('sort', 'ASC')
            ->get();

        return $categories;
    }

    public function findBySlug($slug)
    {
        return $this->category
            ->active()
            ->whereTranslation('slug', $slug)->first();
    }

    public function checkRouteLocale($model, $slug)
    {
        if ($model->translate()->where('slug', $slug)->first()->locale != locale())
            return false;

        return true;
    }

    public function getFeaturedProducts($request)
    {
        $product = $this->prd->with('vendor', 'tags');
        $product = $product->where('featured', '1');

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $product->doesnthave('offer')->orderBy('id', 'desc')->active();
        return $product->take(10)->get();
    }

    public function getLatestOffersData($request)
    {
        $product = $this->prd->with('vendor', 'tags');

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $product->active()->whereHas('offer', function ($query) {
            $query->active()->unexpired()->started();
        });
        return $product->take(10)->get();
    }

    public function getMainCategoriesData($request)
    {
        return $this->category->mainCategories()
            ->whereHas('products', function ($query) {
                $query->active();
            })
            ->active()
//            ->where('id', '<>', '1')
            ->where('show_in_home', '1')
            ->orderBy('sort', 'ASC')
//            ->take(5)
            ->get();

    }

    public function getMostSellingProducts($request)
    {
        $sales = DB::table('products')
            ->rightJoin('order_products', 'products.id', '=', 'order_products.product_id')
            ->selectRaw('products.*, COALESCE(sum(order_products.qty),0) totalQuantity')
            ->groupBy('products.id');

        $result = DB::table('products')
            ->rightJoin('product_variants', function ($join) {
                $join->on('products.id', '=', 'product_variants.product_id');
                $join->join('order_variant_products', function ($join) {
                    $join->on('product_variants.id', '=', 'order_variant_products.product_variant_id');
                });
            })
            ->selectRaw('products.*, COALESCE(sum(order_variant_products.qty),0) totalQuantity')
            ->groupBy('products.id')
            ->union($sales)
            ->orderBy('totalQuantity', 'desc')
            ->take(20)
            ->get();

        return $result;
    }


}
