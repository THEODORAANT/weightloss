<?php 

echo $HTML->title_panel([
    'heading' => isset($list_heading) ? $list_heading : $Lang->get('Listing all orders'),
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
            'edit_link' => '/perch/addons/apps/perch_shop_orders/order',
            'priv'      => 'perch_shop.orders.edit',
        ]);
     $Listing->add_col([
                'title'     => 'Order Product',
                'value'     => function($Item) use ($OrderItems){
                  $items = $OrderItems->get_for_admin($Item->id());

                    if (!isset($items[0]) ) {
                        return "";
                    }
                     $product = $items[0]->sku();
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

    if (!empty($show_question_link)) {
        $Listing->add_col([
            'title'     => 'Questions',
            'value'     => function($Item) use ($API, $Lang, $Customers) {
                $order_id = (int)$Item->id();
                if ($order_id > 0) {
                    $url = $API->app_nav().'/order/questions/?id='.$order_id;
                    $label = $Lang->get('Questions');
                    return '<a class="button button-simple" target="_blank" rel="noopener" href="'.$url.'">'.$label.'</a>';
                }

                $customer_id = (int)$Item->customerID();
                if ($customer_id > 0) {
                    $Customer = $Customers->find($customer_id);
                    if ($Customer && $Customer->memberID()) {
                        $url = '/perch/addons/apps/perch_members/edit/?id='.$Customer->memberID();
                        $label = $Lang->get('Member profile');
                        return '<a class="button button-simple" target="_blank" rel="noopener" href="'.$url.'">'.$label.'</a>';
                    }
                }

                return '';
            },
        ]);
    }
    
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

