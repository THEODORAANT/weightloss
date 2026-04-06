<?php

	$Orders     = new PerchShop_Orders($API);
	$OrderItems = new PerchShop_OrderItems($API);
	$Currencies = new PerchShop_Currencies($API);
	$Customers  = new PerchShop_Customers($API);
	$Countries  = new PerchShop_Countries($API);
	$Addresses  = new PerchShop_Addresses($API);
	$Statuses   = new PerchShop_OrderStatuses($API);
	$Products   = new PerchShop_Products($API);
	$Variants   = new PerchShop_ProductVariants($API);
	$DB         = PerchDB::fetch();

	$Form = $API->get('Form');

	$message = false;
	$status_reason = '';
	$shipping_status_value = 'pending';
	$product_options = [];

	if (!function_exists('comms_service_request_json')) {
		require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_service.php';
	}

	if (!function_exists('normalise_shipping_status_for_pharmacy_table')) {
		function normalise_shipping_status_for_pharmacy_table($value)
		{
			$normalised = strtolower(trim((string)$value));
			switch ($normalised) {
				case 'delayed dispatch':
					return 'DELAYED_DISPATCH';
				case 'dispatched':
					return 'DISPATCHED';
				case 'delivered':
					return 'DELIVERED';
				case 'pending':
				default:
					return 'PENDING';
			}
		}
	}

	if (!function_exists('normalise_shipping_status_from_pharmacy_table')) {
		function normalise_shipping_status_from_pharmacy_table($value)
		{
			$normalised = strtoupper(trim((string)$value));
			switch ($normalised) {
				case 'DELAYED_DISPATCH':
					return 'Delayed dispatch';
				case 'DISPATCHED':
					return 'dispatched';
				case 'DELIVERED':
					return 'delivered';
				case 'PENDING':
				default:
					return 'pending';
			}
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

	if (!function_exists('build_pending_order_status_items')) {
		function build_pending_order_status_items(PerchDB_MySQL $DB, $orderID)
		{
			$rows = $DB->get_rows(
				'SELECT oi.productID, oi.itemQty, pm.pharmacy_productID '
				. 'FROM ' . PERCH_DB_PREFIX . 'shop_order_items oi '
				. 'INNER JOIN ' . PERCH_DB_PREFIX . 'products_match_pharmacy pm ON pm.productID = oi.productID '
				. 'WHERE oi.orderID = ' . $DB->pdb((int)$orderID)
				. ' AND oi.itemType = "product"'
			);

			if (!PerchUtil::count($rows)) {
				return [];
			}

			$items = [];
			foreach ($rows as $row) {
				$product_id = isset($row['pharmacy_productID']) ? trim((string)$row['pharmacy_productID']) : '';
				$qty = isset($row['itemQty']) ? (int)$row['itemQty'] : 0;

				if ($product_id === '' || $qty < 1) {
					continue;
				}

				$items[] = [
					'productId' => $product_id,
					'quantity' => $qty,
				];
			}

			return $items;
		}
	}

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');

		$Order     = $Orders->find($shop_id);


		$Currency    = $Currencies->find($Order->currencyID());
		$Customer    = $Customers->find($Order->customerID());
		$BillingAdr  = $Addresses->find($Order->orderBillingAddress());
		$ShippingAdr = $Addresses->find($Order->orderShippingAddress());
		$current_shipping_status = $DB->get_value(
			'SELECT status FROM '.PERCH_DB_PREFIX.'orders_match_pharmacy WHERE orderID='.$DB->pdb((int)$Order->id()).' LIMIT 1'
		);
		if ($current_shipping_status !== null && $current_shipping_status !== false && trim((string)$current_shipping_status) !== '') {
			$shipping_status_value = normalise_shipping_status_from_pharmacy_table($current_shipping_status);
		}

		if ($Form->submitted()) {

			$data = $Form->receive(['status', 'shipping_status']);
			$status_reason = trim((string)PerchUtil::post('status_reason'));

			if ($Order && isset($data['status']) && $data['status'] !== '') {
				$newStatus = strtolower(trim((string)$data['status']));
				$requires_status_reason = in_array($newStatus, ['cancelled', 'refund', 'refunded'], true);

				if ($requires_status_reason && $status_reason === '') {
					$message = 'Please provide a reason when cancelling or refunding this order.';
				} else {
					$Order->set_status($data['status']);

					if ($requires_status_reason) {
					$orderStatusData = [
						'status' => 'CANCELLED',
						'reason' => $status_reason,
					];

					$statusUpdateResult = comms_service_request_json('POST', '/v1/perch/orders/'.$Order->id().'/status', $orderStatusData);
					if (!is_array($statusUpdateResult)) {
					  $update_query ="UPDATE ".PERCH_DB_PREFIX."orders_match_pharmacy SET status='CANCELLED',pharmacy_message=".$status_reason." WHERE orderID = ".$Order->id();
                       $DB->execute($update_query);
						$message = 'Order updated, but comms status update to CANCELLED failed.';
					}
					}
				}
			}

			if ($Order && isset($data['shipping_status']) && trim((string)$data['shipping_status']) !== '') {
				$shipping_status_value = trim((string)$data['shipping_status']);
				$pharmacy_shipping_status = normalise_shipping_status_for_pharmacy_table($shipping_status_value);
				$DB->execute(
					'UPDATE '.PERCH_DB_PREFIX.'orders_match_pharmacy
					 SET status='.$DB->pdb($pharmacy_shipping_status).'
					 WHERE orderID='.$DB->pdb((int)$Order->id())
				);
			}
		}


		$details = $Order->to_array();

	    $items = $OrderItems->get_for_admin($shop_id);

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

	    $variant_list = $Variants->all();
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


	    if ( isset($_POST['order_items']) && is_array($_POST['order_items'])) {
	    	$updated_order_items = false;
	    	$Settings = PerchSettings::fetch();
	    	$pricing = $Order->orderPricing() ? $Order->orderPricing() : 'standard';
	    	$tax_mode = $Settings->get('perch_shop_price_tax_mode')->val();
	    	if ($pricing === 'trade') {
	    		$tax_mode = $Settings->get('perch_shop_trade_price_tax_mode')->val();
	    	}

	    	$TaxLocations = new PerchShop_TaxLocations($API);
	    	$home_location_id = $DB->get_value('SELECT locationID FROM '.PERCH_DB_PREFIX.'shop_tax_locations WHERE locationIsHome=1 LIMIT 1');
	    	$HomeTaxLocation = $home_location_id ? $TaxLocations->find((int)$home_location_id) : null;
	    	$CustomerTaxLocation = null;
	    	if ($ShippingAdr) {
	    		$CustomerTaxLocation = $TaxLocations->find_matching($ShippingAdr->countryID(), $ShippingAdr->regionID());
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

	    		$current_status = strtolower(trim((string)$Order->orderStatus()));
	    			$message = 'Product updated, but comms status update failed status'.$current_status;
	    		if ($current_status === 'paid' ) {

	    			$pending_items = build_pending_order_status_items($DB, (int)$Order->id());
	    			//print_r($pending_items);
	    			if (PerchUtil::count($pending_items)) {
	    				$orderStatusData = [
	    					'status' => 'PENDING',
	    					'items' => $pending_items,
	    				];

	    				$statusUpdateResult = comms_service_request_json('POST', '/v1/perch/orders/'.$Order->id().'/status', $orderStatusData);
	    				if (!is_array($statusUpdateResult)) {
	    					$message = 'Product updated, but comms status update to PENDING failed.';
	    				}
	    			}
	    		}

	    		$items = $OrderItems->get_for_admin($shop_id);
	    	    if (!$message) {
	    	    	$message = $HTML->success_message('Product have been updated.');
	    	    }
	    	    //echo $message;
	    		//PerchUtil::redirect($API->app_path());
	    	}
	    }
	   

	}else{
	    PerchUtil::redirect($API->app_path());
	}
