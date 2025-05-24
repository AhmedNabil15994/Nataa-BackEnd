<?php

namespace Modules\Advertising\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Modules\Advertising\Entities\Advertising;
use Modules\Core\Traits\ScopesTrait;

class AdvertisingGroup extends Model
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    protected $table = 'advertising_groups';
    protected $fillable = ['position', 'sort', 'status'];
    protected $translationForeignKey = 'ad_group_id';

    public $translatedAttributes = [
        'title', 'slug',
    ];
    public $translationModel = AdvertisingGroupTranslation::class;

    public function adverts()
    {
        return $this->hasMany(Advertising::class, 'ad_group_id');
    }

    public function getPositionAttribute($value)
    {
        switch ($value) {
            case "home":
                return __('advertising::dashboard.advertising_groups.form.home');
            case "categories":
                return __('advertising::dashboard.advertising_groups.form.categories');
            default:
                return null;
        }
    }
}
