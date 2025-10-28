<?php
include(__DIR__ . '/../../../core/runtime/runtime.php');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/routes/injection-logs/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_injection_logs_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$history = perch_members_injection_logs_history($memberId);

if (!is_array($history) || count($history) === 0) {
    wl_injection_logs_error(404, 'No injection history found.');
}

$history = array_values($history);
$intervals = wl_injection_schedule_intervals($history);
$averageIntervalDays = wl_injection_schedule_average_days($intervals);
$frequency = wl_injection_schedule_frequency_label($averageIntervalDays);

$lastEntry = end($history);
try {
    $lastDate = new DateTimeImmutable($lastEntry['injection_date']);
} catch (Exception $e) {
    wl_injection_logs_error(500, 'Unable to parse last injection date.');
}

$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$daysSinceLast = max(0, (int)floor(($now->getTimestamp() - $lastDate->getTimestamp()) / 86400));

$intervalDaysRounded = max(1, (int)ceil($averageIntervalDays));
$nextDueDate = $lastDate->add(DateInterval::createFromDateString($intervalDaysRounded . ' days'));
$daysUntilNext = (int)floor(($nextDueDate->getTimestamp() - $now->getTimestamp()) / 86400);

$adherence = wl_injection_schedule_adherence($intervals, $averageIntervalDays);
$nextStatus = wl_injection_schedule_next_status($daysUntilNext, $averageIntervalDays);
$scheduleStatus = wl_injection_schedule_overall_status($nextStatus, $adherence);

$response = [
    'last_injection' => [
        'date' => wl_format_datetime($lastEntry['injection_date'] ?? null),
        'dose_mg' => wl_injection_logs_format_float($lastEntry['dose_mg'] ?? null),
        'medication_type' => $lastEntry['medication_type'] ?? null,
    ],
    'next_injection' => [
        'due_date' => wl_format_datetime($nextDueDate),
        'days_until' => $daysUntilNext,
        'status' => $nextStatus,
    ],
    'schedule' => [
        'frequency' => $frequency,
        'days_since_last' => $daysSinceLast,
        'adherence_percentage' => round($adherence, 1),
        'status' => $scheduleStatus,
    ],
];

echo json_encode($response);
exit;

function wl_injection_schedule_intervals(array $history)
{
    $intervals = [];

    for ($i = 1, $count = count($history); $i < $count; $i++) {
        $previous = $history[$i - 1];
        $current = $history[$i];

        if (empty($previous['injection_date']) || empty($current['injection_date'])) {
            continue;
        }

        try {
            $prevDate = new DateTimeImmutable($previous['injection_date']);
            $currDate = new DateTimeImmutable($current['injection_date']);
        } catch (Exception $e) {
            continue;
        }

        $diffSeconds = $currDate->getTimestamp() - $prevDate->getTimestamp();
        if ($diffSeconds <= 0) {
            continue;
        }

        $intervals[] = $diffSeconds / 86400;
    }

    return $intervals;
}

function wl_injection_schedule_average_days(array $intervals)
{
    if (empty($intervals)) {
        return 7.0;
    }

    return array_sum($intervals) / count($intervals);
}

function wl_injection_schedule_frequency_label($averageDays)
{
    if ($averageDays >= 6 && $averageDays <= 8) {
        return 'weekly';
    }

    if ($averageDays >= 13 && $averageDays <= 15) {
        return 'biweekly';
    }

    if ($averageDays >= 27 && $averageDays <= 31) {
        return 'monthly';
    }

    return 'every ' . round($averageDays, 1) . ' days';
}

function wl_injection_schedule_adherence(array $intervals, $averageDays)
{
    if (empty($intervals)) {
        return 100.0;
    }

    $onTime = 0;
    $tolerance = max(1.0, $averageDays * 0.25);

    foreach ($intervals as $interval) {
        if (abs($interval - $averageDays) <= $tolerance) {
            $onTime++;
        }
    }

    return ($onTime / count($intervals)) * 100;
}

function wl_injection_schedule_next_status($daysUntilNext, $averageDays)
{
    if ($daysUntilNext < 0) {
        if (abs($daysUntilNext) > $averageDays) {
            return 'missed';
        }

        return 'overdue';
    }

    if ($daysUntilNext <= 1) {
        return 'due_soon';
    }

    return 'upcoming';
}

function wl_injection_schedule_overall_status($nextStatus, $adherence)
{
    if ($nextStatus === 'missed') {
        return 'missed';
    }

    if ($nextStatus === 'overdue') {
        return 'overdue';
    }

    if ($adherence < 70) {
        return 'needs_attention';
    }

    if ($nextStatus === 'due_soon') {
        return 'due_soon';
    }

    return 'on_track';
}
