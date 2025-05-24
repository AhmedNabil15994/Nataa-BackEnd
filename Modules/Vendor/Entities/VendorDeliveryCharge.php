<?php

namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class VendorDeliveryCharge extends Model
{
    use ScopesTrait;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', true)->whereNotNull('delivery');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

}
