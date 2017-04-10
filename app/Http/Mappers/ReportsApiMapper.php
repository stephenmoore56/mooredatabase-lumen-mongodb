<?php
declare(strict_types=1);

namespace App\Http\Mappers;

use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\DB;

/**
 * Methods that call stored procedures and return arrays to controller methods
 *
 * @package  Mappers
 *
 * @author Steve Moore <stephenmoore56@msn.com>
 */

/**
 * ReportsApiMapper class
 *
 */
class ReportsApiMapper {

	/**
	 * Clear memcached; mainly for testing
	 * @access public
	 */
	public static function clearCache() {
		/** @noinspection PhpUndefinedMethodInspection */
		Cache::flush();
	}

	/**
	 * List species and trips by year
	 * @access  public
	 * @return array
	 */
	public static function speciesByYear(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('time')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'           => '$yearNumber',
									'species'       => array('$addToSet' => '$sighting.aou_list_id'),
									'trips'         => array('$addToSet' => '$sighting.trip_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$project' => array(
									'_id'           => 0,
									'yearNumber'    => '$_id',
									'speciesCount'  => array('$size' => '$species'),
									'tripCount'     => array('$size' => '$trips'),
									'sightingCount' => '$sightingCount',
								),
							),
							array('$sort' => array('yearNumber' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species and trips year-to-date by year
	 * @access  public
	 * @return array
	 */
	public static function speciesYTD(): array {
		$monthDay = date('m-d');
		$cacheKey = __METHOD__ . $monthDay;
		$results = Cache::get($cacheKey, function () use ($cacheKey, $monthDay) {
			$results =
				DB::collection('time')
					->raw(function ($collection) use ($monthDay) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'monthDay' => array('$lte' => $monthDay),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'           => '$yearNumber',
									'species'       => array('$addToSet' => '$sighting.aou_list_id'),
									'trips'         => array('$addToSet' => '$sighting.trip_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$project' => array(
									'_id'           => 0,
									'yearNumber'    => '$_id',
									'speciesCount'  => array('$size' => '$species'),
									'tripCount'     => array('$size' => '$trips'),
									'sightingCount' => '$sightingCount',
									'monthDay'      => array('$literal' => $monthDay),
								),
							),
							array('$sort' => array('yearNumber' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species and trips by month
	 * @access  public
	 * @return array
	 */
	public static function speciesByMonth(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('time')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'           => '$monthNumber',
									'species'       => array('$addToSet' => '$sighting.aou_list_id'),
									'trips'         => array('$addToSet' => '$sighting.trip_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'month',
									'localField'   => '_id',
									'foreignField' => 'monthNumber',
									'as'           => 'month',
								),
							),
							array('$unwind' => '$month'),
							array(
								'$project' => array(
									'_id'           => 0,
									'monthNumber'   => '$_id',
									'monthName'     => '$month.monthName',
									'monthLetter'   => '$month.monthLetter',
									'speciesCount'  => array('$size' => '$species'),
									'tripCount'     => array('$size' => '$trips'),
									'sightingCount' => '$sightingCount',
								),
							),
							array('$sort' => array('monthNumber' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species for month
	 * @access  public
	 * @param int $monthNumber
	 * @return array
	 */
	public static function speciesForMonth(int $monthNumber): array {
		$cacheKey = __METHOD__ . serialize([$monthNumber]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $monthNumber) {
			$results =
				DB::collection('time')
					->raw(function ($collection) use ($monthNumber) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'monthNumber' => array('$eq' => $monthNumber),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'         => '$sighting.aou_list_id',
									'monthNumber' => array('$first' => '$monthNumber'),
									'monthName'   => array('$first' => '$monthName'),
									'last_seen'   => array('$max' => '$sighting_date'),
									'sightings'   => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'sightings'       => '$sightings',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'last_seen'       => '$last_seen',
								'monthName'       => '$monthName',
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species for year
	 * @access  public
	 * @param int $year
	 * @return array
	 */
	public static function speciesForYear(int $year): array {
		$cacheKey = __METHOD__ . serialize([$year]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $year) {
			$results =
				DB::collection('time')
					->raw(function ($collection) use ($year) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'yearNumber' => array('$eq' => $year),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'        => '$sighting.aou_list_id',
									'first_seen' => array('$min' => '$sighting_date'),
									'last_seen'  => array('$max' => '$sighting_date'),
									'sightings'  => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'sightings'       => '$sightings',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'first_seen'      => '$first_seen',
								'last_seen'       => '$last_seen',
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * Species detail
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function speciesDetail(int $speciesId): array {
		$cacheKey = __METHOD__ . serialize([$speciesId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $speciesId) {
			$results =
				DB::collection('bird')
					->raw(function ($collection) use ($speciesId) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'aou_list_id' => array('$eq' => $speciesId),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'sighting',
								),
							),
							array(
								'$unwind' => array(
									'path'                       => '$sighting',
									'preserveNullAndEmptyArrays' => true,
								),
							),
							array(
								'$group' => array(
									'_id'              => '$aou_list_id',
									'earliestSighting' => array('$min' => array('$substr' => ['$sighting.sighting_date', 5, 5])),
									'latestSighting'   => array('$max' => array('$substr' => ['$sighting.sighting_date', 5, 5])),
									'first_seen'       => array('$min' => '$sighting.sighting_date'),
									'last_seen'        => array('$max' => '$sighting.sighting_date'),
									'trips'            => array('$addToSet' => '$sighting.trip_id'),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'              => 0,
								'id'               => '$_id',
								'sightings'        => array('$size' => '$trips'),
								'order_name'       => '$bird.order_name',
								'order_notes'      => '$bird.order_notes',
								'common_name'      => '$bird.common_name',
								'scientific_name'  => '$bird.scientific_name',
								'family'           => '$bird.family',
								'subfamily'        => '$bird.subfamily',
								'first_seen'       => '$first_seen',
								'last_seen'        => '$last_seen',
								'earliestSighting' => '$earliestSighting',
								'latestSighting'   => '$latestSighting',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List months for species
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function monthsForSpecies(int $speciesId): array {
		$cacheKey = __METHOD__ . serialize([$speciesId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $speciesId) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) use ($speciesId) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'aou_list_id' => array('$eq' => $speciesId),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'time',
									'localField'   => 'sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'time',
								),
							),
							array('$unwind' => '$time'),
							array(
								'$group' => array(
									'_id'           => '$time.monthNumber',
									'monthName'     => array('$first' => '$time.monthName'),
									'aou_list_id'   => array('$first' => '$aou_list_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array(
								'$project' => array(
									'_id'           => 0,
									'common_name'   => '$bird.common_name',
									'monthNumber'   => '$_id',
									'monthName'     => '$monthName',
									'sightingCount' => '$sightingCount',
								),
							),
							array('$sort' => array('monthNumber' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species by order
	 * @access  public
	 * @return array
	 */
	public static function speciesByOrder(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('bird')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'           => '$order_id',
									'species'       => array('$addToSet' => '$sighting.aou_list_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'order',
									'localField'   => '_id',
									'foreignField' => 'id',
									'as'           => 'order',
								),
							),
							array('$unwind' => '$order'),
							array('$project' => array(
								'_id'                     => 0,
								'id'                      => '$_id',
								'order_name'              => '$order.order_name',
								'order_notes'             => '$order.notes',
								'order_species_count_all' => '$order.order_species_count_all',
								'speciesCount'            => array('$size' => '$species'),
								'sightingCount'           => '$sightingCount',
							)),
							array('$sort' => array('speciesCount' => -1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species for order
	 * @access  public
	 * @param int $orderId
	 * @return array
	 */
	public static function speciesForOrder(int $orderId): array {
		$cacheKey = __METHOD__ . serialize([$orderId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $orderId) {
			$results =
				DB::collection('bird')
					->raw(function ($collection) use ($orderId) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'order_id' => array('$eq' => $orderId),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'       => '$aou_list_id',
									'last_seen' => array('$max' => '$sighting.sighting_date'),
									'sightings' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'sightings'       => '$sightings',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'last_seen'       => '$last_seen',
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List all species sighted
	 * @access  public
	 * @return array
	 */
	public static function speciesAll(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$group' => array(
									'_id'       => '$aou_list_id',
									'last_seen' => array('$max' => '$sighting_date'),
									'sightings' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'sightings'       => '$sightings',
								'order_id'        => '$bird.order_id',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'last_seen'       => '$last_seen',
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrders(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('order')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$project' => array(
								'_id'        => 0,
								'order_name' => '$order_name',
							)),
							array('$sort' => array('order_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrdersAll(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('order')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$project' => array(
								'_id'        => 0,
								'order_name' => '$order_name',
								'notes'      => '$notes',
								'id'         => '$id',
								'sortkey'    => '$sortkey',
							)),
							array('$sort' => array('sortKey' => 1, 'order_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List order ids
	 * @access  public
	 * @return array
	 */
	public static function listOrderIds(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('order')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$sort' => array('order_id' => 1)),
							array('$project' => array(
								'_id'      => 0,
								'order_id' => '$id',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species ids
	 * @access  public
	 * @return array
	 */
	public static function listSpeciesIds(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$sort' => array('aou_list_id' => 1)),
							array(
								'$group' => array(
									'_id' => '$aou_list_id',
								),
							),
							array('$project' => array(
								'_id'         => 0,
								'aou_list_id' => '$_id',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List location ids
	 * @access  public
	 * @return array
	 */
	public static function listLocationIds(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('location')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$sort' => array('id' => 1)),
							array('$project' => array(
								'_id' => 0,
								'id'  => '$id',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * search complete AOU list
	 * @access  public
	 * @param string $searchString
	 * @param int    $orderId
	 * @return array
	 */
	public static function searchAll(string $searchString, int $orderId): array {
		$cacheKey = __METHOD__ . serialize([$searchString, $orderId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $searchString, $orderId) {
			// add following default filter so match filter is never empty;
			// this criteria should always test true
			$searchString = trim(urldecode($searchString));
			$stringMatch = array(
				array(
					'order_id' => array('$gt' => -1),
				),
			);
			if (trim($searchString) != '') {
				$searchCriteria = array('$regex' => $searchString, '$options' => 'si');
				$stringMatch = array(
					array('common_name' => $searchCriteria),
					array('scientific_name' => $searchCriteria),
					array('family' => $searchCriteria),
					array('family' => $searchCriteria),
				);
			}

			// order id filter
			$orderMatch = array();
			$orderMatch['order_id'] = array('$gt' => -1);
			if ($orderId != -1) {
				$orderMatch['order_id'] = array('$eq' => $orderId);
			}

			$results =
				DB::collection('bird')
					->raw(function ($collection) use ($orderMatch, $stringMatch) {
						return $collection->aggregate(array(
							array('$match' => $orderMatch),
							array('$match' => array(
								'$or' => $stringMatch,
							)),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'sighting',
								),
							),
							array(
								'$unwind' => array(
									'path'                       => '$sighting',
									'preserveNullAndEmptyArrays' => true,
								),
							),
							array(
								'$group' => array(
									'_id'       => '$aou_list_id',
									'last_seen' => array('$max' => '$sighting.sighting_date'),
									'trips'     => array('$addToSet' => '$sighting.trip_id'),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'order_id'        => '$bird.order_id',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'last_seen'       => '$last_seen',
								'sightings'       => array('$size' => '$trips'),
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species and trips by location
	 * @access  public
	 * @return array
	 */
	public static function speciesByLocation(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$group' => array(
									'_id'           => '$location_id',
									'species'       => array('$addToSet' => '$aou_list_id'),
									'trips'         => array('$addToSet' => '$trip_id'),
									'sightingCount' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'location',
									'localField'   => '_id',
									'foreignField' => 'id',
									'as'           => 'location',
								),
							),
							array('$unwind' => '$location'),
							array(
								'$lookup' => array(
									'from'         => 'ecs',
									'localField'   => 'location.ecs_subsection_id',
									'foreignField' => 'subsection_id',
									'as'           => 'ecs',
								),
							),
							array('$unwind' => '$ecs'),
							array('$project' => array(
								'id'                => '$_id',
								'trip_count'        => array('$size' => '$trips'),
								'species_count'     => array('$size' => '$species'),
								'sightingCount'     => '$sightingCount',
								'trips'             => array('$size' => '$trips'),
								'country_code'      => '$location.country_code',
								'state_code'        => '$location.state_code',
								'location_name'     => '$location.location_name',
								'county_name'       => '$location.county_name',
								'notes'             => '$location.notes',
								'latitude'          => '$location.latitude',
								'longitude'         => '$location.longitude',
								'ecs_subsection_id' => '$location.ecs_subsection_id',
								'subsection_id'     => '$ecs.subsection_id',
								'subsection_name'   => '$ecs.subsection_name',
								'subsection_url'    => '$ecs.subsection_url',
								'section_name'      => '$ecs.section_name',
								'section_url'       => '$ecs.section_url',
								'province_name'     => '$ecs.province_name',
								'province_url'      => '$ecs.province_url',
							)),
							array('$sort' => array('location_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species and trips by county
	 * @access  public
	 * @return array
	 */
	public static function speciesByCounty(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('location')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'id',
									'foreignField' => 'location_id',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$group' => array(
									'_id'     => '$county_id',
									'species' => array('$addToSet' => '$sighting.aou_list_id'),
									'trips'   => array('$addToSet' => '$sighting.trip_id'),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'county',
									'localField'   => '_id',
									'foreignField' => 'id',
									'as'           => 'county',
								),
							),
							array('$unwind' => '$county'),
							array('$project' => array(
								'_id'          => 0,
								'county_id'    => '$county_id',
								'tripCount'    => array('$size' => '$trips'),
								'speciesCount' => array('$size' => '$species'),
								'countyName'   => '$county.county_name',
							)),
							array('$sort' => array('speciesCount' => -1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species for location
	 * @access  public
	 * @param int $locationId
	 * @return array
	 */
	public static function speciesForLocation(int $locationId): array {
		$cacheKey = __METHOD__ . serialize([$locationId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $locationId) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) use ($locationId) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'location_id' => array('$eq' => $locationId),
								),
							),
							array(
								'$group' => array(
									'_id'       => '$aou_list_id',
									'last_seen' => array('$max' => '$sighting_date'),
									'sightings' => array('$sum' => 1),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'bird',
									'localField'   => '_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'bird',
								),
							),
							array('$unwind' => '$bird'),
							array('$project' => array(
								'_id'             => 0,
								'id'              => '$_id',
								'sightings'       => '$sightings',
								'order_name'      => '$bird.order_name',
								'order_notes'     => '$bird.order_notes',
								'common_name'     => '$bird.common_name',
								'scientific_name' => '$bird.scientific_name',
								'family'          => '$bird.family',
								'subfamily'       => '$bird.subfamily',
								'last_seen'       => '$last_seen',
							)),
							array('$sort' => array('common_name' => 1)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * Location detail
	 * @access  public
	 * @param int $locationId
	 * @return array
	 */
	public static function locationDetail(int $locationId): array {
		$cacheKey = __METHOD__ . serialize([$locationId]);
		$results = Cache::get($cacheKey, function () use ($cacheKey, $locationId) {
			$results =
				DB::collection('sighting')
					->raw(function ($collection) use ($locationId) {
						return $collection->aggregate(array(
							array(
								'$match' => array(
									'location_id' => array('$eq' => $locationId),
								),
							),
							array(
								'$group' => array(
									'_id'     => '$location_id',
									'species' => array('$addToSet' => '$aou_list_id'),
									'trips'   => array('$addToSet' => '$trip_id'),
								),
							),
							array(
								'$lookup' => array(
									'from'         => 'location',
									'localField'   => '_id',
									'foreignField' => 'id',
									'as'           => 'location',
								),
							),
							array('$unwind' => '$location'),
							array(
								'$lookup' => array(
									'from'         => 'ecs',
									'localField'   => 'location.ecs_subsection_id',
									'foreignField' => 'subsection_id',
									'as'           => 'ecs',
								),
							),
							array('$unwind' => '$ecs'),
							array('$project' => array(
								'_id'                 => 0,
								'id'                  => '$_id',
								'trip_count'          => array('$size' => '$trips'),
								'species_count'       => array('$size' => '$species'),
								'country_code'        => '$location.country_code',
								'state_code'          => '$location.state_code',
								'location_name'       => '$location.location_name',
								'county_name'         => '$location.county_name',
								'notes'               => '$location.notes',
								'latitude'            => '$location.latitude',
								'longitude'           => '$location.longitude',
								'ecs_subsection_id'   => '$location.ecs_subsection_id',
								'ecs_subsection_name' => '$ecs.subsection_name',
								'ecs_subsection_url'  => '$ecs.subsection_url',
								'ecs_section_name'    => '$ecs.section_name',
								'ecs_section_url'     => '$ecs.section_url',
								'ecs_province_name'   => '$ecs.province_name',
								'ecs_province_url'    => '$ecs.province_url',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List average / record high and low temperatures for each month
	 * @access  public
	 * @return array
	 */
	public static function monthlyTemperatures(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('temperature')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$sort' => array('monthNumber' => 1)),
							array('$project' => array(
								'_id'              => 0,
								'monthNumber'      => '$monthNumber',
								'avg_low_temp'     => '$avg_low_temp',
								'avg_high_temp'    => '$avg_high_temp',
								'record_low_temp'  => '$record_low_temp',
								'record_high_temp' => '$record_high_temp',
								'days_with_frost'  => '$days_with_frost',
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * List species for order
	 * @access  public
	 * @return array
	 */
	public static function ducksAndWarblers(): array {
		$cacheKey = __METHOD__;
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$results =
				DB::collection('bird')
					->raw(function ($collection) {
						return $collection->aggregate(array(
							array('$match' => array(
								'$or' => array(
									array(
										'family' => array('$eq' => 'Anatidae'),
									),
									array(
										'family' => array('$eq' => 'Parulidae'),
									),
								),
							),
							),
							array(
								'$lookup' => array(
									'from'         => 'sighting',
									'localField'   => 'aou_list_id',
									'foreignField' => 'aou_list_id',
									'as'           => 'sighting',
								),
							),
							array('$unwind' => '$sighting'),
							array(
								'$lookup' => array(
									'from'         => 'time',
									'localField'   => 'sighting.sighting_date',
									'foreignField' => 'sighting_date',
									'as'           => 'time',
								),
							),
							array('$unwind' => '$time'),
							array(
								'$group' => array(
									'_id'         => array(
										'family'      => '$family',
										'monthNumber' => '$time.monthNumber',
									),
									'monthLetter' => array('$first' => '$time.monthLetter'),
									'species'     => array('$addToSet' => '$aou_list_id'),
								),
							),
							array('$project' => array(
								'_id'          => 0,
								'family'       => '$_id.family',
								'monthNumber'  => '$_id.monthNumber',
								'monthLetter'  => '$monthLetter',
								'speciesCount' => array('$size' => '$species'),
							)),
							array('$sort' => array(
								'monthNumber' => 1,
								'family'      => 1,
							)),
						));
					})
					->toArray();
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

}