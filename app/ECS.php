<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ECS extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'ecs';
	protected $primaryKey = '_id';

	public function locations()
	{
		return $this->hasMany('App\Location','ecs_subsection_id','subsection_id');
	}

}