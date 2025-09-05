<?php

/**
 * Calculate next payment date for monthly packages.
 *
 * This helper assumes that payments recur every month on the same day.
 * If the subsequent month has fewer days and the original payment was on
 * a day that doesn't exist in the new month (e.g. Jan 31 -> Feb), the date
 * will roll over to the last day of the month.
 *
 * @param \DateTimeInterface $lastPayment Date of the most recent payment.
 * @return \DateTimeImmutable Date when the next payment is due.
 */
function nextMonthlyPayment(\DateTimeInterface $lastPayment): \DateTimeImmutable
{
    // Clone the date to avoid modifying the original instance
    $date = \DateTimeImmutable::createFromFormat('Y-m-d', $lastPayment->format('Y-m-d'));

    // Add one month; PHP takes care of month-end edge cases
    return $date->modify('+1 month');
}

// If executed directly from the CLI (not when included), accept an input date
// and output the next due date.
if (
    PHP_SAPI === 'cli'
    && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__
    && isset($argv[1])
) {
    $input = new \DateTimeImmutable($argv[1]);
    echo nextMonthlyPayment($input)->format('Y-m-d') . PHP_EOL;
}
