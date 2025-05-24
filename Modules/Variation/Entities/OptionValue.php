<?php

namespace Modules\Variation\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class OptionValue extends Model implements TranslatableContract
{
  	use Translatable , SoftDeletes , ScopesTrait;

    protected $with               = ['translations'];
  	protected $fillable 					= ['status','option_id'];
  	public $translatedAttributes 	= ['title'];
    public $translationModel 			= OptionValueTranslation::class;

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
