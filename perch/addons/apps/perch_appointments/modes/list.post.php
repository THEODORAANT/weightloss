<?php
   
    echo $HTML->title_panel([
            'heading' => $Lang->get('Listing appointments'),
            'button'  => [
                'text' => $Lang->get('Add appointment'),
                'link' => $API->app_nav().'/edit/',
                'icon' => 'core/plus',
                'priv' => 'perch_appointments.create',
                ],
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
            'title'     => 'Title',
            'value'     => 'appointmentDateLabel',
            'sort'      => 'appointmentDateLabel',
            'edit_link' => 'edit',
        ]);

        $Listing->add_col([
                'title'     => $Lang->get('Created Date'),
                'value'     => 'appointmentDate',
                'sort'      => 'appointmentDate',
                'format'    => ['type'=>'date', 'format'=> PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
            ]);


        $Listing->add_delete_action([
                'priv'   => 'perch_appointments.delete',
                'inline' => true,
                'path'   => 'delete',
            ]);

        echo $Listing->render($appointments);

    } // if pages
    
