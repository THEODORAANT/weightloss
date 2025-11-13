<?php

require_once __DIR__ . '/../perch/runtime.php';




$options = getopt('', [
    'customer-id:',
    'order-id:',
    'city:',
    'dry-run',
    'help',
]);



$customerID = isset($options['customer-id']) ? (int) $options['customer-id'] : 0;
$orderID    = isset($options['order-id']) ? (int) $options['order-id'] : 0;
$city       = isset($options['city']) ? trim((string) $options['city']) : '';
$dryRun     = array_key_exists('dry-run', $options);


$city = preg_replace('/\s+/', ' ', $city);


$DB        = PerchDB::fetch();
$tableBase = PERCH_DB_PREFIX . 'shop_orders';

$order = $DB->get_row(
    'SELECT orderID, customerID, orderShippingAddress, orderBillingAddress '
    . 'FROM ' . $tableBase
    . ' WHERE orderID = ' . $DB->pdb($orderID)
    . ' LIMIT 1'
);


$orderCustomerID = isset($order['customerID']) ? (int) $order['customerID'] : 0;
if ($orderCustomerID !== $customerID) {

    exit(1);
}

$addressIDs = [];

foreach (['orderShippingAddress', 'orderBillingAddress'] as $addressKey) {
    if (isset($order[$addressKey]) && (int) $order[$addressKey] > 0) {
        $addressIDs[(int) $order[$addressKey]] = true;
    }
}

$addressRecords = [];

if (PerchUtil::count($addressIDs)) {
    $addressIDList = implode(', ', array_map('intval', array_keys($addressIDs)));
    $sql = 'SELECT addressID, customerID, orderID, addressDynamicFields '
        . 'FROM ' . PERCH_DB_PREFIX . 'shop_addresses '
        . 'WHERE addressID IN (' . $addressIDList . ')';
    $rows = $DB->get_rows($sql);

    if (PerchUtil::count($rows)) {
        foreach ($rows as $row) {
            $addressRecords[(int) $row['addressID']] = $row;
        }
    }
}

$orderSpecificAddresses = $DB->get_rows(
    'SELECT addressID, customerID, orderID, addressDynamicFields '
    . 'FROM ' . PERCH_DB_PREFIX . 'shop_addresses '
    . 'WHERE customerID = ' . $DB->pdb($customerID)
    . ' AND orderID = ' . $DB->pdb($orderID)
);

if (PerchUtil::count($orderSpecificAddresses)) {
    foreach ($orderSpecificAddresses as $row) {
        $addressRecords[(int) $row['addressID']] = $row;
    }
}

if (!PerchUtil::count($addressRecords)) {
    echo 'No addresses were found for customer #' . $customerID . ' and order #' . $orderID . '.' . PHP_EOL;
    exit(0);
}

ksort($addressRecords);

$updated = 0;
$skipped = 0;

foreach ($addressRecords as $addressID => $row) {
    $dynamicFields = [];

    if (!empty($row['addressDynamicFields'])) {
        $decoded = PerchUtil::json_safe_decode($row['addressDynamicFields'], true);
        if (is_array($decoded)) {
            $dynamicFields = $decoded;
        }
    }

    $previousCity = $dynamicFields['city'] ?? null;

    if ($previousCity !== null && trim((string) $previousCity) === $city) {
        echo 'Address #' . $addressID . ' already has the city set to "' . $city . '". Skipping.' . PHP_EOL;
        $skipped++;
        continue;
    }

    $dynamicFields['city'] = $city;

    $payload = [
        'addressDynamicFields' => PerchUtil::json_safe_encode($dynamicFields),
        'addressUpdated'       => date('Y-m-d H:i:s'),
    ];

    echo 'Updating address #' . $addressID . ' (customer #' . (int) $row['customerID']
        . ', order ' . ($row['orderID'] === null ? 'none' : '#' . (int) $row['orderID'])
        . ') to city "' . $city . '"';

    if ($dryRun) {
        echo ' [dry-run]' . PHP_EOL;
        $skipped++;
        continue;
    }

    $result = $DB->update(PERCH_DB_PREFIX . 'shop_addresses', $payload, 'addressID', $addressID);

    if ($result) {
        echo '... done.' . PHP_EOL;
        $updated++;
    } else {
        echo '... no changes applied.' . PHP_EOL;
        $skipped++;
    }
}

echo PHP_EOL . 'Summary: ' . $updated . ' address(es) updated, ' . $skipped . ' skipped.' . PHP_EOL;

exit(0);
