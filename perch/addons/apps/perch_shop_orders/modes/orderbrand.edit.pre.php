<?php  echo "pre";
  /*  $Orders     = new PerchShop_Orders($API);
    $Currencies = new PerchShop_Currencies($API);
    $Customers  = new PerchShop_Customers($API);
$Addresses = new PerchShop_Addresses($API);
$OrderItems = new PerchShop_OrderItems($API);*/
			//$BillingAddress   = $Addresses->get_address($Customer, 'default');
echo "pre22";
   /* $customer_opts = [];
    $customer_list = $Customers->all();
    if (PerchUtil::count($customer_list)) {
        foreach($customer_list as $Customer) {
            $customer_opts[] = [
                'value' => $Customer->id(),
                'label' => $Customer->customerFirstName().' '.$Customer->customerLastName().' ('.$Customer->customerEmail().')',
            ];
        }
    }

    $edit_mode = false;
    $Order     = false;
    $shop_id   = false;
    $message   = false;
    $details   = false;

    if (PerchUtil::get('id')) {
        if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
            PerchUtil::redirect($API->app_path());
        }

        $shop_id = PerchUtil::get('id');
        $Order   = $Orders->find($shop_id);
        $edit_mode = true;
    } else {
        if (!$CurrentUser->has_priv('perch_shop.orders.create')) {
            PerchUtil::redirect($API->app_path());
        }
    }

    $Template = $API->get('Template');
    $Template->set('shop/orders/admin_order.html', 'shop');
    $tags = $Template->find_all_tags_and_repeaters();

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

    $Form->set_required_fields_from_template($Template, $details);

    if ($Form->submitted()) {
        $data = $Form->get_posted_content($Template, $Orders, $Order);

        $postvars = ['customerID'];
        $more = $Form->receive($postvars);

        if (isset($data['customerID']) && $data['customerID'] !== '') {
            $data['customerID'] = (int)$data['customerID'];
    $Customer = $Customers->find( $data['customerID']);
//echo $Customer->id();

            		if ($Customer) {
            		//$ShippingAddress  = $this->get_address($Customer, 'shipping');
            			$ShippingAddress = $Addresses->find_for_customer($Customer->id(), 'shipping');
            			//echo "ShippingAddress1";	print_r($ShippingAddress);
            				if (!$ShippingAddress) {
                        			PerchUtil::debug("no address");


                        				$ShippingAddress = $Addresses->create_from_default($Customer->id(), 'shipping');


                        		}

            			$BillingAddress=$ShippingAddress;

            		}

        }

        if (!$Order) {
            $Currency = $Currencies->get_default();
            if ($Currency) {
                $data['currencyID'] = $Currency->id();
            }
                $properties = PerchUtil::json_safe_decode($data["orderDynamicFields"], true);
//print_r($properties);
            $orderdata = [
            	'orderStatus'               => $properties['status'],
            	'orderGateway'              =>  $properties['gateway'],
            	'orderTotal'                => $properties['total'],
            	'currencyID'                => $data['currencyID'],
            	'orderItemsSubtotal'        => $properties['total'],
            	'orderItemsTax'             => $properties['total'],
            	//'orderItemsTotal'           => $data['total_items'] + $data['total_items_tax'],
            	//'orderShippingSubtotal'     => $data['shipping_without_tax'],
            	//'orderShippingDiscounts'    => $data['total_shipping_discount'],
            	//'orderShippingTax'          => $cart_data['shipping_tax'],
            	//'orderShippingTaxDiscounts' => $cart_data['total_shipping_tax_discount'],
            	//'orderShippingTotal'        => $cart_data['shipping_with_tax'],
            	//'orderDiscountsTotal'       => $cart_data['total_discounts'],
            	//'orderTaxDiscountsTotal'    => $cart_data['total_tax_discount'],
            //	'orderSubtotal'             => $cart_data['total_items_with_shipping'] - $cart_data['total_discounts'],
            //	'orderTaxTotal'             => $cart_data['total_tax'],
            	'orderItemsRefunded'        => 0,
            	'orderTaxRefunded'          => 0,
            	'orderShippingRefunded'     => 0,
            	'orderTotalRefunded'        => 0,
            	'orderTaxID'                => ($Customer->customerTaxIDStatus()=='valid' ? $Customer->customerTaxID() : null),
            	//'orderShippingWeight'       => $cart_data['shipping_weight'],
            	'orderCreated'              => gmdate('Y-m-d H:i:s'),
            	'orderPricing'              => $properties['total'],
            	'orderDynamicFields'        => $data["orderDynamicFields"],
            	'customerID'                => $Customer->id(),
            	//'shippingID'                => $cart_data['shipping_id'],
            	//'orderShippingTaxRate'      => $cart_data['shipping_tax_rate'],
            	'orderBillingAddress'       => ($BillingAddress!=false ?$BillingAddress->id():null),
            	'orderShippingAddress'      => ($ShippingAddress!=false ?$ShippingAddress->id() :null),
            ];

            //print_r($data);
            $Order = $Orders->create($orderdata);
            if ($Order) {
                // ensure customer is persisted on creation
               /* if (isset($data['customerID'])) {
                    $Order->update(['customerID' => $data['customerID']]);
                }*/
                /*items
                $itemsdata = [
                					'itemType'        => 'product',
                					'orderID'         => $Order->id(),
                					'productID'       => $item['id'],
                					'itemPrice'       => $item['price_without_tax'],
                					'itemTax'         => $item['tax'],
                					'itemTotal'       => $item['price_with_tax'],
                					'itemQty'         => $item['qty'],
                					'itemTaxRate'     => $item['tax_rate'],
                					'itemDiscount'    => $item['discount'],
                					'itemTaxDiscount' => $item['tax_discount'],
                				];
                				$OrderItems->create($itemsdata);*/
                				/*shipping item$data = [
                                               				'itemType'        => 'shipping',
                                               				'orderID'         => $orderID,
                                               				'shippingID'      => $cart_data['shipping_id'],
                                               				'itemPrice'       => $cart_data['shipping_without_tax'],
                                               				'itemTax'         => $cart_data['shipping_tax'],
                                               				'itemTotal'       => $cart_data['shipping_with_tax'],
                                               				'itemQty'         => 1,
                                               				'itemTaxRate'     => $cart_data['shipping_tax_rate'],
                                               				'itemDiscount'    => $cart_data['total_shipping_discount'],
                                               				'itemTaxDiscount' => $cart_data['total_shipping_tax_discount'],
                                               			];
                                               			$this->create($data);*/
               /* $Order->assign_invoice_number();
                $Order->index($Template);
              //  PerchUtil::redirect($Perch->get_page().'?id='.$Order->id().'&created=1');
            }
        } else {
            $Order->update($data);
            $Order->index($Template);
        }

        if (is_object($Order)) {
            $message = $HTML->success_message('Your order has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path().'">', '</a>');
        } else {
            $message = $HTML->failure_message('Sorry, that update was not successful.');
        }
    }

    if (PerchUtil::get('created') && !$message) {
        $message = $HTML->success_message('Your order has been successfully created. Return to %s listing%s', '<a href="'.$API->app_path().'">', '</a>');
    }

    if (is_object($Order)) {
        $details = $Order->to_array();
    }
