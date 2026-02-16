<?php
$log_file = PERCH_PATH . '/addons/apps/api/routes/webhook_log.txt';
$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
if ($limit < 1) {
    $limit = 100;
}

$entries = [];
$error = '';

if (!file_exists($log_file)) {
    $error = 'Webhook log file not found.';
} else {
    $contents = file_get_contents($log_file);
    if ($contents === false) {
        $error = 'Unable to read webhook log file.';
    } else {
        $chunks = preg_split("/\n{2,}/", trim($contents));
        $chunks = array_values(array_filter($chunks, static function ($chunk) {
            return trim($chunk) !== '';
        }));

        foreach ($chunks as $chunk) {
            $lines = preg_split('/\r?\n/', trim($chunk));
            $entry = [
                'timestamp' => null,
                'raw_data' => '',
                'decoded' => '',
                'updates' => '',
            ];

            $current_section = null;

            foreach ($lines as $line) {
                $line = rtrim($line, "\r");

                if ($line === '') {
                    continue;
                }

                if (preg_match('/^\[(.*)\]$/', $line, $matches)) {
                    $entry['timestamp'] = $matches[1];
                    $current_section = null;
                    continue;
                }

                $section_prefixes = [
                    'Raw Data: ' => 'raw_data',
                    'Decoded: ' => 'decoded',
                    'Updates: ' => 'updates',
                ];

                $matched_prefix = false;
                foreach ($section_prefixes as $prefix => $section) {
                    if (strpos($line, $prefix) === 0) {
                        $entry[$section] = substr($line, strlen($prefix));
                        $current_section = $section;
                        $matched_prefix = true;
                        break;
                    }
                }

                if (!$matched_prefix && $current_section) {
                    $entry[$current_section] .= ($entry[$current_section] === '' ? '' : "\n") . $line;
                }
            }

            if ($search !== '') {
                $haystack = strtolower(implode("\n", [
                    (string)$entry['raw_data'],
                    (string)$entry['decoded'],
                    (string)$entry['updates'],
                ]));

                if (strpos($haystack, strtolower($search)) === false) {
                    continue;
                }
            }

            $entries[] = $entry;
        }

        if (count($entries) > $limit) {
            $entries = array_slice($entries, -1 * $limit);
        }
    }
}
