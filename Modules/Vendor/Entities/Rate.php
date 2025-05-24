<?php

namespace Modules\Vendor\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class Rate extends Model
{
    use ScopesTrait;

    protected $fillable = [
        'user_id',
        'vendor_id',
        'order_id',
        'rating',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(\Modules\Vendor\Entities\Vendor::class);
    }

}
