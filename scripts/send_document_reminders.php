<?php
$configPath = __DIR__ . '/../perch/config/config.php';
if (!file_exists($configPath)) {
    fwrite(STDERR, 'Perch configuration file not found at ' . $configPath . PHP_EOL);
    fwrite(STDERR, "Unable to send document reminders without the CMS configuration.\n");
    exit(1);
}

require_once __DIR__ . '/../perch/runtime.php';

$options = getopt('', [
    'member-id::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo "Usage: php scripts/send_document_reminders.php [--dry-run] [--member-id=<id>]" . PHP_EOL;
    echo "       --dry-run      Output the actions without sending any emails." . PHP_EOL;
    echo "       --member-id    Limit the run to a single member ID." . PHP_EOL;
    exit(0);
}

$dryRun = array_key_exists('dry-run', $options);
$memberID = isset($options['member-id']) ? (int) $options['member-id'] : null;

$API = new PerchAPI(1.0, 'perch_members');
$DocumentReminders = new PerchMembers_DocumentReminderService($API);

$logger = function ($message) {
    echo $message . PHP_EOL;
};

$sent = $DocumentReminders->process_due_reminders($dryRun, $memberID, $logger);

if ($dryRun) {
    echo 'Dry run completed.' . PHP_EOL;
} else {
    echo 'Sent ' . (int) $sent . ' reminder' . ($sent === 1 ? '' : 's') . '.' . PHP_EOL;
}
