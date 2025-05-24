<?php

namespace Modules\Authorization\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
    protected $fillable = ['display_name','description'];
}
