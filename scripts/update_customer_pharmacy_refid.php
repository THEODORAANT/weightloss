<?php

require_once __DIR__ . '/../perch/runtime.php';
require_once __DIR__ . '/../perch/addons/apps/api/routes/lib/comms_service.php';

$options = getopt('', [
    'email:',
    'dry-run',
    'help',
]);

if (isset($options['help']) || !isset($options['email'])) {
    echo 'Usage: php scripts/update_customer_pharmacy_refid.php --email=<customer-email> [--dry-run]' . PHP_EOL;
    exit(isset($options['help']) ? 0 : 1);
}

$email = trim((string) $options['email']);
$dryRun = array_key_exists('dry-run', $options);

if ($email === '') {
    echo 'Error: --email cannot be empty.' . PHP_EOL;
    exit(1);
}

$commsResponse = comms_service_get_customer_by_email($email);
if (!is_array($commsResponse) || !($commsResponse['success'] ?? false)) {
    echo 'Error: comm service request failed for email: ' . $email . PHP_EOL;
    exit(1);
}

$customerId = comms_service_extract_customer_id($commsResponse);
if ($customerId === '') {
    echo 'Error: no customerId returned for email: ' . $email . PHP_EOL;
    exit(1);
}

$API = new PerchAPI(1.0, 'perch_shop');
$Customers = new PerchShop_Customers($API);
$Customer = $Customers->get_one_by('customerEmail', $email);

if (!$Customer instanceof PerchShop_Customer) {
    $DB = PerchDB::fetch();
    $row = $DB->get_row(
        'SELECT customerID FROM ' . PERCH_DB_PREFIX . 'shop_customers WHERE LOWER(customerEmail)=LOWER(' . $DB->pdb($email) . ') LIMIT 1'
    );

    if (is_array($row) && isset($row['customerID'])) {
        $Customer = $Customers->find((int) $row['customerID']);
    }
}

if (!$Customer instanceof PerchShop_Customer) {
    echo 'Error: no Perch customer found for email: ' . $email . PHP_EOL;
    exit(1);
}

$currentRefId = trim((string) $Customer->pharmacy_refid());

if ($currentRefId === $customerId) {
    echo 'No update needed. pharmacy_refid is already set to: ' . $customerId . PHP_EOL;
    exit(0);
}

if ($dryRun) {
    echo '[dry-run] Would update customer #' . $Customer->id() . ' pharmacy_refid from "' . $currentRefId . '" to "' . $customerId . '"' . PHP_EOL;
    exit(0);
}

$updated = $Customer->update(['pharmacy_refid' => $customerId]);

if (!$updated) {
    echo 'Error: failed to update pharmacy_refid for customer #' . $Customer->id() . PHP_EOL;
    exit(1);
}

echo 'Updated customer #' . $Customer->id() . ' pharmacy_refid to "' . $customerId . '"' . PHP_EOL;
exit(0);
