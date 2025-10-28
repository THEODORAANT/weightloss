<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../weight-measurements/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_weight_goals_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$goal = perch_members_weight_goal_find($memberId);

if (!is_array($goal)) {
    wl_weight_goals_error(404, 'Weight goal not found.');
}

$progress = wl_weight_goals_calculate_progress($goal, $memberId);

$response = [
    'goal' => wl_format_weight_goal($goal),
    'progress' => $progress,
];

echo json_encode($response);
exit;

function wl_weight_goals_calculate_progress(array $goal, $memberId)
{
    $unit = $goal['unit'] ?? 'kg';
    $goalWeight = isset($goal['goal_weight']) ? (float)$goal['goal_weight'] : null;
    $goalWeightKg = wl_weight_goals_convert_to_kg($goalWeight, $unit);
    $startingWeightKg = isset($goal['starting_weight_kg']) ? (float)$goal['starting_weight_kg'] : null;

    $measurementsRepository = wl_weight_measurements_repository();
    $latestMeasurement = $measurementsRepository->fetchLatestForMember($memberId);
    $currentWeightKg = null;
    $currentMeasurementDate = null;

    if (is_array($latestMeasurement) && isset($latestMeasurement['weight_kg'])) {
        $currentWeightKg = (float)$latestMeasurement['weight_kg'];
        if (isset($latestMeasurement['measurement_date'])) {
            try {
                $currentMeasurementDate = new DateTimeImmutable($latestMeasurement['measurement_date']);
            } catch (Exception $e) {
                $currentMeasurementDate = null;
            }
        }
    }

    if ($currentWeightKg === null) {
        $currentWeightKg = $startingWeightKg;
    }

    $currentWeight = wl_weight_goals_round(wl_weight_goals_convert_from_kg($currentWeightKg, $unit));
    $startingWeight = wl_weight_goals_round(wl_weight_goals_convert_from_kg($startingWeightKg, $unit));
    $goalWeightForResponse = wl_weight_goals_round($goalWeight);

    $weightLostKg = null;
    $weightToGoalKg = null;

    if ($startingWeightKg !== null && $currentWeightKg !== null) {
        $weightLostKg = max(0, $startingWeightKg - $currentWeightKg);
    }

    if ($currentWeightKg !== null && $goalWeightKg !== null) {
        $weightToGoalKg = max(0, $currentWeightKg - $goalWeightKg);
    }

    $weightLost = wl_weight_goals_round(wl_weight_goals_convert_from_kg($weightLostKg, $unit));
    $weightToGoal = wl_weight_goals_round(wl_weight_goals_convert_from_kg($weightToGoalKg, $unit));

    $progressPercentage = 0.0;
    if ($startingWeightKg !== null && $goalWeightKg !== null) {
        $denominator = $startingWeightKg - $goalWeightKg;
        if ($denominator > 0 && $weightLostKg !== null) {
            $progressPercentage = max(0, min(100, ($weightLostKg / $denominator) * 100));
        } elseif ($denominator <= 0 && $currentWeightKg !== null && $goalWeightKg !== null) {
            $progressPercentage = $currentWeightKg <= $goalWeightKg ? 100.0 : 0.0;
        }
    }

    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $targetDate = null;
    if (isset($goal['target_date'])) {
        try {
            $targetDate = new DateTimeImmutable($goal['target_date']);
        } catch (Exception $e) {
            $targetDate = null;
        }
    }

    $daysRemaining = null;
    if ($targetDate) {
        $daysRemaining = (int)floor(($targetDate->getTimestamp() - $now->getTimestamp()) / 86400);
        if ($daysRemaining < 0) {
            $daysRemaining = 0;
        }
    }

    $estimatedCompletionDate = null;
    $onTrack = false;

    if ($startingWeightKg !== null && $currentWeightKg !== null && $goalWeightKg !== null) {
        $startDate = null;
        if (isset($goal['created_at'])) {
            try {
                $startDate = new DateTimeImmutable($goal['created_at']);
            } catch (Exception $e) {
                $startDate = null;
            }
        }

        $history = $measurementsRepository->fetchChronologicalForMember($memberId, $startDate);

        if (is_array($history) && count($history) > 1) {
            $firstEntry = reset($history);
            $lastEntry = end($history);

            $firstDate = null;
            if (isset($firstEntry['measurement_date'])) {
                try {
                    $firstDate = new DateTimeImmutable($firstEntry['measurement_date']);
                } catch (Exception $e) {
                    $firstDate = null;
                }
            }

            $lastDate = $currentMeasurementDate;
            if (isset($lastEntry['measurement_date'])) {
                try {
                    $lastDate = new DateTimeImmutable($lastEntry['measurement_date']);
                } catch (Exception $e) {
                    $lastDate = $currentMeasurementDate;
                }
            }

            if ($firstDate && $lastDate && $lastDate > $firstDate) {
                $firstWeightKg = isset($firstEntry['weight_kg']) ? (float)$firstEntry['weight_kg'] : null;
                $lastWeightKg = isset($lastEntry['weight_kg']) ? (float)$lastEntry['weight_kg'] : $currentWeightKg;

                if ($firstWeightKg !== null && $lastWeightKg !== null) {
                    $lostKg = $firstWeightKg - $lastWeightKg;
                    $daysElapsed = ($lastDate->getTimestamp() - $firstDate->getTimestamp()) / 86400;

                    if ($daysElapsed > 0 && $lostKg > 0) {
                        $dailyRate = $lostKg / $daysElapsed;

                        if ($dailyRate > 0 && $weightToGoalKg !== null) {
                            $daysToGoal = $dailyRate > 0 ? $weightToGoalKg / $dailyRate : null;

                            if ($daysToGoal !== null) {
                                $estimatedCompletionDate = $lastDate->add(new DateInterval('P' . max(0, (int)ceil($daysToGoal)) . 'D'));
                            }
                        }
                    }
                }
            }
        }
    }

    if ($estimatedCompletionDate) {
        $estimatedCompletionFormatted = wl_format_datetime($estimatedCompletionDate);
        if ($targetDate) {
            $estimatedComparison = $estimatedCompletionDate;
            if ($estimatedComparison->getTimezone()->getName() !== $targetDate->getTimezone()->getName()) {
                $estimatedComparison = $estimatedCompletionDate->setTimezone($targetDate->getTimezone());
            }
            $onTrack = $estimatedComparison <= $targetDate;
        }
    } else {
        $estimatedCompletionFormatted = null;
    }

    return [
        'current_weight' => $currentWeight,
        'starting_weight' => $startingWeight,
        'goal_weight' => $goalWeightForResponse,
        'weight_lost' => $weightLost,
        'weight_to_goal' => $weightToGoal,
        'progress_percentage' => round($progressPercentage, 1),
        'on_track' => $onTrack,
        'days_remaining' => $daysRemaining,
        'estimated_completion_date' => $estimatedCompletionFormatted,
    ];
}
