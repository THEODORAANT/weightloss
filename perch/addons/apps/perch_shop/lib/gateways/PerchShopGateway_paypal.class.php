<?php
class PerchShopGateway_paypal extends PerchShopGateway_default
{

	public function complete_payment($order_id, $gateway_opts=array())
	{
	echo "complete";

  $config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
    $paymentId = $_GET['paymentId'];
    $payerId = $_GET['PayerID'];

    $accessToken = $this->getPayPalAccessToken();

    $url = $api_url . "/v1/payments/payment/$paymentId/execute";

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ];

    $data = [
        'payer_id' => $payerId,
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    $data = json_decode($response);

    if (isset($data->state) && $data->state == 'approved') {
        echo 'Payment successfully completed!';
    } else {
        echo 'Payment execution failed!';
    }
}


		$Orders = new PerchShop_Orders($this->api);
		$Order  = false;
		echo "callback_looks_valid";print_r($this->callback_looks_valid($details));


		if ($this->callback_looks_valid($details)) {
			$Order = $this->get_order_from_env($Orders,"", $details);
			echo "order";
			print_r($Order );

			if ($Order) {
			    if($Order->orderStatus()!="paid"){
				$this->Order = $Order;
				 // echo "subpayment_methodr"; echo isset( $details["payment_method"]);
				// echo "id"; echo isset($details['payments']);	print_r($details['payments'][0] );


				$result = $this->action_payment_callback($Order, $details, $gateway_opts);

				if ($result) {
					PerchUtil::debug('Completing order');
					return $Order->complete_payment($args, $gateway_opts);
				}else{
					return $result;
				}
				}else{
                 				return [
                 					'status' => 'error',
                 					'message' => 'Order already completed.',
                 				];
                 			}
			}else{
				return [
					'status' => 'error',
					'message' => 'Order not found.',
				];
			}
		}else{
			return [
				'status' => 'error',
				'message' => 'Invalid callback.',
			];
		}
	}
	public function handle_successful_payment($Order, $response, $gateway_opts)
	{

		$Order->finalize_as_paid();


        return true;


	}

	public function handle_failed_payment($Order, $response, $gateway_opts)
	{
		$Order->set_status('payment_failed');

		if (isset($gateway_opts['cancel_url'])) {
			PerchUtil::redirect($gateway_opts['cancel_url']);
		}
	}

	public function get_api_url($config)
	{

		if ($config['test_mode'] ) {
			return 'https://api.sandbox.paypal.com';
		}
		return '';
	}
	public function get_api_key($config)
	{

		if ($config['test_mode'] ) {
			return $config['test']['client_secret'];
		}
		return $config['live']['client_secret'];
	}

	public function get_public_api_key($config)
	{
		if ($config['test_mode']) {
			return $config['test']['client_id'];
		}
		return $config['live']['client_id'];
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
		public function get_revolut_order_details( $order_id)
        	{

    $config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $api_url.'/orders/'.$order_id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Authorization: Bearer '.$api_key
  ),
));

$response = curl_exec($curl);
  $responserev=json_decode($response,true) ;
curl_close($curl);
return $responserev;

    }
function getPayPalAccessToken() {

 $config = PerchShop_Config::get('gateways', $this->slug);

		$api_key = $this->get_api_key($config);

		$api_url = $this->get_api_url($config);
		 $url = $api_url . '/v1/oauth2/token';
            $clientId =$this->get_public_api_key($config);
            $clientSecret = $api_key;
		echo "api_url"; echo $api_url ;
    $headers = [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    $data = json_decode($response);
    return $data->access_token;
}

	public function take_payment($Order, $opts)
	{


    $payment_opts = [
        'intent' => 'sale',
        'payer' => [
            'payment_method' => 'paypal',
        ],
        'transactions' => [
            [
                'amount' => [
                    'total' => $Order->orderTotal(),//'10.00', // Total amount to be charged
                    'currency' => $Order->get_currency_code(), // Currency
                ],
                'description' => 'Payment description-Order #'.$Order->id(),
            ],
        ],
        'redirect_urls' => [
            'return_url' => '', // After payment success
            'cancel_url' => '', // If payment is canceled
        ],
    ];

        $config = PerchShop_Config::get('gateways', $this->slug);
		$opts = array_merge($opts, $payment_opts);

		$opts = $this->format_payment_options($Order, $opts);


    $config = PerchShop_Config::get('gateways', $this->slug);

		$api_key = $this->get_api_key($config);
		$api_url = $this->get_api_url($config);
		    $url = $api_url . '/v1/payments/payment';
		    $accessToken = $this->getPayPalAccessToken();


		echo "api_url"; echo $api_url ;
try{

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>   json_encode($payment_opts),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
      'Authorization: Bearer ' . $accessToken,
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
 $data = json_decode($response);

    if (isset($data->links)) {
        foreach ($data->links as $link) {
            if ($link->rel == 'approval_url') {
                // Redirect user to PayPal for approval
                header("Location: " . $link->href);
                exit();
            }
        }
    } else {
        echo 'Error: Payment creation failed!';
    }

}catch (Exception $e) {
 			print_r($e->getMessage());

}


 return ;
}
	/*public function get_exchange_rate($Order)
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
	}*/

	private function init_native_stripe_api()
	{
		$config = PerchShop_Config::get('gateways', $this->slug);
		$api_key = $this->get_api_key($config);

		\Stripe\Stripe::setApiKey($api_key);
	}

	public function get_order_from_env($Orders, $get, $post)
    	{
    		if (isset($post['id'])) {
    			return $Orders->get_one_by('orderGatewayRef', $post['id']);
    		}
    	}

	public function callback_looks_valid($get=array(), $post=array())
	{
		if (isset($post['id']) or isset($get['id'])) {
			return true;
		}
		return false;
	}

	public function action_payment_callback($Order, $args, $gateway_opts)
    {

    	$result = $Order->finalize_as_paid();
    }
}
