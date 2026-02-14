<?php

require_once __DIR__ . '/../perch/runtime.php';
require_once __DIR__ . '/../perch/addons/apps/api/routes/lib/comms_service.php';

$options = getopt('', [
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo 'Usage: php scripts/update_customer_pharmacy_refid.php [--dry-run]' . PHP_EOL;
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);

$API = new PerchAPI(1.0, 'perch_shop');
$Customers = new PerchShop_Customers($API);
$DB = PerchDB::fetch();

$rows = $DB->get_rows(
    'SELECT customerID, customerEmail FROM ' . PERCH_DB_PREFIX . "shop_customers WHERE customerEmail IS NOT NULL AND customerEmail != '' ORDER BY customerID ASC"
);

if (!PerchUtil::count($rows)) {
    echo 'No customers with email were found.' . PHP_EOL;
    exit(0);
}

$total = 0;
$updated = 0;
$unchanged = 0;
$failed = 0;

foreach ($rows as $row) {
    $total++;

    $localCustomerID = (int) ($row['customerID'] ?? 0);
    $email = trim((string) ($row['customerEmail'] ?? ''));

    if ($localCustomerID <= 0 || $email === '') {
        $failed++;
        continue;
    }

    $Customer = $Customers->find($localCustomerID);
    if (!$Customer instanceof PerchShop_Customer) {
        echo 'Customer #' . $localCustomerID . ' not found in model lookup. Skipping.' . PHP_EOL;
        $failed++;
        continue;
    }

    $commsResponse = comms_service_get_customer_by_email($email);
    if (!is_array($commsResponse) || !($commsResponse['success'] ?? false)) {
        echo 'Comms lookup failed for customer #' . $localCustomerID . ' (' . $email . '). Skipping.' . PHP_EOL;
        $failed++;
        continue;
    }

    $remoteCustomerId = comms_service_extract_customer_id($commsResponse);
    if ($remoteCustomerId === '') {
        echo 'No comms customerId returned for customer #' . $localCustomerID . ' (' . $email . '). Skipping.' . PHP_EOL;
        $failed++;
        continue;
    }

    $currentRefId = trim((string) $Customer->pharmacy_refid());

    if ($currentRefId === $remoteCustomerId) {
        $unchanged++;
        echo 'No change for customer #' . $localCustomerID . ': pharmacy_refid already "' . $remoteCustomerId . '".' . PHP_EOL;
        continue;
    }

    if ($dryRun) {
        $updated++;
        echo '[dry-run] Would update customer #' . $localCustomerID . ' pharmacy_refid from "' . $currentRefId . '" to "' . $remoteCustomerId . '".' . PHP_EOL;
        continue;
    }

    $ok = $Customer->update(['pharmacy_refid' => $remoteCustomerId]);

    if ($ok) {
        $updated++;
        echo 'Updated customer #' . $localCustomerID . ' pharmacy_refid to "' . $remoteCustomerId . '".' . PHP_EOL;
    } else {
        $failed++;
        echo 'Failed to update customer #' . $localCustomerID . '.' . PHP_EOL;
    }
}

echo PHP_EOL;
echo 'Summary: total=' . $total . ', updated=' . $updated . ', unchanged=' . $unchanged . ', failed=' . $failed;
if ($dryRun) {
    echo ' [dry-run]';
}
echo '.' . PHP_EOL;

exit($failed > 0 ? 1 : 0);
