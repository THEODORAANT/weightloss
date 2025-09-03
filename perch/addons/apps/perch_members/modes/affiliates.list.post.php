<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing affiliates'),
        'button'  => [
            'text' => $Lang->get('Add affiliate'),
            'link' => $API->app_nav().'/edit/',
            'icon' => 'core/plus',
            ],
    ], $CurrentUser);

        echo $HTML->title_panel([
            'button'  => [
             'text' => 'Download CSV',
            'onclick'=>'htmlToCSV()',
                'icon' => 'ext/o-cloud-download',
                ],
        ], $CurrentUser);


    if (isset($message)) echo $message;


    /* ----------------------------------------- SMART BAR ----------------------------------------- */

        $Smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);






         /* $Smartbar->add_item([
                    'id'      => 'cf',
                    'title'   => 'By Email',
                    'active'  => PerchRequest::get('email'),
                    'type'    => 'search',
                    'arg'     => 'email'
            ]);*/

        echo $Smartbar->render();


        echo $Alert->output();

        //echo "details****";
        //print_r($details);
      /*  echo $Form->form_start(false, "membersclass");
        echo $Form->fields_from_template($Template, $details, array(), false);

        echo $Form->submit_field('btnSubmit', 'Search', $API->app_path());

        echo $Form->form_end();*/

    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    if (PerchUtil::count($members)) {



        $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
        $first = true;
        $i = 0;
        $Listing->add_col([
                         'title'     => $Lang->get('ID'),
                         'value'     => 'id',
                         'sort'      => 'id',
                         'edit_link' => 'affiliate',
                     ]);
           $Listing->add_col([
                         'title'     => $Lang->get('Affiliate ID'),
                         'value'     => 'affid',
                         'sort'      => 'affid',

                     ]);

     $Listing->add_col([
                         'title'     => $Lang->get('Credit'),
                         'value'     => 'credit',
                         'sort'      => 'credit',

                     ]);
    $Listing->add_col([
                         'title'     => $Lang->get('Member ID'),
                         'value'     => 'member_id',
                         'sort'      => 'member_id',

                     ]);
     $Listing->add_col([
                         'title'     => $Lang->get('Program Type'),
                         'value'     => 'program_type',

                     ]);
        echo $Listing->render($members);


    } // if pages
