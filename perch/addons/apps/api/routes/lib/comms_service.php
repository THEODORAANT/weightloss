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
    $jsonPayload = json_encode($payload);
             // echo "jsonPayload";
               // print_r($jsonPayload );
    if ($jsonPayload === false) {
        return false;
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
     // echo "rrr";
       // print_r($response);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return $status >= 200 && $status < 300;
}

function comms_service_request_json(string $method, string $path, array $payload = [], array $query = []): ?array
{
    $baseUrl = comms_service_base_url();
    if ($baseUrl === '') {
        return null;
    }

    $url = $baseUrl . $path;
    if (!empty($query)) {
        $separator = strpos($url, '?') === false ? '?' : '&';
        $url .= $separator . http_build_query($query);
    }
        //echo "url"; echo $url;
    $ch = curl_init($url);

    if ($ch === false) {
        return null;
    }

    $headers = [
        'Accept: application/json',
    ];

    $authHeader = comms_service_auth_header();
    if ($authHeader) {
        $headers[] = $authHeader;
    }

    $method = strtoupper($method);
    if ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    } else {
        $jsonPayload = json_encode($payload);
          //echo "jsonPayload";
          //  print_r($jsonPayload );
            PerchUtil::debug('Comms member jsonPayload: ' . json_encode($jsonPayload), 'notice');

        if ($jsonPayload === false) {
            return null;
        }
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    }


    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    //echo "response";
     //   print_r($response );
    if ($response === false || $status < 200 || $status >= 300) {
        return null;
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        return null;
    }

    return $decoded;
}

function comms_service_extract_notes(?array $response): array
{
    if (!$response) {
        return [];
    }

    if (isset($response['notes']) && is_array($response['notes'])) {
        return $response['notes'];
    }

    if (isset($response['data']) && is_array($response['data'])) {
        return $response['data'];
    }

    if (isset($response['items']) && is_array($response['items'])) {
        return $response['items'];
    }

    if (isset($response[0]) && is_array($response[0])) {
        return $response;
    }

    return [];
}

function comms_service_extract_customer_id(?array $response): string
{
    if (!$response) {
        return '';
    }

    $candidates = [
        $response['customerId'] ?? null,
        $response['customer_id'] ?? null,
        $response['data']['customerId'] ?? null,
        $response['data']['customer_id'] ?? null,
        $response['customer']['customerId'] ?? null,
        $response['customer']['customer_id'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && $candidate !== '') {
            return $candidate;
        }
    }

    return '';
}

function comms_service_link_member(int $memberID, array $memberData = []): bool
{
    $payload = array_merge($memberData, ['memberID' => $memberID]);
    return comms_service_request('POST', '/v1/perch/members/' . $memberID . '/link', $payload);
}

function comms_service_link_member_response(int $memberID, array $memberData = []): ?array
{
    $payload = array_merge($memberData, ['memberID' => $memberID]);
    return comms_service_request_json('POST', '/v1/perch/members/' . $memberID . '/link', $payload);
}

function comms_service_link_order(int $orderID, array $orderData = []): bool
{
    $payload = array_merge($orderData, ['orderID' => $orderID]);
    return comms_service_request('POST', '/v1/perch/orders/' . $orderID . '/link', $payload);
}

function comms_service_send_member_note(int $memberID, array $noteData = []): bool
{
    $email = '';
    if (isset($noteData['email'])) {
        $email = trim((string) $noteData['email']);
    } elseif (isset($noteData['member_email'])) {
        $email = trim((string) $noteData['member_email']);
    }

    if ($email === '') {
        return false;
    }

    $body = '';
    if (isset($noteData['body'])) {
        $body = trim((string) $noteData['body']);
    }

    $note = '';
    if (isset($noteData['note'])) {
        $note = trim((string) $noteData['note']);
    }

    if ($body === '' && $note === '') {
        return false;
    }

    $type = 'ADMIN';
    if (isset($noteData['type']) && trim((string) $noteData['type']) !== '') {
        $type = strtoupper(trim((string) $noteData['type']));
    } elseif (isset($noteData['note_category']) && trim((string) $noteData['note_category']) !== '') {
        $category = strtolower(trim((string) $noteData['note_category']));
        if ($category === 'clinical') {
            $type = 'CLINICAL';
        } elseif ($category === 'complaint') {
            $type = 'COMPLAINT';
        }
    }

    if (!in_array($type, ['ADMIN', 'CLINICAL', 'COMPLAINT'], true)) {
        $type = 'ADMIN';
    }

    $payload = [
        'email' => $email,
        'type' => $type,
    ];

    if ($body !== '') {
        $payload['body'] = $body;
    }

    if ($note !== '') {
        $payload['note'] = $note;
    }

    $author = '';
    if (isset($noteData['author'])) {
        $author = trim((string) $noteData['author']);
    } elseif (isset($noteData['added_by'])) {
        $author = trim((string) $noteData['added_by']);
    } elseif (isset($noteData['created_by']['name'])) {
        $author = trim((string) $noteData['created_by']['name']);
    }

    if ($author !== '') {
        $payload['author'] = $author;
    }
    $response = comms_service_request_json('POST', '/v1/perch/members/' . $memberID . '/notes', $payload);
echo "note";
print_r($payload);
echo "response";
print_r($response);
    if (is_array($response)) {
        return true;
    }

    return (bool) $response;
}

function comms_service_send_member_note_reply(int $memberID, string $noteID, array $replyData = []): bool
{
    $normalizedNoteID = trim($noteID);
    if ($normalizedNoteID === '') {
        return false;
    }

    $payload = array_merge($replyData, [
        'memberID' => $memberID,
        'note_id' => $normalizedNoteID,
    ]);

    $encodedNoteID = rawurlencode($normalizedNoteID);
    $paths = [
        '/v1/notes/' . $encodedNoteID . '/replies',
        //'/v1/perch/members/' . $memberID . '/notes/' . $encodedNoteID . '/reply',
    ];

    foreach ($paths as $path) {
        if (comms_service_request('POST', $path, $payload)) {
            return true;
        }
    }

    return false;
}

function comms_service_get_member_notes(int $memberID): array
{
    $response = comms_service_request_json('GET', '/v1/perch/members/' . $memberID . '/notes');
    return comms_service_extract_notes($response);
}

function comms_service_get_customer_by_email(string $email): ?array
{
    $normalizedEmail = trim($email);
    if ($normalizedEmail === '') {
        return null;
    }

    return comms_service_request_json('GET', '/v1/perch/customers/' . rawurlencode($normalizedEmail));
}

function comms_service_send_order_note(int $orderID, array $noteData = []): bool
{
    $payload = array_merge($noteData, ['orderID' => $orderID]);
    return comms_service_request('POST', '/v1/perch/orders/' . $orderID . '/notes', $payload);
}

function comms_service_base64url_encode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function comms_service_generate_token(string $secret = '2wwTARwF19RYnS3POX/8UyP8eIKDhx2jjY479IeKGag=', ?int $issuedAt = null): string
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
