<?php

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class ProductAvailability extends Model
{
    use ScopesTrait;

    protected $table = 'product_availability_times';
    protected $fillable = ['product_id', 'day', 'time_from', 'time_to', 'type'];

    public function scopeUnexpired($query)
    {
        return $query->where('time_from', '<=', date('H:i A'))->where('time_to', '>', date('H:i A'));
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
