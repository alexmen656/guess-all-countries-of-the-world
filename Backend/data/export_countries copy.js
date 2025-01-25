const fs = require('fs');
const path = require('path');

// Pfad zur GeoJSON-Datei
const geojsonFilePath = path.join(__dirname, 'cleaned.geojson');

// GeoJSON-Datei einlesen
fs.readFile(geojsonFilePath, 'utf8', (err, data) => {
    if (err) {
        console.error('Fehler beim Lesen der GeoJSON-Datei:', err);
        return;
    }

    try {
        const geojson = JSON.parse(data);
        const countryNames = geojson.features.map(feature => feature.properties.name_en);

        // Pfad zur Ausgabe-JSON-Datei
        const outputFilePath = path.join(__dirname, 'country_names.json');

        // Namen in JSON-Datei speichern
        fs.writeFile(outputFilePath, JSON.stringify(countryNames, null, 2), 'utf8', err => {
            if (err) {
                console.error('Fehler beim Schreiben der JSON-Datei:', err);
                return;
            }
            console.log('Länderliste erfolgreich exportiert!');
        });
    } catch (err) {
        console.error('Fehler beim Parsen der GeoJSON-Datei:', err);
    }
});