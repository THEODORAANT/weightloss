<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/product_format.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    $data = [];
}

$id = $_GET['id'] ?? ($data['id'] ?? null);

if ($id === null || $id === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing product ID'], JSON_UNESCAPED_SLASHES);
    exit;
}

$product = perch_shop_product($id, [
    'skip-template' => true,
    'variants' => true,
    'api' => true,
]);

if ($product) {
    $formatted = wl_format_product($product);
    if ($formatted === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to format product'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    echo json_encode(['product' => $formatted], JSON_UNESCAPED_SLASHES);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found'], JSON_UNESCAPED_SLASHES);
}
