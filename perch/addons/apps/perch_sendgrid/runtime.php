<?php

spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'PerchSendGrid_') === 0) {
        include(PERCH_PATH.'/addons/apps/perch_sendgrid/lib/'.$class_name.'.class.php');
        return true;
    }
    return false;
});

include(__DIR__.'/runtime/forms.php');
