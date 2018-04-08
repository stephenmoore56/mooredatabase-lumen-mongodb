'use strict';

const frisby = require('frisby');
const Joi = frisby.Joi;
//const baseURL = 'http://lumen.local';
const baseURL = 'http://lumen.moore-database.com';

it('Should clear cache', function (done) {
    frisby.get(baseURL + '/api/reports/clearCache')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data', {
            message: Joi.string().required()
        })
        .expect('json', 'data', {
            message: 'Cache flushed.'
        })
        .done(done);
});

it('Should access Species By Month JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesByMonth')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            monthNumber: Joi.number().required(),
            monthName: Joi.string().required(),
            monthLetter: Joi.string().required(),
            tripCount: Joi.number().required(),
            speciesCount: Joi.number().required(),
            sightingCount: Joi.number().required()
        })
        .done(done);
});

it('Species By Year JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesByYear')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            yearNumber: Joi.number().required(),
            tripCount: Joi.number().required(),
            speciesCount: Joi.number().required(),
            sightingCount: Joi.number().required()
        })
        .done(done);
});

it('Species YTD JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesYTD')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            yearNumber: Joi.number().required(),
            tripCount: Joi.number().required(),
            speciesCount: Joi.number().required(),
            sightingCount: Joi.number().required(),
            monthDay: Joi.string().required()
        })
        .done(done);
});

it('Species For Month JSON endpoint; invalid month', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForMonth/14')
        .expect('status', 400)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "400",
            title: "Bad Request"
        })
        .done(done);
});

it('Species For Month JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForMonth/4')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            last_seen: Joi.string().required(),
            monthName: Joi.string().required()
        })
        .done(done);
});

it('Species For Year JSON endpoint; invalid year', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForYear/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Species For Year JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForYear/2014')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            first_seen: Joi.string().required(),
            last_seen: Joi.string().required()
        })
        .done(done);
});

it('Species For Order JSON endpoint; invalid order ID', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForOrder/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Species For Order JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForOrder/14')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            last_seen: Joi.string().required()
        })
        .done(done);
});

it('Species By Order JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesByOrder')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            order_species_count_all: Joi.number().required(),
            speciesCount: Joi.number().required(),
            sightingCount: Joi.number().required()
        })
        .done(done);
});

it('Species By Location JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesByLocation')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            country_code: Joi.string().required(),
            state_code: Joi.string().required(),
            county_name: Joi.string().required(),
            location_name: Joi.string().required(),
            latitude: Joi.number().required(),
            longitude: Joi.number().required(),
            ecs_subsection_id: Joi.number().required(),
            species_count: Joi.number().required(),
            sightingCount: Joi.number().required(),
            trip_count: Joi.number().required(),
            trips: Joi.number().required(), // duplicate field
            subsection_id: Joi.number().required(), // duplicate field
            subsection_name: Joi.string().required(),
            subsection_url: Joi.string().required(),
            section_name: Joi.string().required(),
            section_url: Joi.string().required(),
            province_name: Joi.string().required(),
            province_url: Joi.string().required()
        })
        .done(done);
});

it('Species By County JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesByCounty')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            countyName: Joi.string().required(),
            tripCount: Joi.number().required(),
            speciesCount: Joi.number().required()
        })
        .done(done);
});

it('All Species JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesAll')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            last_seen: Joi.string().required()
        })
        .done(done);
});

it('List Orders JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/listOrders')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            order_name: Joi.string().required()
        })
        .done(done);
});

it('List Orders All JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/listOrdersAll')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number(),
            order_name: Joi.string().required(),
            sortkey: Joi.number().required()
        })
        .done(done);
});

it('Search All using string and all orders', function (done) {
    frisby.get(baseURL + '/api/reports/searchAll/warbler/-1')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required()
        })
        .done(done);
});

it('Search All using string and order', function (done) {
    frisby.get(baseURL + '/api/reports/searchAll/american/14')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required()
        })
        .done(done);
});

