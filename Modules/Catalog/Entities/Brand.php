<?php

namespace Modules\Catalog\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Brand extends Model implements TranslatableContract
{
    use Translatable , SoftDeletes , ScopesTrait;

    protected $with 					    = ['translations'];
  	protected $fillable 					= ['status' , 'image' ,'blogable_type' ,'blogable_id'];
  	public $translatedAttributes 	= [ 'title' , 'slug' , 'seo_description' , 'seo_keywords'];
    public $translationModel 			= BrandTranslation::class;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
