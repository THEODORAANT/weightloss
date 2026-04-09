<?php

echo $HTML->title_panel([
        'heading' => $Lang->get('Listing appointments'),
    ], $CurrentUser);

if (isset($message)) echo $message;

$Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);

$Smartbar->add_item([
    'active' => $filter=='future',
    'title' => $Lang->get('Future'),
    'link'  => $API->app_nav().'?by=future',
]);

$Smartbar->add_item([
    'active' => $filter=='past',
    'title' => $Lang->get('Past'),
    'link'  => $API->app_nav().'?by=past',
]);

echo $Smartbar->render();

if (PerchUtil::count($appointments)) {
    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);

    $Listing->add_col([
        'title'     => 'ID',
        'value'     => 'appointmentID',
        'sort'      => 'appointmentID',
        'edit_link' => 'edit',
    ]);

    $Listing->add_col([
        'title'     => 'Member ID',
        'value'     => 'memberID',
        'sort'      => 'memberID',
    ]);

    $Listing->add_col([
        'title'     => 'Product',
        'value'     => 'productName',
        'sort'      => 'productName',
    ]);

    $Listing->add_col([
        'title'     => 'Price',
        'value'     => 'productPrice',
        'sort'      => 'productPrice',
    ]);

    $Listing->add_col([
        'title'     => 'Date',
        'value'     => 'appointmentDate',
        'sort'      => 'appointmentDate',
    ]);

    $Listing->add_col([
        'title'     => 'Time',
        'value'     => 'slotLabel',
        'sort'      => 'slotLabel',
    ]);

    $Listing->add_col([
        'title'     => 'Goal',
        'value'     => 'goal',
    ]);

    $Listing->add_col([
        'title'     => 'Medical',
        'value'     => 'medical',
    ]);

    $Listing->add_col([
        'title'     => 'Notes',
        'value'     => 'notes',
    ]);

    $Listing->add_col([
        'title'     => 'Confirmed',
        'value'     => 'appointmentConfirmed',
    ]);

    $Listing->add_col([
        'title'     => 'Confirmed At',
        'value'     => 'confirmedAt',
    ]);

    $Listing->add_col([
        'title'     => 'Created At',
        'value'     => 'createdAt',
        'sort'      => 'createdAt',
    ]);

    $Listing->add_delete_action([
        'priv'   => 'perch_appointments.delete',
        'inline' => true,
        'path'   => 'delete',
    ]);

    echo $Listing->render($appointments);
} else {
    echo $HTML->warning_message('No appointments found.');
}
