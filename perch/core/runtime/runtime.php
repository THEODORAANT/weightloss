<?php error_reporting(E_ERROR | E_PARSE);

      if (session_status() === PHP_SESSION_NONE) {
          ob_start();
          session_start();
      }

    if (defined('PERCH_ERROR_MODE')) {
      if (strpos($_SERVER['SCRIPT_NAME'], 'start.php')) {
        die('You have included the Perch runtime in your page template. Please remove it - Runway will include it for you.');
      }else{
        die('You have included the Perch runtime in your page more than once. Please only include it once.');
      }
    }

    define('PERCH_ERROR_MODE', 'SILENT');
	  include(__DIR__.'/../inc/pre_config.php');
    include(__DIR__.'/../../config/config.php');
    if (!defined('PERCH_PRODUCTION_MODE')) define('PERCH_PRODUCTION_MODE', PERCH_PRODUCTION);
    include(PERCH_CORE . '/runtime/loader.php');
    include(PERCH_CORE . '/runtime/core.php');
    include(PERCH_CORE . '/inc/apps.php');
    include(PERCH_PATH . '/core/inc/forms.php');
   	if (PERCH_FEATHERS && file_exists(PERCH_PATH . '/config/feathers.php')){
      include(PERCH_PATH . '/config/feathers.php');
   	}
    include(PERCH_PATH . '/core/inc/feathers.php');

    $domain = $_SERVER['HTTP_HOST'] ?? '';

    // Remove 'www.' if it's part of the domain
    $domain = preg_replace('/^www\./', '', $domain);

    //echo "ref";
   // echo $_GET['ref'];
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
    //print_r( $_SESSION);
    //print_r( $_COOKIE);
