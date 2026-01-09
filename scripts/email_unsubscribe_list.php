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
        $staticConfig = [
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

        $storedConfig = load_email_unsubscribe_storage();
        $config = normalize_email_unsubscribe_config([
            'member_ids'   => array_merge($staticConfig['member_ids'], $storedConfig['member_ids']),
            'customer_ids' => array_merge($staticConfig['customer_ids'], $storedConfig['customer_ids']),
            'emails'       => array_merge($staticConfig['emails'], $storedConfig['emails']),
        ]);
    }

    return $config;
}

function get_email_unsubscribe_storage_path(): string
{
    $rootDir = realpath(__DIR__ . '/..');
    if ($rootDir === false) {
        $rootDir = dirname(__DIR__);
    }

    return $rootDir . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'email_unsubscribe_list.json';
}

/**
 * @return array{member_ids:array<int>,customer_ids:array<int>,emails:array<string>}
 */
function load_email_unsubscribe_storage(): array
{
    $path = get_email_unsubscribe_storage_path();

    if (!file_exists($path)) {
        return [
            'member_ids' => [],
            'customer_ids' => [],
            'emails' => [],
        ];
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return [
            'member_ids' => [],
            'customer_ids' => [],
            'emails' => [],
        ];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [
            'member_ids' => [],
            'customer_ids' => [],
            'emails' => [],
        ];
    }

    return normalize_email_unsubscribe_config($decoded);
}

/**
 * @param array<string,mixed> $config
 * @return array{member_ids:array<int>,customer_ids:array<int>,emails:array<string>}
 */
function normalize_email_unsubscribe_config(array $config): array
{
    $normalized = [
        'member_ids' => [],
        'customer_ids' => [],
        'emails' => [],
    ];

    if (isset($config['member_ids']) && is_array($config['member_ids'])) {
        $normalized['member_ids'] = array_values(array_unique(array_filter(array_map('intval', $config['member_ids']), static function ($id) {
            return $id > 0;
        })));
    }

    if (isset($config['customer_ids']) && is_array($config['customer_ids'])) {
        $normalized['customer_ids'] = array_values(array_unique(array_filter(array_map('intval', $config['customer_ids']), static function ($id) {
            return $id > 0;
        })));
    }

    if (isset($config['emails']) && is_array($config['emails'])) {
        $normalized['emails'] = array_values(array_unique(array_filter(array_map(static function ($email) {
            return strtolower(trim((string) $email));
        }, $config['emails']), static function ($email) {
            return $email !== '';
        })));
    }

    return $normalized;
}

/**
 * @param array<string,mixed> $config
 */
function save_email_unsubscribe_storage(array $config): bool
{
    $path = get_email_unsubscribe_storage_path();
    $dir = dirname($path);

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $normalized = normalize_email_unsubscribe_config($config);
    $payload = json_encode($normalized, JSON_PRETTY_PRINT);
    if ($payload === false) {
        return false;
    }

    return file_put_contents($path, $payload . PHP_EOL, LOCK_EX) !== false;
}

function add_email_unsubscribe_entry(?int $memberID, ?int $customerID, ?string $emailAddress): bool
{
    $memberID = $memberID !== null && $memberID > 0 ? $memberID : null;
    $customerID = $customerID !== null && $customerID > 0 ? $customerID : null;
    $emailAddress = $emailAddress !== null ? trim($emailAddress) : null;

    if ($emailAddress !== null && $emailAddress !== '' && !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $emailAddress = null;
    }

    if ($memberID === null && $customerID === null && ($emailAddress === null || $emailAddress === '')) {
        return false;
    }

    $config = load_email_unsubscribe_storage();

    if ($memberID !== null) {
        $config['member_ids'][] = $memberID;
    }

    if ($customerID !== null) {
        $config['customer_ids'][] = $customerID;
    }

    if ($emailAddress !== null && $emailAddress !== '') {
        $config['emails'][] = $emailAddress;
    }

    return save_email_unsubscribe_storage($config);
}

function build_scripted_email_unsubscribe_url(
    string $siteURL = '',
    ?int $memberID = null,
    ?int $customerID = null,
    ?string $emailAddress = null
): string {
    $query = [];

    if ($memberID !== null && $memberID > 0) {
        $query['member_id'] = $memberID;
    }

    if ($customerID !== null && $customerID > 0) {
        $query['customer_id'] = $customerID;
    }

    if ($emailAddress !== null && trim($emailAddress) !== '') {
        $query['email'] = $emailAddress;
    }

    $base = $siteURL !== '' ? rtrim($siteURL, '/') : '';
    $url = ($base !== '' ? $base : '') . '/scripts/unsubscribe.php';

    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    return $url;
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
