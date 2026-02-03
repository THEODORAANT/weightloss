<?php

function comms_service_base_url(): string
{
    $base = getenv('COMMS_SERVICE_URL') ?: '';
    return rtrim($base, '/');
}

function comms_service_auth_header(): ?string
{
    $token = getenv('COMMS_SERVICE_TOKEN') ?: '';
    if ($token === '') {
        return null;
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
