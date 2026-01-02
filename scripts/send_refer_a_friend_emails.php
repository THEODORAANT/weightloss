<?php

declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

$options = getopt('', [
    'day::',
    'date::',
    'member-id::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo "Usage: php scripts/send_refer_a_friend_emails.php [--dry-run] [--day=monday|thursday] [--date=YYYY-MM-DD] [--member-id=<id>]" . PHP_EOL;
    echo "       --dry-run     Output the actions without sending any emails or writing logs." . PHP_EOL;
    echo "       --day         Override the day-based template selection (default: today)." . PHP_EOL;
    echo "       --date        Override today's date (YYYY-MM-DD) for logging/testing." . PHP_EOL;
    echo "       --member-id   Limit the run to a single member ID (must have an affiliate ID)." . PHP_EOL;
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);

$campaigns = [
    'monday' => [
        'key'      => 'monday',
        'template' => 'refer_a_friend_monday.html',
        'subject'  => 'Refer a Friend & Save with GetWeightLoss',
    ],
    'thursday' => [
        'key'      => 'thursday',
        'template' => 'refer_a_friend_thursday.html',
        'subject'  => 'Love GetWeightLoss? Refer a Friend and Get £5 Off!',
    ],
];

$dateString = $options['date'] ?? 'today';

try {
    $targetDate = new DateTimeImmutable($dateString);
} catch (Exception $exception) {
    fwrite(STDERR, 'Invalid date supplied. Use YYYY-MM-DD or a relative date string.' . PHP_EOL);
    exit(1);
}

$dayOverride = isset($options['day']) ? strtolower((string) $options['day']) : '';
$dayKey = $dayOverride !== '' ? $dayOverride : strtolower($targetDate->format('l'));

if (!isset($campaigns[$dayKey])) {
    echo 'No referral campaign scheduled for ' . ucfirst($dayKey) . '.' . PHP_EOL;
    exit(0);
}

$campaign = $campaigns[$dayKey];
$logDate = $targetDate->format('Y-m-d');

$API = new PerchAPI(1.0, 'perch_members');
$DB = PerchDB::fetch();
$Members = new PerchMembers_Members($API);
$Settings = PerchSettings::fetch();

$siteURLSetting = $Settings->get('siteURL');
$siteURLValue = $siteURLSetting ? trim((string) $siteURLSetting->val()) : '';
$siteURL = $siteURLValue !== '' && $siteURLValue !== '/' ? rtrim($siteURLValue, '/') : 'https://www.getweightloss.co.uk';
if (stripos($siteURL, 'http://') !== 0 && stripos($siteURL, 'https://') !== 0) {
    $siteURL = 'https://' . ltrim($siteURL, '/');
}

$logDir = __DIR__ . '/../logs/refer_a_friend';

if (!$dryRun) {
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    if (!is_writable($logDir)) {
        chmod($logDir, 0777);
    }
}

$logFile = $logDir . '/refer_a_friend_' . $campaign['key'] . '_' . $logDate . '.log';
$alreadySent = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) >= 1) {
            $alreadySent[(int) $parts[0]] = true;
        }
    }
}

$memberFilterSQL = '';
if (isset($options['member-id'])) {
    $memberFilterSQL = ' AND memberID=' . $DB->pdb((int) $options['member-id']);
}

$sql = 'SELECT memberID FROM ' . PERCH_DB_PREFIX . 'members'
    . ' WHERE memberStatus=' . $DB->pdb('active')
    . ' AND memberProperties LIKE ' . $DB->pdb('%"affID"%')
    . $memberFilterSQL;

$rows = $DB->get_rows($sql);

if (!PerchUtil::count($rows)) {
    echo 'No matching members found for the referral campaign.' . PHP_EOL;
    exit(0);
}

$sentCount = 0;
$skippedCount = 0;

foreach ($rows as $row) {
    $memberID = isset($row['memberID']) ? (int) $row['memberID'] : 0;
    if ($memberID <= 0) {
        $skippedCount++;
        continue;
    }

    if (isset($alreadySent[$memberID])) {
        echo 'Skipping member ' . $memberID . ' – already logged for this campaign and date.' . PHP_EOL;
        $skippedCount++;
        continue;
    }

    $Member = $Members->find($memberID);
    if (!$Member instanceof PerchMembers_Member) {
        echo 'Skipping member ' . $memberID . ' – record not found.' . PHP_EOL;
        $skippedCount++;
        continue;
    }

    $properties = decode_properties($Member->memberProperties());
    $affiliateID = trim((string) ($properties['affID'] ?? ''));
    if ($affiliateID === '') {
        echo 'Skipping member ' . $memberID . ' – missing affiliate ID.' . PHP_EOL;
        $skippedCount++;
        continue;
    }

    $emailAddress = trim((string) $Member->memberEmail());
    if ($emailAddress === '' || !PerchUtil::is_valid_email($emailAddress)) {
        echo 'Skipping member ' . $memberID . ' – invalid email address.' . PHP_EOL;
        $skippedCount++;
        continue;
    }

    $firstName = trim((string) ($properties['first_name'] ?? ''));
    if ($firstName === '') {
        $firstName = 'there';
    }

    $affiliateLink = $siteURL . '/?ref=' . rawurlencode($affiliateID);
    $portalLink = $siteURL . '/client/affiliate';

    $emailData = [
        'first_name'      => $firstName,
        'affiliate_id'    => $affiliateID,
        'affiliate_link'  => $affiliateLink,
        'portal_link'     => $portalLink,
        'support_email'   => PERCH_EMAIL_FROM,
    ];

    echo 'Preparing ' . $campaign['key'] . ' email for member ' . $memberID . ' (' . $emailAddress . ').' . PHP_EOL;

    if ($dryRun) {
        $skippedCount++;
        continue;
    }

    $sent = $Member->send_refer_a_friend_email(
        $campaign['template'],
        $campaign['subject'],
        $emailData,
        $emailAddress
    );

    $status = $sent ? 'sent' : 'failed';
    file_put_contents($logFile, $memberID . '|' . $emailAddress . '|' . date('c') . '|' . $status . PHP_EOL, FILE_APPEND | LOCK_EX);

    if ($sent) {
        $sentCount++;
    } else {
        $skippedCount++;
        echo 'Failed to send email to member ' . $memberID . ' (' . $emailAddress . ').' . PHP_EOL;
    }
}

echo 'Referral campaign complete. Sent ' . $sentCount . ' email(s); skipped ' . $skippedCount . '.' . PHP_EOL;

/**
 * @param string|null $json
 * @return array<string,mixed>
 */
function decode_properties($json): array
{
    if ($json === '' || $json === null) {
        return [];
    }

    $decoded = PerchUtil::json_safe_decode($json, true);

    return is_array($decoded) ? $decoded : [];
}
