<?php

namespace Modules\Vendor\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class VendorStatus extends Model implements TranslatableContract
{
    use Translatable, ScopesTrait;

    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];
    public $translationModel = VendorStatusTranslation::class;

}
