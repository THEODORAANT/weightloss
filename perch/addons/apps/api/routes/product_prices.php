<?php
include(__DIR__ . '/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing product ID"]);
    exit;
}

$prices = perch_shop_product_prices((int) $id);

if ($prices === false) {
    http_response_code(404);
    echo json_encode(["error" => "Product not found"]);
    exit;
}

echo json_encode($prices);
