<?php
declare(strict_types=1);

namespace App\Http\Mappers;

use \Illuminate\Support\Facades\Cache;
use Aws\S3\S3Client;

/**
 * Methods for retrieving from S3
 *
 * @package  Mappers
 *
 * @author Steve Moore <stephenmoore56@msn.com>
 */

/**
 * S3Mapper class
 *
 */
class S3Mapper {

	/**
	 * List images in the carousel directory
	 * @access  public
	 * @return array
	 */
	public static function carouselImages(): array {
		$cacheKey = __METHOD__;
		/** @noinspection PhpUndefinedMethodInspection */
		$results = Cache::get($cacheKey, function () use ($cacheKey) {
			$bucket = 'mooredatabase-carousel';
			$url = 'https://s3.amazonaws.com/mooredatabase-carousel/';
			$s3 = self::getS3Client();
			$objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket));
			$results = [];
			foreach ($objects as $object) {
				array_push($results, ['src' => $url . $object['Key'], 'alt' => 'Image']);
			}
			shuffle($results);
			/** @noinspection PhpUndefinedMethodInspection */
			Cache::forever($cacheKey, $results);
			return $results;
		});
		return $results;
	}

	/**
	 * get an instance of the S3 client
	 * @return S3Client
	 */
	private static function getS3Client(): S3Client {
		// Instantiate the client.
		return new S3Client([
			'credentials' => false,
			'region'      => 'us-east-1',
			'version'     => '2006-03-01',
		]);
	}

}
