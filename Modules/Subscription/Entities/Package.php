<?php

namespace Modules\Subscription\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Package extends Model implements TranslatableContract
{
  	use Translatable , SoftDeletes , ScopesTrait;

    protected $with               = ['translations'];
  	protected $fillable 					= ['status' , 'price' , 'special_price' , 'months'];
  	public $translatedAttributes 	= ['description' , 'title' , 'slug' , 'seo_description' , 'seo_keywords'];
    public $translationModel 			= PackageTranslation::class;

}
