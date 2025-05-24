<?php

namespace Modules\Catalog\Repositories\Dashboard;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Modules\Catalog\Entities\AddOnOption;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Entities\ProductImage;
use Modules\Catalog\Entities\AddOn;
use Hash;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Entities\SearchKeyword;
use Modules\Core\Traits\CoreTrait;
use Modules\Core\Traits\SyncRelationModel;
use Modules\Variation\Entities\OptionValue;
use Modules\Variation\Entities\ProductVariant;

class ProductRepository
{
    use SyncRelationModel, CoreTrait;

    protected $product;
    protected $addOn;
    protected $addOnOption;
    protected $prdImg;
    protected $optionValue;
    protected $variantPrd;
    protected $imgPath;

    function __construct(Product $product, AddOn $addOn, AddOnOption $addOnOption, ProductImage $prdImg, OptionValue $optionValue, ProductVariant $variantPrd)
    {
        $this->product = $product;
        $this->addOn = $addOn;
        $this->addOnOption = $addOnOption;
        $this->prdImg = $prdImg;
        $this->optionValue = $optionValue;
        $this->variantPrd = $variantPrd;
        $this->imgPath = public_path('uploads/products');
    }

    public function getAll($order = 'id', $sort = 'desc')
    {
        $products = $this->product->orderBy($order, $sort)->get();
        return $products;
    }

    public function getAllActive($order = 'id', $sort = 'desc')
    {
        $products = $this->product->active()->orderBy($order, $sort)->get();
        return $products;
    }

    public function findById($id)
    {
        $product = $this->product->withDeleted()->with(['tags', 'images','availability_times', 'addOns' => function ($q) {
            $q->with('addOnOptions');
        }])->find($id);
        return $product;
    }

    public function findAddOnsById($id)
    {
        return $this->addOn->with('addOnOptions')->find($id);
    }

    public function findVariantProductById($id)
    {
        return $this->variantPrd->with('productValues')->find($id);
    }

    public function findProductImgById($id)
    {
        return $this->prdImg->find($id);
    }

