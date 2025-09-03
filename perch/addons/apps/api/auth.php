<?php
define('AUTH_SECRET', 'g3tw3!gh1l0ss');

function generate_token($user_id, $email) {
    $payload = json_encode([
        "user_id" => $user_id,
        "email" => $email
    ]);

    $base64Payload = base64_encode($payload);
    $signature = hash_hmac('sha256', $base64Payload, AUTH_SECRET);
    return $base64Payload . '.' . $signature;
}

function verify_token($token) {

    $parts = explode('.', $token);
    if (count($parts) !== 2) return false;

    list($base64Payload, $signature) = $parts;
    $expected = hash_hmac('sha256', $base64Payload, AUTH_SECRET);

    if (!hash_equals($expected, $signature)) return false;

    $payload = json_decode(base64_decode($base64Payload), true);
    return $payload ?: false;
}

function get_bearer_token() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}
