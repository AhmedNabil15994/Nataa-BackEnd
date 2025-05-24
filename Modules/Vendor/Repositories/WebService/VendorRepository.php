<?php

namespace Modules\Vendor\Repositories\WebService;

use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\Section;
use Modules\Vendor\Entities\VendorCategory;
use Modules\Vendor\Entities\VendorDeliveryCharge;

class VendorRepository
{
    protected $section;
    protected $vendor;
    protected $category;
    protected $deliveryCharge;

    function __construct(Vendor $vendor, Section $section, VendorCategory $category, VendorDeliveryCharge $deliveryCharge)
    {
        $this->section = $section;
        $this->vendor = $vendor;
        $this->category = $category;
        $this->deliveryCharge = $deliveryCharge;
    }

    public function getAllSections($request)
    {
        $sections = $this->section->active()->with([
            'vendors' => function ($query) use ($request) {
                $query->active();

                $query->when(!is_null($request->state_id), function ($query) use ($request) {
                    $query->whereHas('deliveryCharge', function ($query) use ($request) {
                        $query->active()->where('state_id', $request->state_id);
                    });
                });

                $query->with(['deliveryCharge' => function ($query) use ($request) {
                    $query->when(!is_null($request->state_id), function ($query) use ($request) {
                        $query->active()->where('state_id', $request->state_id);
                    });
                }]);

                $query->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
                    return $q->whereHas('subbscription', function ($query) {
                        $query->active()->unexpired()->started();
                    });
                });
            },
        ]);

        $sections = $sections->whereHas('vendors', function ($query) use ($request) {
            $query->active();
            $query->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
                return $q->whereHas('subbscription', function ($query) {
                    $query->active()->unexpired()->started();
                });
            });

            $query->when(!is_null($request->state_id), function ($q) use ($request) {
                return $q->whereHas('deliveryCharge', function ($query) use ($request) {
                    $query->active()->where('state_id', $request->state_id);
                });
            });
        });

        if (isset($request->sections_count) && !is_null($request->sections_count))
            $sections = $sections->take($request->sections_count ?? 10);

        return $sections->get();
    }

    public function getAllVendorsCategories($request)
    {
        $categories = $this->category->active()->with([
            'vendors' => function ($query) use ($request) {
                $query->active();

                $query->when(!is_null($request->state_id), function ($query) use ($request) {
                    $query->whereHas('deliveryCharge', function ($query) use ($request) {
                        $query->active()->where('state_id', $request->state_id);
                    });
                });

                $query->with(['deliveryCharge' => function ($query) use ($request) {
                    $query->when(!is_null($request->state_id), function ($query) use ($request) {
                        $query->active()->where('state_id', $request->state_id);
                    });
                }]);

                $query->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
                    return $q->whereHas('subbscription', function ($query) {
                        $query->active()->active()->unexpired()->started();
                    });
                });
            },
        ]);

        // $categories = $categories->where('show_in_home', 1);

        return $categories->whereHas('vendors', function ($query) use ($request) {
            $query->active();
            $query->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
                return $q->whereHas('subbscription', function ($query) {
                    $query->active()->unexpired()->started();
                });
            });

            $query->when(!is_null($request->state_id), function ($q) use ($request) {
                return $q->whereHas('deliveryCharge', function ($query) use ($request) {
                    $query->active()->where('state_id', $request->state_id);
                });
            });
        })->orderBy('sort', 'asc')->get();
    }

    public function getAllVendors($request)
    {
        $vendors = $this->vendor->active();

        if (isset($request['section_id']) && !empty($request['section_id'])) {
            $vendors = $vendors->with(['sections' => function ($query) use ($request) {
                $query->active();
                if ($request['section_id']) {
                    $query->where('vendor_sections.section_id', $request['section_id']);
                }
            }]);
        }

        $vendors = $vendors->with(['categories' => function ($query) use ($request) {
            $query->active();
            if ($request['category_id']) {
                $query->where('vendor_categories_pivot.vendor_category_id', $request['category_id']);
            }
        }]);

        $vendors = $vendors->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
            return $q->whereHas('subbscription', function ($query) {
                $query->active()->unexpired()->started();
            });
        });

        if ($request->with_products == 'yes') {
            // Get Vendor Products
            $vendors = $vendors->with([
                'products' => function ($query) use ($request) {
                    $query->active();
                    $query = $this->returnProductRelations($query, $request);
                    $query->orderBy('products.id', 'DESC');
                },
            ]);
        }

        if (isset($request['section_id']) && !empty($request['section_id'])) {
            $vendors->whereHas('sections', function ($query) use ($request) {
                $query->where('section_id', $request['section_id']);
            });
        }

        if ($request['category_id']) {
            $vendors->whereHas('categories', function ($query) use ($request) {
                $query->where('vendor_categories_pivot.vendor_category_id', $request['category_id']);
            });
        }

        if ($request['state_id']) {
            $vendors->with([
                'deliveryCharge' => function ($query) use ($request) {
                    $query->active()->where('state_id', $request->state_id);
                }
            ]);
            $vendors->whereHas('deliveryCharge', function ($query) use ($request) {
                $query->active()->where('state_id', $request->state_id);
            });
        }

        if ($request['search']) {
            $vendors->whereHas('translations', function ($query) use ($request) {

                $query->where('description', 'like', '%' . $request['search'] . '%');
                $query->orWhere('title', 'like', '%' . $request['search'] . '%');
                $query->orWhere('slug', 'like', '%' . $request['search'] . '%');

            });
        }

        return $vendors->orderBy('id', 'ASC')->get();
    }

    public function getOneVendor($request)
    {
        $vendor = $this->vendor->active();
        $vendor = $vendor->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
            return $q->whereHas('subbscription', function ($query) {
                $query->active()->unexpired()->started();
            });
        });
        return $vendor->find($request->id);
    }

    /*public function getDeliveryChargesByVendorByState($request)
    {
        $charge = $this->charge
            ->where('vendor_id', $request['vendor_id'])
            ->where('state_id', $request['state_id'])
            ->first();

        return $charge;
    }*/

    public function findById($id)
    {
        $vendor = $this->vendor->with(['companies' => function ($q) {
            $q->with('deliveryCharge', 'availabilities');
        }]);

        $vendor = $vendor->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
            return $q->whereHas('subbscription', function ($query) {
                $query->active()->unexpired()->started();
            });
        });

        return $vendor->find($id);
    }

    public function findVendorByIdAndStateId($id, $stateId)
    {
        $vendor = $this->vendor
            ->with(['companies' => function ($q) use ($stateId) {
                $q->active();
                $q->whereHas('deliveryCharge', function ($query) use ($stateId) {
                    $query->active()->where('state_id', $stateId);
                });
                $q->has('availabilities');
            }]);

        $vendor = $vendor->when(config('setting.other.enable_subscriptions') == 1, function ($q) {
            return $q->whereHas('subbscription', function ($query) {
                $query->active()->unexpired()->started();
            });
        });

        $vendor = $vendor->whereHas('states', function ($query) use ($stateId) {
            $query->where('state_id', $stateId);
        });

        return $vendor->find($id);
    }

    public function returnProductRelations($model, $request = null)
    {
        return $model->with([
            'offer' => function ($query) {
                $query->active()->unexpired()->started();
            },
            'options',
            'images',
            'vendor',
            'subCategories',
            'addOns',
            'variants' => function ($q) {
                $q->with(['offer' => function ($q) {
                    $q->active()->unexpired()->started();
                }]);
            },
        ]);
    }

    public function getDeliveryPrice($stateId, $vendorId)
    {
        return $this->deliveryCharge::active()
            ->where('state_id', $stateId)
            ->where('vendor_id', $vendorId)
            ->value('delivery');
    }

}
