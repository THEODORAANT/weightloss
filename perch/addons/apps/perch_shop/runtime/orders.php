<?php

	function perch_shop_order_successful()
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$Order = $ShopRuntime->get_active_order();

		if ($Order) {
			return $Order->is_paid();
		}

		return false;
	}

	function perch_shop_active_order_status()
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$ActiveOrder = $ShopRuntime->get_active_order();

		if ($ActiveOrder) {
			return strtolower((string)$ActiveOrder->orderStatus());
		}

		return null;
	}

	function perch_shop_active_order_has_status($statuses)
	{
		$status = perch_shop_active_order_status();

		if ($status === null) {
			return false;
		}

		if (!is_array($statuses)) {
			$statuses = [$statuses];
		}

		foreach ($statuses as $expected_status) {
			if ($status === strtolower((string)$expected_status)) {
				return true;
			}
		}

		return false;
	}

	function perch_shop_successful_order_id()
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$Order = $ShopRuntime->get_active_order();

		if ($Order) {
			return $Order->id();
		}

		return false;
	}

	function perch_shop_orders($opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' => 'shop/orders/list.html',
				'skip-template' => false,
			], $opts);

		if ($opts['skip-template']) $return = true;
        if (perch_member_logged_in() || isset($opts["api"])) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_orders($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}

	function perch_shop_order($orderID, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' => 'shop/orders/order.html',
				'skip-template' => false,
				'orderID'    => $orderID,
			], $opts);


		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_order($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}
function perch_shop_track_order($orderID, $opts=array(), $return=true)
	{


$opts = PerchUtil::extend([
				'template' 		=> 'shop/orders/track.html',
				'skip-template' => false,
				'orderID'		=> $orderID,
			], $opts);

		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->track_order($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}
	function perch_shop_order_items($orderID, $opts=array(), $return=false)
	{
		$opts = PerchUtil::extend([
				'template' 		=> 'shop/orders/item.html',
				'skip-template' => false,
				'orderID'		=> $orderID,
			], $opts);

		if ($opts['skip-template']) $return = true;

		if (perch_member_logged_in()) {
			$ShopRuntime = PerchShop_Runtime::fetch();
			$r = $ShopRuntime->get_order_items($opts);
		}else{
			$r = '';
			if ($opts['skip-template']) $r = [];
		}

		if ($return) return $r;
		echo $r;
		PerchUtil::flush_output();
	}
        function perch_shop_last_pen_details()
        {
                $details = [
                        'brand' => null,
                        'dose'  => null,
                ];

                if (!perch_member_logged_in()) {
                        return $details;
                }

                $recent_orders = perch_shop_orders([
                        'sort' => 'orderCreated',
                        'sort-order' => 'DESC',
                        'count' => 1,
                        'skip-template' => true,
                ], true);

                if (!is_array($recent_orders) || empty($recent_orders)) {
                        return $details;
                }

                $last_order = $recent_orders[0];
                $last_order_id = $last_order['orderID'] ?? null;

                if (!$last_order_id) {
                        return $details;
                }

                $order_items = perch_shop_order_items($last_order_id, [
                        'skip-template' => true,
                ], true);

                if (!is_array($order_items)) {
                        return $details;
                }

                $perch_shop_api   = null;
                $products_factory = null;

                foreach ($order_items as $item) {
                        if (($item['itemType'] ?? '') !== 'product') {
                                continue;
                        }

                        if (!empty($item['parentID'])) {
                                if ($perch_shop_api === null) {
                                        $perch_shop_api   = new PerchAPI(1.0, 'perch_shop');
                                        $products_factory = new PerchShop_Products($perch_shop_api);
                                }

                                $ParentProduct = $products_factory ? $products_factory->find((int)$item['parentID']) : null;

                                if ($ParentProduct) {
                                        $details['brand'] = $ParentProduct->productTitle();
                                }
                        }

                        if ($details['brand'] === null) {
                                $details['brand'] = $item['productTitle'] ?? $item['title'] ?? null;
                        }

                        $details['dose'] = $item['productVariantDesc'] ?? $item['variant_desc'] ?? null;

                        if ($details['dose'] === null && isset($item['title'])) {
                                $title = $item['title'];
                                if ($details['brand'] === null || strcasecmp($title, $details['brand']) !== 0) {
                                        $details['dose'] = $title;
                                }
                        }

                        break;
                }

                return $details;
        }
