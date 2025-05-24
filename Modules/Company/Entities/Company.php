<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Core\Traits\ScopesTrait;
use Modules\User\Entities\User;
use Modules\Vendor\Entities\Vendor;

class Company extends Model
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];

    protected $fillable = [
        'manager_name', 'image', 'status', 'email', 'password', 'calling_code', 'mobile','user_id'
    ];

    public $translatedAttributes = [
        'name', 'description',
    ];

    public $translationModel = CompanyTranslation::class;

    public function deliveryCharge()
    {
        return $this->hasMany(DeliveryCharge::class, 'company_id');
    }

    public function drivers()
    {
        return $this->hasMany(\Modules\User\Entities\User::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_companies');
    }

    public function availabilities()
    {
        return $this->hasMany(CompanyAvailability::class, 'company_id');
    }


}
