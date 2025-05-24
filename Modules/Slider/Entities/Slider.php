<?php

namespace Modules\Slider\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Slider extends Model
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    protected $table = 'sliders';
    protected $guarded = ['id'];
    public $translatedAttributes = ['title', 'short_description', 'slug'];
    public $translationModel = SliderTranslation::class;
    protected $appends = ['morph_model'];

    public function getMorphModelAttribute()
    {
        return !is_null($this->sliderable) ? (new \ReflectionClass($this->sliderable))->getShortName() : null;
    }

    public function sliderable()
    {
        return $this->morphTo();
    }
}
