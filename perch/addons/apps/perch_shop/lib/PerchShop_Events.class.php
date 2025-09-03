<?php

class PerchShop_Events
{
	public static function order_status($Event)
	{ //echo "order_status";
		$Order = $Event->subject;
		$status = $Event->args[0];
//print_r($Order);echo "status"; echo $status;
		if ($status) {
			$API  = new PerchAPI(1.0, 'perch_shop');
			$OrderStatuses = new PerchShop_OrderStatuses($API);

			$OrderStatus = $OrderStatuses->find_by_key($status);
			##PerchUtil::debug($OrderStatus);
			//echo "OrderStatus";
			//print_r($OrderStatus);

			if ($OrderStatus) {

				// find emails for status
				$Emails = new PerchShop_Emails($API);
				$emails = $Emails->get_for_status($OrderStatus->id());
				//echo "emails"; print_r($emails);

				if (PerchUtil::count($emails)) {
					foreach($emails as $Email) {
					if($Order){
					$Order->send_order_email($Email);
					}

					}
				}
			}
		}else{
			#PerchUtil::debug($Event);
		}
	}

	public static function register_member_login($Event)
	{
		$ShopRuntime = PerchShop_Runtime::fetch();
		$ShopRuntime->register_member_login($Event);
	}
}
