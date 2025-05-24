<?php

namespace Modules\Catalog\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class AddOn extends Model
{
    use Translatable, ScopesTrait;
    protected $with = ['translations'];
    protected $fillable = ['product_id', 'type', 'options_count'];
    public $translatedAttributes = ['name'];
    public $translationModel = AddOnTranslation::class;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function addOnOptions()
    {
        return $this->hasMany(AddOnOption::class, 'add_on_id');
    }

}
