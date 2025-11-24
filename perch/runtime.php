<?php
error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
    ob_start();
    session_start();
}

$domain = $_SERVER['HTTP_HOST'] ?? '';

// Remove 'www.' if it's part of the domain
$domain = preg_replace('/^www\./', '', $domain);

include __DIR__ . '/core/runtime/runtime.php';

// Persist a valid affiliate referrer from the query string
if (isset($_GET['ref'])) {
    $referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_GET['ref']);

    if ($referrer !== '') {
        $API = new PerchAPI(1.0, 'perch_members');
        $Affiliates = new PerchMembers_Affiliates($API);
        $Affiliate = $Affiliates->get_by('affid', $referrer);

        if ($Affiliate) {
            $_SESSION['affiliate_referrer'] = $referrer;

            $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

            setcookie('affiliate_referrer', $referrer, [
                'expires'  => time() + (60 * 60 * 24 * 30), // 30 days
                'path'     => '/',
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }
} elseif (empty($_SESSION['affiliate_referrer']) && isset($_COOKIE['affiliate_referrer'])) {
    $_SESSION['affiliate_referrer'] = preg_replace('/[^A-Za-z0-9]/', '', (string) $_COOKIE['affiliate_referrer']);
}
