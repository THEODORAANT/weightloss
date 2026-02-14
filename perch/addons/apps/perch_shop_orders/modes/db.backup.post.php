<?php

echo $HTML->title_panel([
    'heading' => $Lang->get('Database backup'),
], $CurrentUser);

$smartbar_selection = 'db-backup';
include('_orders_smartbar.php');

echo $HTML->heading2('Create backup');

if ($message) {
    echo '<p class="notification ' . ($message_type === 'error' ? 'notification-warning' : 'notification-success') . '">'
        . $HTML->encode($message)
        . '</p>';
}

echo $Form->form_start();
echo $Form->fields_from_template($Template, [], [], false);
echo $Form->submit_field('btnSubmit', 'Run backup', $API->app_path());
echo $Form->form_end();

echo $HTML->heading2('Recent backup files');

if (!PerchUtil::count($recent_backups)) {
    echo '<p>' . $Lang->get('No backup files found in backups/db.') . '</p>';
} else {
    echo '<table class="d">';
    echo '<thead><tr>';
    echo '<th>' . $Lang->get('File') . '</th>';
    echo '<th>' . $Lang->get('Size (bytes)') . '</th>';
    echo '<th>' . $Lang->get('Last modified') . '</th>';
    echo '</tr></thead><tbody>';

    foreach ($recent_backups as $file) {
        echo '<tr>';
        echo '<td>' . $HTML->encode($file['name']) . '</td>';
        echo '<td>' . $HTML->encode((string) $file['size']) . '</td>';
        echo '<td>' . $HTML->encode($file['modified']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
