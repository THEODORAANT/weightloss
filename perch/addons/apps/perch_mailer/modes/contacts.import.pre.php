<?php
$Contacts = new PerchMailer_Contacts($API);
$imported = $Contacts->sync_from_members();

PerchUtil::redirect($API->app_path() . '/contacts/?imported=' . (int) $imported);
