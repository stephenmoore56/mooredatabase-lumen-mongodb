db.location.find().forEach(function(doc) {
    doc.geo = {
        type: "Point",
        coordinates: [doc.longitude, doc.latitude]
    };
    db.location.save(doc);
})
db.location.createIndex({
    geo: "2dsphere"
})
