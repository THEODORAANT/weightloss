<?php

if (!defined('PERCH_DB_PREFIX')) {
    exit;
}

$API = new PerchAPI(1.0, 'perch_sendgrid');
$UserPrivileges = $API->get('UserPrivileges');
$UserPrivileges->create_privilege('perch_sendgrid', 'Access SendGrid');
