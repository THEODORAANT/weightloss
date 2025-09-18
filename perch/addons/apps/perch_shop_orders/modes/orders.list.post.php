<?php 

if (!isset($list_heading)) {
    $list_heading = $Lang->get('Listing all orders');
}

echo $HTML->title_panel([
    'heading' => $list_heading,
    'button'  => [
        'text' => $Lang->get('Add order'),
        'link' => $API->app_nav().'/order/edit/',
        'icon' => 'core/plus',
        'priv' => 'perch_shop.orders.create',
    ],
], $CurrentUser);


	/* ----------------------------------------- SMART BAR ----------------------------------------- */

    include('_orders_smartbar.php');
       
	/* ----------------------------------------- /SMART BAR ----------------------------------------- */
 echo $Form->form_start(false, "ordersclass");
        echo $Form->fields_from_template($Template, $details, array(), false);

        echo $Form->submit_field('btnSubmit', 'Search', $API->app_path());

        echo $Form->form_end();


    $Listing = new PerchAdminListing($CurrentUser, $HTML, $Lang, $Paging);
    $Listing->add_col([
            'title'     => 'Order',
            'value'     => function($Item) {
                $invoice_number = $Item->orderInvoiceNumber();
                if ($invoice_number == '') {
                    return 'Order '.$Item->id();
                }
                return $invoice_number;
            },
            'sort'      => 'orderInvoiceNumber',
            'edit_link' => 'order',
            'priv'      => 'perch_shop.orders.edit',
        ]);
     $Listing->add_col([
                'title'     => 'Order Product',
                'value'     => function($Item) use ($OrderItems){
                  $items = $OrderItems->get_for_admin($Item->id());
                    $product = $items[0]->sku();
                    if ($product == '') {
                        return "";
                    }
                    return $product;
                },

            ]);
    $Listing->add_col([
            'title'     => 'Date',
            'value'     => 'orderCreated',
            'sort'      => 'orderCreated',
            'format'    => ['type'=>'date', 'format'=>PERCH_DATE_SHORT.' '.PERCH_TIME_SHORT],
        ]);
    $Listing->add_col([
            'title'     => 'Member Profile',
               'value'     => function($Customer) {
  if($Customer->memberID()!=null){
                          return '<a target="_blank" href="/perch/addons/apps/perch_members/edit/?id='.$Customer->memberID().'" >View</a>';
                          }
                          return '';
                      },
            'sort'      => 'customerName',
        ]);
         $Listing->add_col([
                    'title'     => 'Approved Documents',
                    'value'     => function($Customer) use($Tags) {
                    if($Customer->memberID()!=null){
                       /*
                   $documents = $Documents->get_for_member($Customer->memberID());
                       if (is_array($documents)) {
                      foreach($documents as $Document) {
                          if($Document->documentStatus()!="accepted"){
                          return "NO";
                          }

                      }
                       return 'YES';
                      }*/


                    $allTags=$Tags->tags_for_member($Customer->memberID());

                   /*   $tagsArray = $Tags->get_for_member($Customer->memberID());

                  foreach ($tagsArray as $tagObj) {
                      $reflection = new ReflectionClass($tagObj);
                      $property = $reflection->getProperty('details');
                      $property->setAccessible(true);
                      $details = $property->getValue($tagObj);

                      // Add tag to result array
                      if (isset($details['tag'])) {
                          $allTags[] = $details['tag'];
                      }
                  }*/

               //   print_r($allTags);

                          if (is_array($allTags)) {
                                          if(in_array('approved-docs',$allTags)){ return "YES";  }
                                         return 'NO';
                                         }
                                              return '';
                    }

        return '';
                    }

                ]);
         $Listing->add_col([
                 'title'     => 'Customer',
                 'value'     => 'customerName',
                 'sort'      => 'customerName',
             ]);


    $Listing->add_col([
            'title'     => 'Total',
            'value'     => 'orderTotal',
            'sort'      => 'orderTotal',
        ]);
    $Listing->add_col([
            'title'     => 'Status',
            'value'     => 'statusTitle',
            'sort'      => 'orderStatus',
        ]);

    $Listing->add_col([
            'title'     => 'Package',
            'value'     => function($Item) {
                $type = $Item->billing_type();
                return $type ? ucfirst($type) : 'Normal';
            },
            'sort'      => 'billing_type',
        ]);
    
 $Listing->add_col([
            'title'     => 'Send To pharmacy',
            'value'     => function($Item) use ($Orders)  {

              if( $Orders->is_order_send_to_pharmacy($Item->id())){ return "YES";  }
             return 'NO';

            }
            //,'sort'      => 'orderInvoiceNumber'
        ]);

    $Listing->add_delete_action([
            'priv'   => 'perch_shop.orders.delete',
            'inline' => true,
            'path'   => 'delete',
        ]);

    echo $Listing->render($orders);

