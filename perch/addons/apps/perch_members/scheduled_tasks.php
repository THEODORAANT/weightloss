<?php
include __DIR__ . '/scripts/send_christmas_delivery_schedule.php';

PerchScheduledTasks::register_task('perch_members', 'send_christmas_delivery_schedule', 1, function($last_run){

		$API  = new PerchAPI(1.0, 'perch_members');
		$result = perch_members_send_christmas_delivery_schedule($last_run);
		echo "result";print_r($result);
		return $result;
});

PerchScheduledTasks::register_task('perch_members', 'send_refer_a_friend_emails', 1440, function () {
    $scriptPath = realpath(__DIR__ . '/../../../../scripts/send_refer_a_friend_emails.php');
    if ($scriptPath === false) {
        return 'refer-a-friend-script-not-found';
    }

    $phpBinary = defined('PHP_BINARY') && PHP_BINARY !== '' ? PHP_BINARY : 'php';

    $command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($scriptPath);

    $output = [];
    $exitCode = 1;
    exec($command, $output, $exitCode);

    if (!empty($output)) {
        echo implode(PHP_EOL, $output) . PHP_EOL;
    }

    return $exitCode === 0 ? 'ok' : 'error:' . $exitCode;
});
/*
include __DIR__ . '/scripts/send_christmas_delivery_schedule.php';

PerchScheduledTasks::register_task(
    'perch_members',
    'christmas_delivery_schedule',
    1440,
    'perch_members_send_christmas_delivery_schedule'
);
*/
