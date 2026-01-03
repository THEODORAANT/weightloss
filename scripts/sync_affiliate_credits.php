<?php

require_once __DIR__ . '/../perch/runtime.php';





$membersAPI = new PerchAPI(1.0, 'perch_members');
$shopAPI    = new PerchAPI(1.0, 'perch_shop');
$DB         = PerchDB::fetch();

$requiredTables = [
    PERCH_DB_PREFIX . 'affiliates',
    PERCH_DB_PREFIX . 'referrals',
    PERCH_DB_PREFIX . 'purchases',
    PERCH_DB_PREFIX . 'shop_orders',
    PERCH_DB_PREFIX . 'shop_customers',
];


$purchasedOrderIDs = $DB->get_rows_flat('SELECT orderID FROM ' . PERCH_DB_PREFIX . 'purchases');
$orderIdLookup     = [];

if (PerchUtil::count($purchasedOrderIDs)) {
    foreach ($purchasedOrderIDs as $orderID) {
        if ($orderID === null) {
            continue;
        }
        $orderIdLookup[(int) $orderID] = true;
    }
}

$sql = 'SELECT o.orderID, o.customerID, c.memberID, a.id AS affiliate_id, a.affid '
     . 'FROM ' . PERCH_DB_PREFIX . 'shop_orders o '
     . 'INNER JOIN ' . PERCH_DB_PREFIX . 'shop_customers c ON c.customerID = o.customerID '
     . 'INNER JOIN ' . PERCH_DB_PREFIX . 'referrals r ON r.referred_member_id = c.memberID '
     . 'INNER JOIN ' . PERCH_DB_PREFIX . 'affiliates a ON a.affid = r.referrer_affiliate_id '
     . 'WHERE o.orderStatus = ' . $DB->pdb('paid') . ' '
     . 'ORDER BY o.orderCreated ASC, o.orderID ASC';
/*SELECT o.orderID, o.customerID, c.memberID, a.id AS affiliate_id, a.affid FROM getweightlossmain.p4_shop_orders o
   INNER JOIN getweightlossmain.p4_shop_customers c ON c.customerID = o.customerID
  INNER JOIN getweightlossmain.p4_referrals r ON r.referred_member_id = c.memberID
  INNER JOIN getweightlossmain.p4_affiliates a ON a.affid = r.referrer_affiliate_id
   where a.affid="AFFEX3Y4" and o.orderStatus = 'paid' ORDER BY o.orderCreated ASC, o.orderID ASC*/
$orders = $DB->get_rows($sql);

if (!PerchUtil::count($orders)) {
    echo 'No paid orders linked to referrals were found.' . PHP_EOL;
    exit(0);
}

$AffiliateFactory = new PerchMembers_Affiliate($membersAPI);
$OrdersFactory    = new PerchShop_Orders($shopAPI);
$CustomersFactory = new PerchShop_Customers($shopAPI);

$processed = 0;
$skipped   = 0;
$credited  = 0.0;

foreach ($orders as $row) {
    $orderID   = isset($row['orderID']) ? (int) $row['orderID'] : 0;
    $customerID = isset($row['customerID']) ? (int) $row['customerID'] : 0;
    $memberID  = isset($row['memberID']) ? (int) $row['memberID'] : 0;
    $affiliateID = isset($row['affiliate_id']) ? (int) $row['affiliate_id'] : 0;
    $affiliateSlug = $row['affid'] ?? '';

    if ($orderID <= 0 || $customerID <= 0 || $memberID <= 0 || $affiliateID <= 0 || $affiliateSlug === '') {
        continue;
    }

    if (isset($orderIdLookup[$orderID])) {
        $skipped++;
        continue;
    }

    $Order = $OrdersFactory->find($orderID);
    if (!$Order instanceof PerchShop_Order) {
        //write_to_stderr('Unable to load order #' . $orderID . '. Skipping.' . PHP_EOL);
        continue;
    }

    $Customer = $CustomersFactory->find($customerID);
    if (!$Customer instanceof PerchShop_Customer) {
      //  write_to_stderr('Unable to load customer #' . $customerID . ' for order #' . $orderID . '. Skipping.' . PHP_EOL);
        continue;
    }

    $creditBefore = $DB->get_value('SELECT credit FROM ' . PERCH_DB_PREFIX . 'affiliates WHERE id = ' . $DB->pdb($affiliateID) . ' LIMIT 1');
    if ($creditBefore === null) {
        $creditBefore = 0;
    }
    $creditBefore = (float) $creditBefore;

    $isReorder = $Order->isReorder($Customer);

    $AffiliateFactory->recordPurchase($memberID, $orderID, (bool) $isReorder);

    $creditAfter = $DB->get_value('SELECT credit FROM ' . PERCH_DB_PREFIX . 'affiliates WHERE id = ' . $DB->pdb($affiliateID) . ' LIMIT 1');
    if ($creditAfter === null) {
        $creditAfter = 0;
    }
    $creditAfter = (float) $creditAfter;

    $delta = $creditAfter - $creditBefore;
    if (abs($delta) > 0.0001) {
        $credited += $delta;
        echo 'Recorded order #' . $orderID . ' for member #' . $memberID . ' (affiliate ' . $affiliateSlug . '): credit +' . number_format($delta, 2) . PHP_EOL;
    } else {
        echo 'Recorded order #' . $orderID . ' for member #' . $memberID . ' (affiliate ' . $affiliateSlug . '): no credit change' . PHP_EOL;
    }

    $orderIdLookup[$orderID] = true;
    $processed++;
}

if ($processed === 0) {
    echo 'All affiliate purchases are already synced.' . PHP_EOL;
    if ($skipped > 0) {
        echo 'Skipped ' . $skipped . ' previously processed orders.' . PHP_EOL;
    }
    exit(0);
}

echo PHP_EOL . 'Processed ' . $processed . ' order(s).';
if ($skipped > 0) {
    echo ' Skipped ' . $skipped . ' order(s) that were already recorded.';
}
if (abs($credited) > 0.0001) {
    echo ' Total credit added: £' . number_format($credited, 2) . '.';
} else {
    echo ' No net credit adjustments made.';
}
echo PHP_EOL;
