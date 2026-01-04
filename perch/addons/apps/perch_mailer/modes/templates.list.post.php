<?php

echo $HTML->title_panel([
    'heading' => $Lang->get('Mail templates'),
    'button'  => [
        'text' => $Lang->get('Add template'),
        'link' => $API->app_nav() . '/templates/edit/',
        'icon' => 'core/plus',
        'priv' => 'perch_mailer.templates.manage',
    ],
], $CurrentUser);

if (isset($message)) {
    echo $message;
}

$active_section = 'templates';
include(__DIR__ . '/../_subnav.php');

if (PerchUtil::count($templates)) {
    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
        'title'     => $Lang->get('Title'),
        'value'     => 'templateTitle',
        'sort'      => 'templateTitle',
        'edit_link' => 'templates/edit',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Subject'),
        'value' => 'templateSubject',
        'sort'  => 'templateSubject',
    ]);

    $Listing->add_col([
        'title'  => $Lang->get('Updated'),
        'value'  => 'templateUpdated',
        'sort'   => 'templateUpdated',
        'format' => ['type' => 'date', 'format' => PERCH_DATE_SHORT . ' ' . PERCH_TIME_SHORT],
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Preview'),
        'value' => function ($Template, $HTML) use ($API) {
            $url = $API->app_nav() . '/templates/view/?id=' . $Template->id();
            return '<a class="button button-simple" href="' . $HTML->encode($url) . '">' . $HTML->encode('View') . '</a>';
        },
    ]);

    echo $Listing->render($templates);
} else {
    echo $HTML->warning_message($Lang->get('No mail templates have been created yet.'));
}
