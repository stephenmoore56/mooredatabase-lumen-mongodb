<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Location extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'location';
	protected $primaryKey = '_id';

	public function ecs()
	{
		return $this->belongsTo('App\ECS','ecs_subsection_id','subsection_id');
	}

}