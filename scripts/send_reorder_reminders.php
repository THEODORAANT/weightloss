<?php

declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

$options = getopt('', [
    'date::',
    'days::',
    'test-date::',
    'order-id::',
    'customer-id::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo "Usage: php scripts/send_reorder_reminders.php [--dry-run] [--date=YYYY-MM-DD] [--days=<n>] [--order-id=<id>] [--customer-id=<id>]" . PHP_EOL;
    echo "       --dry-run        Output the actions without sending notifications." . PHP_EOL;
    echo "       --date           Process orders created on the specified date (YYYY-MM-DD)." . PHP_EOL;
    echo "       --days           Process orders created N days ago (default: 21)." . PHP_EOL;
    echo "       --test-date      Dry-run orders created on the specified date (YYYY-MM-DD)." . PHP_EOL;
    echo "       --order-id       Limit the run to a specific order ID." . PHP_EOL;
    echo "       --customer-id    Limit the run to a specific customer ID." . PHP_EOL;
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);

$dateOption = $options['date'] ?? null;
$daysOption = $options['days'] ?? null;
$testDateOption = $options['test-date'] ?? null;

if ($dateOption !== null && $daysOption !== null) {
    fwrite(STDERR, "Please specify either --date or --days, not both." . PHP_EOL);
    exit(1);
}

if ($testDateOption !== null && ($dateOption !== null || $daysOption !== null)) {
    fwrite(STDERR, "--test-date can't be combined with --date or --days." . PHP_EOL);
    exit(1);
}

$testMode = false;

if ($testDateOption !== null) {
    $targetDate = DateTimeImmutable::createFromFormat('Y-m-d', $testDateOption);
    if (!$targetDate) {
        fwrite(STDERR, "Invalid test date supplied. Use YYYY-MM-DD." . PHP_EOL);
        exit(1);
    }
    $dryRun = true;
    $testMode = true;
} elseif ($dateOption !== null) {
    $targetDate = DateTimeImmutable::createFromFormat('Y-m-d', $dateOption);
    if (!$targetDate) {
        fwrite(STDERR, "Invalid date supplied. Use YYYY-MM-DD." . PHP_EOL);
        exit(1);
    }
} else {
    $days = $daysOption !== null ? (int)$daysOption : 21;
    if ($days < 0) {
        fwrite(STDERR, "The --days option must be zero or a positive integer." . PHP_EOL);
        exit(1);
    }
    $targetDate = new DateTimeImmutable(sprintf('-%d days', $days));
}

$startOfDay = $targetDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
$endOfDay = $targetDate->setTime(23, 59, 59)->format('Y-m-d H:i:s');
$targetDateString = $targetDate->format('Y-m-d');

if ($testMode) {
    echo 'Test mode: dry-run for orders created on ' . $targetDateString . '.' . PHP_EOL;
}

$API = new PerchAPI(1.0, 'perch_shop');
$DB = PerchDB::fetch();
$Customers = new PerchShop_Customers($API);

$ordersTable = PERCH_DB_PREFIX . 'shop_orders';

$sql = 'SELECT orderID, customerID, orderCreated FROM ' . $ordersTable
    . ' WHERE orderStatus=' . $DB->pdb('paid')
    . ' AND orderDeleted IS NULL'
    . ' AND orderCreated BETWEEN ' . $DB->pdb($startOfDay)
    . ' AND ' . $DB->pdb($endOfDay);

if (isset($options['order-id'])) {
    $sql .= ' AND orderID=' . $DB->pdb((int)$options['order-id']);
}

if (isset($options['customer-id'])) {
    $sql .= ' AND customerID=' . $DB->pdb((int)$options['customer-id']);
}

$sql .= ' ORDER BY orderCreated ASC';

$orders = $DB->get_rows($sql) ?: [];

if (count($orders) === 0) {
    echo 'No matching orders found for ' . $targetDateString . '.' . PHP_EOL;
    exit(0);
}

$logDir = __DIR__ . '/../logs/reorder_reminders';
if (!$dryRun) {
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    if (!is_writable($logDir)) {
        chmod($logDir, 0777);
    }
}

$logFile = $logDir . '/reorder_' . $targetDateString . '.log';
$processedOrders = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) < 4) {
            continue;
        }
        $loggedOrderID = (int)$parts[0];
        $status = $parts[3];
        if (strpos($status, 'error') === 0) {
            continue;
        }
        $processedOrders[$loggedOrderID] = true;
    }
}

$Settings = PerchSettings::fetch();
$siteURLSetting = $Settings->get('siteURL');
$siteURLValue = $siteURLSetting ? trim($siteURLSetting->val()) : '';
$siteURL = '';

