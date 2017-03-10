<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Order extends Eloquent {

	protected $connection = 'mongodb';
	protected $collection = 'order';
	protected $primaryKey = '_id';

}