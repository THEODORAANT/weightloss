<?php
include(__DIR__ . '/../../../../core/runtime/runtime.php');

$logFile = __DIR__ . '/webhook_log.txt';

header('Content-Type: application/json; charset=utf-8');

if (!file_exists($logFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Log file not found.']);
    exit;
}

$limitParam = $_GET['limit'] ?? 50;
$limit = filter_var($limitParam, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($limit === false) {
    http_response_code(400);
    echo json_encode(['error' => 'limit must be an integer greater than or equal to 1.']);
    exit;
}

$contents = file_get_contents($logFile);
if ($contents === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to read log file.']);
    exit;
}

$chunks = preg_split("/\n{2,}/", trim($contents));
$chunks = array_values(array_filter($chunks, static function ($chunk) {
    return trim($chunk) !== '';
}));

$totalEntries = count($chunks);

if ($totalEntries > $limit) {
    $chunks = array_slice($chunks, -1 * $limit);
}

$entries = [];
foreach ($chunks as $chunk) {
    $lines = preg_split('/\r?\n/', trim($chunk));
    $entry = [
        'timestamp' => null,
        'raw_data' => '',
        'decoded' => '',
        'updates' => '',
    ];

    $currentSection = null;

    foreach ($lines as $line) {
        $line = rtrim($line, "\r");

        if ($line === '') {
            continue;
        }

        if (preg_match('/^\[(.*)\]$/', $line, $matches)) {
            $entry['timestamp'] = $matches[1];
            $currentSection = null;
            continue;
        }

        $sectionPrefixes = [
            'Raw Data: ' => 'raw_data',
            'Decoded: ' => 'decoded',
            'Updates: ' => 'updates',
        ];

        $matchedPrefix = false;
        foreach ($sectionPrefixes as $prefix => $section) {
            if (strpos($line, $prefix) === 0) {
                $entry[$section] = substr($line, strlen($prefix));
                $currentSection = $section;
                $matchedPrefix = true;
                break;
            }
        }

        if (!$matchedPrefix && $currentSection) {
            $entry[$currentSection] .= ($entry[$currentSection] === '' ? '' : "\n") . $line;
        }
    }

    $entries[] = $entry;
}

echo json_encode([
    'total_entries' => $totalEntries,
    'returned_entries' => count($entries),
    'entries' => $entries,
]);
