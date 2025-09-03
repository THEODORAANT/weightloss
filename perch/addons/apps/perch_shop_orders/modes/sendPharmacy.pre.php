<? ob_start(); ?>
<?php
$details="";
  $success=false;
$message="";
$Orders     = new PerchShop_Orders($API);
	$OrderItems = new PerchShop_OrderItems($API);
	$Customers  = new PerchShop_Customers($API);
	  $Tags = new PerchMembers_Tags($API);
	//echo "post";
  //  print_r($_POST);
	if (PerchUtil::get('id')) {



			$shop_id = PerchUtil::get('id');

        		$Order     = $Orders->find($shop_id);
        		$details = $Order->to_array();
        	//echo "order".$Order->is_paid(); //print_r($Order);
        //	echo $Order->customerID();
        		$Customer    = $Customers->find($Order->customerID());
        			   if(isset($_POST["orderID"]) && $Order->is_paid()){

        		 $apiresponse= $Order->sendOrdertoPharmacy( $Customer);


                                     if($apiresponse["success"]){

                                                         $Tag  = $Tags->find_by_tag('pending-docs');
                                                         if (is_object($Tag)) {
                                                             $Tag->remove_from_member($Customer->memberID());
                                                             }
                                                              $Tag = $Tags->find_or_create('approved-docs');
                                                               $Tag->add_to_member($Customer->memberID());

                                     $success=true;
                                     $message='The order has been successfully send to the pharmacy .';

                                                          }else{
                                                            $message=$apiresponse["data"]["message"];

                                                          }

                                 // print_r($apiresponse);
                                  }
	}


// && perch_member_has_tag('approved-docs')








