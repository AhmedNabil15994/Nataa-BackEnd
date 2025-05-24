<?php

namespace Modules\Authorization\Entities;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole implements TranslatableContract
{
		use Translatable;

		protected $with 					    = ['translations'];
		protected $fillable 					= ['name'];
		public $translatedAttributes 	= ['display_name','description'];
		public $translationModel 			= RoleTranslation::class;

}
