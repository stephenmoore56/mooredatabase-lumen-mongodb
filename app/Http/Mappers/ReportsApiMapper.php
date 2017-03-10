<?php
declare(strict_types = 1);

namespace App\Http\Mappers;

use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\DB;

/** models; for future reference */
use App\Bird as Bird;
use App\Location as Location;

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
		return
			DB::connection('mongodb')
				->collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'     => '$yearNumber',
								'species' => array('$addToSet' => '$aou_list_id'),
								'trips'   => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								"_id"          => 0,
								'yearNumber'   => '$_id',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
					));
				})
				->toArray();
	}

	/**
	 * List species and trips by month
	 * @access  public
	 * @return array
	 */
	public static function speciesByMonth(): array {
		return
			DB::connection('mongodb')
				->collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'         => '$monthNumber',
								'monthName'   => array('$first' => '$monthName'),
								'monthLetter' => array('$first' => '$monthLetter'),
								'species'     => array('$addToSet' => '$aou_list_id'),
								'trips'       => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								"_id"          => 0,
								'monthNumber'  => '$_id',
								'monthName'    => '$monthName',
								'monthLetter'  => '$monthLetter',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
					));
				})
				->toArray();
	}

	/**
	 * List species for month
	 * @access  public
	 * @param int $monthNumber
	 * @return array
	 */
	public static function speciesForMonth(int $monthNumber): array {
		return DB::connection('mongodb')
			->collection('sighting')
			->raw(function ($collection) use ($monthNumber) {
				return $collection->aggregate(array(
					array(
						'$match' => array(
							'monthNumber' => array('$eq' => $monthNumber),
						),
					),
					array(
						'$group' => array(
							'_id'       => '$aou_list_id',
							'monthName' => array('$first' => '$monthName'),
							'last_seen' => array('$max' => '$sighting_date'),
							'sightings' => array('$sum' => 1),
						),
					),
					array(
						'$lookup' => array(
							'from'         => "bird",
							'localField'   => "_id",
							'foreignField' => "aou_list_id",
							'as'           => "bird",
						),
					),
					array('$unwind' => '$bird'),
					array('$project' => array(
						"_id"             => 0,
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
	}

	/**
	 * List species for year
	 * @access  public
	 * @param int $year
	 * @return array
	 */
	public static function speciesForYear(int $year): array {
		return DB::connection('mongodb')
			->collection('sighting')
			->raw(function ($collection) use ($year) {
				return $collection->aggregate(array(
					array(
						'$match' => array(
							'yearNumber' => array('$eq' => $year),
						),
					),
					array(
						'$group' => array(
							'_id'        => '$aou_list_id',
							'monthName'  => array('$first' => '$monthName'),
							'first_seen' => array('$min' => '$sighting_date'),
							'last_seen'  => array('$max' => '$sighting_date'),
							'sightings'  => array('$sum' => 1),
						),
					),
					array(
						'$lookup' => array(
							'from'         => "bird",
							'localField'   => "_id",
							'foreignField' => "aou_list_id",
							'as'           => "bird",
						),
					),
					array('$unwind' => '$bird'),
					array('$project' => array(
						"_id"             => 0,
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
						'monthName'       => '$monthName',
					)),
					array('$sort' => array('common_name' => 1)),
				));
			})
			->toArray();
	}

	/**
	 * Species detail
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function speciesDetail(int $speciesId): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_getSpecies2(?);', [$speciesId]);
	}

	/**
	 * List months for species
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function monthsForSpecies(int $speciesId): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listMonthsForSpecies2(?);', [$speciesId]);
	}

	/**
	 * List months for species
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function sightingsByMonth(int $speciesId): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listMonthsForSpecies(?);', [$speciesId]);
	}

	/**
	 * List species by order
	 * @access  public
	 * @return array
	 */
	public static function speciesByOrder(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesByOrder();');
	}

	/**
	 * List species for order
	 * @access  public
	 * @param int $orderId
	 * @return array
	 */
	public static function speciesForOrder(int $orderId): array {
		return DB::connection('mongodb')
			->collection('sighting')
			->raw(function ($collection) use ($orderId) {
				return $collection->aggregate(array(
					array(
						'$match' => array(
							'order_id' => array('$eq' => $orderId),
						),
					),
					array(
						'$group' => array(
							'_id'       => '$aou_list_id',
							'monthName' => array('$first' => '$monthName'),
							'last_seen' => array('$max' => '$sighting_date'),
							'sightings' => array('$sum' => 1),
						),
					),
					array(
						'$lookup' => array(
							'from'         => "bird",
							'localField'   => "_id",
							'foreignField' => "aou_list_id",
							'as'           => "bird",
						),
					),
					array('$unwind' => '$bird'),
					array('$project' => array(
						"_id"             => 0,
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
	}

	/**
	 * List all species
	 * @access  public
	 * @return array
	 */
	public static function speciesAll(): array {
		return DB::connection('mongodb')
			->collection('sighting')
			->raw(function ($collection) {
				return $collection->aggregate(array(
					array(
						'$group' => array(
							'_id'       => '$aou_list_id',
							'monthName' => array('$first' => '$monthName'),
							'last_seen' => array('$max' => '$sighting_date'),
							'sightings' => array('$sum' => 1),
						),
					),
					array(
						'$lookup' => array(
							'from'         => "bird",
							'localField'   => "_id",
							'foreignField' => "aou_list_id",
							'as'           => "bird",
						),
					),
					array('$unwind' => '$bird'),
					array('$project' => array(
						"_id"             => 0,
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
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrders(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listOrders();');
	}

	/**
	 * List order ids
	 * @access  public
	 * @return array
	 */
	public static function listOrderIds(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listOrderIds();');
	}

	/**
	 * List species ids
	 * @access  public
	 * @return array
	 */
	public static function listSpeciesIds(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesIds();');
	}

	/**
	 * List location ids
	 * @access  public
	 * @return array
	 */
	public static function listLocationIds(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listLocationIds();');
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrdersAll(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listOrdersAll();');
	}

	/**
	 * search complete AOU list
	 * @access  public
	 * @param string $searchString
	 * @param int    $orderId
	 * @return array
	 */
	public static function searchAll(string $searchString, int $orderId): array {
		$searchString = urldecode($searchString);
		return self::executeProcedure(__METHOD__, 'CALL proc_searchAll(?,?);', [$searchString, $orderId]);
	}

	/**
	 * List species and trips by location
	 * @access  public
	 * @return array
	 */
	public static function speciesByLocation(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listLocations2();');
	}

	/**
	 * List species and trips by county
	 * @access  public
	 * @return array
	 */
	public static function speciesByCounty(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesByCounty();');
	}

	/**
	 * List species by month for ducks and warblers
	 * @access  public
	 * @return array
	 */
	public static function twoSpeciesByMonth(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listTwoSpeciesByMonth();');
	}

	/**
	 * Monthly average and record temperatures
	 * @access  public
	 * @return array
	 */
	public static function monthlyTemps(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listMonthlyAverages();');
	}

	/**
	 * List species for location
	 * @access  public
	 * @param int $locationId
	 * @return array
	 */
	public static function speciesForLocation(int $locationId): array {
		return DB::connection('mongodb')
			->collection('sighting')
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
							'monthName' => array('$first' => '$monthName'),
							'last_seen' => array('$max' => '$sighting_date'),
							'sightings' => array('$sum' => 1),
						),
					),
					array(
						'$lookup' => array(
							'from'         => "bird",
							'localField'   => "_id",
							'foreignField' => "aou_list_id",
							'as'           => "bird",
						),
					),
					array('$unwind' => '$bird'),
					array('$project' => array(
						"_id"             => 0,
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
	}

	/**
	 * Location detail
	 * @access  public
	 * @param int $locationId
	 * @return array
	 */
	public static function locationDetail(int $locationId): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_getLocation2(?);', [$locationId]);
	}

	/**
	 * @param string $method
	 * @param string $callStatement
	 * @param array  $params
	 * @return array
	 */
	private static function executeProcedure(string $method, string $callStatement, array $params = []): array {
		$cacheKey = $method . serialize($params);
		/** @noinspection PhpUndefinedMethodInspection */
		$results = Cache::get($cacheKey, function () use ($cacheKey, $callStatement, $params) {
			/** @noinspection PhpUndefinedMethodInspection */
			$results = DB::select($callStatement, $params);
			/** @noinspection PhpUndefinedMethodInspection */
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

}