<?php
$Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

$Smartbar->add_item([
    'active' => $active_section === 'templates',
    'title'  => $Lang->get('Templates'),
    'link'   => $API->app_path() . '/templates/',
    'icon'   => 'core/o-newspaper',
]);

$Smartbar->add_item([
    'active' => $active_section === 'triggers',
    'title'  => $Lang->get('Triggers'),
    'link'   => $API->app_path() . '/triggers/',
    'icon'   => 'core/o-activity',
]);

$Smartbar->add_item([
    'active' => $active_section === 'contacts',
    'title'  => $Lang->get('Contacts'),
    'link'   => $API->app_path() . '/contacts/',
    'icon'   => 'core/o-user',
]);

if ($active_section === 'contacts') {
    $Smartbar->add_item([
        'title'    => $Lang->get('Sync from members'),
        'link'     => $API->app_path() . '/contacts/import/',
        'icon'     => 'core/o-cloud-download',
        'position' => 'end',
    ]);
}

echo $Smartbar->render();
