<?php

class PerchSendGrid_Factory
{
    private $api_key = null;
    private $list_id = null;
    private $base_url = 'https://api.sendgrid.com/v3';

    private function load_settings()
    {
        if ($this->api_key !== null) {
            return;
        }

        $Settings = PerchSettings::fetch();
        $this->api_key = trim((string) $Settings->get('perch_sendgrid_api_key')->val());
        $this->list_id = trim((string) $Settings->get('perch_sendgrid_list_id')->val());
    }

    public function upsert_contact($data)
    {
        $this->load_settings();

        if ($this->api_key === '' || $this->list_id === '') {
            return false;
        }

        $email = $this->extract_email($data);

        if ($email === '') {
            return false;
        }

        $contact = [
            'email' => $email,
        ];

        $first_name = $this->extract_value($data, ['first_name', 'FirstName'], 'fields');
        if ($first_name !== '') {
            $contact['first_name'] = $first_name;
        }

        $last_name = $this->extract_value($data, ['last_name', 'LastName'], 'fields');
        if ($last_name !== '') {
            $contact['last_name'] = $last_name;
        }

        $payload = [
            'list_ids' => [$this->list_id],
            'contacts' => [$contact],
        ];

        return $this->request('PUT', '/marketing/contacts', $payload);
    }



    public function send_dynamic_template_email($template_id, $from, $to_recipients, $global_dynamic_data = [], $options = [], $api_key = null)
    {
        $this->load_settings();

        $active_api_key = $api_key;
        if ($active_api_key === null || trim((string) $active_api_key) === '') {
            $active_api_key = $this->api_key;
        }

        if (trim((string) $active_api_key) === '') {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'Missing SendGrid API key'];
        }

        if (trim((string) $template_id) === '') {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'Missing templateId'];
        }

        if (!is_array($from) || empty($from['email'])) {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'Missing from.email'];
        }

        if (!is_array($to_recipients) || !PerchUtil::count($to_recipients)) {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'No recipients provided'];
        }

        $personalizations = [];
        foreach ($to_recipients as $recipient) {
            if (!is_array($recipient) || empty($recipient['email'])) {
                continue;
            }

            $to_obj = ['email' => $recipient['email']];
            if (!empty($recipient['name'])) {
                $to_obj['name'] = $recipient['name'];
            }

            $dynamic_data = is_array($global_dynamic_data) ? $global_dynamic_data : [];
            if (!empty($recipient['dynamic_data']) && is_array($recipient['dynamic_data'])) {
                $dynamic_data = array_merge($dynamic_data, $recipient['dynamic_data']);
            }

            $personalization = [
                'to' => [$to_obj],
            ];

            if (!empty($dynamic_data)) {
                $personalization['dynamic_template_data'] = $dynamic_data;
            }

            $personalizations[] = $personalization;
        }

        if (!PerchUtil::count($personalizations)) {
            return ['ok' => false, 'status' => 0, 'body' => '', 'error' => 'No valid recipients (missing emails)'];
        }

        $payload = [
            'from' => [
                'email' => $from['email'],
            ],
            'template_id' => $template_id,
            'personalizations' => $personalizations,
        ];

        if (!empty($from['name'])) {
            $payload['from']['name'] = $from['name'];
        }

        if (!empty($options['reply_to']['email'])) {
            $payload['reply_to'] = ['email' => $options['reply_to']['email']];
            if (!empty($options['reply_to']['name'])) {
                $payload['reply_to']['name'] = $options['reply_to']['name'];
            }
        }

        if (!empty($options['categories']) && is_array($options['categories'])) {
            $payload['categories'] = array_values($options['categories']);
        }

        if (!empty($options['cc']) && is_array($options['cc'])) {
            $payload['personalizations'][0]['cc'] = $options['cc'];
        }

        if (!empty($options['bcc']) && is_array($options['bcc'])) {
            $payload['personalizations'][0]['bcc'] = $options['bcc'];
        }

        if (!empty($options['custom_args']) && is_array($options['custom_args'])) {
            $payload['personalizations'][0]['custom_args'] = $options['custom_args'];
        }

        if (!empty($options['headers']) && is_array($options['headers'])) {
            $payload['headers'] = $options['headers'];
        }

        $ch = curl_init($this->base_url.'/mail/send');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$active_api_key,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_TIMEOUT => 30,
        ]);

        $body = curl_exec($ch);
        $curl_error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curl_error) {
            PerchUtil::debug('SendGrid cURL error: '.$curl_error, 'error');
            return ['ok' => false, 'status' => $status, 'body' => (string) $body, 'error' => $curl_error];
        }

        $ok = ($status >= 200 && $status < 300);

        if (!$ok) {
            PerchUtil::debug('SendGrid mail send error '.$status.': '.$body, 'error');
        }

        return [
            'ok' => $ok,
            'status' => $status,
            'body' => (string) $body,
            'error' => $ok ? null : ('SendGrid request failed with status '.$status),
        ];
    }

    public function updateSendgridContactCustomFields($email, $customFields = [])
    {
        $this->load_settings();

        if ($this->api_key === '') {
            return [
                'status' => 0,
                'response' => '',
            ];
        }

        $url = 'https://api.sendgrid.com/v3/marketing/contacts';

        $data = [
            'contacts' => [
                [
                    'email' => $email,
                    'custom_fields' => $customFields,
                ],
            ],
        ];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->api_key,
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'response' => $response,
        ];
    }

    private function extract_email($data)
    {
        $email = $data['email'] ?? $data['email_address'] ?? '';
        return trim((string) $email);
    }

    private function extract_value($data, $keys, $nested_key = null)
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && $data[$key] !== '') {
                return trim((string) $data[$key]);
            }

            if ($nested_key && isset($data[$nested_key]) && is_array($data[$nested_key]) && isset($data[$nested_key][$key]) && $data[$nested_key][$key] !== '') {
                return trim((string) $data[$nested_key][$key]);
            }
        }

        return '';
    }

    private function request($method, $path, $payload)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->base_url.$path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '.$this->api_key,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            PerchUtil::debug('SendGrid cURL error: '.$error, 'error');
            return false;
        }

        if ($status < 200 || $status >= 300) {
            PerchUtil::debug('SendGrid API error '.$status.': '.$response, 'error');
            return false;
        }

        return true;
    }
}
