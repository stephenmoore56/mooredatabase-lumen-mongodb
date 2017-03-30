<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use \Exception as Exception;
use App\Http\Mappers\ReportsApiMapper as Mapper;
use App\Http\Mappers\S3Mapper as S3Mapper;

/**
 * Methods that serve JSON data for reports
 *
 * @package  Controllers
 *
 * @author Steve Moore <stephenmoore56@msn.com>
 */

/**
 * ReportsApiController class
 *
 */
class ReportsApiController extends Controller {
	// messages for HTTP error status codes
	const HTTP_NOT_FOUND_MESSAGE = "Not Found";
	const HTTP_BAD_REQUEST_MESSAGE = "Bad Request";

	/**
	 * Clear memcached; mainly for testing
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function clearCache() {
		try {
			Mapper::clearCache();
			$results = ['message' => 'Cache flushed.'];
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	public static function mongoTest() {
		try {
			$results = Mapper::mongoTest();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species and trips by month
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesByMonth() {
		try {
			$results = Mapper::speciesByMonth();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}


	/**
	 * List species and trips by year
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesByYear() {
		try {
			$results = Mapper::speciesByYear();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species for month
	 * @access  public
	 * @param int $monthNumber
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesForMonth(int $monthNumber) {
		try {
			if ($monthNumber > 12 || $monthNumber < 1) {
				return self::formatErrorResponse(Response::HTTP_BAD_REQUEST, self::HTTP_BAD_REQUEST_MESSAGE);
			}
			$results = Mapper::speciesForMonth($monthNumber);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species for year
	 * @access  public
	 * @param int $year
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesForYear(int $year) {
		try {
			$results = Mapper::speciesForYear($year);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}


	/**
	 * Species detail
	 * @access  public
	 * @param int $speciesId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesDetail(int $speciesId) {
		try {
			$results = Mapper::speciesDetail($speciesId);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List months for species
	 * @access  public
	 * @param int $speciesId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function monthsForSpecies(int $speciesId) {
		try {
			$results = Mapper::monthsForSpecies($speciesId);
			if ($results == []) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species by order
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesByOrder() {
		try {
			$results = Mapper::speciesByOrder();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species for order
	 * @access  public
	 * @param int $orderId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesForOrder(int $orderId) {
		try {
			$results = Mapper::speciesForOrder($orderId);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List all species
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesAll() {
		try {
			$results = Mapper::speciesAll();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List orders
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function listOrders() {
		try {
			$results = Mapper::listOrders();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List order ids
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function listOrderIds() {
		try {
			$results = Mapper::listOrderIds();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species ids
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function listSpeciesIds() {
		try {
			$results = Mapper::listSpeciesIds();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List location ids
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function listLocationIds() {
		try {
			$results = Mapper::listLocationIds();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List orders
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function listOrdersAll() {
		try {
			$results = Mapper::listOrdersAll();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * search complete AOU list
	 * @access  public
	 * @param string $searchString
	 * @param int    $orderId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function searchAll(string $searchString, int $orderId) {
		try {
			$results = Mapper::searchAll($searchString, $orderId);
			if ($results == []) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species and trips by location
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesByLocation() {
		try {
			$results = Mapper::speciesByLocation();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species and trips by county
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesByCounty() {
		try {
			$results = Mapper::speciesByCounty();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List species for location
	 * @access  public
	 * @param int $locationId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function speciesForLocation(int $locationId) {
		try {
			$results = Mapper::speciesForLocation($locationId);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * Location detail
	 * @access  public
	 * @param int $locationId
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function locationDetail(int $locationId) {
		try {
			$results = Mapper::locationDetail($locationId);
			if (!count($results)) {
				return self::formatErrorResponse(Response::HTTP_NOT_FOUND, self::HTTP_NOT_FOUND_MESSAGE);
			}
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List average and record low/high temperatures for each month
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function monthlyTemperatures() {
		try {
			$results = Mapper::monthlyTemperatures();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List sightings by month for ducks and warblers
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function ducksAndWarblers() {
		try {
			$results = Mapper::ducksAndWarblers();
			return self::formatNormalResponse(Response::HTTP_OK, $results);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

	/**
	 * List carousel images
	 * @access  public
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public static function carouselImages() {
		try {
			// choose an image at random from the array
			$results = S3Mapper::carouselImages();
			$result = [$results[array_rand($results)]];
			return self::formatNormalResponse(Response::HTTP_OK,$result);
		} catch (Exception $e) {
			return self::formatErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
		}
	}

}
