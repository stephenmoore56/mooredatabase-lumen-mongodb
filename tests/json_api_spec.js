'use strict';

let frisby = require('frisby');
let baseURL = 'http://lumen.local';
// let baseURL = 'http://lumen.moore-database.com';

frisby.create('Clear cache')
    .get(baseURL + '/api/reports/clearCache')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data', {
        message: String
    })
    .expectJSON('data', {
        message: 'Cache flushed.'
    })
    .toss();

frisby.create('Species By Month JSON endpoint')
    .get(baseURL + '/api/reports/speciesByMonth')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        monthNumber: Number,
        monthName: String,
        monthLetter: String,
        tripCount: Number,
        speciesCount: Number
    })
    .toss();

frisby.create('Two Species By Month JSON endpoint')
    .get(baseURL + '/api/reports/twoSpeciesByMonth')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        monthNumber: Number,
        monthName: String,
        monthLetter: String,
        speciesCountAnseriformes: Number,
        speciesCountPasseriformes: Number
    })
    .toss();

frisby.create('Monthly average and record temps JSON endpoint')
    .get(baseURL + '/api/reports/monthlyTemps')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        month_number: Number,
        month_name: String,
        month_abbrev: String,
        month_letter: String,
        avg_low_temp: Number,
        avg_high_temp: Number,
        record_low_temp: Number,
        record_high_temp: Number,
        avg_precipitation: Number,
        avg_snowfall: Number
    })
    .toss();

frisby.create('Species By Year JSON endpoint')
    .get(baseURL + '/api/reports/speciesByYear')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        yearNumber: Number,
        tripCount: Number,
        speciesCount: Number
    })
    .toss();

frisby.create('Species For Month JSON endpoint; invalid month')
    .get(baseURL + '/api/reports/speciesForMonth/14')
    .expectStatus(400)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "400",
        title: "Bad Request"
    })
    .toss();

frisby.create('Species For Month JSON endpoint')
    .get(baseURL + '/api/reports/speciesForMonth/4')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        order_species_count: Number,
        sightings: Number,
        last_seen: String,
        monthName: String
    })
    .toss();

frisby.create('Species For Year JSON endpoint; invalid year')
    .get(baseURL + '/api/reports/speciesForYear/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Species For Year JSON endpoint')
    .get(baseURL + '/api/reports/speciesForYear/2014')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        order_species_count: Number,
        sightings: Number,
        first_seen: String,
        last_seen: String
    })
    .toss();

frisby.create('Species For Order JSON endpoint; invalid order ID')
    .get(baseURL + '/api/reports/speciesForOrder/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Species For Order JSON endpoint')
    .get(baseURL + '/api/reports/speciesForOrder/14')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        order_species_count: Number,
        sightings: Number,
        last_seen: String
    })
    .toss();

frisby.create('Species By Order JSON endpoint')
    .get(baseURL + '/api/reports/speciesByOrder')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        order_species_count_all: Number,
        speciesCount: Number
    })
    .toss();

frisby.create('Species By Location JSON endpoint')
    .get(baseURL + '/api/reports/speciesByLocation')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        country_code: String,
        state_code: String,
        county_name: String,
        location_name: String,
        latitude: Number,
        longitude: Number,
        ecs_subsection_id: Number,
        species_count: Number,
        trip_count: Number,
        trips: Number, // duplicate field
        subsection_id: Number, // duplicate field
        subsection_name: String,
        subsection_url: String,
        section_name: String,
        section_url: String,
        province_name: String,
        province_url: String
    })
    .toss();

frisby.create('Species By County JSON endpoint')
    .get(baseURL + '/api/reports/speciesByCounty')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        countyName: String,
        tripCount: Number,
        speciesCount: Number
    })
    .toss();

frisby.create('All Species JSON endpoint')
    .get(baseURL + '/api/reports/speciesAll')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        order_species_count: Number,
        sightings: Number,
        last_seen: String,
        displayGroupHeader: String
    })
    .toss();

