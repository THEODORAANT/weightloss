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

if (!function_exists('wl_questionnaire_user_id_key')) {
    function wl_questionnaire_user_id_key($mode)
    {
        return $mode === 'reorder'
            ? 'questionnaire_reorder_user_id'
            : 'questionnaire_user_id';
    }
}

if (!function_exists('wl_get_or_create_questionnaire_user_id')) {
    function wl_get_or_create_questionnaire_user_id($mode, callable $generator)
    {
        $sessionUserIdKey = wl_questionnaire_user_id_key($mode);
        $meta = wl_questionnaire_session_meta($mode);
        $sessionKey = $meta['session_key'];

        $existingUserId = '';
        if (isset($_SESSION[$sessionUserIdKey]) && is_string($_SESSION[$sessionUserIdKey])) {
            $existingUserId = trim((string)$_SESSION[$sessionUserIdKey]);
        }

        if ($existingUserId === '' && isset($_SESSION[$sessionKey]['uuid']) && is_string($_SESSION[$sessionKey]['uuid'])) {
            $existingUserId = trim((string)$_SESSION[$sessionKey]['uuid']);
        }

        if ($existingUserId === '') {
            $existingUserId = (string)$generator();
        }

        $_SESSION[$sessionUserIdKey] = $existingUserId;
        $_SESSION[$sessionKey]['uuid'] = $existingUserId;

        return $existingUserId;
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

        $sessionUserIdKey = wl_questionnaire_user_id_key($mode);
        if (
            empty($_SESSION[$sessionUserIdKey]) &&
            isset($_SESSION[$sessionKey]['uuid']) &&
            is_string($_SESSION[$sessionKey]['uuid'])
        ) {
            $_SESSION[$sessionUserIdKey] = trim((string)$_SESSION[$sessionKey]['uuid']);
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
        $sessionUserIdKey = wl_questionnaire_user_id_key($mode);
        unset($_SESSION[$meta['session_key']]);
        unset($_SESSION[$sessionUserIdKey]);
        setcookie($meta['cookie_key'], '', time() - 3600, '/');
    }
}
