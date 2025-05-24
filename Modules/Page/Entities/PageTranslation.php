<?php

namespace Modules\Page\Entities;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use HasSlug;

    protected $fillable = ['description' , 'title' , 'slug' , 'seo_description' , 'seo_keywords' , 'page_id'];

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
