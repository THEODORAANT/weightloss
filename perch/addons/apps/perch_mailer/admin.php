<?php
if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_mailer')) {
    $this->register_app('perch_mailer', 'Mailer', 3, 'Manage PerchMailer templates, triggers, and contacts', '1.0');

    $this->add_setting('perch_mailer_from_name', 'Mailer sender name', 'text', '');
    $this->add_setting('perch_mailer_from_email', 'Mailer sender email', 'text', '');
}

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'PerchMailer_') === 0) {
        include(PERCH_PATH . '/addons/apps/perch_mailer/lib/' . $class_name . '.class.php');
        return true;
    }

    return false;
});
