<?php 

    echo $HTML->title_panel([
        'heading' => $Lang->get('Listing all packages'),
        'button'  => [
            'text' => $Lang->get('Add package'),
            'link' => $API->app_nav().'/package/edit/',
            'icon' => 'core/plus',
            'priv' => 'perch_shop.orders.create',
        ],
    ], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */

    include('_orders_smartbar.php');
       
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'ID',
            'value'     =>'packageID',
            'sort'      => 'packageID',
            'edit_link' => 'edit',
            'priv'      => 'perch_shop.orders.edit',
        ]);
        $Listing->add_col([
                'title'     => 'Billing Type',
                'value'     => 'billing_type',
                'sort'      => 'billing_type',
            ]);
    $Listing->add_col([
            'title'     => 'Date',
            'value'     => 'created',
            'sort'      => 'created',
            'format'    => ['type'=>'date', 'format'=>PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
        ]);
            $Listing->add_col([
                    'title'     => 'Next Billing Date',
                    'value'     => 'nextBillingDate',
                    'sort'      => 'nextBillingDate',
                    'format'    => ['type'=>'date', 'format'=>PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
                ]);
    $Listing->add_col([
            'title'     => 'Customer',
            'value'     => 'customerName',
            'sort'      => 'customerName',
        ]);
    $Listing->add_col([
            'title'     => 'Months',
            'value'     => 'months',
            'sort'      => 'months',
        ]);
    $Listing->add_col([
            'title'     => 'Status',
            'value'     => 'paymentStatus',
            'sort'      => 'paymentStatus',
        ]);
    

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.orders.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($packages);

