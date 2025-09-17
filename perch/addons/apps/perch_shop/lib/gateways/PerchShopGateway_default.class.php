<?php

use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;

class PerchShopGateway_default
{
	protected $api;
	protected $slug = 'default';
	public $omnipay_name = null;

	public $payment_method    = 'purchase';
	public $authorize_method    = 'authorize';
	public $completion_method = 'completePurchase';

	public function __construct($api, $slug='default')
	{
		$this->api = $api;
		$this->slug = $slug;
		if (is_null($this->omnipay_name)) {
			$this->omnipay_name = ucfirst($slug);
		}
	}

	public function get_default_parameters()
	{
		$Omnipay = Omnipay::create($this->omnipay_name);
		return $Omnipay->getDefaultParameters();
	}

	public function get_payment_intent_data($paymentIntentId){
		$Gateway = PerchShop_Gateways::get('stripe');
    	$config = PerchShop_Config::get('gateways', $this->slug);
    	$key 	 = $Gateway->get_public_api_key($config);
    	$stripeSecretKey 	 = $Gateway->get_api_key($config);
    	 // Fetch PaymentIntent from Stripe
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/$paymentIntentId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':');

            $response = curl_exec($ch);
            curl_close($ch);

            $paymentIntent = json_decode($response, true);
            return $paymentIntent;
	}

