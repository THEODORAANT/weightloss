<?php

	function perch_shop_email($id, $secret=false)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		echo $ShopRuntime->get_email_content($id, $secret);
	}

	 function send_monthly_notification( $Customer,$message){
    	 	$ShopRuntime = PerchShop_Runtime::fetch();
         		return $ShopRuntime->send_monthly_notification($Customer, $message);
    	 }
