<?php

namespace Modules\Vendor\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Area\Entities\State;
use Modules\Company\Entities\Company;
use Modules\Core\Traits\ScopesTrait;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderProduct;
use Modules\Order\Entities\OrderVariantProduct;
use Modules\Order\Entities\OrderVendor;
use Modules\User\Entities\User;

class Vendor extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    public $translationModel = VendorTranslation::class;
    public $translatedAttributes = [
        'description', 'title', 'slug', 'seo_description', 'seo_keywords'
    ];
    protected $guarded = ['id'];

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'vendor_payments');
    }

    public function openingStatus()
    {
        return $this->belongsTo(VendorStatus::class, 'vendor_status_id');
    }

    public function sellers()
    {
        return $this->belongsToMany(\Modules\User\Entities\User::class, 'vendor_sellers', 'vendor_id', 'seller_id')->withTimestamps();
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'vendor_sections');
    }

    public function subbscription()
    {
        return $this->hasOne(\Modules\Subscription\Entities\Subscription::class)->latest();
    }

    public function subscriptionHistory()
    {
        return $this->hasMany(\Modules\Subscription\Entities\SubscriptionHistory::class);
    }

    public function products()
    {
        return $this->hasMany(\Modules\Catalog\Entities\Product::class);
    }

    public function subscribed()
    {
        return $this->subbscription()->active()->unexpired()->started();
    }

    public function rates()
    {
        return $this->hasMany(\Modules\Vendor\Entities\Rate::class, 'vendor_id');
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'vendor_companies');
    }

    public function states()
    {
        return $this->belongsToMany(State::class, 'vendor_states');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_vendors');
    }

    public function orderProducts()
    {
        $request =  request();
        $base = $this->hasMany(OrderProduct::class, 'vendor_id');
        if ($request->order_id) {
            $base->where("order_products.order_id", $request->order_id);
        }
        return $base;
    }

    public function orderVariations()
    {
        $request =  request();
        $base = $this->hasMany(OrderVariantProduct::class, 'vendor_id');
        if ($request->order_id) {
            $base->where("order_variant_products.order_id", $request->order_id);
        }
        return $base;
    }

    public function deliveryCharge()
    {
        return $this->hasMany(VendorDeliveryCharge::class, 'vendor_id');
    }

    public function categories()
    {
        return $this->belongsToMany(VendorCategory::class, 'vendor_categories_pivot')->withTimestamps();
    }

    public function subCategories()
    {
        return $this->belongsToMany(VendorCategory::class, 'vendor_categories_pivot')
            ->whereNotNull('vendor_categories.vendor_category_id')->withTimestamps();
    }

    public function parentCategories()
    {
        return $this->belongsToMany(VendorCategory::class, 'vendor_categories_pivot')
            ->whereNull('vendor_categories.vendor_category_id')->withTimestamps();
    }

    public function drivers()
    {
        return $this->belongsToMany(User::class, 'vendor_drivers')->withTimestamps();
    }

    public function workTimes()
    {
        return $this->hasMany(VendorWorkTime::class, 'vendor_id');
    }
}
