<?php
declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line." . PHP_EOL);
    exit(1);
}

$API     = new PerchAPI(1.0, 'perch_members');
$DB      = PerchDB::fetch();
$Members = new PerchMembers_Members($API);

$columns = $DB->get_rows('SHOW COLUMNS FROM ' . PERCH_DB_PREFIX . 'purchases LIKE "payout_id"');
if (!PerchUtil::count($columns)) {
    fwrite(STDERR, "The purchases table does not contain a payout_id column. Nothing to do." . PHP_EOL);
    exit(1);
}

$payouts = $DB->get_rows('SELECT id, affiliate_id, requested_at FROM ' . PERCH_DB_PREFIX . 'affiliate_payouts ORDER BY id ASC');

if (!PerchUtil::count($payouts)) {
    echo "No affiliate payouts found." . PHP_EOL;
    exit(0);
}

$memberCache = [];
$totalUpdated = 0;
$totalInserted = 0;

foreach ($payouts as $payout) {
    $payoutID = (int) $payout['id'];
    echo 'Processing payout #' . $payoutID . PHP_EOL;

    $sql = 'SELECT pu.*, o.orderInvoiceNumber, o.orderCreated, o.orderTotal '
         . 'FROM ' . PERCH_DB_PREFIX . 'purchases pu '
         . 'LEFT JOIN ' . PERCH_DB_PREFIX . 'shop_orders o ON o.orderID = pu.orderID '
         . 'WHERE pu.payout_id = ' . $DB->pdb($payoutID) . ' '
         . 'ORDER BY o.orderCreated ASC, pu.id ASC';

    $purchases = $DB->get_rows($sql);

    if (!PerchUtil::count($purchases)) {
        echo "  No purchases linked to this payout. Creating empty snapshot." . PHP_EOL;
        $activity = [];
        $purchaseSnapshot = [];
    } else {
        [$activity, $purchaseSnapshot] = buildSnapshots($purchases, $Members, $memberCache);
    }

    $data = [
        'referral_snapshot' => PerchUtil::json_safe_encode($activity),
        'purchase_snapshot' => PerchUtil::json_safe_encode($purchaseSnapshot),
    ];

    $existing = $DB->get_value('SELECT id FROM ' . PERCH_DB_PREFIX . 'affiliate_payout_details WHERE payout_id = ' . $DB->pdb($payoutID) . ' LIMIT 1');

    if ($existing) {
        $DB->update(
            PERCH_DB_PREFIX . 'affiliate_payout_details',
            $data,
            'payout_id',
            $payoutID
        );
        $totalUpdated++;
        echo "  Updated existing payout detail record." . PHP_EOL;
    } else {
        $data['payout_id'] = $payoutID;
        $DB->insert(PERCH_DB_PREFIX . 'affiliate_payout_details', $data);
        $totalInserted++;
        echo "  Inserted payout detail record." . PHP_EOL;
    }
}

echo PHP_EOL . 'Done. Inserted: ' . $totalInserted . ', Updated: ' . $totalUpdated . PHP_EOL;

function buildSnapshots(array $purchases, PerchMembers_Members $Members, array &$memberCache): array
{
    $activityMap = [];
    $purchaseSnapshot = [];

    foreach ($purchases as $purchase) {
        $memberID = isset($purchase['member_id']) ? (int) $purchase['member_id'] : 0;

        if ($memberID > 0 && !isset($activityMap[$memberID])) {
            $context = getMemberContext($memberID, $Members, $memberCache);
            $activityMap[$memberID] = [
                'member_id'   => $memberID,
                'label'       => $context['label'],
                'email'       => $context['email'],
                'order_lines' => [],
            ];
        }

        $summary = describeOrder($purchase);

        if ($memberID > 0 && $summary !== '') {
            $activityMap[$memberID]['order_lines'][] = $summary;
        }

        $purchaseSnapshot[] = simplifyPurchase($purchase);
    }

    $activity = [];
    foreach ($activityMap as $row) {
        $activity[] = [
            'member_id'   => $row['member_id'],
            'user'        => $row['label'],
            'email'       => $row['email'],
            'order_count' => count($row['order_lines']),
            'orders'      => implode('; ', $row['order_lines']),
        ];
    }

    return [$activity, $purchaseSnapshot];
}

function getMemberContext(int $memberID, PerchMembers_Members $Members, array &$cache): array
{
    if (isset($cache[$memberID])) {
        return $cache[$memberID];
    }

    $label = 'Member #' . $memberID;
    $email = null;

    $Member = $Members->find($memberID);
    if ($Member instanceof PerchMembers_Member) {
        $details = $Member->to_array();
        $email = $details['memberEmail'] ?? ($details['email'] ?? null);

        $nameParts = [];
        if (!empty($details['first_name'])) {
            $nameParts[] = $details['first_name'];
        }
        if (!empty($details['last_name'])) {
            $nameParts[] = $details['last_name'];
        }

        $name = trim(implode(' ', $nameParts));
        if ($name !== '') {
            $label = $name;
        } elseif (!empty($email)) {
            $label = $email;
        }
    }

    $cache[$memberID] = [
        'label' => $label,
        'email' => $email,
    ];

    return $cache[$memberID];
}

function describeOrder(array $purchase): string
{
    $parts = [];

    $invoice = $purchase['orderInvoiceNumber'] ?? null;
    $orderID = $purchase['orderID'] ?? null;
    if ($invoice || $orderID) {
        $parts[] = '#' . ($invoice ?: $orderID);
    }

    $created = $purchase['orderCreated'] ?? null;
    if ($created && $created !== '0000-00-00 00:00:00') {
        $parts[] = substr($created, 0, 10);
    }

    $total = $purchase['orderTotal'] ?? null;
    if ($total !== null && $total !== '') {
        $parts[] = (string) $total;
    }

    return implode(' | ', $parts);
}

function simplifyPurchase(array $purchase): array
{
    return [
        'purchase_id'   => isset($purchase['id']) ? (int) $purchase['id'] : null,
        'member_id'     => isset($purchase['member_id']) ? (int) $purchase['member_id'] : null,
        'order_id'      => isset($purchase['orderID']) ? (int) $purchase['orderID'] : null,
        'order_invoice' => $purchase['orderInvoiceNumber'] ?? null,
        'order_created' => $purchase['orderCreated'] ?? null,
        'order_total'   => $purchase['orderTotal'] ?? null,
    ];
}
