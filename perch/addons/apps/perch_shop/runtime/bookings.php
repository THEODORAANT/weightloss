<?php
	function perch_shop_booking($orderID=0, $opts=array(), $return=false)

	{

	$ShopRuntime = PerchShop_Runtime::fetch();
    		$Order = $ShopRuntime->get_active_order();

    		if ($Order) {
    			$orderid= $Order->id();
    		}

		return $ShopRuntime->order_booking($orderid,$opts);
	}

?>
