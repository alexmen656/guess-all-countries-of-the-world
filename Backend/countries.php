<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$json_file_path = 'data/countries.min.geojson';

if (file_exists($json_file_path)) {
    $json_data = file_get_contents($json_file_path);
    $geojson = json_decode($json_data, true);

    foreach ($geojson['features'] as &$feature) {
        $country = $feature['properties']['name'];
        $feature['properties']['guessed'] = false;
    }

    echo json_encode($geojson);
} else {
    echo json_encode(['error' => 'File not found']);
}
?>