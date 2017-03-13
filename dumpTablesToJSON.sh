#! /bin/bash

OUTPUT_DIR="./json_data"

php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding birds $OUTPUT_DIR/bird.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding trip_view $OUTPUT_DIR/trip.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding birding_locations $OUTPUT_DIR/location.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding ecs $OUTPUT_DIR/ecs.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding sighting_view $OUTPUT_DIR/sighting.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding order_view $OUTPUT_DIR/order.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding county $OUTPUT_DIR/county.json
php dump_table_to_json.php 127.0.0.1 root gsnyder56 birding month $OUTPUT_DIR/month.json

mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields aou_list_id     --collection bird 	    --jsonArray --file $OUTPUT_DIR/bird.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields id              --collection trip 	    --jsonArray --file $OUTPUT_DIR/trip.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields id              --collection location	--jsonArray --file $OUTPUT_DIR/location.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields subsection_id   --collection ecs 		--jsonArray --file $OUTPUT_DIR/ecs.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields id              --collection sighting	--jsonArray --file $OUTPUT_DIR/sighting.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields id              --collection order	--jsonArray --file $OUTPUT_DIR/order.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields id              --collection county	--jsonArray --file $OUTPUT_DIR/county.json
mongoimport -u smoore -p gsnyder56 --authenticationDatabase admin --db birding --upsertFields monthNumber     --collection month	--jsonArray --file $OUTPUT_DIR/month.json
