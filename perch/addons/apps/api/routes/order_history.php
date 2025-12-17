<?php
include(__DIR__ .'/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/lib/pharmacy.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$memberID = (int)$payload['user_id'];

// Get database connection
$db = PerchDB::fetch();
$prefix = PERCH_DB_PREFIX;

// Query to get all orders for this member with full details
// Note: Using direct substitution instead of parameterized query due to database layer quirk
$sql = "
    SELECT
        o.*,
        c.customerFirstName,
        c.customerLastName,
        c.customerEmail,
        c.customerDynamicFields as customerData,
        sa.addressLine1 as shippingAddress1,
        sa.addressDynamicFields as shippingAddressData,
        sa.countryID as shippingCountryID,
        ba.addressLine1 as billingAddress1,
        ba.addressDynamicFields as billingAddressData,
        ba.countryID as billingCountryID,
        sc.country as shippingCountryName,
        sc.iso2 as shippingCountryISO2,
        bc.country as billingCountryName,
        bc.iso2 as billingCountryISO2,
        os.statusTitle as orderStatusTitle
    FROM {$prefix}shop_orders o
    LEFT JOIN {$prefix}shop_customers c ON o.customerID = c.customerID
    LEFT JOIN {$prefix}shop_addresses sa ON o.orderShippingAddress = sa.addressID
    LEFT JOIN {$prefix}shop_addresses ba ON o.orderBillingAddress = ba.addressID
    LEFT JOIN {$prefix}shop_countries sc ON sa.countryID = sc.countryID
    LEFT JOIN {$prefix}shop_countries bc ON ba.countryID = bc.countryID
    LEFT JOIN {$prefix}shop_order_statuses os ON o.orderStatus = os.statusKey
    WHERE c.memberID = {$memberID}
    AND o.orderDeleted IS NULL
    ORDER BY o.orderCreated DESC
";

$orders = $db->get_rows($sql);

if (!$orders) {
    echo json_encode([]);
    exit;
}

// Get order IDs for pharmacy lookup and order items
$orderIDs = [];
foreach ($orders as $order) {
    $orderIDs[] = (int)$order['orderID'];
}

// Get pharmacy information
$pharmacyLookup = get_pharmacy_lookup($orderIDs);

// Get order items for all orders
$orderItemsSQL = "
    SELECT
        oi.orderID,
        oi.itemID,
        oi.itemType,
        oi.productID,
        oi.itemPrice,
        oi.itemTax,
        oi.itemDiscount,
        oi.itemTotal,
        oi.itemQty,
        oi.itemDynamicFields,
        p.title as productTitle,
        p.sku as productSku,
        p.productDynamicFields as productData
    FROM {$prefix}shop_order_items oi
    LEFT JOIN {$prefix}shop_products p ON oi.productID = p.productID
    WHERE oi.orderID IN (" . $db->implode_for_sql_in($orderIDs) . ")
    ORDER BY oi.itemID
";

$allOrderItems = $db->get_rows($orderItemsSQL);

// Organize items by orderID
$itemsByOrder = [];
if ($allOrderItems) {
    foreach ($allOrderItems as $item) {
        $orderId = (int)$item['orderID'];
        if (!isset($itemsByOrder[$orderId])) {
            $itemsByOrder[$orderId] = [];
        }

        // Parse dynamic fields if they exist
        $itemDynamicFields = [];
        if (!empty($item['itemDynamicFields'])) {
            $parsed = json_decode($item['itemDynamicFields'], true);
            if (is_array($parsed)) {
                $itemDynamicFields = $parsed;
            }
        }

        $productDynamicFields = [];
        if (!empty($item['productData'])) {
            $parsed = json_decode($item['productData'], true);
            if (is_array($parsed)) {
                $productDynamicFields = $parsed;
            }
        }

        $itemsByOrder[$orderId][] = [
            'itemID' => (int)$item['itemID'],
            'itemType' => $item['itemType'],
            'productID' => isset($item['productID']) ? (int)$item['productID'] : null,
            'productTitle' => $item['productTitle'] ?? null,
            'productSku' => $item['productSku'] ?? null,
            'itemPrice' => $item['itemPrice'],
            'itemTax' => $item['itemTax'],
            'itemDiscount' => $item['itemDiscount'],
            'itemTotal' => $item['itemTotal'],
            'itemQty' => (int)$item['itemQty'],
            'itemDetails' => $itemDynamicFields,
            'productDetails' => $productDynamicFields
        ];
    }
}

// Build the response
$response = [];
foreach ($orders as $order) {
    $orderId = (int)$order['orderID'];

    // Parse address data
    $shippingAddress = null;
    if (!empty($order['shippingAddressData'])) {
        $shippingData = json_decode($order['shippingAddressData'], true);
        if (is_array($shippingData)) {
            $shippingAddress = [
                'address1' => $shippingData['address_1'] ?? $order['shippingAddress1'] ?? '',
                'address2' => $shippingData['address_2'] ?? '',
                'city' => $shippingData['city'] ?? '',
                'postcode' => $shippingData['postcode'] ?? '',
                'countryID' => $order['shippingCountryID'] ?? null,
                'country' => $order['shippingCountryName'] ?? '',
                'countryISO2' => $order['shippingCountryISO2'] ?? ''
            ];
        }
    }

    $billingAddress = null;
    if (!empty($order['billingAddressData'])) {
        $billingData = json_decode($order['billingAddressData'], true);
        if (is_array($billingData)) {
            $billingAddress = [
                'address1' => $billingData['address_1'] ?? $order['billingAddress1'] ?? '',
                'address2' => $billingData['address_2'] ?? '',
                'city' => $billingData['city'] ?? '',
                'postcode' => $billingData['postcode'] ?? '',
                'countryID' => $order['billingCountryID'] ?? null,
                'country' => $order['billingCountryName'] ?? '',
                'countryISO2' => $order['billingCountryISO2'] ?? ''
            ];
        }
    }

    // Parse customer data
    $customerData = [];
    if (!empty($order['customerData'])) {
        $parsed = json_decode($order['customerData'], true);
        if (is_array($parsed)) {
            $customerData = $parsed;
        }
    }

    // Build order object
    $orderData = [
        'orderID' => $orderId,
        'orderStatus' => $order['orderStatus'],
        'orderStatusTitle' => $order['orderStatusTitle'] ?? $order['orderStatus'],
        'orderInvoiceNumber' => $order['orderInvoiceNumber'],
        'orderTotal' => $order['orderTotal'],
        'orderSubtotal' => $order['orderSubtotal'],
        'orderTaxTotal' => $order['orderTaxTotal'],
        'orderShippingTotal' => $order['orderShippingTotal'],
        'orderDiscountsTotal' => $order['orderDiscountsTotal'],
        'orderGateway' => $order['orderGateway'],
        'orderGatewayRef' => $order['orderGatewayRef'],
        'orderCreated' => $order['orderCreated'],
        'orderUpdated' => $order['orderUpdated'],
        'customer' => [
            'firstName' => $order['customerFirstName'],
            'lastName' => $order['customerLastName'],
            'email' => $order['customerEmail'],
            'details' => $customerData
        ],
        'shippingAddress' => $shippingAddress,
        'billingAddress' => $billingAddress,
        'items' => $itemsByOrder[$orderId] ?? [],
        'pharmacy' => isset($pharmacyLookup[$orderId]) ? $pharmacyLookup[$orderId] : null
    ];

    $response[] = $orderData;
}

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
