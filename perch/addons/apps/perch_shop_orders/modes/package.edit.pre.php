<?php

	$Packages   = new PerchShop_Packages($API);
	$PackageItems = new PerchShop_PackageItems($API);
	$Customers  = new PerchShop_Customers($API);


	$Form = $API->get('Form');

	$message = false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');

		$Package     = $Packages->find($shop_id);


		$Customer    = $Customers->find($Package->customerID());

		/*if ($Form->submitted()) {

			$data = $Form->receive(['status']);

			if ($Order) {
				$Order->set_status($data['status']);
			}
		}*/


		//$details = $Package->to_array();

	    $items = $PackageItems->get_for_admin($Package->uuid());
	   

	}else{
	    PerchUtil::redirect($API->app_path());
	}


