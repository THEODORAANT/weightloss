<?php

require_once __DIR__ . '/perch/runtime.php';

$API       = new PerchAPI(1.0, 'perch_shop');
$DB        = PerchDB::fetch();
$Customers = new PerchShop_Customers($API);
$emailOptOutCustomers = [
    // Add customer IDs who have requested not to receive payment reminder emails.
];
$table  = PERCH_DB_PREFIX . 'shop_packages';
$tableitems  = PERCH_DB_PREFIX . 'shop_package_items';
$target = (new DateTimeImmutable('+1 week'))->format('Y-m-d');
echo $target;
$log_dir  = __DIR__ . '/logs/notifications';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}
if (!is_writable($log_dir)) {
    chmod($log_dir, 0777);
}
$log_file = $log_dir . '/send_payment_notification' . $target . '.log';

$sent = [];
if (file_exists($log_file)) {
    foreach (file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode('|', $line);
        if (count($parts) >= 5) {
            $sent['item:' . $parts[0]] = true;
        } elseif (count($parts) >= 2) {
            $sent['legacy:' . $parts[0] . '|' . $parts[1]] = true;
        }
    }
}

$writeLog = function ($itemID, $customerID, $billingDate, $status) use ($log_file) {
    $line = $itemID . '|' . $customerID . '|' . $billingDate . '|' . date('c') . '|' . $status . "\n";
    file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
};

$sql = 'SELECT p.customerID, i.billingDate, i.itemID FROM ' . $tableitems .
       ' as i inner join   ' . $table . ' as p WHERE i.packageID=p.uuid and  p.billing_type="monthly" and i.paymentStatus=' . $DB->pdb('pending') .
       ' AND i.billingDate=' . $DB->pdb($target);
$packages = $DB->get_rows($sql);

if (PerchUtil::count($packages)) {
    foreach ($packages as $package) {
        $itemID = isset($package['itemID']) ? (int) $package['itemID'] : 0;
        $customerID = isset($package['customerID']) ? (int) $package['customerID'] : 0;
        $billingDate = $package['billingDate'] ?? '';
        $itemKey = 'item:' . $itemID;
        $legacyKey = 'legacy:' . $customerID . '|' . $billingDate;

        if (($itemID && isset($sent[$itemKey])) || isset($sent[$legacyKey])) {
            $writeLog($itemID, $customerID, $billingDate, 'skipped');
            $sent[$itemKey] = true;
            if (isset($sent[$legacyKey])) {
                unset($sent[$legacyKey]);
            }
            continue;
        }

        if (in_array($customerID, $emailOptOutCustomers, true)) {
            $writeLog($itemID, $customerID, $billingDate, 'skipped-email-opt-out');
            echo 'Skipping customer ' . $customerID . ' – opted out of payment reminder emails.' . PHP_EOL;
            continue;
        }

        $Customer = $Customers->find((int) $customerID);
        if (!$Customer) {
            continue;
        }
        $memberID = $Customer->memberID();
        $title    = 'Upcoming Payment Reminder';
        $message  = 'Your next payment is due on ' . $billingDate . '. Please complete it from your portal.';

        perch_member_add_notification($memberID, $title, $message);
       try{

 $emailsend=send_monthly_notification( $Customer,$message);
 echo "email sent";echo $Customer->customerEmail();echo $emailsend;
      /*       $API  = new PerchAPI(1.0, 'perch_shop');
             $Email = $API->get('Email');
           $Email->subject('Upcoming Payment Reminder');
           $Email->senderName('Weightloss');
           $Email->senderEmail('no-reply@example.com');
           $Email->recipientEmail($Customer->customerEmail());
           $Email->body('Your next payment is due on ' . $package['billingDate'] . '. Please complete it from your portal.');

           $Email->send();*/
           } catch (Exception $exc) {
           print_r($exc);
           }
        $writeLog($itemID, $customerID, $billingDate, 'sent');
        $sent[$itemKey] = true;
    }
}
