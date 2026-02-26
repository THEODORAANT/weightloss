<?php

class PerchShop_Order extends PerchShop_Base
{
	protected $factory_classname = 'PerchShop_Orders';
	protected $table             = 'shop_orders';
	protected $pk                = 'orderID';
	protected $index_table       = 'shop_admin_index';

	protected $modified_date_column = 'orderUpdated';
    public $deleted_date_column  = 'orderDeleted';

    protected $event_prefix = 'shop.order';

    protected $date_fields = ['orderUpdated', 'orderCreated'];

        protected $duplicate_fields  = [
                                                                                'orderStatus'       => 'status',
                                                                                'customerID'        => 'customer',
                                                                                'orderTotal'        => 'total',
                                                                                'orderCurrency'     => 'currency',
                                                                                'orderGateway'      => 'gateway'
                                                                        ];
    protected static $questionnaireOrderColumnAvailable = null;

	public function get_currency_code()
	{
		$Currencies = new PerchShop_Currencies($this->api);
		$Currency = $Currencies->find($this->currencyID());
		return $Currency->currencyCode();
	}

    public function get_reporting_currency()
    {
        $Currencies = new PerchShop_Currencies($this->api);
        return $Currencies->get_reporting_currency();
    }

    public function get_shipping()
    {
        if ($this->shippingID()) {
            $Shippings = new PerchShop_Shippings($this->api);
            return $Shippings->find((int)$this->shippingID());
        }


        return null;
    }

    public function get_promotions()
    {
        $sql = 'SELECT promoID FROM '.PERCH_DB_PREFIX.'shop_order_promotions
                WHERE orderID='.$this->db->pdb((int)$this->id());
        $ids = $this->db->get_rows_flat($sql);

        if (PerchUtil::count($ids)) {

            $out = [];
            $Promotions = new PerchShop_Promotions($this->api);

            foreach($ids as $id) {
                $Promotion = $Promotions->find((int)$id);
                if ($Promotion) {
                    $out[]  = $Promotion;
                }
            }

            return $out;
        }
        return null;
    }

    protected function questionnaireHasOrderColumn()
    {
        if (self::$questionnaireOrderColumnAvailable !== null) {
            return self::$questionnaireOrderColumnAvailable;
        }

        $table = PERCH_DB_PREFIX.'questionnaire';
        $sql   = "SHOW COLUMNS FROM `{$table}` LIKE 'question_order'";
        $exists = $this->db->get_value($sql);

        self::$questionnaireOrderColumnAvailable = $exists ? true : false;

        return self::$questionnaireOrderColumnAvailable;
    }

    public function get_discount_code()
    {
        $promos = $this->get_promotions();

        if (PerchUtil::count($promos)) {
            foreach($promos as $Promo) {
                $code = $Promo->get('discount_code');
                if ($code) return $code;
            }
        }

        return false;
    }


	public function take_payment($method='purchase', $opts=array())
	{
	//print_r($this->orderGateway());

		 if ((float)$this->orderTotal() <= 0) {
                                $this->finalize_as_paid('pending');

                                       echo "<script>window.location.href = '" . $opts['return_url'] . "?pending=1';</script>";

                                return true;
                        }
                        $Gateway = PerchShop_Gateways::get($this->orderGateway());
                        return $Gateway->take_payment($this, $opts);
	}

	public function complete_payment($args, $gateway_opts=array())
	{
		PerchUtil::debug('completing payment');
		$this->finalize_as_paid();
		return true;
	}

