<?php

         function perch_shop_register_customer_from_api($memberID,$data){
          $ShopRuntime = PerchShop_Runtime::fetch();
        return $ShopRuntime->register_customer_from_api( $memberID,$data);
        }
        function perch_shop_add_to_cart_for_api($member_id,$product, $qty=1,$cart_id=0, $replace=false)
        	{         $ShopRuntime = PerchShop_Runtime::fetch();

        		 return $ShopRuntime->add_cart_api($member_id,$product, $qty,$cart_id, $replace);


        	}
	function perch_shop_checkout_for_api($memberID,$cartID,$gateway, $payment_opts)
	{

		$ShopRuntime = PerchShop_Runtime::fetch();
		return $ShopRuntime->checkout_api($memberID,$cartID,$gateway, $payment_opts);

	}
		function perch_shop_update_pharmacy_order_webhook($data)
    	{

    		//$ShopRuntime = PerchShop_Runtime::fetch();
    		return ''; //$ShopRuntime->update_pharmacy_order_webhook($data);

    	}
	function perch_shop_confirmPayment_for_api($memberID,$paymentIntentId)
	{

		$ShopRuntime = PerchShop_Runtime::fetch();
		return $ShopRuntime->confirmPayment_for_api($memberID,$paymentIntentId);

	}
function perch_shop_createPaymentIntent_for_api($memberID,$payment_method,$order_id)
	{
	$ShopRuntime = PerchShop_Runtime::fetch();

		return $ShopRuntime->createPaymentIntent_for_api($memberID,$payment_method,$order_id);

	}

	function perch_shop_get_cart_for_api($member_id)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		return $ShopRuntime->get_cart_api($member_id);
	}

	function perch_shop_clear_cart_for_api($member_id)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		return $ShopRuntime->clear_cart_api($member_id);
	}

	function perch_shop_authPayment_for_api($memberID,$gateway){
	 $Gateway = PerchShop_Gateways::get($gateway);

                    		$config  = PerchShop_Config::get('gateways', 'stripe');

                    			$secretkey 	 = $Gateway->get_api_key($config);
                    			return  $secretkey 	;

	}
?>
