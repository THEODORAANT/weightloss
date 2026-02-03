<?php

function comms_service_base_url(): string
{
    $base = getenv('COMMS_SERVICE_URL') ?: '';
    if ($base === '' && defined('COMMS_SERVICE_URL')) {
        $base = COMMS_SERVICE_URL;
    }
    return rtrim($base, '/');
}

function comms_service_auth_header(): ?string
{
    $token = getenv('COMMS_SERVICE_TOKEN') ?: '';
    if ($token === '') {
        if (function_exists('comms_service_generate_token')) {
            $token = comms_service_generate_token();
        }
        if ($token === '') {
            return null;
        }
    }

    return 'Authorization: Bearer ' . $token;
}

function comms_service_request(string $method, string $path, array $payload = []): bool
{
    $baseUrl = comms_service_base_url();
    if ($baseUrl === '') {
        return false;
    }

    $url = $baseUrl . $path;
    $ch = curl_init($url);

    if ($ch === false) {
        return false;
    }

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    $authHeader = comms_service_auth_header();
    if ($authHeader) {
        $headers[] = $authHeader;
    }

    $jsonPayload = json_encode($payload);
    if ($jsonPayload === false) {
        return false;
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return $status >= 200 && $status < 300;
}

function comms_service_link_member(int $memberID, array $memberData = []): bool
{
    $payload = array_merge($memberData, ['memberID' => $memberID]);
    return comms_service_request('POST', '/v1/perch/members/' . $memberID . '/link', $payload);
}

function comms_service_link_order(int $orderID, array $orderData = []): bool
{
    $payload = array_merge($orderData, ['orderID' => $orderID]);
    return comms_service_request('POST', '/v1/perch/orders/' . $orderID . '/link', $payload);
}

function comms_service_send_member_note(int $memberID, array $noteData = []): bool
{
    $payload = array_merge($noteData, ['memberID' => $memberID]);
    return comms_service_request('POST', '/v1/perch/members/' . $memberID . '/notes', $payload);
}

function comms_service_base64url_encode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function comms_service_generate_token(string $secret = 'jwtgrtts', ?int $issuedAt = null): string
{
    $issuedAt = $issuedAt ?? time();

    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT',
    ];

    $payload = [
        'tenant_id' => 'gwl-cy',
        'actor' => [
            'role' => 'admin',
            'user_id' => 'cli',
            'display_name' => 'CLI',
        ],
        'iss' => 'perch',
        'aud' => 'comms-service',
        'iat' => $issuedAt,
        'exp' => $issuedAt + 3600,
    ];

    $segments = [
        comms_service_base64url_encode(json_encode($header)),
        comms_service_base64url_encode(json_encode($payload)),
    ];

    $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
    $segments[] = comms_service_base64url_encode($signature);

    return implode('.', $segments);
}
