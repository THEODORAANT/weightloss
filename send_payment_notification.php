<?php

require_once __DIR__ . '/perch/runtime.php';

$API       = new PerchAPI(1.0, 'perch_shop');
$DB        = PerchDB::fetch();
$Customers = new PerchShop_Customers($API);
$table  = PERCH_DB_PREFIX . 'shop_packages';
$tableitems  = PERCH_DB_PREFIX . 'shop_package_items';
$target = (new DateTimeImmutable('+1 week'))->format('Y-m-d');

$log_file = __DIR__ . '/send_payment_notification.log';
$sent     = [];
if (file_exists($log_file)) {
    foreach (file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode('|', $line);
        if (count($parts) >= 2) {
            $sent[$parts[0] . '|' . $parts[1]] = true;
        }
    }
}

$sql = 'SELECT p.customerID, i.billingDate FROM ' . $tableitems .
       ' as i inner join   ' . $table . ' as p WHERE i.packageID=p.uuid and  p.billing_type="monthly" and i.paymentStatus=' . $DB->pdb('pending') .
       ' AND i.billingDate=' . $DB->pdb($target);
$packages = $DB->get_rows($sql);

if (PerchUtil::count($packages)) {
    foreach ($packages as $package) {
        $key = $package['customerID'] . '|' . $package['billingDate'];
        if (isset($sent[$key])) {
            file_put_contents(
                $log_file,
                $key . '|' . date('c') . "|skipped\n",
                FILE_APPEND | LOCK_EX
            );
            continue;
        }

        $Customer = $Customers->find((int)$package['customerID']);
        if (!$Customer) {
            continue;
        }
        $memberID = $Customer->memberID();
        $title    = 'Upcoming Payment Reminder';
        $message  = 'Your next payment is due on ' . $package['billingDate'] . '. Please complete it from your portal.';

        perch_member_add_notification($memberID, $title, $message);
        $Email = new PerchEmail('');
        $Email->subject('Upcoming Payment Reminder');
        $Email->senderName('Weightloss');
        $Email->senderEmail('no-reply@example.com');
        $Email->recipientEmail($Customer->customerEmail());
        $Email->body('Your next payment is due on ' . $package['billingDate'] . '. Please complete it from your portal.');

        $Email->send();
        file_put_contents(
            $log_file,
            $key . '|' . date('c') . "|sent\n",
            FILE_APPEND | LOCK_EX
        );
        $sent[$key] = true;
    }
}

