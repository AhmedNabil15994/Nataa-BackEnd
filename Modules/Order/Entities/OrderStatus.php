<?php

namespace Modules\Order\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\ScopesTrait;

class OrderStatus extends Model implements TranslatableContract
{
    use Translatable, ScopesTrait;

    protected $with = ['translations'];
    protected $guarded = ['id'];
    public $translatedAttributes = ['title'];
    public $translationModel = OrderStatusTranslation::class;

    public function orderStatusesHistory()
    {
        return $this->belongsToMany(Order::class, 'order_statuses_history')->withPivot(['user_id'])->withTimestamps();
    }
}
