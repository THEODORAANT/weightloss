<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if (isset($payload['user_id'])) {
    $memberID = (int)$payload['user_id'];
    $db = PerchDB::fetch();
    $prefix = PERCH_DB_PREFIX;

    $sql = "SELECT COUNT(DISTINCT o.orderID)
            FROM {$prefix}shop_orders o
            INNER JOIN {$prefix}shop_customers c ON o.customerID = c.customerID
            INNER JOIN {$prefix}shop_order_items oi ON oi.orderID = o.orderID
            INNER JOIN {$prefix}shop_products p ON p.productID = oi.productID
            INNER JOIN {$prefix}shop_index si
                ON si.itemKey = 'productID'
                AND si.indexKey = '_category'
                AND (si.indexValue = 'products/weight-loss/' OR si.indexValue LIKE 'products/weight-loss/%')
                AND si.itemID = CASE WHEN p.parentID > 0 THEN p.parentID ELSE p.productID END
            WHERE c.memberID = {$memberID}
            AND o.orderDeleted IS NULL
            AND o.orderStatus IN (
                SELECT os.statusKey FROM {$prefix}shop_order_statuses os
                WHERE os.statusDeleted IS NULL
                AND os.statusIndex >= (
                    SELECT os2.statusIndex FROM {$prefix}shop_order_statuses os2
                    WHERE os2.statusKey = 'paid' AND os2.statusDeleted IS NULL
                )
            )";

    $count = $db->get_count($sql);
    echo json_encode(["is_reorder" => (bool)$count]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Member not found"]);
}
