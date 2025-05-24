<?php

namespace Modules\Catalog\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class AddOnOption extends Model
{
    use Translatable, ScopesTrait;

    public $timestamps = false;
    protected $with = ['translations'];
    protected $fillable = ['add_on_id', 'price', 'default', 'image'];
    public $translatedAttributes = ['option'];
    public $translationModel = AddOnOptionTranslation::class;
}
