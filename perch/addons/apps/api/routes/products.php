<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../lib/product_format.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    $data = [];
}

$category = $_GET['category'] ?? ($data['category'] ?? 'products/weight-loss');

$rawProducts = perch_shop_products([
    'category' => $category,
    'skip-template' => true,
    'variants' => true,
    'api' => true,
]);

$products = [];

if (is_array($rawProducts)) {
    foreach ($rawProducts as $product) {
        $formatted = wl_format_product($product);
        if ($formatted !== null) {
            $products[] = $formatted;
        }
    }
}

echo json_encode(['products' => $products], JSON_UNESCAPED_SLASHES);
