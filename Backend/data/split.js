const fs = require('fs');
const path = require('path');

// Read the GeoJSON file
const geojsonPath = path.join(__dirname, 'cleaned.geojson');
const geojsonData = JSON.parse(fs.readFileSync(geojsonPath, 'utf8'));

// Function to split MultiPolygon into individual Polygons
function splitMultiPolygon(feature) {
  const polygons = [];
  if (feature.geometry.type === 'MultiPolygon') {
    feature.geometry.coordinates.forEach((polygonCoords) => {
      const newFeature = {
        type: 'Feature',
        properties: { ...feature.properties },
        geometry: {
          type: 'Polygon',
          coordinates: polygonCoords,
        },
      };
      polygons.push(newFeature);
    });
  } else {
    polygons.push(feature);
  }
  return polygons;
}

// Process each feature in the GeoJSON
const newFeatures = [];
geojsonData.features.forEach((feature) => {
  const splitFeatures = splitMultiPolygon(feature);
  newFeatures.push(...splitFeatures);
});

// Create a new GeoJSON object
const newGeojsonData = {
  type: 'FeatureCollection',
  features: newFeatures,
};

// Write the new GeoJSON to a file
const outputPath = path.join(__dirname, 'countries.geojson');
fs.writeFileSync(outputPath, JSON.stringify(newGeojsonData, null, 2));

console.log('GeoJSON processing complete. Output written to europe_split.geojson');