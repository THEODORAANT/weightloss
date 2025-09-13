<?php
if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_notification_logs')) {
    $this->register_app('perch_notification_logs', 'Notification Logs', 1, 'View notification log entries', '1.0');
}
