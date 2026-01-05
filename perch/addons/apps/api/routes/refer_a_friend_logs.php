<?php

include(__DIR__ . '/../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json; charset=utf-8');

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$dateParam = $_GET['date'] ?? null;
if ($dateParam === null || $dateParam === '') {
    http_response_code(400);
    echo json_encode(['error' => 'date is required (YYYY-MM-DD).']);
    exit;
}

try {
    $targetDate = new DateTimeImmutable($dateParam);
} catch (Exception $exception) {
    http_response_code(400);
    echo json_encode(['error' => 'date must be a valid date string (YYYY-MM-DD).']);
    exit;
}

$logDate = $targetDate->format('Y-m-d');

$allowedCampaigns = ['monday', 'thursday'];
$dayParam = $_GET['day'] ?? null;

if ($dayParam !== null && $dayParam !== '') {
    $campaignKey = strtolower((string)$dayParam);
    if (!in_array($campaignKey, $allowedCampaigns, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'day must be either monday or thursday.']);
        exit;
    }
} else {
    $campaignKey = strtolower($targetDate->format('l'));
    if (!in_array($campaignKey, $allowedCampaigns, true)) {
        http_response_code(404);
        echo json_encode(['error' => 'No referral campaign configured for the supplied date.']);
        exit;
    }
}

$rootDir = realpath(__DIR__ . '/../../../../../');
if ($rootDir === false) {
    $rootDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
}

$logDir = $rootDir . '/logs/refer_a_friend';
$logFile = $logDir . '/refer_a_friend_' . $campaignKey . '_' . $logDate . '.log';

if (!file_exists($logFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Log file not found for the given date and campaign.']);
    exit;
}

$lines = file($logFile, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to read log file.']);
    exit;
}

$entries = [];
foreach ($lines as $line) {
    if (trim($line) === '') {
        continue;
    }

    $parts = explode('|', $line);

    $entries[] = [
        'member_id' => isset($parts[0]) && ctype_digit($parts[0]) ? (int)$parts[0] : null,
        'email' => $parts[1] ?? null,
        'timestamp' => $parts[2] ?? null,
        'status' => $parts[3] ?? null,
    ];
}

echo json_encode([
    'date' => $logDate,
    'campaign' => $campaignKey,
    'total_entries' => count($entries),
    'entries' => $entries,
]);
