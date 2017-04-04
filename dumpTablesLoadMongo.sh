#! /bin/bash

OUTPUT_DIR="./json_data"
MYSQL_USER="smoore"
MYSQL_PASSWORD="zZqcG2K4pe"
MYSQL_HOST="mysql57test.cd02yxrr7fxm.us-east-1.rds.amazonaws.com"

sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding birds $OUTPUT_DIR/bird.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding trip_view $OUTPUT_DIR/trip.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding birding_locations $OUTPUT_DIR/location.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding ecs $OUTPUT_DIR/ecs.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding sighting_view $OUTPUT_DIR/sighting.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding order_view $OUTPUT_DIR/order.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding county $OUTPUT_DIR/county.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding month $OUTPUT_DIR/month.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding time_view $OUTPUT_DIR/time.json
sudo php dump_table_to_json.php $MYSQL_HOST $MYSQL_USER $MYSQL_PASSWORD birding temperature_view $OUTPUT_DIR/temperature.json

MONGO_USER="smoore"
MONGO_PASSWORD="gsnyder56"
MONGO_HOST="172.30.0.77"
MONGO_PORT="27017"

mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields aou_list_id     --collection bird 	--jsonArray --file $OUTPUT_DIR/bird.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields id              --collection trip 	--jsonArray --file $OUTPUT_DIR/trip.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields id              --collection location	--jsonArray --file $OUTPUT_DIR/location.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields subsection_id   --collection ecs 	--jsonArray --file $OUTPUT_DIR/ecs.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields id              --collection sighting	--jsonArray --file $OUTPUT_DIR/sighting.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields id              --collection order	--jsonArray --file $OUTPUT_DIR/order.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields id              --collection county	--jsonArray --file $OUTPUT_DIR/county.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields monthNumber     --collection month	--jsonArray --file $OUTPUT_DIR/month.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields sighting_date   --collection time	--jsonArray --file $OUTPUT_DIR/time.json
mongoimport -h $MONGO_HOST -p $MONGO_PORT -u $MONGO_USER -p $MONGO_PASSWORD --authenticationDatabase admin --db birding --upsertFields monthNumber     --collection temperature	--jsonArray --file $OUTPUT_DIR/temperature.json
