<?php

// Recalculate the correct credit for affiliate AFFEX3Y4 so only the first paid
// order for each referred member is rewarded at £7.50.

require_once __DIR__ . '/../perch/runtime.php';

$DB = PerchDB::fetch();

$affiliateSlug     = 'AFFEX3Y4';
$firstOrderPayout  = 7.50;
$paidStatus        = $DB->pdb('paid');
$affiliate         = $DB->get_row('SELECT id, credit FROM ' . PERCH_DB_PREFIX . 'affiliates WHERE affid=' . $DB->pdb($affiliateSlug) . ' LIMIT 1');

if (!$affiliate) {
    echo 'Affiliate with slug ' . $affiliateSlug . ' not found.' . PHP_EOL;
    exit(1);
}

$affiliateID = (int) $affiliate['id'];

$rows = $DB->get_rows(
    'SELECT r.referred_member_id
     FROM ' . PERCH_DB_PREFIX . 'referrals r
     INNER JOIN ' . PERCH_DB_PREFIX . 'shop_customers c ON c.memberID = r.referred_member_id
     INNER JOIN ' . PERCH_DB_PREFIX . 'shop_orders o ON o.customerID = c.customerID AND o.orderStatus = ' . $paidStatus . '
     WHERE r.referrer_affiliate_id = ' . $DB->pdb($affiliateSlug) . '
     GROUP BY r.referred_member_id'
);

$qualifiedMembers = PerchUtil::count($rows);
$totalEarned      = $qualifiedMembers * $firstOrderPayout;
$totalPaidOut     = (float) $DB->get_value(
    'SELECT COALESCE(SUM(amount), 0)
     FROM ' . PERCH_DB_PREFIX . 'affiliate_payouts
     WHERE affiliate_id = ' . $DB->pdb($affiliateID)
);

$newCredit = max(0, $totalEarned - $totalPaidOut);

$DB->execute(
    'UPDATE ' . PERCH_DB_PREFIX . 'affiliates
     SET credit = ' . $DB->pdb($newCredit) . '
     WHERE id = ' . $DB->pdb($affiliateID) . '
     LIMIT 1'
);

echo 'Affiliate: ' . $affiliateSlug . PHP_EOL;
echo 'Current credit before fix: £' . number_format((float) $affiliate['credit'], 2) . PHP_EOL;
echo 'Qualified referred members: ' . $qualifiedMembers . PHP_EOL;
echo 'Total earned (first orders only): £' . number_format($totalEarned, 2) . PHP_EOL;
echo 'Total paid out: £' . number_format($totalPaidOut, 2) . PHP_EOL;
echo 'New credit set to: £' . number_format($newCredit, 2) . PHP_EOL;
echo 'Done.' . PHP_EOL;
