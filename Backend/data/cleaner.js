const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'custom.geojson');

// Read the GeoJSON file
fs.readFile(filePath, 'utf8', (err, data) => {
    if (err) {
        console.error('Error reading the file:', err);
        return;
    }

    // Parse the GeoJSON data
    let geoJson;
    try {
        geoJson = JSON.parse(data);
    } catch (parseErr) {
        console.error('Error parsing the JSON:', parseErr);
        return;
    }

    // Iterate over each feature and remove all properties except 'name_en'
    geoJson.features.forEach(feature => {
        if (feature.properties) {
            const nameEn = feature.properties.name_en;
            feature.properties = { name_en: nameEn };
        }
    });

    // Write the modified GeoJSON back to the file
    fs.writeFile("cleaned.geojson", JSON.stringify(geoJson, null, 2), 'utf8', writeErr => {
        if (writeErr) {
            console.error('Error writing the file:', writeErr);
            return;
        }
        console.log('File successfully updated.');
    });
});