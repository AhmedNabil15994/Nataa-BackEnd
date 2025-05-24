<?php

namespace Modules\Authorization\Entities;

use Illuminate\Database\Eloquent\Model;

class PermissionTranslation extends Model
{
		  protected $fillable = ['description','permission_id'];
}
