<?php

echo $HTML->title_panel([
    'heading' => $Lang->get('Mail contacts'),
    'button'  => [
        'text' => $Lang->get('Add contact'),
        'link' => $API->app_nav() . '/contacts/edit/',
        'icon' => 'core/plus',
        'priv' => 'perch_mailer.contacts.manage',
    ],
], $CurrentUser);

if (isset($message)) {
    echo $message;
}

$active_section = 'contacts';
include(__DIR__ . '/../_subnav.php');

if (PerchUtil::count($contacts)) {
    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
        'title'     => $Lang->get('Name'),
        'value'     => function ($Contact, $HTML) {
            $name = trim($Contact->contactFirstName() . ' ' . $Contact->contactLastName());
            return $HTML->encode($name ?: $Contact->contactEmail());
        },
        'sort'      => 'contactLastName',
        'edit_link' => 'contacts/edit',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Email'),
        'value' => 'contactEmail',
        'sort'  => 'contactEmail',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Member ID'),
        'value' => function ($Contact, $HTML) {
            $memberID = $Contact->memberID();
            return $memberID ? $HTML->encode($memberID) : '&ndash;';
        },
        'sort' => 'memberID',
    ]);

    $Listing->add_col([
        'title' => $Lang->get('Status'),
        'value' => 'contactStatus',
        'sort'  => 'contactStatus',
    ]);

    $Listing->add_col([
        'title'  => $Lang->get('Updated'),
        'value'  => 'contactUpdated',
        'sort'   => 'contactUpdated',
        'format' => ['type' => 'date', 'format' => PERCH_DATE_SHORT . ' ' . PERCH_TIME_SHORT],
    ]);

    echo $Listing->render($contacts);
} else {
    echo $HTML->warning_message($Lang->get('No mail contacts have been added yet.'));
}