if ($siteURLValue !== '' && $siteURLValue !== '/') {
    $siteURL = rtrim($siteURLValue, '/');
    if (stripos($siteURL, 'http://') !== 0 && stripos($siteURL, 'https://') !== 0) {
        $siteURL = 'https://' . ltrim($siteURL, '/');
    }
}

$reorderPath = '/order/re-order';
$reorderURL = $siteURL !== '' ? $siteURL . $reorderPath : $reorderPath;

$senderName = defined('PERCH_EMAIL_FROM_NAME') ? PERCH_EMAIL_FROM_NAME : 'Weightloss';
$senderEmail = defined('PERCH_EMAIL_FROM') ? PERCH_EMAIL_FROM : 'no-reply@example.com';

$appendLog = function (int $orderID, int $customerID, string $status) use ($logFile): void {
    $line = $orderID . '|' . $customerID . '|' . date('c') . '|' . $status . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
};

$notifiedCustomers = [];
$sentCount = 0;
$skippedCount = 0;

foreach ($orders as $order) {
    $orderID = (int)$order['orderID'];
    $customerID = (int)$order['customerID'];

    if (isset($processedOrders[$orderID])) {
        echo 'Skipping order ' . $orderID . ' – reminder already logged.' . PHP_EOL;
        $skippedCount++;
        continue;
    }

    if (isset($notifiedCustomers[$customerID])) {
        echo 'Skipping order ' . $orderID . ' – customer ' . $customerID . ' already queued for a reminder.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-duplicate-customer');
        }
        $skippedCount++;
        continue;
    }

    $laterOrderSQL = 'SELECT orderID FROM ' . $ordersTable
        . ' WHERE customerID=' . $DB->pdb($customerID)
        . ' AND orderStatus=' . $DB->pdb('paid')
        . ' AND orderDeleted IS NULL'
        . ' AND orderCreated>' . $DB->pdb($order['orderCreated'])
        . ' ORDER BY orderCreated DESC LIMIT 1';

    $laterOrderID = $DB->get_value($laterOrderSQL);
    if ($laterOrderID) {
        echo 'Skipping order ' . $orderID . ' – customer has a later paid order #' . $laterOrderID . '.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-later-order');
        }
        $skippedCount++;
        continue;
    }

    $Customer = $Customers->find($customerID);
    if (!$Customer) {
        echo 'Skipping order ' . $orderID . ' – customer record not found.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-missing-customer');
        }
        $skippedCount++;
        continue;
    }

    $emailAddress = trim((string)$Customer->customerEmail());
    if ($emailAddress === '') {
        echo 'Skipping order ' . $orderID . ' – customer has no email address.' . PHP_EOL;
        if (!$dryRun) {
            $appendLog($orderID, $customerID, 'skipped-missing-email');
        }
        $skippedCount++;
        continue;
    }

    $firstName = trim((string)$Customer->customerFirstName());
    if ($firstName === '') {
        $firstName = 'there';
    }

    $orderDate = new DateTimeImmutable($order['orderCreated']);
    $orderDateHuman = $orderDate->format('j F Y');

    $title = 'Time to reorder';
    $message = "It's been about three weeks since your order on {$orderDateHuman}. You can place your next order and pay online at {$reorderURL}.";

    echo 'Preparing reminder for order ' . $orderID . ' (customer ' . $customerID . ').' . PHP_EOL;

    if ($dryRun) {
        $notifiedCustomers[$customerID] = true;
        continue;
    }

    try {
        $Email = $API->get('Email');
        $Email->subject('Time to reorder your medication');
        $Email->senderName($senderName);
        $Email->senderEmail($senderEmail);
        $Email->recipientEmail($emailAddress);
        $Email->set_template('members/emails/reorder_reminder.html');
        $Email->set_bulk([
            'first_name' => $firstName,
            'order_date' => $orderDateHuman,
            'reorder_url' => $reorderURL,
            'sender_name' => $senderName,
        ]);

        $Email->send();
    } catch (Exception $exception) {
        echo 'Failed to send email for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
        $appendLog($orderID, $customerID, 'error-email');
        $skippedCount++;
        continue;
    }

    $memberID = (int)$Customer->memberID();
    if ($memberID > 0) {
        try {
            perch_member_add_notification($memberID, $title, $message);
        } catch (Exception $exception) {
            echo 'Failed to create notification for order ' . $orderID . ': ' . $exception->getMessage() . PHP_EOL;
            $appendLog($orderID, $customerID, 'sent-notification-error');
            $notifiedCustomers[$customerID] = true;
            $sentCount++;
            continue;
        }
    }

    $appendLog($orderID, $customerID, 'sent');
    $notifiedCustomers[$customerID] = true;
    $sentCount++;
}

echo 'Finished. Sent ' . $sentCount . ' reminder' . ($sentCount === 1 ? '' : 's') . ' and skipped ' . $skippedCount . '.' . PHP_EOL;
