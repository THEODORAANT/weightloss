<?php
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'PerchMailer_') === 0) {
        include(PERCH_PATH . '/addons/apps/perch_mailer/lib/' . $class_name . '.class.php');
        return true;
    }

    return false;
});

function perch_mailer_trigger($trigger_slug, $memberID = null, $vars = [], $contactEmail = null)
{
    $API = new PerchAPI(1.0, 'perch_mailer');
    $Service = new PerchMailer_Service($API);

    return $Service->send_trigger($trigger_slug, $memberID, $vars, $contactEmail);
}

function perch_mailer_sync_contacts_from_members()
{
    $API = new PerchAPI(1.0, 'perch_mailer');
    $Contacts = new PerchMailer_Contacts($API);

    return $Contacts->sync_from_members();
}
