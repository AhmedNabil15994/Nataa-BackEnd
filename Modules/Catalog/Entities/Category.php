<?php

namespace Modules\Catalog\Entities;

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

class Category extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations', 'children'];
    // protected $with 					    = ['translations','children','parent','products'];
    protected $fillable = ['status', 'show_in_home', 'image', 'cover', 'category_id', 'color', 'sort'];
    public $translatedAttributes = ['title', 'slug', 'seo_description', 'seo_keywords'];
    public $translationModel = CategoryTranslation::class;

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
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

    public function occasions()
    {
        return $this->hasMany(Occasion::class, 'category_id');
    }

    public function children()
    {
        $vendor = app('vendorObject') ?? null;

        $categories = $this->hasMany(Category::class, 'category_id')->withCount(['products' => function ($q) use ($vendor) {
            $q->active();
            if (!is_null($vendor)) {
                $q->where('vendor_id', $vendor->id);
            }
        }]);

        if (!is_null(request()->route()) && in_array(request()->route()->getName(), ['api.home', 'frontend.home'])) {
            $categories = $categories->where('show_in_home', 1);
        }

        if (!is_null(request()->route()) && !request()->is(locale() . '/dashboard/*')) {
            $categories = $categories->whereHas('products', function ($query) {
                $query->active();
            });
        }

        // Get Child Category Products
        $categories = $categories->with([
            'products' => function ($query) use ($vendor) {
                $query->active()
                    ->with([
                        'offer' => function ($query) {
                            $query->active()->unexpired()->started();
                        },
                        'options',
                        'images',
                        'vendor',
                        'variants' => function ($q) {
                            $q->with(['offer' => function ($q) {
                                $q->active()->unexpired()->started();
                            }]);
                        },
                    ]);
                if (!is_null($vendor)) {
                    $query->where('vendor_id', $vendor->id);
                }
                $query->orderBy('id', 'DESC')/*->limit(10)*/
                ;
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
        return $this->hasMany(Category::class, 'category_id')
            ->has('products')
            ->whereNotNull('categories.category_id');
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
