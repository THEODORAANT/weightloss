<?php

class PerchMembers_PharmacyApiClient
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct(string $apiUrl, string $apiKey)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
    }

    /**
     * Send a customer note to the pharmacy API.
     *
     * @param string $email
     * @param string $note
     * @param bool   $isPinned
     *
     * @return array{success:bool, data:mixed, http_code?:int, message?:string}
     */
    public function sendCustomerNote(string $email, string $note, bool $isPinned = false): array
    {
        $payload = [
            'email'    => $email,
            'note'     => $note,
            'isPinned' => $isPinned,
        ];

        $url = "{$this->apiUrl}/customers/notes";

        $headers = [
            'Content-Type: application/json',
            'x-api-key: '.$this->apiKey,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'data'    => null,
                'message' => 'cURL error: '.$error,
            ];
        }

        $decoded = json_decode((string) $response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success'   => true,
                'data'      => $decoded,
                'http_code' => $httpCode,
            ];
        }

        $message = 'HTTP error code: '.$httpCode;
        if (is_array($decoded) && isset($decoded['message']) && $decoded['message'] !== '') {
            $message = $decoded['message'];
        }

        return [
            'success'   => false,
            'data'      => $decoded,
            'http_code' => $httpCode,
            'message'   => $message,
        ];
    }
}