	public function set_status($status)
	{
	//echo 'Setting order status to '.$status;
	//echo 'Setting order status to 2 '.$this->orderStatus();
        PerchUtil::debug('Setting order status to '.$status);
        if ($this->orderStatus() != $status) {
            // echo "1";

            $result = $this->intelliupdate([ 'status' => $status ]);
  //echo "2";
            $Perch = Perch::fetch();
            $Perch->event('shop.order_status_update', $this, $status);
  //echo "3";print_r($result); echo "***";
            return $result;
        }else{
              if($status=="paid"){
             //echo "2";
            $Perch = Perch::fetch();
            $Perch->event('shop.order_status_update', $this, $status);
              }
            PerchUtil::debug('Status already set.', 'error');
        }

		return true;
	}
		public function getPharmacyOrderbyOrderid( $order_id){
		        	$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'orders_match_pharmacy
                                                WHERE orderID='.$this->db->pdb((int)$order_id).' order by created_at desc';
                                                 // echo "products_match_pharmacy";
                                                	//print_r($sql);
                     $orders = $this->db->get_rows($sql);
                     return  $orders;


		}
        public function getOrderPharmacyDetails( $orderNumber){
           $db   = PerchDB::fetch();
           $table = PERCH_DB_PREFIX.'orders_match_pharmacy';
           $stored = $db->get_row('SELECT * FROM '.$table.' WHERE pharmacy_orderID='.$db->pdb($orderNumber).' ORDER BY created_at DESC LIMIT 1');

           $details = [];

           if (PerchUtil::count($stored)) {
               $status = $stored['status'] ?? $stored['pharmacy_status'] ?? $stored['order_status'] ?? '';
               $tracking = $stored['trackingno'] ?? $stored['tracking_no'] ?? $stored['trackingnumber'] ?? $stored['tracking_number'] ?? $stored['trackingref'] ?? $stored['tracking_reference'] ?? '';
               $dispatchDate = $stored['dispatch_date'] ?? $stored['dispatchdate'] ?? $stored['dispatched_at'] ?? $stored['dispatcheddate'] ?? '';

               if ($status !== '') {
                   $details['status'] = $status;
                   $details['statusText'] = $stored['status_text'] ?? $status;
               }

               if ($dispatchDate !== '') {
                   $details['dispatchDate'] = $dispatchDate;
               }

               if ($tracking !== '') {
                   $details['trackingNo'] = $tracking;
                   $details['trackingUrl'] = 'https://www.royalmail.com/track-your-item#/tracking-results/'.urlencode($tracking);
               }

               if (isset($stored['created_at']) && $stored['created_at'] && (!isset($details['status']) || strcasecmp($details['status'], 'completed') !== 0)) {
                   $sentTs = strtotime((string)$stored['created_at']);
                   if ($sentTs) {
                       $details['daysSinceSent'] = (int)floor((time() - $sentTs) / 86400);
                   }
               }
           }

           if (!isset($details['status']) || $details['status'] === '' || !isset($details['trackingNo'])) {
               $pharmacy_api = new PerchShop_PharmacyOrderApiClient('https://api.myprivatechemist.com/api', '4a1f7a59-9d24-4e38-a3ff-9f8be74c916b');
               $response = $pharmacy_api->getOrderDetails($orderNumber);
               if ($response['success'] && isset($response['data'])) {
                   $remote = $response['data'];
                   foreach (['status', 'statusText', 'dispatchDate', 'trackingNo'] as $key) {
                       if ((!isset($details[$key]) || $details[$key] === '') && isset($remote[$key]) && $remote[$key] !== '') {
                           $details[$key] = $remote[$key];
                       }
                   }

                   if (!isset($details['trackingUrl']) && isset($details['trackingNo']) && $details['trackingNo'] !== '') {
                       $details['trackingUrl'] = 'https://www.royalmail.com/track-your-item#/tracking-results/'.urlencode($details['trackingNo']);
                   }
               }
           }

           return $details;
        }

public function isReorder($Customer){
		$Products = new PerchShop_Products($this->api);
		$products = $Products->get_by_category('products/weight-loss');
//echo "isReorder";print_r($products );
		if (!PerchUtil::count($products)) {
			return false;
		}

		$product_ids = [];
		foreach ($products as $Product) {
			$product_ids[] = (int)$Product->id();
		}

		if (!PerchUtil::count($product_ids)) {
			return false;
		}

		$db = PerchDB::fetch();
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT COUNT(DISTINCT o.orderID)
				FROM '.PERCH_DB_PREFIX.'shop_orders o
				INNER JOIN '.PERCH_DB_PREFIX.'shop_order_items oi ON oi.orderID = o.orderID
				WHERE o.customerID='.$db->pdb((int)$Customer->id()).'
					AND o.orderStatus IN ('.$db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
					AND oi.productID IN ('.$db->implode_for_sql_in($product_ids).')';

		return $db->get_count($sql) >= 2;
	}
	public function sendOrdertoPharmacy( $Customer){
        require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_service.php';
        $pharmacy_api = new PerchShop_PharmacyOrderApiClient('https://api.myprivatechemist.com/api', '4a1f7a59-9d24-4e38-a3ff-9f8be74c916b');
       	$Addresses  = new PerchShop_Addresses($this->api);
       $ShippingAddr = $Addresses->find((int)$this->orderShippingAddress());
           	$Members = new PerchMembers_Members($this->api);
           	$Member = $Members->find($Customer->memberID());
      $Products = new PerchShop_Products($this->api);

        $OrderItems = new PerchShop_OrderItems($this->api);
         $Orders = new PerchShop_Orders($this->api);
        $items = $OrderItems->get_by('orderID', $this->id());
            $ShopRuntime = PerchShop_Runtime::fetch();
            $reorder = $ShopRuntime->customer_has_paid_order($Customer->memberID(), 'products/weight-loss');

                $order_items = [];
                    $questions_items=[];
               $questionnaire_type="first-order";
                 $orders = $Orders->findAll_for_customer($Customer);

                                            if (PerchUtil::count($orders) && PerchUtil::count($orders)>=2) {
                                             //if($reorder){
                                  					      $questionnaire_type="re-order";
                                  					 }

             echo "reorder **";print_r($reorder);

        if (PerchUtil::count($items)) {
        	foreach($items as $Item) {
        	$sql = 'SELECT * FROM '.PERCH_DB_PREFIX.'products_match_pharmacy
                                        WHERE productID='.$this->db->pdb((int)$Item->productID());
                                         // echo "products_match_pharmacy";
                                        	//print_r($sql);
        $Product = $Products->find((int)$Item->productID());

         	/*if ($Product) {
         	  if ($Product->is_variant()) {
                        $questionnaire_type="re-order";
                    }

           }*/


             $prod = $this->db->get_row($sql);
            //  echo "prod **";
                                                    	//print_r($prod);
                            if (PerchUtil::count($prod)) {
        	$order_items[]  =  [
                                                         "productId" =>  $prod["pharmacy_productID"],
                                                         "quantity" => $Item->itemQty(),
                                                     ];
                                                     }
        	}
        }

                $Questionnaires = new PerchMembers_Questionnaires($this->api);
                $questionnaire_type_key = ($questionnaire_type === 're-order') ? 're-order' : 'first-order';
               // echo "questionnaire_type_key"; echo $questionnaire_type_key;
                $allowed_questions = array_keys($Questionnaires->get_questions_answers($questionnaire_type_key));
                $allowed_questions = array_flip($allowed_questions);


                $questionnaireID = null;
                $dynamicFields  = PerchUtil::json_safe_decode($this->orderDynamicFields(), true);
                $questionnaire_notes = '';

                if (is_array($dynamicFields)) {
                    if (isset($dynamicFields['questionnaires']) && is_array($dynamicFields['questionnaires'])) {
                        if (!empty($dynamicFields['questionnaires'][$questionnaire_type])) {
                            $questionnaireID = (int)$dynamicFields['questionnaires'][$questionnaire_type];
                        }
                    } elseif (!empty($dynamicFields['questionnaire_qid'])) {
                        $questionnaireID = (int)$dynamicFields['questionnaire_qid'];
                    }

                    if (!empty($dynamicFields['questionnaire_notes'])) {
                        $questionnaire_notes = trim((string) $dynamicFields['questionnaire_notes']);
                    }
                }

                if (!$questionnaireID) {
                    $sql_latest_qid = 'SELECT qid FROM '.PERCH_DB_PREFIX.'questionnaire'
                        .' WHERE `type`='.$this->db->pdb($questionnaire_type)
                        .' AND member_id='.$this->db->pdb((int)$Member->id())
                        .' ORDER BY created_at DESC LIMIT 1';
                    $questionnaireID = (int)$this->db->get_value($sql_latest_qid);
                }

              /*  $sql_questionnaire = 'SELECT * FROM '.PERCH_DB_PREFIX.'questionnaire'
                        .' WHERE `type`='.$this->db->pdb($questionnaire_type)
                        .' AND member_id='.$this->db->pdb((int)$Member->id());*/

                        $sql_questionnaire = 'SELECT *
                            FROM '.PERCH_DB_PREFIX.'questionnaire
                            WHERE `type` = '.$this->db->pdb($questionnaire_type).'
                              AND member_id = '.$this->db->pdb((int)$Member->id()).'
                              AND (order_id IS NULL OR order_id = '.$this->db->pdb((int)$this->id()).')';


                if ($questionnaireID) {
                    $sql_questionnaire .= ' AND qid='.$this->db->pdb($questionnaireID);
                }

                if ($this->questionnaireHasOrderColumn()) {
                    $sql_questionnaire .= ' ORDER BY (question_order IS NULL), question_order ASC, created_at ASC, id ASC';
                } else {
                    $sql_questionnaire .= ' ORDER BY created_at ASC, id ASC';
                }
             //   echo "questionnaire";
             //  echo $sql_questionnaire;

                $questionnaire = $this->db->get_rows($sql_questionnaire);
             //   print_r($questionnaire);

                if (PerchUtil::count($questionnaire)) {
                    $question_entries = [];
                    $answers_by_slug = [];

                    foreach ($questionnaire as $questiondet) {
                        $question_slug = $questiondet['question_slug'] ?? null;

                        if ($question_slug === null || !isset($allowed_questions[$question_slug])) {
                            continue;
                        }

                        if (!isset($questiondet["question_text"], $questiondet["answer_text"])) {
                            continue;
                        }

                        $question_text = (string)$questiondet["question_text"];
                        $answer_text = (string)$questiondet["answer_text"];

                        if ($question_text === '' || $answer_text === '') {
                            continue;
                        }

                        $answers_by_slug[$question_slug] = $answer_text;
                        $question_entries[] = [
                            'slug' => $question_slug,
                            'question' => $question_text,
                            'answer' => $answer_text,
                        ];
                    }

                    if (!empty($question_entries)) {
                        $answer_indicates_allergies = static function ($answerText) {
                            $normalized = strtolower(trim((string)$answerText));

                            return $normalized !== '' && strpos($normalized, 'yes') === 0;
                        };

                        foreach ($question_entries as $entry) {
                            if ($entry['slug'] === 'allergy_details') {
                                $allergy_answer = $answers_by_slug['allergies'] ?? null;

                                if ($allergy_answer === null || !$answer_indicates_allergies($allergy_answer)) {
                                    continue;
                                }
                            }

                            $questions_items[] = [
                                "question" => $entry['question'],
                                "answer" => $entry['answer'],
                            ];
                        }
                    }
                }

        if ($questionnaire_notes !== '') {
            $questions_items[] = [
                "question" => "Admin notes",
                "answer" => $questionnaire_notes,
            ];
        }
if(!PerchUtil::count($questions_items)){
     echo "reorder"; echo  $reorder ;
 echo $sql_questionnaire;
echo "questions_items";
	print_r($questions_items);
	die();exit();
}
/*echo "questions_items";
	print_r($questions_items);
        echo "order_items";
	print_r($order_items);
                      echo "ShippingAdr";
	print_r($ShippingAddr);*/
         $shippingAddressLine1 = '';
         $shippingAddressLine2 = '';
         $shippingCity = '';
         $shippingPostCode = '';
         $shippingCountry = '';

         if ($ShippingAddr instanceof PerchShop_Address) {
             $shippingAddressLine1 = $ShippingAddr->get('address_1') ?? '';
             $shippingAddressLine2 = $ShippingAddr->get('address_2') ?? '';
             $shippingCity = $ShippingAddr->get('city') ?? '';
             $shippingPostCode = $ShippingAddr->get('postcode') ?? '';
             $shippingCountry = $ShippingAddr->get_country_name() ?? '';
         }

         $orderData = [
             "status" => "PAYMENT_RECEIVED",
             "customerId" => $Customer->pharmacy_refid(),
             "items" => $order_items,
             "shipping" => [
                 "addressLine1" => $shippingAddressLine1,
                 "addressLine2" => $shippingAddressLine2,
                 "city" => $shippingCity,
                 "postCode" => $shippingPostCode,
                 "country" => $shippingCountry
             ],
             "assessment" => $questions_items,
             "notes" => $questionnaire_notes
         ];

           $response = [];
           $sendResult = comms_service_request_json('POST', '/v1/perch/orders/'.$this->id().'/create', $orderData);
           $response = [
               'success' => $sendResult,
               'data' => [
                   'message' => $sendResult ? 'Order sent to comms service.' : 'Failed to send order to comms service.'
               ],
           ];
                      //     echo "response";
         //	print_r($response);
         	//die();exit();
         if(isset($response["success"]["ok"])){
    $pharmacy_data = [
               'orderID'    => $this->id(),
               'pharmacy_orderID'    => $response["success"]["orderNumber"],
               'status'=> "PAYMENT_RECEIVED",
               'pharmacy_message' =>$response["data"]["message"],
           ];
         	}else{
         	print_r(json_encode($orderData));
            	echo '/v1/perch/orders/'.$this->id().'/create';
         		print_r($response);
                     	die();exit();
         	 $pharmacy_data = [
                           'orderID'    => $this->id(),
                           'pharmacy_orderID'    => '',
                           'pharmacy_message' =>$response["data"]["message"],
                       ];
         	}

         	$pharmacy_api->addOrderPharmacytodb($pharmacy_data);

return $response;
	}


	public function finalize_as_paid($status='paid')
	{ //echo "finalize_as_paid"; echo $this->orderGateway();
		$Gateway = PerchShop_Gateways::get($this->orderGateway());
		$Gateway->finalize_as_paid($this);
//echo "1";
        $this->assign_invoice_number();
//echo "3";
        $this->set_status($status);
//echo "44";
		// Get products
		$Products = new PerchShop_Products($this->api);
		$products = $Products->get_for_order($this->id());
//echo "55";
        // Get customer
        $Customers = new PerchShop_Customers($this->api);
        $Customer = $Customers->find($this->customerID());
//echo "66";
        if ($Customer && $Customer->memberID()) {
                $Addresses = new PerchShop_Addresses($this->api);
                $ShippingAddress = null;

                if ($this->orderShippingAddress()) {
                        $ShippingAddress = $Addresses->find((int)$this->orderShippingAddress());
                } else {
                        $ShippingAddress = $Addresses->find_for_customer($Customer->id(), 'shipping');
                }

                if ($ShippingAddress instanceof PerchShop_Address) {
                        $Runtime = PerchShop_Runtime::fetch();
                        $Runtime->update_member_shipping_profile_from_address($ShippingAddress, $Customer->memberID());
                }

        }

                // Update stock levels
                $is_reorder=false;
                if (PerchUtil::count($products)) {
                        foreach($products as $Product) {
                                if ($Product->itemQty()) {

					$adjustment = 0 - ((int)$Product->itemQty());
					$Product->update_stock_level($adjustment);	
				}
				//echo "66";
                // Apply tags
                $Product->apply_tags_to_customer($Customer);
			}
		}
//echo "88";
        // Get exchange rate, if we can.
        $exchange_rate = $Gateway->get_exchange_rate($this);
        if ($exchange_rate!==null) {
            $this->update([
                'orderExchangeRate' => $exchange_rate,
                ]);
                // echo "99";
        }else{
            $Currencies = new PerchShop_Currencies($this->api);
            $ReportingCurrency = $Currencies->get_reporting_currency();

            if ($ReportingCurrency) {
                if ($this->currencyID() == $ReportingCurrency->id()) {
                    $exchange_rate = 1;
                }else{
                    $Currency = $Currencies->find((int)$this->currencyID());
                    $exchange_rate = $Currency->currencyRate();
                }

                $this->update([
                    'orderExchangeRate' => $exchange_rate,
                ]);
              //  echo "10010";
            }

        }
        if($this->is_paid()){
        $data["email"]= $Customer->email();


        $orderCreated = new DateTime($this->orderCreated());
         $data["FirstName"]=$Customer->first_name();
         $isreorder=$this->isReorder($Customer);
        if($isreorder){
          $data["NextOrderDoseDate"]= $this->orderCreated();
        }else{
         $data["FirstOrderDate"]= $this->orderCreated();


        }
        $Affiliate = new PerchMembers_Affiliate($this->api);
                                   // $Affiliate->addCommission($memberid, $amount);
                  $Affiliate->recordPurchase($Customer->memberID(),$this->id(),$isreorder);
                 // exit();
        perch_emailoctopus_update_contact($data);
       // echo "perch_member_add_commission";
//$this->send_order_email_trustpilot($this->details['orderStatus']);

        }


	}

	public function is_paid()
	{
		PerchUtil::debug('Is paid?');
		if ($this->details['orderStatus']=='paid') {
			return true;
		}

		return false;
	}

	public function sync_order_items()
	{
		$OrderItems = new PerchShop_OrderItems($this->api);
		$OrderItems->sync_for_order($this->orderID());
	}

	public function copy_order_items_from_cart($Cart, $cart_data)
	{
		$OrderItems = new PerchShop_OrderItems($this->api);
		$OrderItems->copy_from_cart($this->orderID(), $Cart, $cart_data);

        if (PerchUtil::count($cart_data['promotions'])) {
            foreach($cart_data['promotions'] as $Promotion) {
                $this->log_promotion($Promotion);
            }
        }
	}

    public function freeze_addresses()
    {
        $Addresses = new PerchShop_Addresses($this->api);

        $data = [];

        if ($this->orderBillingAddress()) {
            $data['orderBillingAddress'] = $Addresses->freeze_for_order($this->orderBillingAddress(), $this->id());
        }

        if ($this->orderShippingAddress()) {

            if ($this->orderShippingAddress() == $this->orderBillingAddress()) {
                // same address for both
                $data['orderShippingAddress'] = $data['orderBillingAddress'];
            }else{
                $data['orderShippingAddress'] = $Addresses->freeze_for_order($this->orderShippingAddress(), $this->id());    
            }
            
        }

        $this->update($data);
    }

    private function log_promotion($Promotion)
    {
        $data = [
            'orderID'    => $this->id(),
            'promoID'    => $Promotion->id(),
            'customerID' => $this->customerID(),
        ];

        $this->db->insert(PERCH_DB_PREFIX.'shop_order_promotions', $data);
    }

	private function _get_gateway_payment_options($opts)
	{
		$Gateway = PerchShop_Gateways::get($this->orderGateway());
		return $Gateway->format_payment_options($opts);
	}

	private function _process_gateway_specific_response($args, $gateway_opts=array())
	{
		$Gateway = PerchShop_Gateways::get($this->orderGateway());
		return $Gateway->produce_payment_response($args, $gateway_opts);
	}

    public function assign_invoice_number()
    {
        $number = $this->get_next_invoice_number();
        $Settings = $this->api->get('Settings');
        $format = $Settings->get('perch_shop_invoice_number_format')->val();

        $invoice_number = sprintf($format, $number);

        $this->update([
            'orderInvoiceNumber' => $invoice_number,
            ]);
    }

    private function get_next_invoice_number()
    {
        $sql = "UPDATE ".PERCH_DB_PREFIX."shop_orders_meta SET metaValue=last_insert_id(metaValue+1) WHERE id='last_invoice_number'";
        $this->db->execute($sql);
        $val = $this->db->get_value('SELECT last_insert_id()');
        return (int)$val;
    }

	public function to_array()
    {
        $out = $this->details;

        $dynamic_field_col = str_replace('ID', 'DynamicFields', $this->pk);
        if (isset($out[$dynamic_field_col]) && $out[$dynamic_field_col] != '') {
            $dynamic_fields = PerchUtil::json_safe_decode($out[$dynamic_field_col], true);
            if (PerchUtil::count($dynamic_fields)) {
            	$dynamic_fields = $this->flatten_array('', $dynamic_fields);
                //$out = array_merge($dynamic_fields, $out);

                foreach($dynamic_fields as $key=>$value) {
                    $out['perch_'.$key] = $value;
                }
                $out = array_merge($dynamic_fields, $out);
            }
        }

        $Statuses = new PerchShop_OrderStatuses($this->api);
        $Status = $Statuses->get_one_by('statusKey', $out['orderStatus']);
        if ($Status) {
            $out = array_merge($out, $Status->to_array());
        }

        return $out;
    }

    public function set_transaction_reference($ref)
    {
    	$this->update(['orderGatewayRef'=>$ref]);
    	if (!$this->is_order_send_to_pharmacy($this->id())) {
    	  $Customers = new PerchShop_Customers($this->api);
                $Customer = $Customers->find($this->customerID());
                  $this->sendOrdertoPharmacy($Customer);
              }
    }

       public function send_order_email_trustpilot($status)
        { //echo "send_order_email_trustpilot"; echo $status;
        if ($status) {

          $OrderStatuses = new PerchShop_OrderStatuses($this->api);

        			$OrderStatus = $OrderStatuses->find_by_key($status);
        			##PerchUtil::debug($OrderStatus);

        		}
        		 //echo "OrderStatus";print_r($OrderStatus->statusID());
        $ShopEmails = new PerchShop_Emails($this->api);
        $ShopEmail = $ShopEmails->get_for_status($OrderStatus->statusID());
//	 echo "ShopEmail";print_r($ShopEmail);echo "emailTemplate";echo $ShopEmail["emailTemplate"];

    	$Customers = new PerchShop_Customers($this->api);
    	$Customer = $Customers->find($this->customerID());

    	$Members = new PerchMembers_Members($this->api);
    	$Member = $Members->find($Customer->memberID());
  if (is_array($ShopEmail)) {
                $ShopEmail = reset($ShopEmail);
        }


    	$Email = $this->api->get('Email');
        $Email->set_template('shop/emails/'.$ShopEmail->emailTemplate(), 'shop');
        $Email->set_bulk($this->to_array());
        $Email->set_bulk($ShopEmail->to_array());
        $Email->set_bulk($Customer->to_array());
        $Email->set_bulk($Member->to_array());

        $Addresses = new PerchShop_Addresses($this->api);

        $ShippingAddr = $Addresses->find((int)$this->orderShippingAddress());
        $Email->set_bulk($ShippingAddr->format_for_template('shipping'));

		$BillingAddr = $Addresses->find((int)$this->orderBillingAddress());
        $Email->set_bulk($BillingAddr->format_for_template('billing'));

        $OrderItems = new PerchShop_OrderItems($this->api);
        $items = $OrderItems->get_by('orderID', $this->id());

        $order_items = [];

        if (PerchUtil::count($items)) {
        	foreach($items as $Item) {
        		$order_items[]  = $Item->to_array();
        	}
        }
        $result = $this->to_array();
        $result['items'] = $order_items;

        $data = $this->format_invoice_for_template($result);

		$Email->set_bulk($data);

        $Email->senderName($ShopEmail->sender_name());
        $Email->senderEmail($ShopEmail->sender_email());
          //  $Email->recipientEmail("getweightloss.co.uk+25a853a1a5@invite.trustpilot.com");
             $Email->recipientEmail("perchrunway@gmail.com");
                            $Email->bccToEmail("getweightloss.co.uk+25a853a1a5@invite.trustpilot.com");
    $Email->send();
}
    public function send_order_email(PerchShop_Email $ShopEmail)
    {
        PerchUtil::debug('Sending customer email');

    	$Customers = new PerchShop_Customers($this->api);
    	$Customer = $Customers->find($this->customerID());
        $Orders= new PerchShop_Orders($this->api);
    	$Members = new PerchMembers_Members($this->api);
    	$Member = $Members->find($Customer->memberID());
        $orders = $Orders->findAll_for_customer($Customer);

       if (PerchUtil::count($orders) && PerchUtil::count($orders)==1) {

    	$Email = $this->api->get('Email');
        $Email->set_template('shop/emails/'.$ShopEmail->emailTemplate(), 'shop');
        $Email->set_bulk($this->to_array());
        $Email->set_bulk($ShopEmail->to_array());
        $Email->set_bulk($Customer->to_array());
        $Email->set_bulk($Member->to_array());

        $Addresses = new PerchShop_Addresses($this->api);

        $ShippingAddr = $Addresses->find((int)$this->orderShippingAddress());
        $Email->set_bulk($ShippingAddr->format_for_template('shipping'));

		$BillingAddr = $Addresses->find((int)$this->orderBillingAddress());
        $Email->set_bulk($BillingAddr->format_for_template('billing'));

        $OrderItems = new PerchShop_OrderItems($this->api);
        $items = $OrderItems->get_by('orderID', $this->id());

        $order_items = [];

        if (PerchUtil::count($items)) {
        	foreach($items as $Item) {
        		$order_items[]  = $Item->to_array();	
        	}
        }
        $result = $this->to_array();
        $result['items'] = $order_items;

        $data = $this->format_invoice_for_template($result);

		$Email->set_bulk($data);
        
        $Email->senderName($ShopEmail->sender_name());
        $Email->senderEmail($ShopEmail->sender_email());



        switch ($ShopEmail->emailFor()) {

            // Send to the customer
            case 'customer':
                $Email->recipientEmail($Member->memberEmail());
                $Email->bccToEmail("getweightloss.co.uk+25a853a1a5@invite.trustpilot.com");
                break;

            // Send to the customer, BCC the admin
            case 'customer_bcc':
                $Email->recipientEmail($Member->memberEmail());
                $Email->bccToEmail($ShopEmail->emailRecipient());
                break;

            // Send to the admin
            case 'admin':
                $Email->recipientEmail($ShopEmail->emailRecipient());
                break;

        }


        $Email->send();

        }
    }

    public function get_for_template()
    {
    	$OrderItems = new PerchShop_OrderItems($this->api);
        $items = $OrderItems->get_by('orderID', $this->id());

        $order_items = [];

        if (PerchUtil::count($items)) {
        	foreach($items as $Item) {
        		$order_items[]  = $Item->to_array();	
        	}
        }
        $result = $this->to_array();
        $result['items'] = $order_items;

        $result = $this->format_invoice_for_template($result);

        return $result;
    }

    public function template($opts)
    {
    	$html  = '';
        $single_mode = false;
    	$items = [$this->get_for_template()];

    	if (isset($opts['return-objects']) && $opts['return-objects']) {
            return $items;
        }
        $render_html = true;

        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $render_html = false;
            if (isset($opts['return-html'])&& $opts['return-html']==true) {
                $render_html = true;
            }
        }

    	// template
        if (is_callable($opts['template'])) {
            $callback = $opts['template'];
            $template = $callback($items);
        }else{
            $template = $opts['template'];
        }

        if (is_object($this->api)) {
            $Template = $this->api->get('Template');
            $Template->set($template,'shop');
        }else{
            $Template = new PerchTemplate($template, 'shop');
        }


        if ($render_html) {

            if (PerchUtil::count($items)) {

                if (isset($opts['split-items']) && $opts['split-items']) {
                    $html = $Template->render_group($items, false);
                }else{
                    $html = $Template->render_group($items, true);
                }

            }else{

                $Template->use_noresults();
                $html = $Template->render(array());
            }

        }


        if (isset($opts['skip-template']) && $opts['skip-template']==true) {

            if ($single_mode) return $Item->to_array();

            $processed_vars = $items;
            #if (PerchUtil::count($items)) {
            #    foreach($items as $Item) {
            #        $processed_vars[] = $Item->to_array();
            #    }
            #}

            if (PerchUtil::count($processed_vars)) {

                $category_field_ids    = $Template->find_all_tag_ids('categories');
                //PerchUtil::debug($category_field_ids, 'notice');

                foreach($processed_vars as &$item) {
                    if (PerchUtil::count($item)) {
                        foreach($item as $key => &$field) {
                            if (in_array($key, $category_field_ids)) {
                                $field = $this->_process_category_field($field);
                            }
                            if (is_array($field) && isset($field['processed'])) {
                                $field = $field['processed'];
                            }
                            if (is_array($field) && isset($field['_default'])) {
                                $field = $field['_default'];
                            }
                        }
                    }
                }
            }

            if (isset($opts['return-html'])&& $opts['return-html']==true) {
                $processed_vars['html'] = $html;
            }

            return $processed_vars;
        }
 
        if (is_array($html)) {
            // split-items
            if (PerchUtil::count($html)) {
                $Template = new PerchTemplate();
                foreach($html as &$html_item) {
                    if (strpos($html_item, '<perch:')!==false) {
                        $html_item = $Template->apply_runtime_post_processing($html_item);
                    }
                }
            }
        }else{
            if (strpos($html, '<perch:')!==false) {
                $Template = new PerchTemplate();
                $html     = $Template->apply_runtime_post_processing($html);
            }
        }


        return $html;
    }

