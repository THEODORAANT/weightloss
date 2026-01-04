<?php

echo $HTML->title_panel([
    'heading' => $heading1,
], $CurrentUser);

if (isset($message)) {
    echo $message;
}

$active_section = 'templates';
include(__DIR__ . '/../_subnav.php');

echo $HTML->heading2($heading2);

echo $Form->form_start();
echo $Form->fields_from_template($Template, $details);
echo $Form->submit_field('submit', $Lang->get('Save template'), $API->app_path());
echo $Form->form_end();
