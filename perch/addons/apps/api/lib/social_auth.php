<?php

if (!function_exists('api_social_base64url_decode')) {
    function api_social_base64url_decode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder > 0) {
            $input .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($input, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}

if (!function_exists('api_social_encode_length')) {
    function api_social_encode_length(int $length)
    {
        if ($length <= 0x7F) {
            return chr($length);
        }

        $temp = ltrim(pack('N', $length), "\x00");
        return chr(0x80 | strlen($temp)) . $temp;
    }
}

if (!function_exists('api_social_jwk_to_pem')) {
    function api_social_jwk_to_pem(array $jwk)
    {
        if (!isset($jwk['kty']) || $jwk['kty'] !== 'RSA' || !isset($jwk['n'], $jwk['e'])) {
            return null;
        }

        $modulus = api_social_base64url_decode($jwk['n']);
        $exponent = api_social_base64url_decode($jwk['e']);

        if ($modulus === null || $exponent === null) {
            return null;
        }

        if (ord($modulus[0]) > 0x7F) {
            $modulus = "\x00" . $modulus;
        }

        $modulusEncoded = "\x02" . api_social_encode_length(strlen($modulus)) . $modulus;
        $exponentEncoded = "\x02" . api_social_encode_length(strlen($exponent)) . $exponent;

        $rsaPublicKey = "\x30" . api_social_encode_length(strlen($modulusEncoded) + strlen($exponentEncoded))
            . $modulusEncoded . $exponentEncoded;

        $bitString = "\x03" . api_social_encode_length(strlen($rsaPublicKey) + 1) . "\x00" . $rsaPublicKey;
        $algorithmIdentifier = "\x30\x0d\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01\x05\x00";
        $sequence = "\x30" . api_social_encode_length(strlen($algorithmIdentifier) + strlen($bitString))
            . $algorithmIdentifier . $bitString;

        $pem = "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($sequence), 64, "\n")
            . "-----END PUBLIC KEY-----\n";

        return $pem;
    }
}

if (!function_exists('api_social_fetch_jwks')) {
    function api_social_fetch_jwks(string $url)
    {
        static $cache = [];
        $now = time();

        if (isset($cache[$url]) && $cache[$url]['expires_at'] > $now) {
            return $cache[$url]['keys'];
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || !isset($data['keys']) || !is_array($data['keys'])) {
            return null;
        }

        $cache[$url] = [
            'keys' => $data['keys'],
            'expires_at' => $now + 3600,
        ];

        return $cache[$url]['keys'];
    }
}

if (!function_exists('api_social_get_allowed_audiences')) {
    function api_social_get_allowed_audiences(string $envName)
    {
        $raw = getenv($envName);
        if ($raw === false || $raw === '') {
            return [];
        }

        $parts = array_map('trim', explode(',', $raw));
        return array_values(array_filter($parts, static function ($value) {
            return $value !== '';
        }));
    }
}

if (!function_exists('api_social_verify_with_jwks')) {
    function api_social_verify_with_jwks(string $jwt, string $jwksUrl, array $allowedIssuers, array $allowedAudiences)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return [
                'ok' => false,
                'status' => 400,
                'error' => 'Invalid token format.',
            ];
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        $header = json_decode(api_social_base64url_decode($headerB64) ?? '', true);
        $payload = json_decode(api_social_base64url_decode($payloadB64) ?? '', true);
        $signature = api_social_base64url_decode($signatureB64);

        if (!is_array($header) || !is_array($payload) || $signature === null) {
            return [
                'ok' => false,
                'status' => 400,
                'error' => 'Unable to decode token.',
            ];
        }

        if (($header['alg'] ?? '') !== 'RS256') {
            return [
                'ok' => false,
                'status' => 400,
                'error' => 'Unsupported token algorithm.',
            ];
        }

        $kid = $header['kid'] ?? null;
        if (!$kid) {
            return [
                'ok' => false,
                'status' => 400,
                'error' => 'Token is missing a key identifier.',
            ];
        }

        $keys = api_social_fetch_jwks($jwksUrl);
        if (!$keys) {
            return [
                'ok' => false,
                'status' => 503,
                'error' => 'Unable to download signing keys.',
            ];
        }

        $jwk = null;
        foreach ($keys as $key) {
            if (isset($key['kid']) && $key['kid'] === $kid) {
                $jwk = $key;
                break;
            }
        }

        if (!$jwk) {
            return [
                'ok' => false,
                'status' => 401,
                'error' => 'Token was not signed by a recognised key.',
            ];
        }

        $publicKey = api_social_jwk_to_pem($jwk);
        if (!$publicKey) {
            return [
                'ok' => false,
                'status' => 401,
                'error' => 'Unable to construct public key for signature verification.',
            ];
        }

        $verified = openssl_verify($headerB64 . '.' . $payloadB64, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        if ($verified !== 1) {
            return [
                'ok' => false,
                'status' => 401,
                'error' => 'Token signature is invalid.',
            ];
        }

        $now = time();
        $leeway = 120;
        if (isset($payload['exp']) && ($payload['exp'] + $leeway) < $now) {
            return [
                'ok' => false,
                'status' => 401,
                'error' => 'Token has expired.',
            ];
        }

        if (isset($payload['nbf']) && ($payload['nbf'] - $leeway) > $now) {
            return [
                'ok' => false,
                'status' => 401,
                'error' => 'Token cannot be used yet.',
            ];
        }

        if (!empty($allowedIssuers)) {
            $issuer = $payload['iss'] ?? null;
            if (!$issuer || !in_array($issuer, $allowedIssuers, true)) {
                return [
                    'ok' => false,
                    'status' => 401,
                    'error' => 'Token was issued by an unexpected issuer.',
                ];
            }
        }

        if (!empty($allowedAudiences)) {
            $aud = $payload['aud'] ?? null;
            $audValid = false;
            if (is_array($aud)) {
                foreach ($aud as $candidate) {
                    if (in_array($candidate, $allowedAudiences, true)) {
                        $audValid = true;
                        break;
                    }
                }
            } elseif ($aud !== null) {
                $audValid = in_array($aud, $allowedAudiences, true);
            }

            if (!$audValid) {
                return [
                    'ok' => false,
                    'status' => 401,
                    'error' => 'Token audience is not allowed.',
                ];
            }
        }

        return [
            'ok' => true,
            'status' => 200,
            'payload' => $payload,
        ];
    }
}

if (!function_exists('api_social_verify_google_id_token')) {
    function api_social_verify_google_id_token(string $token, array $allowedAudiences = [])
    {
        $result = api_social_verify_with_jwks(
            $token,
            'https://www.googleapis.com/oauth2/v3/certs',
            ['https://accounts.google.com', 'accounts.google.com'],
            $allowedAudiences
        );

        if (!$result['ok']) {
            return $result;
        }

        if (empty($result['payload']['email'])) {
            return [
                'ok' => false,
                'status' => 422,
                'error' => 'Google did not return an email address for this account.',
            ];
        }

        return $result;
    }
}

if (!function_exists('api_social_verify_apple_identity_token')) {
    function api_social_verify_apple_identity_token(string $token, array $allowedAudiences = [])
    {
        $result = api_social_verify_with_jwks(
            $token,
            'https://appleid.apple.com/auth/keys',
            ['https://appleid.apple.com'],
            $allowedAudiences
        );

        if (!$result['ok']) {
            return $result;
        }

        if (empty($result['payload']['email'])) {
            return [
                'ok' => false,
                'status' => 422,
                'error' => 'Apple did not include an email address with this identity token.',
            ];
        }

        return $result;
    }
}

if (!function_exists('api_social_extract_names')) {
    function api_social_extract_names(array $payload, array $input)
    {
        $first = $payload['given_name'] ?? $payload['givenName'] ?? null;
        $last = $payload['family_name'] ?? $payload['familyName'] ?? null;

        if (!$first && isset($payload['name'])) {
            $parts = preg_split('/\s+/', trim($payload['name']));
            if ($parts && count($parts) > 0) {
                $first = array_shift($parts);
                if (!$last && !empty($parts)) {
                    $last = implode(' ', $parts);
                }
            }
        }

        if (!$first && isset($input['first_name'])) {
            $first = $input['first_name'];
        }

        if (!$last && isset($input['last_name'])) {
            $last = $input['last_name'];
        }

        if (!$first && isset($input['given_name'])) {
            $first = $input['given_name'];
        }

        if (!$last && isset($input['family_name'])) {
            $last = $input['family_name'];
        }

        if (isset($input['full_name']) && is_array($input['full_name'])) {
            $first = $first ?? ($input['full_name']['givenName'] ?? $input['full_name']['given_name'] ?? null);
            $last = $last ?? ($input['full_name']['familyName'] ?? $input['full_name']['family_name'] ?? null);
        }

        $first = $first ? trim((string)$first) : '';
        $last = $last ? trim((string)$last) : '';

        return [$first, $last];
    }
}

if (!function_exists('api_social_normalise_email')) {
    function api_social_normalise_email(string $email)
    {
        return strtolower(trim($email));
    }
}

if (!function_exists('api_social_assign_tag')) {
    function api_social_assign_tag(int $memberID, string $tag, ?string $label = null)
    {
        $API = new PerchAPI(1.0, 'perch_members');
        $Tags = new PerchMembers_Tags($API);
        $Tag = $Tags->find_or_create($tag, $label ?? $tag);
        if (is_object($Tag)) {
            $Tag->add_to_member($memberID, false);
        }
    }
}

if (!function_exists('api_social_ensure_customer')) {
    function api_social_ensure_customer(int $memberID, array $customerData)
    {
        if (!class_exists('PerchShop_Customers')) {
            return;
        }

        $API = new PerchAPI(1.0, 'perch_shop');
        $Customers = new PerchShop_Customers($API);
        $Customer = $Customers->find_by_memberID($memberID);

        if (!$Customer) {
            perch_shop_register_customer_from_api($memberID, $customerData);
        }
    }
}

if (!function_exists('api_social_upsert_member')) {
    function api_social_upsert_member(string $provider, array $payload, array $input = [])
    {
        $email = api_social_normalise_email($payload['email'] ?? '');
        if ($email === '') {
            return [
                'ok' => false,
                'status' => 422,
                'error' => 'An email address is required to create or locate a member.',
            ];
        }

        [$firstName, $lastName] = api_social_extract_names($payload, $input);

        $API = new PerchAPI(1.0, 'perch_members');
        $Members = new PerchMembers_Members($API);
        $Member = $Members->get_one_by('memberEmail', $email);
        $created = false;

        $profileUpdates = array_filter([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'oauth_provider' => $provider,
            'oauth_subject' => $payload['sub'] ?? null,
            'oauth_email_verified' => isset($payload['email_verified']) ? in_array($payload['email_verified'], [true, 'true', 1, '1'], true) : null,
            'oauth_last_login' => date('c'),
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        $customerData = array_filter([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ], static function ($value) {
            return $value !== null && $value !== '';
        });

        if ($Member) {
            $memberID = (int)$Member->id();
            $existing = $Member->to_array();

            $updates = $profileUpdates;
            if ($firstName === '' && isset($existing['first_name'])) {
                unset($updates['first_name']);
            } elseif ($firstName !== '' && isset($existing['first_name']) && $existing['first_name'] === $firstName) {
                unset($updates['first_name']);
            }

            if ($lastName === '' && isset($existing['last_name'])) {
                unset($updates['last_name']);
            } elseif ($lastName !== '' && isset($existing['last_name']) && $existing['last_name'] === $lastName) {
                unset($updates['last_name']);
            }

            if (!empty($updates)) {
                perch_member_api_update_profile($memberID, $updates);
            }

            api_social_ensure_customer($memberID, $customerData);
            $memberDetails = perch_member_profile($memberID);

            return [
                'ok' => true,
                'status' => 200,
                'member_id' => $memberID,
                'member' => $memberDetails,
                'created' => $created,
            ];
        }

        try {
            $randomPassword = bin2hex(random_bytes(32));
        } catch (Exception $ex) {
            $randomPassword = sha1(uniqid((string)mt_rand(), true));
        }

        $registrationData = array_merge([
            'email' => $email,
            'password' => $randomPassword,
            'device' => 'app',
        ], $profileUpdates);

        if ($firstName !== '') {
            $registrationData['first_name'] = $firstName;
        }
        if ($lastName !== '') {
            $registrationData['last_name'] = $lastName;
        }

        $memberID = perch_member_api_register($registrationData);
        if (!$memberID) {
            $Member = $Members->get_one_by('memberEmail', $email);
            if ($Member) {
                $memberID = (int)$Member->id();
            } else {
                return [
                    'ok' => false,
                    'status' => 500,
                    'error' => 'Unable to create a member record for this account.',
                ];
            }
        } else {
            $created = true;
        }

        $Member = $Members->find($memberID);
        if ($Member) {
            api_social_assign_tag($memberID, 'register-' . $provider, ucfirst($provider) . ' registration');
        }

        if (!empty($profileUpdates)) {
            perch_member_api_update_profile($memberID, $profileUpdates);
        }

        api_social_ensure_customer($memberID, $customerData);

        $memberDetails = perch_member_profile($memberID);

        return [
            'ok' => true,
            'status' => 200,
            'member_id' => $memberID,
            'member' => $memberDetails,
            'created' => $created,
        ];
    }
}
