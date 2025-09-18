<?php
$log_dir = realpath(__DIR__.'/../../../../../logs/notifications');
//$log_dir  = __DIR__ . '/logs/notifications';
//$log_dir  = __DIR__ . '/logs/notifications';
$logs = [];
if ($log_dir && is_dir($log_dir)) {
    $files = glob($log_dir . '/send_payment_notification*.log');
    sort($files);
    foreach ($files as $file) {
        $entries = [];
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $parts = explode('|', $line);
            if (count($parts) >= 5) {
                $entries[] = [
                    'itemID' => $parts[0],
                    'customerID' => $parts[1],
                    'billingDate' => $parts[2],
                    'loggedAt' => $parts[3],
                    'status' => $parts[4] ?? ''
                ];
            } elseif (count($parts) >= 3) {
                $entries[] = [
                    'itemID' => '',
                    'customerID' => $parts[0],
                    'billingDate' => $parts[1],
                    'loggedAt' => $parts[2],
                    'status' => $parts[3] ?? ''
                ];
            }
        }
        $logs[basename($file)] = $entries;
    }
}
