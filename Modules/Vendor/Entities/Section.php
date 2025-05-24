<?php

namespace Modules\Vendor\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Section extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];

    protected $fillable = ['status', 'image'];
    public $translatedAttributes = ['description', 'title', 'slug', 'seo_description', 'seo_keywords'];
    public $translationModel = SectionTranslation::class;


    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_sections');
    }
}
