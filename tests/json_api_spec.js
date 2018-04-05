'use strict';

const frisby = require('frisby');
// let baseURL = 'http://mongodb.local';
const baseURL = 'http://lumen.moore-database.com';

it('Should clear cache', function() {
    frisby.get(baseURL + '/api/reports/clearCache')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data', {
        message: String
    })
    .expect('json', 'data', {
        message: 'Cache flushed.'
    })
    .done();
});

it('Should access Species By Month JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesByMonth')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        monthNumber: Number,
        monthName: String,
        monthLetter: String,
        tripCount: Number,
        speciesCount: Number,
        sightingCount: Number
    })
    .done();
});

it('Species By Year JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesByYear')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        yearNumber: Number,
        tripCount: Number,
        speciesCount: Number,
        sightingCount: Number
    })
    .done();
});

it('Species YTD JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesYTD')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        yearNumber: Number,
        tripCount: Number,
        speciesCount: Number,
        sightingCount: Number,
        monthDay: String
    })
    .done();
});

it('Species For Month JSON endpoint; invalid month', function() {
    frisby.get(baseURL + '/api/reports/speciesForMonth/14')
    .expect('status', 400)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "400",
        title: "Bad Request"
    })
    .done();
});

it('Species For Month JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesForMonth/4')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String,
        monthName: String
    })
    .done();
});

it('Species For Year JSON endpoint; invalid year', function() {
    frisby.get(baseURL + '/api/reports/speciesForYear/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Species For Year JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesForYear/2014')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        first_seen: String,
        last_seen: String
    })
    .done();
});

it('Species For Order JSON endpoint; invalid order ID', function() {
    frisby.get(baseURL + '/api/reports/speciesForOrder/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Species For Order JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesForOrder/14')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String
    })
    .done();
});

it('Species By Order JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesByOrder')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        order_notes: String,
        order_species_count_all: Number,
        speciesCount: Number,
        sightingCount: Number
    })
    .done();
});

it('Species By Location JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesByLocation')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        country_code: String,
        state_code: String,
        county_name: String,
        location_name: String,
        latitude: Number,
        longitude: Number,
        ecs_subsection_id: Number,
        species_count: Number,
        sightingCount: Number,
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
    .done();
});

it('Species By County JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesByCounty')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        countyName: String,
        tripCount: Number,
        speciesCount: Number
    })
    .done();
});

it('All Species JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesAll')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        order_notes: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String
    })
    .done();
});

it('List Orders JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/listOrders')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        order_name: String
    })
    .done();
});

it('List Orders All JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/listOrdersAll')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        notes: String,
        sortkey: Number
    })
    .done();
});

it('Search All using string and all orders', function() {
    frisby.get(baseURL + '/api/reports/searchAll/warbler/-1')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Search All using string and order', function() {
    frisby.get(baseURL + '/api/reports/searchAll/american/14')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Search All using URL-encoded space and all orders', function() {
    frisby.get(baseURL + '/api/reports/searchAll/%20/-1')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Search All using space and all orders', function() {
    frisby.get(baseURL + '/api/reports/searchAll/ /-1')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Search all using wierd search term and all orders', function() {
    frisby.get(baseURL + '/api/reports/searchAll/dodoxxx/-1')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Species Detail JSON endpoint; invalid species ID', function() {
    frisby.get(baseURL + '/api/reports/speciesDetail/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Species Detail JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesDetail/992')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Months For Species JSON endpoint; invalid species ID', function() {
    frisby.get(baseURL + '/api/reports/monthsForSpecies/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Months For Species JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/monthsForSpecies/992')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        common_name: String,
        monthNumber: Number,
        monthName: String,
        sightingCount: Number
    })
    .done();
});

it('List Order Ids JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/listOrderIds')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        order_id: Number
    })
    .done();
});

it('List Species Ids JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/listSpeciesIds')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        aou_list_id: Number
    })
    .done();
});

it('List Location Ids JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/listLocationIds')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number
    })
    .done();
});

it('Species For Location JSON endpoint; invalid location ID', function() {
    frisby.get(baseURL + '/api/reports/speciesForLocation/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Species For Location JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/speciesForLocation/14')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        id: Number,
        order_name: String,
        common_name: String,
        scientific_name: String,
        family: String,
        subfamily: String,
        sightings: Number,
        last_seen: String
    })
    .done();
});

it('Location detail JSON endpoint; invalid species ID', function() {
    frisby.get(baseURL + '/api/reports/locationDetail/9999')
    .expect('status', 404)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','errors', {
        status: String,
        title: String
    })
    .expect('json','errors', {
        status: "404",
        title: "Not Found"
    })
    .done();
});

it('Location detail JSON endpoint', function() {
    frisby.get(baseURL + '/api/reports/locationDetail/14')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
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
    .done();
});

it('Monthly temperatures', function() {
    frisby.get(baseURL + '/api/reports/monthlyTemperatures')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        monthNumber: Number,
        avg_low_temp: Number,
        record_low_temp: Number,
        avg_high_temp: Number,
        record_high_temp: Number,
        days_with_frost: Number
    })
    .done();
});

it('Ducks and Warblers', function() {
    frisby.get(baseURL + '/api/reports/ducksAndWarblers')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        monthNumber: Number,
        speciesCount: Number,
        family: String,
        monthLetter: String
    })
    .done();
});

it('Carousel image endpoint', function() {
    frisby.get(baseURL + '/api/reports/carouselImages')
    .expect('status', 200)
    .expect('header', 'Content-Type', 'application/json')
    .expect('jsonTypes','data.?', {
        src: String,
        alt: String
    })
    .done();
});

