<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Bird extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'bird';
	protected $primaryKey = '_id';
}