<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing product ID"]);
    exit;
}

$variants = perch_shop_product_variants($id, ['skip-template' => true]);

if ($variants) {
    echo json_encode(['variants' => $variants]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Product not found"]);
}