    public function findAddOnsOptionById($id)
    {
        return $this->addOnOption->find($id);
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $data = [
                //'image' => $request->image ? path_without_domain($request->image) : url(config('setting.logo')),
                'status' => $request->status == 'on' ? 1 : 0,
                'featured' => $request->featured == 'on' ? 1 : 0,
                'price' => $request->price,
                'sku' => $request->sku,
                "shipment" => $request->shipment,
                'sort' => $request->sort ?? 0,
            ];

            if ($request->manage_qty == 'limited')
                $data['qty'] = $request->qty;
            else
                $data['qty'] = null;

            if (!is_null($request->image)) {
                $imgName = $this->uploadImage($this->imgPath, $request->image);
                $data['image'] = 'uploads/products/' . $imgName;
            } else {
                $data['image'] = $request->product_old_image ?? url(config('setting.logo'));
            }

            if (config('setting.other.is_multi_vendors') == 1)
                $data['vendor_id'] = $request->vendor_id;
            else
                $data['vendor_id'] = config('setting.default_vendor') ?? null;

            if (auth()->user()->can('pending_products_for_approval'))
                $data['pending_for_approval'] = $request->pending_for_approval == 'on' ? 1 : 0;

            $product = $this->product->create($data);

            $this->translateTable($product, $request);

            $product->categories()->sync(int_to_array($request->category_id));
            if ($request->offer_status != "on") {
                $this->productVariants($product, $request);
            }

            $this->productOffer($product, $request);
            $this->productAvailability($product, $request);

            // Add Product Images
            if (isset($request->images) && !empty($request->images)) {

                $imgPath = public_path('uploads/products');

                foreach ($request->images as $k => $img) {
                    $imgName = $img->hashName();
                    $img->move($imgPath, $imgName);

                    $product->images()->create([
                        'image' => $imgName,
                    ]);
                }
            }

            $hiddenGalleryImages = $this->removeEmptyValuesFromArray($request->hidden_images);
            if (!empty($hiddenGalleryImages)) {
                foreach ($hiddenGalleryImages as $k => $img) {
                    $product->images()->create([
                        'image' => $img,
                    ]);
                }
            }

            // Add Product Tags
            if (isset($request->tags) && !empty($request->tags)) {

                $tagsCollection = collect($request->tags);
                $filteredTags = $tagsCollection->filter(function ($value, $key) {
                    return $value != null && $value != '';
                });
                $tags = $filteredTags->all();

                $product->tags()->sync($tags);
            }

            // Add Product search keywords
            if (isset($request->search_keywords) && !empty($request->search_keywords)) {
                $searchKeywordsCollection = collect($request->search_keywords);
                $filteredSearchKeywords = $searchKeywordsCollection->filter(function ($value, $key) {
                    return $value != null && $value != '';
                });
                $searchKeywords = $filteredSearchKeywords->all();

                $ids = [];
                foreach ($searchKeywords as $searchKeyword) {
                    $keyword = SearchKeyword::firstOrCreate(
                        ['id' => $searchKeyword],
                        ['title' => $searchKeyword, 'status' => 1]
                    );
                    if ($keyword)
                        $ids[] = $keyword->id;
                }
                $product->searchKeywords()->sync($ids);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        // dd($request->all());
        $product = $this->findById($id);
        $restore = $request->restore ? $this->restoreSoftDelte($product) : null;

        if (isset($request->images) && !empty($request->images)) {
            $sync = $this->syncRelation($product, 'images', $request->images);
        }

        try {

            $data = [
                'featured' => $request->featured == 'on' ? 1 : 0,
                'status' => $request->status == 'on' ? 1 : 0,
                'sku' => $request->sku,
                "shipment" => $request->shipment,
                'sort' => $request->sort ?? 0,
            ];

            if (config('setting.other.is_multi_vendors') == 1)
                $data['vendor_id'] = $request->vendor_id;
            else
                $data['vendor_id'] = config('setting.default_vendor') ?? null;

            if (auth()->user()->can('edit_products_price'))
                $data['price'] = $request->price;

            if (auth()->user()->can('edit_products_qty')) {
                if ($request->manage_qty == 'limited')
                    $data['qty'] = $request->qty;
                else
                    $data['qty'] = null;
            }

            if (auth()->user()->can('edit_products_image')) {
                if ($request->image) {
                    File::delete($product->image); ### Delete old image
                    $imgName = $this->uploadImage($this->imgPath, $request->image);
                    $data['image'] = 'uploads/products/' . $imgName;
                } else {
                    $data['image'] = $product->image;
                }
            }

            if (auth()->user()->can('pending_products_for_approval'))
                $data['pending_for_approval'] = $request->pending_for_approval == 'on' ? 1 : 0;

            $product->update($data);

            $this->translateTable($product, $request, 'edit');

            if (auth()->user()->can('edit_products_category'))
                $product->categories()->sync(int_to_array($request->category_id));

            if ($request->offer_status == "on") {
                $product->variants()->delete();
            } else {
                $this->productVariants($product, $request);
            }

            if (auth()->user()->can('edit_products_price'))
                $this->productOffer($product, $request);

            if (auth()->user()->can('edit_products_gallery')) {
                // Create Or Update Product Images
                if (isset($request->images) && !empty($request->images)) {

                    $imgPath = public_path('uploads/products');

                    // Update Old Images
                    if (isset($sync['updated']) && !empty($sync['updated'])) {

                        foreach ($sync['updated'] as $k => $id) {

                            $oldImgObj = $product->images()->find($id);
                            File::delete('uploads/products/' . $oldImgObj->image); ### Delete old image

                            $img = $request->images[$id];
                            $imgName = $img->hashName();
                            $img->move($imgPath, $imgName);

                            $oldImgObj->update([
                                'image' => $imgName,
                            ]);
                        }
                    }

                    // Add New Images
                    foreach ($request->images as $k => $img) {

                        if (!in_array($k, $sync['updated'])) {

                            $imgName = $img->hashName();
                            $img->move($imgPath, $imgName);

                            $product->images()->create([
                                'image' => $imgName,
                            ]);
                        }
                    }
                }
            }
            $this->productAvailability($product, $request);

            // Update Product Tags
            if (isset($request->tags) && !empty($request->tags)) {

                $tagsCollection = collect($request->tags);
                $filteredTags = $tagsCollection->filter(function ($value, $key) {
                    return $value != null && $value != '';
                });
                $tags = $filteredTags->all();

                $product->tags()->sync($tags);
            }

            // Add Product search keywords
            if (isset($request->search_keywords) && !empty($request->search_keywords)) {

                $searchKeywordsCollection = collect($request->search_keywords);
                $filteredSearchKeywords = $searchKeywordsCollection->filter(function ($value, $key) {
                    return $value != null && $value != '';
                });
                $searchKeywords = $filteredSearchKeywords->all();

                $ids = [];
                foreach ($searchKeywords as $searchKeyword) {
                    $keyword = SearchKeyword::firstOrCreate(
                        ['id' => $searchKeyword],
                        ['title' => $searchKeyword, 'status' => 1]
                    );
                    if ($keyword)
                        $ids[] = $keyword->id;
                }
                $product->searchKeywords()->sync($ids);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function approveProduct($request, $id)
    {
        DB::beginTransaction();
        $product = $this->findById($id);

        try {
            $data = [];
            if (auth()->user()->can('review_products')) {
                $data['pending_for_approval'] = $request->pending_for_approval == 'on' ? true : false;
                $product->update($data);
            } else
                return false;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function restoreSoftDelte($model)
    {
        $model->restore();
    }

    public function translateTable($model, $request, $action = '')
    {
        foreach (config('translatable.locales') as $k => $locale) {

            if ($action == '' || $action == 'create' || ($action == 'edit' && auth()->user()->can('edit_products_title')))
                $model->translateOrNew($locale)->title = $request['title'][$locale];

            if ($action == '' || $action == 'create' || ($action == 'edit' && auth()->user()->can('edit_products_description'))) {
                if (!is_null($request['short_description'][$locale])) $model->translateOrNew($locale)->short_description = $request['short_description'][$locale];
                if (!is_null($request['description'][$locale])) $model->translateOrNew($locale)->description = $request['description'][$locale];
            }

            if (!is_null($request['seo_description'][$locale])) $model->translateOrNew($locale)->seo_description = $request['seo_description'][$locale];
            if (!is_null($request['seo_keywords'][$locale])) $model->translateOrNew($locale)->seo_keywords = $request['seo_keywords'][$locale];
        }

        $model->save();
    }

    public function translateAddOnTable($model, $request)
    {
        foreach ($request['option_name'] as $locale => $value) {
            $model->translateOrNew($locale)->name = $value;
        }

        $model->save();
    }

    public function translateAddOnOptionTable($model, $options)
    {
        foreach ($options as $locale => $value) {
            $model->translateOrNew($locale)->option = $value;
        }
        $model->save();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->findById($id);
            if ($model)
                File::delete($model->image); ### Delete old image

            if ($model->trashed()) :
                $model->forceDelete();
            else :
                $model->delete();
            endif;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteSelected($request)
    {
        DB::beginTransaction();

        try {

            foreach ($request['ids'] as $id) {
                $model = $this->delete($id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteAddOns($id)
    {
        DB::beginTransaction();

        try {
            $model = $this->findAddOnsById($id);
            if ($model) {
                $addonsOptionsImages = array_column($model->addOnOptions->toArray() ?? [], 'image') ?? [];
                if (count($addonsOptionsImages) > 0) {
                    foreach ($addonsOptionsImages as $k => $image) {
                        if (!in_array($image, config('core.config.special_images'))) {
                            File::delete($image); ### Delete old image
                        }
                    }
                }
                $model->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteAddOnsOption($id)
    {
        DB::beginTransaction();

        try {
            $model = $this->findAddOnsOptionById($id);
            if ($model) {
                if (!in_array($model->image, config('core.config.special_images'))) {
                    File::delete($model->image); ### Delete old image
                }
                $model->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteProductImg($id)
    {
        DB::beginTransaction();

        try {

            $model = $this->findProductImgById($id);

            if ($model) {
                File::delete('uploads/products/' . $model->image); ### Delete old image
                $model->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function createAddOns($request, $id)
    {
        DB::beginTransaction();

        try {

            $product = $this->findById($id);
            if ($product) {

                $dataInput = [];
                if (isset($request->add_ons_type) && !empty($request->add_ons_type)) {
                    $dataInput['type'] = $request->add_ons_type;
                    if (isset($request->options_count_select) && $request->options_count_select == 'on') {
                        $dataInput['options_count'] = $request->options_count ?? null;
                    }
                }

                if (isset($request->add_on_id) && !empty($request->add_on_id)) {
                    $addOn = AddOn::find($request->add_on_id);
                    $addOn->update($dataInput);
                } else {
                    $addOn = $product->addOns()->create($dataInput);
                }
                $this->translateAddOnTable($addOn, $request);

                // Add AddOns options

                if (count($request->price) > 0 && count($request->rowId) > 0) {
                    foreach ($request->rowId as $k => $rowID) {

                        if (isset($request->add_on_id) && !empty($request->add_on_id)) {
                            ############################## Start Update Options ##############################
                            $OldAddOnOption = AddOnOption::find($rowID);
                            if ($OldAddOnOption) {

                                if (isset($request->image[$k]) && $request->image[$k] instanceof UploadedFile) {
                                    if (!empty($OldAddOnOption->image) && !in_array($OldAddOnOption->image, config('core.config.special_images'))) {
                                        File::delete($OldAddOnOption->image); ### Delete old image
                                    }
                                    $image = $this->uploadAddonsOptionImage($request->image[$k]);
                                } else {
                                    $image = $OldAddOnOption->image;
                                }

                                // Update Old
                                $OldAddOnOption->update([
                                    'price' => $request->price[$k],
                                    'image' => $image,
                                    'default' => isset($request->default) && $request->default == $rowID ? true : null,
                                ]);

                                $optionsArr = array_values($request->option);
                                $this->translateAddOnOptionTable($OldAddOnOption, $optionsArr[$k]);
                            } else {
                                $image = isset($request->image[$k]) ? $this->uploadAddonsOptionImage($request->image[$k]) : null;
                                // Add New One
                                $newAddOnOption = $addOn->addOnOptions()->create([
                                    'price' => $request->price[$k],
                                    'image' => $image,
                                    'default' => isset($request->default) && $request->default == $rowID ? true : null,
                                ]);

                                $optionsArr = array_values($request->option);
                                $this->translateAddOnOptionTable($newAddOnOption, $optionsArr[$k]);
                            }
                            ############################## Start Update Options ##############################
                        } else {
                            ############################## Start Add New Options ##############################
                            $image = isset($request->image[$k]) ? $this->uploadAddonsOptionImage($request->image[$k]) : null;
                            $addOnOption = $addOn->addOnOptions()->create([
                                'price' => $request->price[$k],
                                'image' => $image,
                                'default' => isset($request->default) && $request->default == $rowID ? true : null,
                            ]);
                            $optionsArr = array_values($request->option);
                            $this->translateAddOnOptionTable($addOnOption, $optionsArr[$k]);
                            ############################## End Add New Options ##############################
                        }
                    }
                }

                DB::commit();
                return $this->findAddOnsById($addOn->id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function uploadAddonsOptionImage($imgFile)
    {
        if ($imgFile instanceof UploadedFile) {
            $imgName = $this->uploadImage(public_path(config('core.config.product_img_path')), $imgFile);
            $image = config('core.config.product_img_path') . '/' . $imgName;
        } else {
            $image = null;
        }
        return $image;
    }

    public function QueryTable($request)
    {
        $query = $this->product->with(['translations', 'vendor', 'categories']);
        $query = $query->approved();

        $query->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->input('search.value') . '%');
            $query->orWhere(function ($query) use ($request) {
                $query->whereHas('translations', function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->input('search.value') . '%');
                    $query->orWhere('slug', 'like', '%' . $request->input('search.value') . '%');
                });
            });

            $query->orWhereHas('categories.translations', function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('search.value') . '%');
            });

        });

        return $this->filterDataTable($query, $request);
    }

    public function reviewProductsQueryTable($request)
    {
        $query = $this->product->with(['translations', 'vendor']);
        $query = $query->notApproved();

        $query->where(function ($query) use ($request) {
            $query->where('id', 'like', '%' . $request->input('search.value') . '%');
            $query->orWhere(function ($query) use ($request) {
                $query->whereHas('translations', function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->input('search.value') . '%');
                    $query->orWhere('slug', 'like', '%' . $request->input('search.value') . '%');
                });
            });
        });

        return $this->filterDataTable($query, $request);
    }

    public function filterDataTable($query, $request)
    {
        // Search Categories by Created Dates
        if (isset($request['req']['from']) && $request['req']['from'] != '')
            $query->whereDate('created_at', '>=', $request['req']['from']);

        if (isset($request['req']['to']) && $request['req']['to'] != '')
            $query->whereDate('created_at', '<=', $request['req']['to']);

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'only')
            $query->onlyDeleted();

        if (isset($request['req']['deleted']) && $request['req']['deleted'] == 'with')
            $query->withDeleted();

        if (isset($request['req']['status']) && $request['req']['status'] == '1')
            $query->active();

        if (isset($request['req']['status']) && $request['req']['status'] == '0')
            $query->unactive();

        if (isset($request['req']['vendor']) && !empty($request['req']['vendor']))
            $query->where('vendor_id', $request['req']['vendor']);

        if (isset($request['req']['categories']) && $request['req']['categories'] != '') {

            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('product_categories.category_id', $request['req']['categories']);
            });

        }

        return $query;
    }

    public function productVariants($model, $request)
    {
        $oldValues = isset($request['variants']['_old']) ? $request['variants']['_old'] : [];

        $sync = $this->syncRelation($model, 'variants', $oldValues);

        if ($sync['deleted']) {
            $model->variants()->whereIn('id', $sync['deleted'])->delete();
        }

        if ($sync['updated']) {

            foreach ($sync['updated'] as $id) {

                foreach ($request['upateds_option_values_id'] as $key => $varianteId) {

                    $variation = $model->variants()->find($id);

                    $data = [
                        'sku' => $request['_variation_sku'][$id],
                        'price' => $request['_variation_price'][$id],
                        'status' => isset($request['_variation_status'][$id]) && $request['_variation_status'][$id] == 'on' ? 1 : 0,
                        'qty' => $request['_variation_qty'][$id],
                        "shipment" => isset($request["_vshipment"][$id]) ? $request["_vshipment"][$id] : null,
//                        'image' => $request['_v_images'][$id] ? path_without_domain($request['_v_images'][$id]) : $model->image
                    ];

                    if (!is_null($request['_v_images']) && isset($request['_v_images'][$id])) {
                        $imgName = $this->uploadImage($this->imgPath, $request['_v_images'][$id]);
                        $data['image'] = 'uploads/products/' . $imgName;
                    }

                    $variation->update($data);

                    if (isset($request["_v_offers"][$id]))
                        $this->variationOffer($variation, $request["_v_offers"][$id]);
                }
            }
        }

        $selectedOptions = [];

        if ($request['option_values_id']) {

            foreach ($request['option_values_id'] as $key => $value) {

                // dd($request->all(), $key);

                $data = [
                    'sku' => $request['variation_sku'][$key],
                    'price' => $request['variation_price'][$key],
                    'status' => isset($request['variation_status'][$key]) && $request['variation_status'][$key] == 'on' ? 1 : 0,
                    'qty' => $request['variation_qty'][$key],
                    "shipment" => isset($request["vshipment"][$key]) ? $request["vshipment"][$key] : null,
//                    'image' => $request['v_images'][$key] ? path_without_domain($request['v_images'][$key]) : $model->image
                ];

                if (!is_null($request['v_images']) && isset($request['v_images'][$key])) {
                    $imgName = $this->uploadImage($this->imgPath, $request['v_images'][$key]);
                    $data['image'] = 'uploads/products/' . $imgName;
                } else {
                    $data['image'] = $model->image;
                }

                $variant = $model->variants()->create($data);

                if (isset($request["v_offers"][$key]))
                    $this->variationOffer($variant, $request["v_offers"][$key]);

                foreach ($value as $key2 => $value2) {

                    $optVal = $this->optionValue->find($value2);
                    if ($optVal) {
                        if (!in_array($optVal->option_id, $selectedOptions)) {
                            array_push($selectedOptions, $optVal->option_id);
                        }
                    }

                    $option = $model->options()->updateOrCreate([
                        'option_id' => $optVal->option_id,
                        'product_id' => $model['id'],
                    ]);

                    $variant->productValues()->create([
                        'product_option_id' => $option['id'],
                        'option_value_id' => $value2,
                        'product_id' => $model['id'],
                    ]);
                }
            }
        }

        /*if (count($selectedOptions) > 0) {
            foreach ($selectedOptions as $option_id) {
                $option = $model->options()->updateOrCreate([
                    'option_id' => $option_id,
                    'product_id' => $model['id'],
                ]);
            }
        }*/

        /*if (count($selectedOptions) > 0) {
            $model->productOptions()->sync($selectedOptions);
        }*/
    }

    public function productOffer($model, $request)
    {
        if (isset($request['offer_status']) && $request['offer_status'] == 'on') {
            $data = [
                'status' => ($request['offer_status'] == 'on') ? true : false,
                // 'offer_price' => $request['offer_price'] ? $request['offer_price'] : $model->offer->offer_price,
                'start_at' => $request['start_at'] ? $request['start_at'] : $model->offer->start_at,
                'end_at' => $request['end_at'] ? $request['end_at'] : $model->offer->end_at,
            ];

            if ($request['offer_type'] == 'amount' && !is_null($request['offer_price'])) {
                $data['offer_price'] = $request['offer_price'];
                $data['percentage'] = null;
            } elseif ($request['offer_type'] == 'percentage' && !is_null($request['offer_percentage'])) {
                $data['offer_price'] = null;
                $data['percentage'] = $request['offer_percentage'];
            } else {
                $data['offer_price'] = null;
                $data['percentage'] = null;
            }

            $model->offer()->updateOrCreate(['product_id' => $model->id], $data);
        } else {
            if ($model->offer) {
                $model->offer()->delete();
            }
        }
    }

    public function productAvailability($model, $request)
    {
        if (isset($request['availability']) && !empty($request['availability'])) {
            $model->availability_times()->delete();
            $data = [];
            foreach($request['availability'] as $day => $dayTimes){
                if(in_array($day,$request['days_status'])){
                    for ($i = 0; $i < count($dayTimes['time_from']) ; $i++) {
                        $data[] = [
                            'product_id'    => $model->id,
                            'day'   => $day,
                            'time_from' => date('H:i',strtotime($dayTimes['time_from'][$i])),
                            'time_to' => date('H:i',strtotime($dayTimes['time_to'][$i])),
                            'type' => $dayTimes['type'],
                        ];
                    }
                }
            }
            $model->availability_times()->insert($data);
        }
    }

    public function variationOffer($model, $request)
    {

        if (isset($request['status']) && $request['status'] == 'on') {

            $model->offer()->updateOrCreate(
                ['product_variant_id' => $model->id],
                [
                    'status' => ($request['status'] == 'on') ? true : false,
                    'offer_price' => $request['offer_price'] ? $request['offer_price'] : $model->offer->offer_price,
                    'start_at' => $request['start_at'] ? $request['start_at'] : $model->offer->start_at,
                    'end_at' => $request['end_at'] ? $request['end_at'] : $model->offer->end_at,
                ]
            );
        } else {
            if ($model->offer) {
                $model->offer()->delete();
            }
        }
    }

    public function getProductDetailsById($id)
    {
        $product = $this->product->query();

        $product = $product->with([
            'categories',
            'vendor',
            'tags',
            'images',
            'offer',
            'variants' => function ($q) {
                $q->with(['offer', 'productValues' => function ($q) {
                    $q->with(['productOption.option', 'optionValue']);
                }]);
            },
            'addOns' => function ($q) {
                $q->with('addOnOptions');
            }
        ]);

        $product = $product->find($id);
        return $product;
    }
}
