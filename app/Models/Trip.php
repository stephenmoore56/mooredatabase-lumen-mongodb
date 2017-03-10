<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Trip extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'trip';
	protected $primaryKey = '_id';

}