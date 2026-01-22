<?php
    $Orders     = new PerchShop_Orders($API);
    $Currencies = new PerchShop_Currencies($API);
    $Customers  = new PerchShop_Customers($API);
    $Addresses = new PerchShop_Addresses($API);
    $OrderItems = new PerchShop_OrderItems($API);
    $Products = new PerchShop_Products($API);
    $ProductVariants = new PerchShop_ProductVariants($API);
    $DB = PerchDB::fetch();
			//$BillingAddress   = $Addresses->get_address($Customer, 'default');

    $customer_opts = [];
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
    $order_items = [];
    $product_options = [];
    $Customer = null;
    $BillingAddress = null;
    $ShippingAddress = null;

    if (PerchUtil::get('id')) {
        if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
            PerchUtil::redirect($API->app_path());
        }

        $shop_id = PerchUtil::get('id');
        $Order   = $Orders->find($shop_id);
        $edit_mode = true;
        if ($Order) {
            $Customer = $Customers->find($Order->customerID());
            $BillingAddress = $Addresses->find($Order->orderBillingAddress());
            $ShippingAddress = $Addresses->find($Order->orderShippingAddress());
            $order_items = $OrderItems->get_for_admin($Order->id());
        }
    } else {
        if (!$CurrentUser->has_priv('perch_shop.orders.create')) {
            PerchUtil::redirect($API->app_path());
        }
    }

    $product_list = $Products->get_for_admin_listing();
    if (PerchUtil::count($product_list)) {
        foreach ($product_list as $Product) {
            $label = $Product->title();
            $sku = $Product->sku();
            if ($sku) {
                $label .= ' ('.$sku.')';
            }
            $product_options[(int)$Product->id()] = $label;
        }
    }

    $variant_list = $ProductVariants->all();
    if (PerchUtil::count($variant_list)) {
        foreach ($variant_list as $Variant) {
            $label = $Variant->title();
            if ($Variant->is_variant()) {
                $Parent = $Variant->get_parent();
                if ($Parent) {
                    $label = $Parent->title();
                }
                $variant_desc = $Variant->productVariantDesc();
                if ($variant_desc) {
                    $label .= ' - '.$variant_desc;
                }
            }
            $sku = $Variant->sku();
            if ($sku) {
                $label .= ' ('.$sku.')';
            }
            $product_options[(int)$Variant->id()] = $label;
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
                $Order->assign_invoice_number();
                $Order->index($Template);
              //  PerchUtil::redirect($Perch->get_page().'?id='.$Order->id().'&created=1');
            }
        } else {
            $Order->update($data);
            $Order->index($Template);
        }

        if (is_object($Order) && isset($_POST['order_items']) && is_array($_POST['order_items'])) {
            $updated_order_items = false;
            $Settings = PerchSettings::fetch();
            $Currency = $Currencies->find($Order->currencyID());
            $pricing = $Order->orderPricing() ? $Order->orderPricing() : 'standard';
            $tax_mode = $Settings->get('perch_shop_price_tax_mode')->val();
            if ($pricing === 'trade') {
                $tax_mode = $Settings->get('perch_shop_trade_price_tax_mode')->val();
            }

            $TaxLocations = new PerchShop_TaxLocations($API);
            $home_location_id = $DB->get_value('SELECT locationID FROM '.PERCH_DB_PREFIX.'shop_tax_locations WHERE locationIsHome=1 LIMIT 1');
            $HomeTaxLocation = $home_location_id ? $TaxLocations->find((int)$home_location_id) : null;
            $CustomerTaxLocation = null;
            if ($ShippingAddress) {
                $CustomerTaxLocation = $TaxLocations->find_matching($ShippingAddress->countryID(), $ShippingAddress->regionID());
            }
            if (!$CustomerTaxLocation && $HomeTaxLocation) {
                $CustomerTaxLocation = $HomeTaxLocation;
            }
            if (!$HomeTaxLocation && $CustomerTaxLocation) {
                $HomeTaxLocation = $CustomerTaxLocation;
            }

            foreach ($_POST['order_items'] as $item_id => $item_data) {
                $item_id = (int)$item_id;
                if ($item_id < 1) {
                    continue;
                }

                $Item = $OrderItems->find($item_id);
                if (!$Item || (int)$Item->orderID() !== (int)$Order->id()) {
                    continue;
                }

                if ($Item->itemType() !== 'product') {
                    continue;
                }

                $new_product_id = isset($item_data['product_id']) ? (int)$item_data['product_id'] : 0;
                $qty = isset($item_data['qty']) ? (int)$item_data['qty'] : (int)$Item->itemQty();
                if ($qty < 1) {
                    $qty = 1;
                }

                if ($new_product_id < 1) {
                    continue;
                }

                $Product = $Products->find($new_product_id);
                if (!$Product || !$Currency) {
                    continue;
                }

                $Totaliser = new PerchShop_CartTotaliser();
                $customer_pays_tax = true;
                if ($Customer && $CustomerTaxLocation && $HomeTaxLocation) {
                    $customer_pays_tax = $Customer->pays_tax($CustomerTaxLocation, $HomeTaxLocation);
                }

                $price_data = null;
                if ($CustomerTaxLocation && $HomeTaxLocation) {
                    $price_data = $Product->get_prices($qty, $pricing, $tax_mode, $CustomerTaxLocation, $HomeTaxLocation, $Currency, $Totaliser, $customer_pays_tax);
                }

                if (!is_array($price_data)) {
                    $price_data = [
                        'price_without_tax' => normalise_decimal($Item->itemPrice()) ?? 0.0,
                        'price_with_tax' => normalise_decimal($Item->itemTotal()) ?? 0.0,
                        'tax' => normalise_decimal($Item->itemTax()) ?? 0.0,
                        'tax_rate' => $Item->itemTaxRate(),
                    ];
                }

                $payload = [
                    'productID'   => $Product->id(),
                    'itemPrice'   => format_decimal((float)$price_data['price_without_tax']),
                    'itemTotal'   => format_decimal((float)$price_data['price_with_tax']),
                    'itemTax'     => format_decimal((float)$price_data['tax']),
                    'itemQty'     => $qty,
                    'itemTaxRate' => $price_data['tax_rate'],
                ];

                $result = $DB->update(
                    PERCH_DB_PREFIX.'shop_order_items',
                    $payload,
                    'itemID',
                    $item_id
                );

                if ($result) {
                    $updated_order_items = true;
                }
            }

            if ($updated_order_items) {
                $order_update = recalculate_order_totals($DB, (int)$Order->id());
                $order_update['orderUpdated'] = date('Y-m-d H:i:s');
                $Order->update($order_update);
            }
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

    if (!function_exists('normalise_decimal')) {
        function normalise_decimal($value)
        {
            if (is_float($value) || is_int($value)) {
                return (float) $value;
            }

            if (is_string($value)) {
                $filtered = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
                if ($filtered === '' || !is_numeric($filtered)) {
                    return null;
                }

                return (float) $filtered;
            }

            return null;
        }
    }

    if (!function_exists('format_decimal')) {
        function format_decimal($value)
        {
            $value = (float)$value;
            if (abs($value) < 0.0005) {
                $value = 0.0;
            }

            return number_format($value, 2, '.', '');
        }
    }

    if (!function_exists('recalculate_order_totals')) {
        function recalculate_order_totals(PerchDB_MySQL $DB, $orderID, $override = null)
        {
            $rows = $DB->get_rows(
                'SELECT itemID, itemType, itemPrice, itemTotal, itemTax, itemDiscount, itemTaxDiscount, itemQty '
                . 'FROM ' . PERCH_DB_PREFIX . 'shop_order_items '
                . 'WHERE orderID = ' . $DB->pdb((int)$orderID)
            );

            if (!$rows) {
                return [
                    'orderItemsSubtotal'        => format_decimal(0.0),
                    'orderItemsTax'             => format_decimal(0.0),
                    'orderItemsTotal'           => format_decimal(0.0),
                    'orderShippingSubtotal'     => format_decimal(0.0),
                    'orderShippingDiscounts'    => format_decimal(0.0),
                    'orderShippingTax'          => format_decimal(0.0),
                    'orderShippingTaxDiscounts' => format_decimal(0.0),
                    'orderShippingTotal'        => format_decimal(0.0),
                    'orderDiscountsTotal'       => format_decimal(0.0),
                    'orderTaxDiscountsTotal'    => format_decimal(0.0),
                    'orderSubtotal'             => format_decimal(0.0),
                    'orderTaxTotal'             => format_decimal(0.0),
                    'orderTotal'                => format_decimal(0.0),
                ];
            }

            if ($override !== null) {
                $replaced = false;
                foreach ($rows as &$row) {
                    if ((int) $row['itemID'] === (int) $override['itemID']) {
                        $row = array_merge($row, $override);
                        $replaced = true;
                        break;
                    }
                }
                unset($row);

                if (!$replaced) {
                    $rows[] = $override;
                }
            }

            $itemsSubtotal = 0.0;
            $itemsTax = 0.0;
            $itemDiscounts = 0.0;
            $itemTaxDiscounts = 0.0;

            $shippingSubtotal = 0.0;
            $shippingTax = 0.0;
            $shippingDiscounts = 0.0;
            $shippingTaxDiscounts = 0.0;

            foreach ($rows as $row) {
                $type = $row['itemType'];
                $price = normalise_decimal($row['itemPrice']) ?? 0.0;
                $total = normalise_decimal($row['itemTotal']) ?? 0.0;
                $tax = normalise_decimal($row['itemTax']) ?? ($total - $price);
                $discount = normalise_decimal($row['itemDiscount']) ?? 0.0;
                $taxDiscount = normalise_decimal($row['itemTaxDiscount']) ?? 0.0;
                $qty = isset($row['itemQty']) ? (int) $row['itemQty'] : 1;

                if ($type === 'shipping') {
                    $shippingSubtotal += $price * $qty;
                    $shippingTax += $tax * $qty;
                    $shippingDiscounts += $discount;
                    $shippingTaxDiscounts += $taxDiscount;
                } elseif ($type === 'product') {
                    $itemsSubtotal += $price * $qty;
                    $itemsTax += ($total - $price) * $qty;
                    $itemDiscounts += $discount;
                    $itemTaxDiscounts += $taxDiscount;
                } elseif ($type === 'discount') {
                    $itemDiscounts += $discount + ($price * $qty);
                    $itemTaxDiscounts += $taxDiscount + ($tax * $qty);
                }
            }

            $orderDiscountsTotal = $itemDiscounts + $shippingDiscounts;
            $orderTaxDiscountsTotal = $itemTaxDiscounts + $shippingTaxDiscounts;

            $orderItemsSubtotal = $itemsSubtotal;
            $orderItemsTax = $itemsTax - $itemTaxDiscounts;
            $orderItemsTotal = $orderItemsSubtotal + $orderItemsTax;

            $orderShippingSubtotal = $shippingSubtotal;
            $orderShippingTax = $shippingTax - $shippingTaxDiscounts;
            $orderShippingTotal = $orderShippingSubtotal + $orderShippingTax;

            $orderSubtotal = ($orderItemsSubtotal + $orderShippingSubtotal) - $orderDiscountsTotal;
            $orderTaxTotal = $orderItemsTax + $orderShippingTax;
            $orderTotal = $orderSubtotal + $orderTaxTotal;

            return [
                'orderItemsSubtotal'        => format_decimal($orderItemsSubtotal),
                'orderItemsTax'             => format_decimal($orderItemsTax),
                'orderItemsTotal'           => format_decimal($orderItemsTotal),
                'orderShippingSubtotal'     => format_decimal($orderShippingSubtotal),
                'orderShippingDiscounts'    => format_decimal($shippingDiscounts),
                'orderShippingTax'          => format_decimal($orderShippingTax),
                'orderShippingTaxDiscounts' => format_decimal($shippingTaxDiscounts),
                'orderShippingTotal'        => format_decimal($orderShippingTotal),
                'orderDiscountsTotal'       => format_decimal($orderDiscountsTotal),
                'orderTaxDiscountsTotal'    => format_decimal($orderTaxDiscountsTotal),
                'orderSubtotal'             => format_decimal($orderSubtotal),
                'orderTaxTotal'             => format_decimal($orderTaxTotal),
                'orderTotal'                => format_decimal($orderTotal),
            ];
        }
    }
