<?php

declare(strict_types=1);

$options = getopt('', [
    'orders::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo 'Usage: php scripts/send_specific_orders_to_pharmacy.php [--orders=123,456] [--dry-run]' . PHP_EOL;
    echo '       --orders   Comma-separated list of order IDs to process. If omitted, uses ORDER_IDS in the script.' . PHP_EOL;
    echo '       --dry-run  Validate and print what would be sent without calling sendOrdertoPharmacy().' . PHP_EOL;
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);

require_once __DIR__ . '/../perch/runtime.php';
require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_sync.php';

// Default list for quick ad-hoc runs. Update as needed.
const ORDER_IDS = [4830];

$orderIDs = ORDER_IDS;
if (isset($options['orders']) && $options['orders'] !== false && trim((string)$options['orders']) !== '') {
    $rawIDs = explode(',', (string)$options['orders']);
    $orderIDs = [];
    foreach ($rawIDs as $rawID) {
        $id = (int)trim($rawID);
        if ($id > 0) {
            $orderIDs[] = $id;
        }
    }
}

$orderIDs = array_values(array_unique($orderIDs));

if (count($orderIDs) === 0) {
    fwrite(STDERR, 'No order IDs provided. Set ORDER_IDS in the script or pass --orders=123,456.' . PHP_EOL);
    exit(1);
}

$api = new PerchAPI(1.0, 'perch_shop');
$Orders = new PerchShop_Orders($api);
$Customers = new PerchShop_Customers($api);

$processed = 0;
$sent = 0;
$skipped = 0;
$failed = 0;

foreach ($orderIDs as $orderID) {
    $processed++;
    $Order = $Orders->find((int)$orderID);

    if (!$Order) {
        echo '[skip] Order #' . $orderID . ' was not found.' . PHP_EOL;
        $skipped++;
        continue;
    }

    if (!$Order->is_paid()) {
        echo '[skip] Order #' . $orderID . ' is not paid.' . PHP_EOL;
        $skipped++;
        continue;
    }

    $Customer = $Customers->find((int)$Order->customerID());
    if (!$Customer) {
        echo '[fail] Order #' . $orderID . ' has no valid customer.' . PHP_EOL;
        $failed++;
        continue;
    }

    $customerId = trim((string)$Customer->pharmacy_refid());
    if ($customerId === '') {
        $memberID = (int)$Customer->memberID();
        if ($memberID <= 0) {
            echo '[fail] Order #' . $orderID . ' customer has no memberID; cannot sync customerId.' . PHP_EOL;
            $failed++;
            continue;
        }

        if ($dryRun) {
            echo '[dry-run] Order #' . $orderID . ' missing customerId; would call comms_sync_member(' . $memberID . ').' . PHP_EOL;
        } else {
            $synced = comms_sync_member($memberID);
            if (!$synced) {
                echo '[fail] Order #' . $orderID . ' failed to sync member #' . $memberID . ' for customerId.' . PHP_EOL;
                $failed++;
                continue;
            }
        }

        $Customer = $Customers->find((int)$Order->customerID());
        $customerId = $Customer ? trim((string)$Customer->pharmacy_refid()) : '';
        if ($customerId === '') {
            echo '[fail] Order #' . $orderID . ' still has empty customerId after sync.' . PHP_EOL;
            $failed++;
            continue;
        }
    }

    if ($dryRun) {
        echo '[dry-run] Would send order #' . $orderID . ' for customer #' . $Customer->id() . ' to pharmacy.' . PHP_EOL;
        $sent++;
        continue;
    }

    try {
        $response = $Order->sendOrdertoPharmacy($Customer);
        echo '[sent] Order #' . $orderID . ' sent. Response: ' . json_encode($response) . PHP_EOL;
        $sent++;
    } catch (Throwable $e) {
        echo '[fail] Order #' . $orderID . ' failed: ' . $e->getMessage() . PHP_EOL;
        $failed++;
    }
}

echo 'Done. processed=' . $processed . ', sent=' . $sent . ', skipped=' . $skipped . ', failed=' . $failed . PHP_EOL;

if ($failed > 0) {
    exit(2);
}

exit(0);
