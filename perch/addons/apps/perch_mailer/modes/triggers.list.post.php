<?php

echo $HTML->title_panel([
    'heading' => $Lang->get('Mail triggers'),
    'button'  => [
        'text' => $Lang->get('Add trigger'),
        'link' => $API->app_nav() . '/triggers/edit/',
        'icon' => 'core/plus',
        'priv' => 'perch_mailer.triggers.manage',
    ],
], $CurrentUser);

if (isset($message)) {
    echo $message;
}

$active_section = 'triggers';
include(__DIR__ . '/../_subnav.php');

if (PerchUtil::count($triggers)) {
    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
        'title'     => $Lang->get('Title'),
        'value'     => 'triggerTitle',
        'sort'      => 'triggerTitle',
        'edit_link' => 'triggers/edit',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Slug'),
        'value' => 'triggerSlug',
        'sort'  => 'triggerSlug',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Template'),
        'value' => function ($Trigger) use ($template_lookup, $HTML) {
            $id = (int) $Trigger->triggerTemplateID();
            if ($id && isset($template_lookup[$id])) {
                return $HTML->encode($template_lookup[$id]);
            }
            return '&ndash;';
        },
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Active'),
        'value' => function ($Trigger, $HTML) {
            return $Trigger->triggerActive() ? $HTML->encode('Yes') : $HTML->encode('No');
        },
        'sort' => 'triggerActive',
    ]);

    echo $Listing->render($triggers);
} else {
    echo $HTML->warning_message($Lang->get('No mail triggers have been created yet.'));
}
