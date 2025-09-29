<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

include __DIR__ . '/../../../../../core/runtime/runtime.php';
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../lib/social_auth.php';

$data = json_decode(file_get_contents('php://input') ?: '[]', true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    return;
}

$token = $data['identity_token'] ?? $data['token'] ?? $data['id_token'] ?? null;
if (!$token || !is_string($token)) {
    http_response_code(400);
    echo json_encode(['error' => 'An Apple identity token must be supplied.']);
    return;
}

$allowedAudiences = api_social_get_allowed_audiences('APPLE_SIGNIN_CLIENT_IDS');
$result = api_social_verify_apple_identity_token($token, $allowedAudiences);
if (!$result['ok']) {
    http_response_code($result['status']);
    echo json_encode(['error' => $result['error']]);
    return;
}

$upsert = api_social_upsert_member('apple', $result['payload'], $data);
if (!$upsert['ok']) {
    http_response_code($upsert['status']);
    echo json_encode(['error' => $upsert['error']]);
    return;
}

$memberID = (int)$upsert['member_id'];
$memberEmail = $result['payload']['email'];
$token = generate_token($memberID, $memberEmail);

$response = [
    'token' => $token,
    'is_new_member' => $upsert['created'],
    'member' => $upsert['member'],
];

echo json_encode($response);

