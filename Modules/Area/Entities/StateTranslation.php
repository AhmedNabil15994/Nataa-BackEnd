<?php

namespace Modules\Area\Entities;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class StateTranslation extends Model
{
    use HasSlug;

    protected $fillable = [ 'title' , 'slug' , 'seo_description' , 'seo_keywords'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
