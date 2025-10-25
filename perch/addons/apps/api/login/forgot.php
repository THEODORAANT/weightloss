<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Method not allowed']);
    return;
}

include __DIR__ . '/../../../../../core/runtime/runtime.php';

$input = file_get_contents('php://input');
$data = json_decode($input ?: '[]', true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    return;
}

$email = $data['email'] ?? null;
if (!$email || !is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'A valid email address must be supplied.']);
    return;
}

$reset = reset_member_password_api($email);

if ($reset) {
    echo json_encode([
        'success' => true,
        'message' => 'If the email matches an account, reset instructions have been sent.'
    ]);
    return;
}

http_response_code(500);
echo json_encode(['error' => 'Unable to reset password.']);

