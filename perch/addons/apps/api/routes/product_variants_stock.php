<?php
include(__DIR__ . '/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    $data = [];
}

$productId = $_GET['product_id'] ?? ($data['product_id'] ?? null);
if ($productId !== null) {
    if (!is_numeric($productId) || (int)$productId < 1) {
        http_response_code(400);
        echo json_encode(['error' => 'product_id must be a positive integer']);
        exit;
    }
    $productId = (int) $productId;
}

$db = PerchDB::fetch();
$prefix = PERCH_DB_PREFIX;

$where = [
    'variants.parentID IS NOT NULL',
    'variants.productDeleted IS NULL',
    'parent.productDeleted IS NULL'
];

if ($productId !== null) {
    $where[] = 'variants.parentID = ' . $db->pdb($productId);
}

$sql = "
    SELECT
        variants.productID AS variantID,
        variants.parentID AS productID,
        variants.title,
        variants.productVariantDesc,
        variants.sku,
        variants.stock_level AS variantStock,
        parent.stock_level AS parentStock,
        parent.productStockOnParent AS stockOnParent,
        CASE
            WHEN parent.productStockOnParent = 1 THEN parent.stock_level
            ELSE variants.stock_level
        END AS resolvedStock
    FROM {$prefix}shop_products variants
    INNER JOIN {$prefix}shop_products parent
        ON variants.parentID = parent.productID
    WHERE " . implode(' AND ', $where) . "
    ORDER BY variants.parentID ASC, variants.productID ASC
";

$rows = $db->get_rows($sql);

$variants = [];

if (PerchUtil::count($rows)) {
    foreach ($rows as $row) {
        $resolvedStock = $row['resolvedStock'];
        if ($resolvedStock !== null && is_numeric($resolvedStock)) {
            $resolvedStock = (int) $resolvedStock;
        } else {
            $resolvedStock = null;
        }

        $variants[] = [
            'variant_id' => isset($row['variantID']) ? (int) $row['variantID'] : null,
            'product_id' => isset($row['productID']) ? (int) $row['productID'] : null,
            'sku' => $row['sku'] ?? '',
            'title' => $row['title'] ?? '',
            'variant' => $row['productVariantDesc'] ?? null,
            'stock_level' => $resolvedStock,
            'stock_on_parent' => ($row['stockOnParent'] ?? null) ? true : false,
            'variant_stock_raw' => isset($row['variantStock']) && is_numeric($row['variantStock']) ? (int) $row['variantStock'] : null,
            'parent_stock_raw' => isset($row['parentStock']) && is_numeric($row['parentStock']) ? (int) $row['parentStock'] : null,
        ];
    }
}

echo json_encode(['variants' => $variants], JSON_UNESCAPED_SLASHES);
