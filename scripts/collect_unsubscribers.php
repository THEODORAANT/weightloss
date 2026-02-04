<?php

declare(strict_types=1);

require_once __DIR__ . '/email_unsubscribe_list.php';

$unsubscribeConfig = load_email_unsubscribe_storage();

$unsubscribers = array_merge(
    array_map(static function (int $id): array {
        return ['type' => 'member_id', 'value' => $id];
    }, $unsubscribeConfig['member_ids']),
    array_map(static function (int $id): array {
        return ['type' => 'customer_id', 'value' => $id];
    }, $unsubscribeConfig['customer_ids']),
    array_map(static function (string $email): array {
        return ['type' => 'email', 'value' => $email];
    }, $unsubscribeConfig['emails'])
);

if (PHP_SAPI === 'cli') {
    echo json_encode($unsubscribers, JSON_PRETTY_PRINT) . PHP_EOL;
}

return $unsubscribers;