public function take_klarna_payment($Order, $opts)
	{

	$paymentIntentId = $_GET['payment_intent'] ?? null;

    if (!$paymentIntentId) {
        $this->handle_failed_payment($Order, null, $opts);
        die("No payment intent ID provided.");
    }



    $paymentIntent = $this->get_payment_intent_data($paymentIntentId);
   // echo "response";
   // print_r($paymentIntent);

    // Show status
    $status = $paymentIntent['status'] ?? 'unknown';

    if ($status === 'succeeded') {
      //  echo " Payment successful!";
         $Order->update(['orderGateway'=>"stripe-klarna",'orderGatewayRef'=>$paymentIntentId]);

          if($this->handle_successful_payment($Order, $response, $opts)){
                        if (isset($opts['success_url'])) {
                              // PerchUtil::hold_redirects();
                                PerchUtil::redirect($opts['success_url']);
                            }
                           // echo "Payment successful";
                                    if (isset($opts['return_url'])) {
                                    echo "<script>window.location.href = '".$opts['return_url']."'</script>";
                                    exit;
        //PerchUtil::redirect($opts['return_url']."?confirm=false&payment_intent=".$paymentIntentReference);
                                    }
                    }
        // You can now fulfill the order, update DB, etc.
    } else{
     $this->handle_failed_payment($Order, null, $opts);

                   return ;
    }
}
public function take_payment($Order, $opts)
{
		$Customers = new PerchShop_Customers($this->api);
        $Customer = $Customers->find($Order->customerID());

    $orderTotal = $Order->orderTotal(); // already in pence
    $amount = (int) round($orderTotal * 100); // Convert to 12900 (pence) — GOOD

    $currency = $Order->get_currency_code(); // "gbp", "usd", etc.
    $product_name = 'GetWeightLoss Order #' . $Order->id();
        $config = PerchShop_Config::get('gateways', $this->slug);
	//	$opts = array_merge($opts, $payment_opts);
	if($Order->customerID()==191){
	$config['test_mode']=true;
	}
	$stripe_secret_key = $this->get_api_key($config);
    $success_url ="https://".$_SERVER['HTTP_HOST'].$opts['return_url']."?session_id={CHECKOUT_SESSION_ID}";
    $cancel_url ="https://".$_SERVER['HTTP_HOST'].$opts['cancel_url'];

    //$stripe_secret_key = 'sk_live_xxx'; // replace with your Stripe secret key

    // Create Checkout Session via cURL
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $stripe_secret_key . ':');

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        //'payment_method_types[]' => 'card',
       // 'payment_method_types[]' => 'klarna',
       'payment_method_types[]' =>$opts["payment_method_types"],
         'customer_email' => $Customer->customerEmail(),
        'line_items[0][price_data][currency]' => $currency,
        'line_items[0][price_data][product_data][name]' => $product_name,
        'line_items[0][price_data][unit_amount]' => $amount,
        'line_items[0][quantity]' => 1,

        'mode' => 'payment',
        'success_url' => $success_url,
        'cancel_url' => $cancel_url,

        //'billing_address_collection' => 'required',
        'customer_creation' => 'always',
        //'payment_method_options[klarna][preferred_locale]' => 'en-GB',
    ]));


    $response = curl_exec($ch);

    curl_close($ch);

    $data = json_decode($response, true);

     if (!isset($opts['redirect'])) {
     $opts['redirect']=true;
     }
    if (isset($data['url'])) {
        // Optional: save session ID for tracking
        $Order->set_transaction_reference($data['id']);


				   if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
                              $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
                              }

                              if(isset($_SESSION['perch_shop_package_id']) ){
                                                            $Packages = new PerchShop_Packages($this->api);
                                                            $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);


                                                            if ($Package) {
                                                             $Package->set_orderID($Order->id());
                                                                  //  $Package->update(['customerID' => $Customer->id()]);
                                                            }


                                                    }
        // Redirect to Stripe Checkout

        if(!$opts['redirect']){
        return $data['url'];
        }else{
       echo "<script>window.location.href = '" . $data['url'] . "';</script>";
        }

        exit;
    } else {
        echo "data";
        print_r($data);
        // Log or show error
        PerchUtil::debug('Stripe session creation failed', 'error');
        PerchUtil::debug($data, 'error');
       // return $this->handle_failed_payment($Order, null, $opts);
    }
}

	public function take_paymentold($Order, $opts)
	{ //echo "take_payment";
		$payment_opts = [
				'amount'        => $Order->orderTotal(),
				'currency'      => $Order->get_currency_code(),
				'transactionId' => $Order->id(),
				'clientIp'		=> PerchUtil::get_client_ip(),
				'description'	=> 'NLCLINIC Order #'.$Order->id(),

		    ];



		// optionally get the payment card (usually just customer details, not card numbers)
		$card = $this->get_payment_card($Order);
		if ($card) {
			$payment_opts['card'] = $card;
		}
        $config = PerchShop_Config::get('gateways', $this->slug);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);
        //print_r($opts);

		// Send purchase request
		if( isset($opts['confirm'])){

		$payment_method = $this->authorize_method;
		$Omnipay = $this->get_omnipay_intents_instance();

		}if( isset($opts['confirm_klarna'])){

                $this->take_klarna_payment($Order, $opts);
                exit;
		}else{

		$payment_method = $this->payment_method;
		$Omnipay = $this->get_omnipay_instance();
		}


	  try{
    	$response = $Omnipay->$payment_method($opts)->send();
    	//print_r($response);


         }catch(Exception $e) {
         //echo 'Message: ' .$e->getMessage();
          /*  if (isset($opts['cancel_url'])) {
              PerchUtil::redirect($opts['cancel_url']);
             }*/
              if (isset($opts['cancel_url'])) {

                     echo("<script>location.href = '/".$opts['cancel_url']."';</script>");
              return ;
             }
            // print_r($opts);

            // echo "Payment Failed";
               $this->handle_failed_payment($Order, null, $opts);

               return ;
         }
    			// Process response
		if ($response->isSuccessful()) {

        //old strpe
        if(method_exists($response, 'getPaymentIntentReference') && $response->getPaymentIntentReference()!=null){

        $paymentIntentReference = $response->getPaymentIntentReference();
        }else{

          $paymentIntentReference =$response->getTransactionReference();
        }


			 $Order->set_transaction_reference($paymentIntentReference);

		    // Payment was successful
		    PerchUtil::debug('Payment successful');

            if($this->handle_successful_payment($Order, $response, $opts)){
                if (isset($opts['success_url'])) {
                      // PerchUtil::hold_redirects();
                        PerchUtil::redirect($opts['success_url']);
                    }
                 //   echo "Payment successful";
                            if (isset($opts['return_url'])) {
                        echo "<script>window.location.href = '".$opts['return_url']."?confirm=false&payment_intent=".$paymentIntentReference."'</script>";
                            exit;
//PerchUtil::redirect($opts['return_url']."?confirm=false&payment_intent=".$paymentIntentReference);
                            }
            }

		    return ;


		} elseif ($response->isRedirect()) {

            if( isset($opts['confirm'])){
		        $paymentIntentReference = $response->getPaymentIntentReference();

			    $Order->set_transaction_reference($paymentIntentReference);//$response->getTransactionReference());
			}else{


            			$Order->set_transaction_reference($response->getTransactionReference());
			}

			$this->store_data_before_redirect($Order, $response, $opts);

		    // Redirect to offsite payment gateway
		    PerchUtil::debug('Payment redirect response');
		    PerchUtil::debug($response);

		    //if (!PerchUtil::get_hold_redirects()) {
	          $response->redirect();
		   // }



		} else {

		    // Payment failed
		    PerchUtil::debug('Payment failed', 'error');
		    PerchUtil::debug($response,  'error');
		    return $this->handle_failed_payment($Order, $response, $opts);
		}
	}

	public function confirm_payment($Order, $opts, $gateway_opts){
           // echo "confirm_payment ";
		$payment_opts = [
		        'amount'   => $Order->orderTotal(),
		        'currency' => $Order->get_currency_code(),
		    ];
        $payment_opts = array_merge($payment_opts, $gateway_opts);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);
		if(isset($opts["confirm"]) && $opts["confirm"]=="false"){

            $Order->finalize_as_paid();

			if (isset($opts['success_url'])) {
                $orderid=(string)$Order->id();
                                   // PerchUtil::redirect($opts['success_url']);
                    echo("<script>location.href = '".$opts['success_url']."?order=".$orderid."';</script>");

                                    return ;
                                }

		}else{

		$config = PerchShop_Config::get('gateways', $this->slug);

        		$Omnipay = Omnipay::create($this->omnipay_name.'\PaymentIntents');
        		$this->set_credentials($Omnipay, $config);

        		$payment_method = 'confirm';//$this->completion_method;


        	//	$response = $Omnipay->$payment_method($opts)->send();
                $response = $Omnipay->confirm([
                            'paymentIntentReference' => $opts['payment_intent'],
                            'returnUrl' =>$opts['return_url'],
                             'capture_method'=> 'automatic'
                 ])->send();

                // echo "confirm";print_r($response);
                // echo "getCaptureMethod"; echo $response->getCaptureMethod();
                if($response->getCaptureMethod()=='manual'){

        	       $response_capture = $Omnipay->capture([
                            'paymentIntentReference' => $opts['payment_intent']
                        ])->send();
            //echo "response_capture in"; print_r($response_capture );
        		// Process response
        	   if ($response_capture->isSuccessful()) {

        	    PerchUtil::debug('Payment successful');
            	$Order->update(['orderGatewayRef'=>$response->getTransactionReference()]);
            	//echo "Order in"; print_r($Order );echo "opts in"; print_r($opts );

            	$Order->finalize_as_paid();
                //$successresult= $this->handle_successful_payment($Order, $response, $opts);


                if (isset($opts['success_url'])) {

                                                   // PerchUtil::redirect($opts['success_url']);
                                    echo("<script>location.href = '".$opts['success_url']."';</script>");

                                                    return ;
                                                }

        		} else {
                  $successresult=  $this->handle_failed_payment($Order, $response_capture, $gateway_opts);
        		    // Payment failed
        		    PerchUtil::debug('Payment failed', 'error');

        		       if (isset($opts['cancel_url'])) {

                                                                       // PerchUtil::redirect($opts['success_url']);
                                                        echo("<script>location.href = '".$opts['cancel_url']."';</script>");

                                                                        return ;
                                                                    }

        		}
		}


    }


		return false;
	}

	public function complete_paymentold($Order, $opts)
	{
if($opts["confirm"]){

		$payment_opts = [
		        'amount'   => $Order->orderTotal(),
		        'currency' => $Order->get_currency_code(),
		    ];

		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);

		$config = PerchShop_Config::get('gateways', $this->slug);

		$Omnipay = Omnipay::create($this->omnipay_name);
		$this->set_credentials($Omnipay, $config);

		$payment_method = $this->completion_method;


		$response = $Omnipay->$payment_method($opts)->send();


		// Process response
		if ($response->isSuccessful()) {

		    // Payment was successful
		    PerchUtil::debug('Payment successful');
		    $Order->update(['orderGatewayRef'=>$response->getTransactionReference()]);
		    return $this->handle_successful_payment($Order, $response, $opts);

		} elseif ($response->isRedirect()) {

			$Order->set_transaction_reference($response->getTransactionReference());
			$this->store_data_before_redirect($Order, $response, $opts);

		    // Redirect to offsite payment gateway
		    PerchUtil::debug('Payment redirect response');
		   // $response->redirect();

		} else {

		    // Payment failed
		    PerchUtil::debug('Payment failed', 'error');
		    PerchUtil::debug($response, 'error');
		    return $this->handle_failed_payment($Order, $response, $opts);
		}
}
		return false;
	}

	public function get_api_key($config)
	{

		if ($config['test_mode']) {
			return 'Bearer '.$config['test']['api_key'];
		}
		return 'Bearer '.$config['live']['api_key'];
	}

	public function get_public_api_key($config)
	{
		return false;
	}

	public function format_payment_options(PerchShop_Order $Order, array $opts)
	{
		return $opts;
	}

	public function produce_payment_response(array $args, array $gateway_opts)
	{
		return;
	}

	public function get_order_from_env($Orders, $get, $post)
	{
		return false;
	}

	public function callback_looks_valid($get, $post)
	{
		return false;
	}

	public function get_callback_args($get, $post)
	{
		return $get;
	}

	public function action_payment_callback($Order, $args, $opts)
	{
return true;
	}

	public function finalize_as_paid($Order)
	{
		return true;
	}

	public function handle_successful_payment($Order, $response, $gateway_opts)
	{
		$Order->finalize_as_paid();

		return $response;
	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{   //echo "handle_failed_payment";
		$Order->set_status('payment_failed');
		echo $response->getMessage();
		 if (isset($gateway_opts['cancel_url'])) {

                                                                               // PerchUtil::redirect($opts['success_url']);
                                                                echo("<script>location.href = '".$gateway_opts['cancel_url']."';</script>");

                                                                                return ;
                                                                            }
		return false;
	}

	public function set_credentials(&$Omnipay, $config)
	{
		$api_key = $this->get_api_key($config);

		if ($api_key) {
			$Omnipay->setApiKey($api_key);
		}
	}

	public function store_data_before_redirect($Order, $response, $opts)
	{

	}

	public function get_card_address($Order)
	{
		return false;
	}

	public function get_omnipay_intents_instance()
    {

    	$config = PerchShop_Config::get('gateways', $this->slug);
    	$Omnipay = Omnipay::create($this->omnipay_name.'\PaymentIntents');
    	$this->set_credentials($Omnipay, $config);

    	return $Omnipay;
    }

	public function get_omnipay_instance()
	{

		$config = PerchShop_Config::get('gateways', $this->slug);
		$Omnipay = Omnipay::create($this->omnipay_name);
		$this->set_credentials($Omnipay, $config);
		return $Omnipay;
	}

	public function get_transaction_data($Order)
	{  //echo "get_transaction_data"; print_r($Order);
	if (strpos($Order->orderGatewayRef(), 'pi_') === 0) {
               // It starts with 'pi'
              return $this->get_payment_intent_data($Order->orderGatewayRef());

            }else{
		$Omnipay = $this->get_omnipay_instance();

		$transaction = $Omnipay->fetchTransaction();
		$transaction->setTransactionReference($Order->orderGatewayRef());
		$response 	= $transaction->send();
		return $response->getData();
		}
	}

	public function get_payment_card($Order)
	{
		$Customers = new PerchShop_Customers($this->api);
        $Customer = $Customers->find($Order->customerID());

        $Addresses = new PerchShop_Addresses($this->api);

        $ShippingAddr = $Addresses->find((int)$Order->orderShippingAddress());
        $BillingAddr  = $Addresses->find((int)$Order->orderBillingAddress());

		$data = [
			'firstName'        => $Customer->customerFirstName(),
			'lastName'         => $Customer->customerLastName(),
			'billingAddress1'  => $BillingAddr->get('address_1'),
			'billingAddress2'  => $BillingAddr->get('address_2'),
			'billingCity'      => $BillingAddr->get('city'),
			'billingPostcode'  => $BillingAddr->get('postcode'),
			'billingState'     => $BillingAddr->get('county'),
			'billingCountry'   => $BillingAddr->get_country_iso2(),
			'shippingAddress1' => $ShippingAddr->get('address_1'),
			'shippingAddress2' => $ShippingAddr->get('address_2'),
			'shippingCity'     => $ShippingAddr->get('city'),
			'shippingPostcode' => $ShippingAddr->get('postcode'),
			'shippingState'    => $ShippingAddr->get('county'),
			'shippingCountry'  => $ShippingAddr->get_country_iso2(),
			'company'		   => $BillingAddr->get('addressCompany'),
			'email'            => $Customer->customerEmail(),
		];

		$card = new CreditCard($data);

		return $card;
	}

	public function get_exchange_rate($Order)
	{
		return null;
	}
}
