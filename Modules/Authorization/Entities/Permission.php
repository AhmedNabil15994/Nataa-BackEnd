<?php

namespace Modules\Authorization\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission implements TranslatableContract
{
  	use Translatable;

    protected $with 					    = ['translations'];
  	protected $fillable 					= ['display_name','name'];
  	public $translatedAttributes 	= ['description'];
    public $translationModel 			= PermissionTranslation::class;

}
