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

$API = new PerchAPI(1.0, 'perch_shop');
$OrderStatuses = new PerchShop_OrderStatuses($API);
$paidStatuses = $OrderStatuses->get_status_and_above('paid');
if (!is_array($paidStatuses) || !count($paidStatuses)) {
    $paidStatuses = ['paid'];
}

$statusSql = $db->implode_for_sql_in($paidStatuses);

$where = [
    'variants.parentID IS NOT NULL',
    'variants.productDeleted IS NULL',
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
        COALESCE(SUM(CASE WHEN o.orderID IS NOT NULL THEN oi.itemQty ELSE 0 END), 0) AS totalPaidQuantity
    FROM {$prefix}shop_products variants
    LEFT JOIN {$prefix}shop_order_items oi
        ON oi.productID = variants.productID
        AND oi.itemType = 'product'
    LEFT JOIN {$prefix}shop_orders o
        ON oi.orderID = o.orderID
        AND o.orderDeleted IS NULL
        AND o.orderStatus IN ({$statusSql})
    WHERE " . implode(' AND ', $where) . "
    GROUP BY variants.productID, variants.parentID, variants.title, variants.productVariantDesc, variants.sku
    ORDER BY variants.parentID ASC, variants.productID ASC
";

$rows = $db->get_rows($sql);

$variants = [];

if (PerchUtil::count($rows)) {
    foreach ($rows as $row) {
        $variants[] = [
            'variant_id' => isset($row['variantID']) ? (int) $row['variantID'] : null,
            'product_id' => isset($row['productID']) ? (int) $row['productID'] : null,
            'sku' => $row['sku'] ?? '',
            'title' => $row['title'] ?? '',
            'variant' => $row['productVariantDesc'] ?? null,
            'total_paid' => isset($row['totalPaidQuantity']) ? (int) $row['totalPaidQuantity'] : 0,
        ];
    }
}

echo json_encode(['variants' => $variants], JSON_UNESCAPED_SLASHES);
