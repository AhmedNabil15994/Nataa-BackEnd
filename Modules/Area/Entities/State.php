<?php

namespace Modules\Area\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\ScopesTrait;
use Modules\User\Entities\User;
use Modules\Vendor\Entities\DeliveryCharge;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorDeliveryCharge;

class State extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, ScopesTrait;

    protected $with = ['translations'];
    protected $fillable = ['status', 'city_id'];
    public $translatedAttributes = ['title', 'slug'];
    public $translationModel = StateTranslation::class;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function deliveryCharge()
    {
        return $this->hasOne(\Modules\Company\Entities\DeliveryCharge::class, 'state_id');
    }

    public function vendorDeliveryCharge()
    {
        return $this->hasOne(VendorDeliveryCharge::class, 'state_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_states');
    }

    public function stateDrivers()
    {
        return $this->belongsToMany(User::class, 'driver_state')->withTimestamps();
    }

}
