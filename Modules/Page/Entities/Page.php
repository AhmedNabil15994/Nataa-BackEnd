<?php

namespace Modules\Page\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Page extends Model implements TranslatableContract
{
  	use Translatable , SoftDeletes , ScopesTrait;

    protected $with               = ['translations'];
  	protected $fillable 					= ['status','type','page_id'];
  	public $translatedAttributes 	= ['description' , 'title' , 'slug' , 'seo_description' , 'seo_keywords'];
    public $translationModel 			= PageTranslation::class;

}
