<?php

namespace Modules\Vendor\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Payment extends Model implements TranslatableContract
{
    use Translatable , ScopesTrait;

    protected $fillable = [ 'code' , 'image' ];

    public $translatedAttributes 	= [ 'title' ];
    public $translationModel 			= PaymentTranslation::class;


}
