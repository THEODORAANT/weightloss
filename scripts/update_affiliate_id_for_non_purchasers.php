<?php

require_once __DIR__ . '/../perch/runtime.php';

$opts = getopt('', [
    'from::',
    'to::',
    'cutoff::',
    'dry-run',
]);

$fromAffiliate = isset($opts['from']) ? trim((string) $opts['from']) : 'AFFKKPJK';
$toAffiliate   = isset($opts['to']) ? trim((string) $opts['to']) : 'AFFHSU08';
$cutoffInput   = isset($opts['cutoff']) ? trim((string) $opts['cutoff']) : '2026-01-07 00:00:00';
$dryRun        = array_key_exists('dry-run', $opts);

if ($fromAffiliate === '' || $toAffiliate === '') {
    fwrite(STDERR, "Both --from and --to affiliate IDs are required.\n");
    exit(1);
}

$cutoffTimestamp = strtotime($cutoffInput);
if ($cutoffTimestamp === false) {
    fwrite(STDERR, "Invalid --cutoff value: {$cutoffInput}\n");
    exit(1);
}

$cutoff = date('Y-m-d H:i:s', $cutoffTimestamp);

$DB = PerchDB::fetch();

$sql = 'SELECT r.id, r.referred_member_id, m.memberEmail, m.memberCreated '
     . 'FROM ' . PERCH_DB_PREFIX . 'referrals r '
     . 'INNER JOIN ' . PERCH_DB_PREFIX . 'members m ON m.memberID = r.referred_member_id '
     . 'WHERE r.referrer_affiliate_id = ' . $DB->pdb($fromAffiliate) . ' '
     . 'AND m.memberCreated < ' . $DB->pdb($cutoff) . ' '
     . 'AND NOT EXISTS ( '
     . '    SELECT 1 FROM ' . PERCH_DB_PREFIX . 'purchases p '
     . '    WHERE p.member_id = r.referred_member_id '
     . ') '
     . 'AND NOT EXISTS ( '
     . '    SELECT 1 '
     . '    FROM ' . PERCH_DB_PREFIX . 'shop_customers c '
     . '    INNER JOIN ' . PERCH_DB_PREFIX . 'shop_orders o ON o.customerID = c.customerID '
     . '    WHERE c.memberID = r.referred_member_id '
     . '      AND o.orderStatus = ' . $DB->pdb('paid') . ' '
     . ') '
     . 'ORDER BY m.memberCreated ASC, r.id ASC';

$rows = $DB->get_rows($sql);
$count = PerchUtil::count($rows);

echo 'From affiliate: ' . $fromAffiliate . PHP_EOL;
echo 'To affiliate: ' . $toAffiliate . PHP_EOL;
echo 'Cutoff (exclusive): ' . $cutoff . PHP_EOL;
echo 'Mode: ' . ($dryRun ? 'dry-run' : 'execute') . PHP_EOL;
echo 'Matched referrals: ' . $count . PHP_EOL;

if (!$count) {
    echo 'No matching referrals found. Nothing to update.' . PHP_EOL;
    exit(0);
}

foreach ($rows as $row) {
    $memberID = (int) ($row['referred_member_id'] ?? 0);
    $email = (string) ($row['memberEmail'] ?? '');
    $created = (string) ($row['memberCreated'] ?? '');
    echo '- referral #' . (int) $row['id'] . ' member #' . $memberID . ' (' . $email . ') registered ' . $created . PHP_EOL;
}

if ($dryRun) {
    echo 'Dry run complete. No changes were made.' . PHP_EOL;
    exit(0);
}

$updateSQL = 'UPDATE ' . PERCH_DB_PREFIX . 'referrals '
          . 'SET referrer_affiliate_id = ' . $DB->pdb($toAffiliate) . ' '
          . 'WHERE id IN (' . implode(',', array_map(static function ($row) {
              return (int) $row['id'];
          }, $rows)) . ')';

$updated = $DB->execute($updateSQL);

if ($updated === false) {
    fwrite(STDERR, 'Update failed.' . PHP_EOL);
    exit(1);
}

echo 'Updated ' . $count . ' referral(s) from ' . $fromAffiliate . ' to ' . $toAffiliate . '.' . PHP_EOL;
