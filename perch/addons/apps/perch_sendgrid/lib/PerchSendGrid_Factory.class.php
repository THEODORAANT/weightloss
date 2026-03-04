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
