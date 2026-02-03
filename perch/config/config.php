<?php

// Comms Service configuration.
// Generate COMMS_SERVICE_TOKEN with:
// node -e "
// const jwt=require('jsonwebtoken');
// console.log(jwt.sign(
// {
// tenant_id:'gwl-cy',
// actor:{role:'admin',user_id:'cli',display_name:'CLI'},
// iss:'perch',
// aud:'comms-service'
// },
// '2wwTARwF19RYnS3POX/8UyP8eIKDhx2jjY479IeKGag=',
// { algorithm:'HS256', expiresIn:'1h' }
// ));
// "

if (!defined('COMMS_SERVICE_URL')) {
    define('COMMS_SERVICE_URL', getenv('COMMS_SERVICE_URL') ?: '');
}

$commsToken = getenv('COMMS_SERVICE_TOKEN') ?: '';
if ($commsToken === '') {
    if (defined('PERCH_PATH')) {
        require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_service.php';
    }
    if (function_exists('comms_service_generate_token')) {
        $commsToken = comms_service_generate_token();
    }
}

if (!defined('COMMS_SERVICE_TOKEN')) {
    define('COMMS_SERVICE_TOKEN', $commsToken);
}
