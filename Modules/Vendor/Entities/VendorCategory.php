<?php

namespace Modules\Vendor\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Advertising\Entities\Advertising;
use Modules\Core\Traits\ScopesTrait;
use Modules\Notification\Entities\GeneralNotification;
use Modules\Occasion\Entities\Occasion;
use Modules\Slider\Entities\Slider;

class VendorCategory extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations', 'children'];
    // protected $with = ['translations','children','parent','vendors'];
    protected $fillable = ['status', 'show_in_home', 'image', 'cover', 'vendor_category_id', 'color', 'sort'];
    public $translatedAttributes = ['title', 'slug', 'seo_description', 'seo_keywords'];
    public $translationModel = VendorCategoryTranslation::class;

    public function scopeMainCategories($query)
    {
        return $query->whereNull('vendor_category_id');
    }

    public function parent()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_categories_pivot')->withTimestamps();
    }

    public function getParentsAttribute()
    {
        $parents = collect([]);
        $parent = $this->parent;
        while (!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }
        return $parents;
    }

    public function children()
    {
        $categories = $this->hasMany(VendorCategory::class, 'vendor_category_id');
        if (!is_null(request()->route()) && in_array(request()->route()->getName(), ['api.home', 'frontend.home'])) {
            $categories = $categories->where('show_in_home', 1);
        }

        if (!is_null(request()->route()) && !request()->is(locale() . '/dashboard/*')) {
            $categories = $categories->whereHas('vendors', function ($query) {
                $query->active();
            });
        }

        // Get Child Category vendors
        $categories = $categories->with([
            'vendors' => function ($query) {
                $query->active();
                $query->orderBy('id', 'DESC');
            },
        ]);
        return $categories;
    }

    public function childrenRecursive()
    {
        return $this->children()->active()->with('childrenRecursive');
    }

    public function subCategories()
    {
        return $this->hasMany(VendorCategory::class, 'vendor_category_id')
            ->has('vendors')
            ->whereNotNull('vendor_categories.vendor_category_id');
    }

    public function getAllRecursiveChildren()
    {
        $category = new Collection();
        foreach ($this->children as $cat) {
            $category->push($cat);
            $category = $category->merge($cat->getAllRecursiveChildren());
        }
        return $category;
    }

    public function adverts()
    {
        return $this->morphMany(Advertising::class, 'advertable');
    }

    public function generalNotifications()
    {
        return $this->morphMany(GeneralNotification::class, 'notifiable');
    }

    public function sliders()
    {
        return $this->morphMany(Slider::class, 'sliderable');
    }

}
