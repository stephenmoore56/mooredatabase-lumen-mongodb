<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Sighting extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'sighting';
	protected $primaryKey = '_id';

}