frisby.create('List Orders JSON endpoint')
    .get(baseURL + '/api/reports/listOrders')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        order_name: String
    })
    .toss();

frisby.create('List Orders All JSON endpoint')
    .get(baseURL + '/api/reports/listOrdersAll')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        notes: String,
        sortkey: Number
    })
    .toss();

frisby.create('Search All using string and all orders')
    .get(baseURL + '/api/reports/searchAll/warbler/-1')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: function (val) {
            expect(val)
                .toBeTypeOrNull(String);
        }
    })
    .toss();

frisby.create('Search All using string and order')
    .get(baseURL + '/api/reports/searchAll/american/14')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: function (val) {
            expect(val)
                .toBeTypeOrNull(String);
        }
    })
    .toss();

frisby.create('Search All using URL-encoded space and all orders')
    .get(baseURL + '/api/reports/searchAll/%20/-1')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: function (val) {
            expect(val)
                .toBeTypeOrNull(String);
        }
    })
    .toss();

frisby.create('Search All using space and all orders')
    .get(baseURL + '/api/reports/searchAll/ /-1')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: function (val) {
            expect(val)
                .toBeTypeOrNull(String);
        }
    })
    .toss();

frisby.create('Species Detail JSON endpoint; invalid species ID')
    .get(baseURL + '/api/reports/speciesDetail/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Species Detail JSON endpoint')
    .get(baseURL + '/api/reports/speciesDetail/992')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String,
        earliestSighting: String,
        latestSighting: String
    })
    .toss();

frisby.create('Months For Species JSON endpoint; invalid species ID')
    .get(baseURL + '/api/reports/monthsForSpecies/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Months For Species JSON endpoint')
    .get(baseURL + '/api/reports/monthsForSpecies/992')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        common_name: String,
        monthNumber: Number,
        monthName: String,
        sightingCount: Number
    })
    .toss();

frisby.create('Sightings By Month JSON endpoint; invalid species ID')
    .get(baseURL + '/api/reports/sightingsByMonth/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Sightings By Month JSON endpoint')
    .get(baseURL + '/api/reports/sightingsByMonth/992')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        common_name: String,
        monthNumber: Number,
        monthName: String,
        sightingCount: Number
    })
    .toss();

frisby.create('List Order Ids JSON endpoint')
    .get(baseURL + '/api/reports/listOrderIds')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        order_id: Number
    })
    .toss();

frisby.create('List Species Ids JSON endpoint')
    .get(baseURL + '/api/reports/listSpeciesIds')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        aou_list_id: Number
    })
    .toss();

frisby.create('List Location Ids JSON endpoint')
    .get(baseURL + '/api/reports/listLocationIds')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number
    })
    .toss();

frisby.create('Species For Location JSON endpoint; invalid location ID')
    .get(baseURL + '/api/reports/speciesForLocation/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Species For Location JSON endpoint')
    .get(baseURL + '/api/reports/speciesForLocation/14')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String
    })
    .toss();

frisby.create('Location detail JSON endpoint; invalid species ID')
    .get(baseURL + '/api/reports/locationDetail/9999')
    .expectStatus(404)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('errors', {
        status: String,
        title: String
    })
    .expectJSON('errors', {
        status: "404",
        title: "Not Found"
    })
    .toss();

frisby.create('Location detail JSON endpoint')
    .get(baseURL + '/api/reports/locationDetail/14')
    .expectStatus(200)
    .expectHeader('Content-Type', 'application/json')
    .expectJSONTypes('data.*', {
        id: Number,
        country_code: String,
        state_code: String,
        location_name: String,
        county_name: String,
        notes: String,
        latitude: Number,
        longitude: Number,
        ecs_subsection_id: Number,
        ecs_subsection_name: String,
        ecs_subsection_url: String,
        trip_count: Number,
        species_count: Number
    })
    .toss();