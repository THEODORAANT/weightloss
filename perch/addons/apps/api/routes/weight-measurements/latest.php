<?php
include(__DIR__ . '/../../../../../core/runtime/runtime.php');

require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/helpers.php';

$token = get_bearer_token();
$payload = verify_token($token);

if (!$payload) {
    wl_weight_measurements_error(401, 'Unauthorized');
}

$memberId = $payload['user_id'];
$repository = wl_weight_measurements_repository();
$measurement = $repository->fetchLatestForMember($memberId);

if (!is_array($measurement)) {
    wl_weight_measurements_error(404, 'No measurements found.');
}

echo json_encode(wl_format_measurement($measurement));
exit;
