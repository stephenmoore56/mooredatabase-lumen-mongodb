#! /bin/bash

OUTPUT_DIR="./json_data"

php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding aou_list $OUTPUT_DIR/aou_list.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding aou_order $OUTPUT_DIR/aou_order.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding county $OUTPUT_DIR/county.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding ecs_province $OUTPUT_DIR/ecs_province.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding ecs_section $OUTPUT_DIR/ecs_section.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding ecs_subsection $OUTPUT_DIR/ecs_subsection.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding location $OUTPUT_DIR/location.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding monthly_averages $OUTPUT_DIR/monthly_averages.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding sighting $OUTPUT_DIR/sighting.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding trip $OUTPUT_DIR/trip.json

mongoimport --db birding --drop --collection aou_list 		--jsonArray --file $OUTPUT_DIR/aou_list.json
mongoimport --db birding --drop --collection aou_order 		--jsonArray --file $OUTPUT_DIR/aou_order.json
mongoimport --db birding --drop --collection county 		--jsonArray --file $OUTPUT_DIR/county.json
mongoimport --db birding --drop --collection ecs_province 	--jsonArray --file $OUTPUT_DIR/ecs_province.json
mongoimport --db birding --drop --collection ecs_section 	--jsonArray --file $OUTPUT_DIR/ecs_section.json
mongoimport --db birding --drop --collection ecs_subsection 	--jsonArray --file $OUTPUT_DIR/ecs_subsection.json
mongoimport --db birding --drop --collection location 		--jsonArray --file $OUTPUT_DIR/location.json
mongoimport --db birding --drop --collection monthly_averages 	--jsonArray --file $OUTPUT_DIR/monthly_averages.json
mongoimport --db birding --drop --collection sighting 		--jsonArray --file $OUTPUT_DIR/sighting.json
mongoimport --db birding --drop --collection trip 		--jsonArray --file $OUTPUT_DIR/trip.json
