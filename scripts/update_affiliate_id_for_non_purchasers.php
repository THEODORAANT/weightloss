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

$candidateSQL = 'SELECT m.memberID, m.memberEmail, m.memberCreated, m.memberProperties '
    . 'FROM ' . PERCH_DB_PREFIX . 'members m '
    . 'WHERE m.memberCreated < ' . $DB->pdb($cutoff) . ' '
    . 'AND (m.memberProperties LIKE ' . $DB->pdb('%"referrer":"' . $fromAffiliate . '"%') . ' '
    . '     OR m.memberProperties LIKE ' . $DB->pdb('%"affID":"' . $fromAffiliate . '"%') . ') '
    . 'ORDER BY m.memberCreated ASC, m.memberID ASC';

$candidates = $DB->get_rows($candidateSQL);

$purchaseMemberIDs = [];
$purchasedRows = $DB->get_rows(
    'SELECT DISTINCT member_id FROM ' . PERCH_DB_PREFIX . 'purchases WHERE member_id IS NOT NULL'
);

if (PerchUtil::count($purchasedRows)) {
    foreach ($purchasedRows as $row) {
        $memberID = (int) ($row['member_id'] ?? 0);
        if ($memberID > 0) {
            $purchaseMemberIDs[$memberID] = true;
        }
    }
}

$paidOrderMemberIDs = [];
$paidRows = $DB->get_rows(
    'SELECT DISTINCT c.memberID '
    . 'FROM ' . PERCH_DB_PREFIX . 'shop_customers c '
    . 'INNER JOIN ' . PERCH_DB_PREFIX . 'shop_orders o ON o.customerID = c.customerID '
    . 'WHERE o.orderStatus = ' . $DB->pdb('paid')
);

if (PerchUtil::count($paidRows)) {
    foreach ($paidRows as $row) {
        $memberID = (int) ($row['memberID'] ?? 0);
        if ($memberID > 0) {
            $paidOrderMemberIDs[$memberID] = true;
        }
    }
}

$matches = [];

if (PerchUtil::count($candidates)) {
    foreach ($candidates as $row) {
        $memberID = (int) ($row['memberID'] ?? 0);
        if ($memberID <= 0) {
            continue;
        }

        if (isset($purchaseMemberIDs[$memberID]) || isset($paidOrderMemberIDs[$memberID])) {
            continue;
        }

        $propertiesRaw = (string) ($row['memberProperties'] ?? '');
        $properties = PerchUtil::json_safe_decode($propertiesRaw, true);

        if (!is_array($properties)) {
            continue;
        }

        $changed = false;

        if (($properties['referrer'] ?? null) === $fromAffiliate) {
            $properties['referrer'] = $toAffiliate;
            $changed = true;
        }

        if (($properties['affID'] ?? null) === $fromAffiliate) {
            $properties['affID'] = $toAffiliate;
            $changed = true;
        }

        if (!$changed) {
            continue;
        }

        $matches[] = [
            'memberID' => $memberID,
            'memberEmail' => (string) ($row['memberEmail'] ?? ''),
            'memberCreated' => (string) ($row['memberCreated'] ?? ''),
            'memberProperties' => PerchUtil::json_safe_encode($properties),
        ];
    }
}

$count = count($matches);

echo 'From affiliate: ' . $fromAffiliate . PHP_EOL;
echo 'To affiliate: ' . $toAffiliate . PHP_EOL;
echo 'Cutoff (exclusive): ' . $cutoff . PHP_EOL;
echo 'Mode: ' . ($dryRun ? 'dry-run' : 'execute') . PHP_EOL;
echo 'Matched members: ' . $count . PHP_EOL;

if ($count === 0) {
    echo 'No matching members found. Nothing to update.' . PHP_EOL;
    exit(0);
}

foreach ($matches as $row) {
    echo '- member #' . $row['memberID'] . ' (' . $row['memberEmail'] . ') registered ' . $row['memberCreated'] . PHP_EOL;
}

$memberIDs = array_map(static function ($row) {
    return (int) $row['memberID'];
}, $matches);

$memberIDList = implode(',', array_unique($memberIDs));
$referralsToUpdate = 0;

if ($memberIDList !== '') {
    $referralsToUpdate = (int) $DB->get_value(
        'SELECT COUNT(*) FROM ' . PERCH_DB_PREFIX . 'referrals '
        . 'WHERE referred_member_id IN (' . $memberIDList . ') '
        . 'AND referrer_affiliate_id = ' . $DB->pdb($fromAffiliate)
    );
}

echo 'Matched referral rows to update: ' . $referralsToUpdate . PHP_EOL;

if ($dryRun) {
    echo 'Dry run complete. No changes were made.' . PHP_EOL;
    exit(0);
}

$updatedMembers = 0;

foreach ($matches as $row) {
    $ok = $DB->update(
        PERCH_DB_PREFIX . 'members',
        [
            'memberProperties' => $row['memberProperties'],
        ],
        'memberID',
        $row['memberID']
    );

    if ($ok !== false) {
        $updatedMembers++;
    }
}

$updatedReferrals = 0;

if ($memberIDList !== '') {
    $updatedReferrals = $DB->execute(
        'UPDATE ' . PERCH_DB_PREFIX . 'referrals '
        . 'SET referrer_affiliate_id = ' . $DB->pdb($toAffiliate) . ' '
        . 'WHERE referred_member_id IN (' . $memberIDList . ') '
        . 'AND referrer_affiliate_id = ' . $DB->pdb($fromAffiliate)
    );

    if ($updatedReferrals === false) {
        fwrite(STDERR, 'Failed to update referrals table.' . PHP_EOL);
        exit(1);
    }
}

echo 'Updated ' . $updatedMembers . ' member(s) in memberProperties.' . PHP_EOL;
echo 'Updated ' . (int) $updatedReferrals . ' referral row(s).' . PHP_EOL;

if ($updatedMembers !== $count) {
    fwrite(STDERR, 'Warning: expected to update ' . $count . ' member(s), updated ' . $updatedMembers . '.' . PHP_EOL);
    exit(1);
}
