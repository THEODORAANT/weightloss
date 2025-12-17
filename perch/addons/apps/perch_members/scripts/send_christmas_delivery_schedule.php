<?php

declare(strict_types=1);

/**
 * Send the Christmas delivery schedule email to active members.
 *
 * This task is scheduled to run daily but will only send on the configured
 * campaign dates. It also logs each run to prevent duplicate sends on the
 * same day.
 *
 * @param string|null $last_run Timestamp of last execution provided by scheduler.
 *
 * @return array{result:string,message:string}
 */
function perch_members_send_christmas_delivery_schedule($last_run = null)
{
    $API     = new PerchAPI(1.0, 'perch_members');
    $Members = new PerchMembers_Members($API);

    $timezone     = new DateTimeZone('Europe/London');
    $today        = new DateTimeImmutable('now', $timezone);
    $todayString  = $today->format('Y-m-d');
    $campaignYear = 2025;

    $scheduledDates = [
        sprintf('%04d-12-17', $campaignYear),
        sprintf('%04d-12-19', $campaignYear),
    ];

    if (!in_array($todayString, $scheduledDates, true)) {
        return [
            'result'  => 'OK',
            'message' => 'No Christmas delivery schedule email to send today.',
        ];
    }

    $rootDir = realpath(__DIR__ . '/../../../../');
    if ($rootDir === false) {
        $rootDir = dirname(dirname(dirname(dirname(__DIR__))));
    }

    $logDir = $rootDir . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0777, true);
    }

    $logFile = $logDir . DIRECTORY_SEPARATOR . 'christmas_delivery_schedule.log';
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (isset($parts[0]) && $parts[0] === $todayString) {
                return [
                    'result'  => 'OK',
                    'message' => 'Christmas delivery schedule email already sent for ' . $todayString . '.',
                ];
            }
        }
    }

  // $members = $Members->get_by_status('active');
$members[] = $Members->get_one_by('memberEmail', "theodoraantoniou@live.com");
    if (!PerchUtil::count($members)) {
        return [
            'result'  => 'OK',
            'message' => 'No active members found for Christmas delivery schedule email.',
        ];
    }

    $sent = 0;
    $failed = 0;
    $skipped = 0;

    foreach ($members as $Member) {
        if (!$Member instanceof PerchMembers_Member) {
            $skipped++;
            continue;
        }

        $recipientEmail = trim((string) $Member->memberEmail());
        if ($recipientEmail === '' || !PerchUtil::is_valid_email($recipientEmail)) {
            $skipped++;
            continue;
        }

        $memberData = $Member->to_array();

        $Email = $API->get('Email');
        $Email->set_template('members/emails/christmas_delivery_schedule.html');
        $Email->set_bulk($memberData);
        $Email->subject('Christmas Delivery Schedules');
        $Email->senderName(PERCH_EMAIL_FROM_NAME);
        $Email->senderEmail(PERCH_EMAIL_FROM);
        $Email->recipientEmail($recipientEmail);

        if ($Email->send()) {
            $sent++;
        } else {
            $failed++;
        }
    }

    $logLine = $todayString . '|sent=' . $sent . '|skipped=' . $skipped . '|failed=' . $failed . PHP_EOL;
    @file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

    $result = $failed > 0 ? 'FAILED' : 'OK';
    $message = 'Sent ' . $sent . ', skipped ' . $skipped . ', failed ' . $failed . ' for ' . $todayString . '.';

    return [
        'result'  => $result,
        'message' => $message,
    ];
}
