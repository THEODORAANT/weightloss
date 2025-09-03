<?php error_reporting(E_ERROR | E_PARSE);

if (session_status() === PHP_SESSION_NONE) {
       ob_start();
       session_start();
   } ?>
<?php

$domain = $_SERVER['HTTP_HOST'];

// Remove 'www.' if it's part of the domain
$domain = preg_replace('/^www\./', '', $domain);

?>
<?php
    include(__DIR__.'/core/runtime/runtime.php');

