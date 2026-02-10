<?php

if (!function_exists('wl_questionnaire_session_map')) {
    function wl_questionnaire_session_map()
    {
        return [
            'first_time' => [
                'session_key' => 'questionnaire',
                'cookie_key' => 'questionnaire',
            ],
            'reorder' => [
                'session_key' => 'questionnaire-reorder',
                'cookie_key' => 'questionnaire_reorder',
            ],
        ];
    }
}

if (!function_exists('wl_questionnaire_session_meta')) {
    function wl_questionnaire_session_meta($mode)
    {
        $map = wl_questionnaire_session_map();
        return $map[$mode] ?? $map['first_time'];
    }
}

if (!function_exists('wl_restore_questionnaire_session')) {
    function wl_restore_questionnaire_session($mode)
    {
        $meta = wl_questionnaire_session_meta($mode);
        $sessionKey = $meta['session_key'];
        $cookieKey = $meta['cookie_key'];

        if (!isset($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }

        if (empty($_SESSION[$sessionKey]) && isset($_COOKIE[$cookieKey])) {
            $cookieQuestionnaire = json_decode($_COOKIE[$cookieKey], true);
            $_SESSION[$sessionKey] = is_array($cookieQuestionnaire) ? $cookieQuestionnaire : [];
        }

        return $_SESSION[$sessionKey];
    }
}

if (!function_exists('wl_save_questionnaire_session')) {
    function wl_save_questionnaire_session($mode, $ttl = 3600)
    {
        $meta = wl_questionnaire_session_meta($mode);
        $sessionKey = $meta['session_key'];
        $cookieKey = $meta['cookie_key'];

        $value = isset($_SESSION[$sessionKey]) && is_array($_SESSION[$sessionKey])
            ? $_SESSION[$sessionKey]
            : [];

        setcookie($cookieKey, json_encode($value), time() + $ttl, '/');
    }
}

if (!function_exists('wl_clear_questionnaire_session')) {
    function wl_clear_questionnaire_session($mode)
    {
        $meta = wl_questionnaire_session_meta($mode);
        unset($_SESSION[$meta['session_key']]);
        setcookie($meta['cookie_key'], '', time() - 3600, '/');
    }
}
