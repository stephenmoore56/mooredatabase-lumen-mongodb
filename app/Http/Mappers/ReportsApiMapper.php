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
	 * List species and trips by month
	 * @access  public
	 * @return array
	 */
	public static function speciesByMonth(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesByMonth();');
	}

	/**
	 * List species and trips by year
	 * @access  public
	 * @return array
	 */
	public static function speciesByYear(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesByYear();');
	}

	/**
	 * List species for month
	 * @access  public
	 * @param int $monthNumber
	 * @return array
	 */
	public static function speciesForMonth(int $monthNumber): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesForMonth(?);', [$monthNumber]);
	}

	/**
	 * List species for year
	 * @access  public
	 * @param int $year
	 * @return array
	 */
	public static function speciesForYear(int $year): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesForYear(?);', [$year]);
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
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesForOrder(?);', [$orderId]);
	}

	/**
	 * List all species
	 * @access  public
	 * @return array
	 */
	public static function speciesAll(): array {
		return self::executeProcedure(__METHOD__, 'CALL proc_listSpeciesAll();');
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
	 * @param int $orderId
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
		return self::executeProcedure(__METHOD__, 'CALL proc_listSightingsForLocation2(?);', [$locationId]);
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
	 * @param array $params
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