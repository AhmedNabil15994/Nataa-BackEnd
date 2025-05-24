<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderVariantProduct extends Model
{
    protected $guarded = ['id'];
    
    public function variant()
    {
        return $this->belongsTo(\Modules\Variation\Entities\ProductVariant::class, 'product_variant_id');
    }

    public function orderVariantValues()
    {
        return $this->hasMany(OrderVariantProductValue::class);
    }
}
