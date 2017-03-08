<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */
$app->get('/api/reports/clearCache', 'ReportsApiController@clearCache');
$app->get('/api/reports/speciesByMonth', 'ReportsApiController@speciesByMonth');
$app->get('/api/reports/speciesByYear', 'ReportsApiController@speciesByYear');
$app->get('/api/reports/speciesForMonth/{monthNumber:[1-9]\d*}', 'ReportsApiController@speciesForMonth');
$app->get('/api/reports/speciesByOrder', 'ReportsApiController@speciesByOrder');
$app->get('/api/reports/speciesForOrder/{orderId:[1-9]\d*}', 'ReportsApiController@speciesForOrder');
$app->get('/api/reports/speciesByLocation', 'ReportsApiController@speciesByLocation');
$app->get('/api/reports/speciesByCounty', 'ReportsApiController@speciesByCounty');
$app->get('/api/reports/speciesAll', 'ReportsApiController@speciesAll');
$app->get('/api/reports/listOrders', 'ReportsApiController@listOrders');
$app->get('/api/reports/listOrdersAll', 'ReportsApiController@listOrdersAll');
$app->get('/api/reports/searchAll/{searchString}/{orderId:\-?1|[1-9]\d*}', 'ReportsApiController@searchAll');
$app->get('/api/reports/speciesDetail/{speciesId:[1-9]\d*}', 'ReportsApiController@speciesDetail');
$app->get('/api/reports/monthsForSpecies/{speciesId:[1-9]\d*}', 'ReportsApiController@monthsForSpecies');
$app->get('/api/reports/listOrderIds', 'ReportsApiController@listOrderIds');
$app->get('/api/reports/listSpeciesIds', 'ReportsApiController@listSpeciesIds');
$app->get('/api/reports/listLocationIds', 'ReportsApiController@listLocationIds');
$app->get('/api/reports/speciesForLocation/{locationId:[1-9]\d*}', 'ReportsApiController@speciesForLocation');
$app->get('/api/reports/locationDetail/{locationId:[1-9]\d*}', 'ReportsApiController@locationDetail');
$app->get('/api/reports/twoSpeciesByMonth', 'ReportsApiController@twoSpeciesByMonth');
$app->get('/api/reports/monthlyTemps', 'ReportsApiController@monthlyTemps');
$app->get('/api/reports/sightingsByMonth/{speciesId:[1-9]\d*}', 'ReportsApiController@sightingsByMonth');
$app->get('/api/reports/sightingsByMonth/{speciesId:[1-9]\d*}', 'ReportsApiController@sightingsByMonth');
$app->get('/api/reports/speciesForYear/{year:[0-9]\d*}', 'ReportsApiController@speciesForYear');
