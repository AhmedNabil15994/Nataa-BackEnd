<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderDriver extends Model
{
    protected $guarded = ['id'];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }

    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }
}
