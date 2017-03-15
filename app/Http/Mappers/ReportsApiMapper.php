<?php
declare(strict_types = 1);

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
		return
			DB::collection('sighting')
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
						array('$sort' => array('yearNumber' => 1)),
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
			DB::collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'     => '$monthNumber',
								'species' => array('$addToSet' => '$aou_list_id'),
								'trips'   => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "month",
								'localField'   => "_id",
								'foreignField' => "monthNumber",
								'as'           => "month",
							),
						),
						array('$unwind' => '$month'),
						array(
							'$project' => array(
								"_id"          => 0,
								'monthNumber'  => '$_id',
								'monthName'    => '$month.monthName',
								'monthLetter'  => '$month.monthLetter',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
						array('$sort' => array('monthNumber' => 1)),
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
		return
			DB::collection('sighting')
				->raw(function ($collection) use ($monthNumber) {
					return $collection->aggregate(array(
						array(
							'$match' => array(
								'monthNumber' => array('$eq' => $monthNumber),
							),
						),
						array(
							'$group' => array(
								'_id'         => '$aou_list_id',
								'monthNumber' => array('$first' => '$monthNumber'),
								'last_seen'   => array('$max' => '$sighting_date'),
								'sightings'   => array('$sum' => 1),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "month",
								'localField'   => "monthNumber",
								'foreignField' => "monthNumber",
								'as'           => "month",
							),
						),
						array('$unwind' => '$month'),
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
							'monthName'       => '$month.monthName',
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
		return
			DB::collection('sighting')
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
		return
			DB::collection('bird')
				->raw(function ($collection) use ($speciesId) {
					return $collection->aggregate(array(
						array(
							'$match' => array(
								'aou_list_id' => array('$eq' => $speciesId),
							),
						),
						array('$project' => array(
							'aou_list_id' => '$aou_list_id',
						)),
						array(
							'$lookup' => array(
								'from'         => "sighting",
								'localField'   => "aou_list_id",
								'foreignField' => "aou_list_id",
								'as'           => "sighting",
							),
						),
						array(
							'$unwind' => array(
								'path'                       => '$sighting',
								'preserveNullAndEmptyArrays' => true,
							),
						),
						array(
							'$project' => array(
								'aou_list_id'   => '$aou_list_id',
								'sighting_date' => '$sighting.sighting_date',
								'trip_id'       => '$sighting.trip_id',
							),
						),
						array(
							'$group' => array(
								'_id'              => '$aou_list_id',
								'earliestSighting' => array('$min' => array('$substr' => ['$sighting_date', 5, 5])),
								'latestSighting'   => array('$max' => array('$substr' => ['$sighting_date', 5, 5])),
								'first_seen'       => array('$min' => '$sighting_date'),
								'last_seen'        => array('$max' => '$sighting_date'),
								'trips'            => array('$addToSet' => '$trip_id'),
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
							"_id"              => 0,
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
	}

	/**
	 * List months for species
	 * @access  public
	 * @param int $speciesId
	 * @return array
	 */
	public static function monthsForSpecies(int $speciesId): array {
		return
			DB::collection('sighting')
				->raw(function ($collection) use ($speciesId) {
					return $collection->aggregate(array(
						array(
							'$match' => array(
								'aou_list_id' => array('$eq' => $speciesId),
							),
						),
						array(
							'$group' => array(
								'_id'           => '$monthNumber',
								'aou_list_id'   => array('$first' => '$aou_list_id'),
								'sightingCount' => array('$sum' => 1),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "month",
								'localField'   => "_id",
								'foreignField' => "monthNumber",
								'as'           => "month",
							),
						),
						array('$unwind' => '$month'),
						array(
							'$lookup' => array(
								'from'         => "bird",
								'localField'   => "aou_list_id",
								'foreignField' => "aou_list_id",
								'as'           => "bird",
							),
						),
						array('$unwind' => '$bird'),
						array(
							'$project' => array(
								"_id"           => 0,
								'common_name'   => '$bird.common_name',
								'monthNumber'   => '$_id',
								'monthName'     => '$month.monthName',
								'sightingCount' => '$sightingCount',
							),
						),
						array('$sort' => array('monthNumber' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List species by order
	 * @access  public
	 * @return array
	 */
	public static function speciesByOrder(): array {
		return
			DB::collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'     => '$order_id',
								'species' => array('$addToSet' => '$aou_list_id'),
								'trips'   => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								"_id"          => 0,
								'order_id'     => '$_id',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "order",
								'localField'   => "order_id",
								'foreignField' => "id",
								'as'           => "order",
							),
						),
						array('$unwind' => '$order'),
						array('$project' => array(
							"_id"                     => 0,
							'id'                      => '$order_id',
							'order_name'              => '$order.order_name',
							'order_notes'             => '$order.notes',
							'order_species_count_all' => '$order.order_species_count_all',
							'speciesCount'            => '$speciesCount',
						)),
						array('$sort' => array('speciesCount' => -1)),
					));
				})
				->toArray();
	}

	/**
	 * List species for order
	 * @access  public
	 * @param int $orderId
	 * @return array
	 */
	public static function speciesForOrder(int $orderId): array {
		return
			DB::collection('sighting')
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
						)),
						array('$sort' => array('common_name' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List all species sighted
	 * @access  public
	 * @return array
	 */
	public static function speciesAll(): array {
		return
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
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrders(): array {
		return
			DB::collection('order')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array('$project' => array(
							"_id"        => 0,
							'order_name' => '$order_name',
						)),
						array('$sort' => array('order_name' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List orders
	 * @access  public
	 * @return array
	 */
	public static function listOrdersAll(): array {
		return
			DB::collection('order')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array('$project' => array(
							"_id"        => 0,
							'order_name' => '$order_name',
							'notes'      => '$notes',
							'id'         => '$id',
							'sortkey'    => '$sortkey',
						)),
						array('$sort' => array('sortKey' => 1, 'order_name' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List order ids
	 * @access  public
	 * @return array
	 */
	public static function listOrderIds(): array {
		return
			DB::collection('order')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array('$project' => array(
							"_id"      => 0,
							'order_id' => '$id',
						)),
						array('$sort' => array('order_id' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List species ids
	 * @access  public
	 * @return array
	 */
	public static function listSpeciesIds(): array {
		return
			DB::collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id' => '$aou_list_id',
							),
						),
						array('$project' => array(
							"_id"         => 0,
							'aou_list_id' => '$_id',
						)),
						array('$sort' => array('aou_list_id' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List location ids
	 * @access  public
	 * @return array
	 */
	public static function listLocationIds(): array {
		return
			DB::collection('location')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array('$project' => array(
							"_id" => 0,
							'id'  => '$id',
						)),
						array('$sort' => array('id' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * search complete AOU list
	 * @access  public
	 * @param string $searchString
	 * @param int    $orderId
	 * @return array
	 */
	public static function searchAll(string $searchString, int $orderId): array {

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

		return
			DB::collection('bird')
				->raw(function ($collection) use ($orderMatch, $stringMatch) {
					return $collection->aggregate(array(
						array('$match' => $orderMatch),
						array('$match' => array(
							'$or' => $stringMatch,
						)),
						array('$project' => array(
							'aou_list_id' => '$aou_list_id',
						)),
						array(
							'$lookup' => array(
								'from'         => "sighting",
								'localField'   => "aou_list_id",
								'foreignField' => "aou_list_id",
								'as'           => "sighting",
							),
						),
						array(
							'$unwind' => array(
								'path'                       => '$sighting',
								'preserveNullAndEmptyArrays' => true,
							),
						),
						array(
							'$project' => array(
								'aou_list_id'   => '$aou_list_id',
								'sighting_date' => '$sighting.sighting_date',
								'trip_id'       => '$sighting.trip_id',
							),
						),
						array(
							'$group' => array(
								'_id'       => '$aou_list_id',
								'last_seen' => array('$max' => '$sighting_date'),
								'trips'     => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								'last_seen' => '$last_seen',
								'sightings' => array('$size' => '$trips'),
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
							'common_name'     => '$bird.common_name',
							'scientific_name' => '$bird.scientific_name',
							'family'          => '$bird.family',
							'subfamily'       => '$bird.subfamily',
							'order_id'        => '$bird.order_id',
							'order_name'      => '$bird.order_name',
							'order_notes'     => '$bird.order_notes',
							'last_seen'       => '$last_seen',
							'sightings'       => '$sightings',
						)),
						array('$sort' => array('common_name' => 1)),
					));
				})
				->toArray();
	}

	/**
	 * List species and trips by location
	 * @access  public
	 * @return array
	 */
	public static function speciesByLocation(): array {
		return
			DB::collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'     => '$location_id',
								'species' => array('$addToSet' => '$aou_list_id'),
								'trips'   => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								"_id"          => 0,
								'location_id'  => '$_id',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "location",
								'localField'   => "location_id",
								'foreignField' => "id",
								'as'           => "location",
							),
						),
						array('$unwind' => '$location'),
						array(
							'$lookup' => array(
								'from'         => "ecs",
								'localField'   => "location.ecs_subsection_id",
								'foreignField' => "subsection_id",
								'as'           => "ecs",
							),
						),
						array('$unwind' => '$ecs'),
						array('$project' => array(
							"_id"               => 0,
							'id'                => '$location_id',
							'trip_count'        => '$tripCount',
							'species_count'     => '$speciesCount',
							'trips'             => '$speciesCount',
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
	}

	/**
	 * List species and trips by county
	 * @access  public
	 * @return array
	 */
	public static function speciesByCounty(): array {
		return
			DB::collection('sighting')
				->raw(function ($collection) {
					return $collection->aggregate(array(
						array(
							'$group' => array(
								'_id'     => '$county_id',
								'species' => array('$addToSet' => '$aou_list_id'),
								'trips'   => array('$addToSet' => '$trip_id'),
							),
						),
						array(
							'$project' => array(
								"_id"          => 0,
								'county_id'    => '$_id',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "county",
								'localField'   => "county_id",
								'foreignField' => "id",
								'as'           => "county",
							),
						),
						array('$unwind' => '$county'),
						array('$project' => array(
							"_id"          => 0,
							'tripCount'    => '$tripCount',
							'speciesCount' => '$speciesCount',
							'countyName'   => '$county.county_name',
						)),
						array('$sort' => array('species_count' => -1)),
					));
				})
				->toArray();
	}

	/**
	 * List species for location
	 * @access  public
	 * @param int $locationId
	 * @return array
	 */
	public static function speciesForLocation(int $locationId): array {
		return
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
		return
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
							'$project' => array(
								"_id"          => 0,
								'location_id'  => '$_id',
								'speciesCount' => array('$size' => '$species'),
								'tripCount'    => array('$size' => '$trips'),
							),
						),
						array(
							'$lookup' => array(
								'from'         => "location",
								'localField'   => "location_id",
								'foreignField' => "id",
								'as'           => "location",
							),
						),
						array('$unwind' => '$location'),
						array(
							'$lookup' => array(
								'from'         => "ecs",
								'localField'   => "location.ecs_subsection_id",
								'foreignField' => "subsection_id",
								'as'           => "ecs",
							),
						),
						array('$unwind' => '$ecs'),
						array('$project' => array(
							"_id"                 => 0,
							'id'                  => '$location_id',
							'trip_count'          => '$tripCount',
							'species_count'       => '$speciesCount',
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
	}

}