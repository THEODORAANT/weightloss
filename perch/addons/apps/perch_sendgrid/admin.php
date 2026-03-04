<?php

if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_sendgrid')) {
    $this->register_app('perch_sendgrid', 'SendGrid', 10, 'Integrate with SendGrid marketing contacts', '1.0');

    $this->add_setting('perch_sendgrid_api_key', 'SendGrid API Key', 'text', '');
    $this->add_setting('perch_sendgrid_list_id', 'SendGrid List ID', 'text', '');
}

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'PerchSendGrid_') === 0) {
        include(PERCH_PATH.'/addons/apps/perch_sendgrid/lib/'.$class_name.'.class.php');
        return true;
    }
    return false;
});
