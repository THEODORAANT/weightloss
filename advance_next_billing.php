<?php

require_once __DIR__ . '/perch/runtime.php';
require_once __DIR__ . '/next_payment_scheduler.php';

if (PHP_SAPI !== 'cli') {
    exit; // Only allow CLI usage
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php advance_next_billing.php <packageID>\n");
    exit(1);
}

$API = new PerchAPI(1.0, 'perch_shop');
$DB  = PerchDB::fetch();

$packageID = (int)$argv[1];
$table     = PERCH_DB_PREFIX . 'shop_packages';

// Fetch current nextBillingDate for the package
$sql    = 'SELECT nextBillingDate FROM ' . $table . ' WHERE packageID=' . $DB->pdb($packageID);
$record = $DB->get_row($sql);

if (!$record || empty($record['nextBillingDate'])) {
    fwrite(STDERR, "Package not found or nextBillingDate missing\n");
    exit(1);
}

$lastBilling = new DateTimeImmutable($record['nextBillingDate']);
$nextBilling = nextMonthlyPayment($lastBilling)->format('Y-m-d');

$update = 'UPDATE ' . $table . ' SET nextBillingDate=' . $DB->pdb($nextBilling)
    . ' WHERE packageID=' . $DB->pdb($packageID);
$DB->execute($update);

echo "nextBillingDate set to {$nextBilling} for package {$packageID}" . PHP_EOL;
