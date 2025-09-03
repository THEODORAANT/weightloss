<?php
header('Content-Type: application/json');

if (!isset($_POST['query']) || empty($_POST['query'])) {
    echo json_encode([]);
    exit;
}

$query = $_POST['query'];
$url = 'https://api.postcodes.io/postcodes?q=' . urlencode($query);

$response = file_get_contents($url);
if ($response === FALSE) {
    echo json_encode([]);
    exit;
}

$data = json_decode($response, true);
if ($data['status'] !== 200 || empty($data['result'])) {
    echo json_encode([]);
    exit;
}

// Build simple suggestion strings
$suggestions = array_map(function($item) {
    return $item['postcode'] . ' - ' . $item['admin_ward'];
}, $data['result']);

echo json_encode(array_slice($suggestions, 0, 5)); // Limit to 5 suggestions
