<?php

namespace Modules\Variation\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Catalog\Entities\Product;
use Modules\Core\Traits\ScopesTrait;

class Option extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    protected $fillable = ['status', 'option_as_filter'];
    public $translatedAttributes = ['title'];
    public $translationModel = OptionTranslation::class;

    public function scopeActiveInFilter($query)
    {
        return $query->where('option_as_filter', true);
    }

    public function scopeUnActiveInFilter($query)
    {
        return $query->where('option_as_filter', false);
    }

    public function values()
    {
        return $this->hasMany(OptionValue::class);
    }

    public function productOptions()
    {
        return $this->belongsToMany(Product::class, 'product_options');
    }
}
