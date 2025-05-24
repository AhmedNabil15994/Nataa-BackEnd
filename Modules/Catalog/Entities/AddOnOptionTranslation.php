<?php

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;

class AddOnOptionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['option'];
}
