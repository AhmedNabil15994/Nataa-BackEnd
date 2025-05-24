<?php

namespace Modules\Company\Entities;

use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->whereNotNull('delivery');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