it('Search All using URL-encoded space and all orders', function (done) {
    frisby.get(baseURL + '/api/reports/searchAll/ /-1')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required()
        })
        .done(done);
});

it('Search All using space and all orders', function (done) {
    frisby.get(baseURL + '/api/reports/searchAll/ /-1')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required()
        })
        .done(done);
});

it('Search all using wierd search term and all orders', function (done) {
    frisby.get(baseURL + '/api/reports/searchAll/dodoxxx/-1')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Species Detail JSON endpoint; invalid species ID', function (done) {
    frisby.get(baseURL + '/api/reports/speciesDetail/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Species Detail JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesDetail/992')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            order_notes: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            last_seen: Joi.string().required(),
            earliestSighting: Joi.string().required(),
            latestSighting: Joi.string().required()
        })
        .done(done);
});

it('Months For Species JSON endpoint; invalid species ID', function (done) {
    frisby.get(baseURL + '/api/reports/monthsForSpecies/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Months For Species JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/monthsForSpecies/992')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            common_name: Joi.string().required(),
            monthNumber: Joi.number().required(),
            monthName: Joi.string().required(),
            sightingCount: Joi.number().required()
        })
        .done(done);
});

it('List Order Ids JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/listOrderIds')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            order_id: Joi.number().required()
        })
        .done(done);
});

it('List Species Ids JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/listSpeciesIds')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            aou_list_id: Joi.number().required()
        })
        .done(done);
});

it('List Location Ids JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/listLocationIds')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required()
        })
        .done(done);
});

it('Species For Location JSON endpoint; invalid location ID', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForLocation/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Species For Location JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/speciesForLocation/14')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            order_name: Joi.string().required(),
            common_name: Joi.string().required(),
            scientific_name: Joi.string().required(),
            family: Joi.string().required(),
            sightings: Joi.number().required(),
            last_seen: Joi.string().required()
        })
        .done(done);
});

it('Location detail JSON endpoint; invalid species ID', function (done) {
    frisby.get(baseURL + '/api/reports/locationDetail/9999')
        .expect('status', 404)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'errors', {
            status: Joi.string().required(),
            title: Joi.string().required()
        })
        .expect('json', 'errors', {
            status: "404",
            title: "Not Found"
        })
        .done(done);
});

it('Location detail JSON endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/locationDetail/14')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            id: Joi.number().required(),
            country_code: Joi.string().required(),
            state_code: Joi.string().required(),
            location_name: Joi.string().required(),
            county_name: Joi.string().required(),
            notes: Joi.string().required(),
            latitude: Joi.number().required(),
            longitude: Joi.number().required(),
            ecs_subsection_id: Joi.number().required(),
            ecs_subsection_name: Joi.string().required(),
            ecs_subsection_url: Joi.string().required(),
            trip_count: Joi.number().required(),
            species_count: Joi.number().required()
        })
        .done(done);
});

it('Monthly temperatures', function (done) {
    frisby.get(baseURL + '/api/reports/monthlyTemperatures')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            monthNumber: Joi.number().required(),
            avg_low_temp: Joi.number().required(),
            record_low_temp: Joi.number().required(),
            avg_high_temp: Joi.number().required(),
            record_high_temp: Joi.number().required(),
            days_with_frost: Joi.number().required()
        })
        .done(done);
});

it('Ducks and Warblers', function (done) {
    frisby.get(baseURL + '/api/reports/ducksAndWarblers')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            monthNumber: Joi.number().required(),
            speciesCount: Joi.number().required(),
            family: Joi.string().required(),
            monthLetter: Joi.string().required()
        })
        .done(done);
});

it('Carousel image endpoint', function (done) {
    frisby.get(baseURL + '/api/reports/carouselImages')
        .expect('status', 200)
        .expect('header', 'Content-Type', 'application/json')
        .expect('jsonTypes', 'data.*', {
            src: Joi.string().required(),
            alt: Joi.string().required()
        })
        .done(done);
});
