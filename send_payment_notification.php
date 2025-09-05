<?php

require_once __DIR__ . '/perch/runtime.php';

$API       = new PerchAPI(1.0, 'perch_shop');
$DB        = PerchDB::fetch();
$Customers = new PerchShop_Customers($API);

$table  = PERCH_DB_PREFIX . 'shop_packages';
$target = (new DateTimeImmutable('+1 week'))->format('Y-m-d');

$sql = 'SELECT customerID, nextBillingDate FROM ' . $table .
       ' WHERE status=' . $DB->pdb('pending') .
       ' AND nextBillingDate=' . $DB->pdb($target);

$packages = $DB->get_rows($sql);

if (PerchUtil::count($packages)) {
    foreach ($packages as $package) {
        $Customer = $Customers->find((int)$package['customerID']);
        if (!$Customer) {
            continue;
        }

        $Email = new PerchEmail('');
        $Email->subject('Upcoming Payment Reminder');
        $Email->senderName('Weightloss');
        $Email->senderEmail('no-reply@example.com');
        $Email->recipientEmail($Customer->customerEmail());
        $Email->body('Your next payment is due on ' . $package['nextBillingDate'] . '. Please complete it from your portal.');
        $Email->send();
    }
}

