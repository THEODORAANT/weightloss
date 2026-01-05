<?php

declare(strict_types=1);

/**
 * Central list of recipients who should never receive scripted emails.
 * Add member IDs, customer IDs, or email addresses to the appropriate list.
 */
function get_email_unsubscribe_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = [
            'member_ids'   => [
                // Member IDs to exclude from all scripted email sends.
            ],
            'customer_ids' => [
                // Customer IDs to exclude from all scripted email sends.
                1160,
            ],
            'emails'       => [
                // Email addresses to exclude from all scripted email sends.
            ],
        ];

        $config['emails'] = array_map(
            static function ($email) {
                return strtolower(trim((string) $email));
            },
            $config['emails']
        );
    }

    return $config;
}

function is_member_unsubscribed(?int $memberID): bool
{
    if ($memberID === null || $memberID <= 0) {
        return false;
    }

    $config = get_email_unsubscribe_config();

    return in_array($memberID, $config['member_ids'], true);
}

function is_customer_unsubscribed(?int $customerID): bool
{
    if ($customerID === null || $customerID <= 0) {
        return false;
    }

    $config = get_email_unsubscribe_config();

    return in_array($customerID, $config['customer_ids'], true);
}

function is_email_unsubscribed(?string $emailAddress): bool
{
    if ($emailAddress === null) {
        return false;
    }

    $normalizedEmail = strtolower(trim($emailAddress));

    if ($normalizedEmail === '') {
        return false;
    }

    $config = get_email_unsubscribe_config();

    return in_array($normalizedEmail, $config['emails'], true);
}