	private function format_invoice_for_template($result)
	{
		if (PerchUtil::count($result)) {

			$Products 	= new PerchShop_Products($this->api);
			$Currencies = new PerchShop_Currencies($this->api);
			$Shippings  = new PerchShop_Shippings($this->api);

			$Currency = $Currencies->find((int)$this->currencyID());
			$Shipping = $Shippings->find((int)$this->shippingID());

			if (isset($result['items'])) {

				$Totaliser = new PerchShop_CartTotaliser;

                $items = [];

				foreach($result['items'] as $item) {

					if ($item['itemType'] == 'product') {
						$item['identifier'] = $item['itemID'];
						$item['quantity'] = $item['itemQty'];

						$Product = $Products->find((int)$item['productID']);


						$item = array_merge($item, $Product->to_array());

						$exclusive_price = $item['itemPrice'];
						$qty             = $item['itemQty'];
						$inclusive_price = $item['itemTotal'];
						$tax_rate        = $item['itemTaxRate'];
                        $discount        = $item['itemDiscount'];
                        $tax_discount    = $item['itemTaxDiscount'];

						$Totaliser->add_to_items($exclusive_price*$qty, $tax_rate);
	                	$Totaliser->add_to_tax(($inclusive_price - $exclusive_price)*$qty, $tax_rate);

                        $Totaliser->add_to_item_discounts($discount, $tax_rate);
                        $Totaliser->add_to_tax_discounts($tax_discount, $tax_rate);

						$item['price_without_tax']           = $Currency->format_numeric($exclusive_price);
		                $item['price_without_tax_formatted'] = $Currency->format_display($exclusive_price);

		                $item['total_without_tax']           = $Currency->format_numeric($exclusive_price*$qty);
		                $item['total_without_tax_formatted'] = $Currency->format_display($exclusive_price*$qty);

		                $item['tax']                         = $Currency->format_numeric($inclusive_price - $exclusive_price);
		                $item['tax_formatted']               = $Currency->format_display($inclusive_price - $exclusive_price);

		                $item['total_tax']                   = $Currency->format_numeric(($inclusive_price - $exclusive_price)*$qty);
		                $item['total_tax_formatted']         = $Currency->format_display(($inclusive_price - $exclusive_price)*$qty);

		                $item['tax_rate']                    = $tax_rate;

		                $item['price_with_tax']              = $Currency->format_numeric($inclusive_price);
		                $item['price_with_tax_formatted']    = $Currency->format_display($inclusive_price);

		                $item['total_with_tax']              = $Currency->format_numeric($inclusive_price*$qty);
		                $item['total_with_tax_formatted']    = $Currency->format_display($inclusive_price*$qty);

                        $item['discount']                    = $discount;
                        $item['tax_discount']                = $tax_discount;


						if (isset($item['productVariantDesc'])) {
							$item['variant_desc'] = $item['productVariantDesc'];
						}

						unset($item['Product']);

						ksort($item);
                        $items[] = $item;
					}

					if ($item['itemType'] == 'shipping') {

						$exclusive_price = $item['itemPrice'];
                    	$inclusive_price = $item['itemTotal'];
                    	$tax_rate        = $item['itemTaxRate'];

                    	$Totaliser->add_to_shipping($exclusive_price, $tax_rate);
                		$Totaliser->add_to_shipping_tax(($inclusive_price - $exclusive_price), $tax_rate);

                        $result['shipping_method']                = $Shipping->title();
                        $result['shipping']                       = true;

						$result['shipping_without_tax']           = $Currency->format_numeric($exclusive_price);
						$result['shipping_without_tax_formatted'] = $Currency->format_display($exclusive_price);
						
						$result['shipping_tax']                   = $Currency->format_numeric($inclusive_price - $exclusive_price);
						$result['shipping_tax_formatted']         = $Currency->format_display($inclusive_price - $exclusive_price);
						
						$result['shipping_tax_rate']              = $tax_rate;
						
						$result['shipping_with_tax']              = $Currency->format_numeric($inclusive_price);
						$result['shipping_with_tax_formatted']    = $Currency->format_display($inclusive_price);

						$result['shipping_id'] 					  = $Shipping->id();

						#unset($item);
					}

					
				}

                $result['items'] = $items;

                $result = array_merge($result, $Totaliser->to_array($Currency, $this));
				$result = array_merge($result, $Currency->to_array());

                $result['invoice_number'] = $result['orderInvoiceNumber'];
                $result['exchange_rate'] = $result['orderExchangeRate'];

                $discount_code = $this->get_discount_code();
                if ($discount_code) {
                    $result['discount_code'] = $discount_code;
                };
				
			}

		}

		return $result;
	}

}
