<?php

namespace Modules\Catalog\Entities;

use Illuminate\Database\Eloquent\Model;

class AddOnTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
