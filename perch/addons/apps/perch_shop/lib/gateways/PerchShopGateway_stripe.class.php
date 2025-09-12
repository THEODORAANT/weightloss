<?php
class PerchShopGateway_stripe extends PerchShopGateway_default
{
	public function handle_successful_payment($Order, $response, $gateway_opts)
	{

		//$Order->finalize_as_paid();


       // return true;


	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}


	public function get_api_key($config)
	{
		if ($config['test_mode'] ) {
			return $config['test']['secret_key'];
		}
		return $config['live']['secret_key'];
	}

	public function get_public_api_key($config)
	{

		if ($config['test_mode']) {
			return $config['test']['publishable_key'];
		}
		return $config['live']['publishable_key'];
	}

	public function get_card_address($Order)
	{
		$data = $this->get_transaction_data($Order);

		if (isset($data['source']) && isset($data['source']['country'])) {
			return [
				'country' => $data['source']['country']
			];
		}

		return false;
	}

	public function get_exchange_rate($Order)
	{
		$this->init_native_stripe_api();
		if (strpos($Order->orderGatewayRef(), 'pi') === 0) {
           // It starts with 'pi'
             return null;

        }else{
        		$Charge = \Stripe\Charge::retrieve($Order->orderGatewayRef());

        		if ($Charge) {
        			$BalanceTransaction = \Stripe\BalanceTransaction::retrieve($Charge->balance_transaction);

        			$rate = ((float)$Charge->amount / (float)$BalanceTransaction->amount);
        			return $rate;
        		}
        }



		return null;
	}

	private function init_native_stripe_api()
	{
		$config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		\Stripe\Stripe::setApiKey($api_key);
	}

		public function get_order_from_env($Orders, $get, $post)
    	{
    		if (isset($get['session_id'])) {
    			return $Orders->get_one_by('orderGatewayRef', $get['session_id']);
    		}
    	}

	public function callback_looks_valid($get, $post)
	{
return true;
		/*if (isset($get['payment_intent'])) {
			return true;
		}
		return false;*/
	}


	function createPaymentIntent($memberID,$payment_method,$order_id){
		$Orders = new PerchShop_Orders($this->api);
    			$Order = $Orders->find((int)$order_id);

		$Customers = new PerchShop_Customers($this->api);
            $Customer = $Customers->find($Order->customerID());
            if($memberID==$Customer->memberID()){



        $orderTotal = $Order->orderTotal(); // already in pence
        $amount = (int) round($orderTotal * 100); // Convert to 12900 (pence) — GOOD

        $currency = $Order->get_currency_code(); // "gbp", "usd", etc.
        $product_name = 'GetWeightLoss Order #' . $Order->id();
        $config = PerchShop_Config::get('gateways', $this->slug);
    	//	$opts = array_merge($opts, $payment_opts);
    	$secretKey = $this->get_api_key($config);

            	// PaymentIntent details


                $data = [
                    'amount' => $amount,
                    'currency' => $currency,
                    'payment_method' => $payment_method,
                    //'confirmation_method' => 'automatic',
                    'confirm' => 'true',
                    'automatic_payment_methods' => [
                        'enabled' => 'true',
                        'allow_redirects' => 'never' // prevents redirect-based methods
                    ],
                    'receipt_email' => $Customer->customerEmail(),
                    'description' =>$product_name,
                    'metadata' => [
                        'order_id' => $product_name
                    ]
                ];

	// Initialize cURL
    $ch = curl_init('https://api.stripe.com/v1/payment_intents');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ':');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   // echo $httpCode;
    curl_close($ch);

    // Output result
    header('Content-Type: application/json');
    http_response_code($httpCode);
    $data_transaction = json_decode($response, true);
    if(isset($data_transaction['id'])){
      $Order->set_transaction_reference($data_transaction['id']);
       // return $data_transaction;
       return json_encode([
           "success" => true,
           "payment_intent_id" => $data_transaction['id'],
           "message" => "Payment successful"
       ]);
    }else{
       return json_encode([
           "success" => false,
           "payment_intent_id" => "na",
           "message" => "Payment unsuccessful"
       ]);
    }



    }
    return false;
	}
 function sendStripePayout($accountId, $amount) {
         $config = PerchShop_Config::get('gateways', $this->slug);
        	$secretKey = $this->get_api_key($config);

                  $data = http_build_query([
                      'amount' => intval($amount * 100),
                      'currency' => 'gbp',
                      'destination' => $accountId,
                      'description' => 'Affiliate payout'
                  ]);

                  $ch = curl_init('https://api.stripe.com/v1/transfers');
                  curl_setopt($ch, CURLOPT_USERPWD, $secretKey . ":");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $response = curl_exec($ch);
                  if (curl_errno($ch)) {
                      return "Error: " . curl_error($ch);
                  }
                  curl_close($ch);
                  return $response;
              }
public function action_payment_callback($Order, $args, $opts)
	{ //echo "action_payment_callback";
	//print_r($Order);
	if (isset($_GET['session_id'])) {
        $session_id = $_GET['session_id'];
        $config = PerchShop_Config::get('gateways', $this->slug);
       	$stripe_secret_key = $this->get_api_key($config);

        // Make cURL request to retrieve session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/checkout/sessions/$session_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':');

        $response = curl_exec($ch);
       //  echo "action_response";
        //	print_r($response);
        curl_close($ch);

        $session = json_decode($response, true);
        $opts['success_url'] ="https://".$_SERVER['HTTP_HOST'].$opts['success_url']."?session_id={CHECKOUT_SESSION_ID}";
        $opts['cancel_url'] ="https://".$_SERVER['HTTP_HOST'].$opts['cancel_url'];
        // Check session payment status
        if ($session && isset($session['payment_status']) && $session['payment_status'] === 'paid') {
            // Success: mark order as paid in your system

            $transaction_reference = $session['id'] ?? $session['payment_intent'];
       ///echo "transaction_reference";
       //	print_r($transaction_reference);
            // Example logic for updating Perch order:
           // $Order = $Order->find_by_transaction_reference($transaction_reference);
             //$Order =$Orders->get_one_by('orderGatewayRef',$transaction_reference);
            if ($Order->orderGatewayRef()!=null) {
             //   $Order->set_status('paid');
                // Redirect to thank-you page or show confirmation
                //echo "Payment successful! Order marked as paid.";
              $Order->update(['orderGatewayRef'=>$session['payment_intent']]);
                 $success= $this->handle_successful_payment($Order, $response, $opts);
                   if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
                                               $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
                                               }

                                               if(isset($_SESSION['perch_shop_package_id']) ){
                                                $Packages = new PerchShop_Packages($this->api);
                                               $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);
                                                                                                          // echo "package";print_r( $Package);

                                                          if ($Package) {             $Package->set_status();
                                                                                                                 //  $Package->update(['customerID' => $Customer->id()]);
                                                                                                           }
                                               }

                                               if(isset($_COOKIE['draft_package_item'])){
                                                  $PackageItems = new PerchShop_PackageItems($this->api);
$PackageItem  = $PackageItems->find($_COOKIE['draft_package_item']);
 $PackageItem->set_status_paid($Order->orderID());

                                               }
                           return true;

            } else {
            $this->handle_failed_payment($Order, $response, $opts);
                return false;//"Payment successful, but no matching order found.";
            }
        } else {
            echo "Payment incomplete or session invalid.";
        }
    } else {
        echo "No session_id provided.";
    }
	}


}
