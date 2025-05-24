<?php

namespace Modules\Area\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Country extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    protected $fillable = ['status', 'code'];
    public $translatedAttributes = ['title', 'slug'];
    public $translationModel = CountryTranslation::class;

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
    }

    public function states()
    {
        return $this->hasManyThrough(State::class, City::class);
    }

}
