<?php

class PerchShop_Runtime
{
	private static $instance;

	private $api                  = null;
	private $cart_id              = null;
	private $Cart                 = null;
	public $cart_items            = [];
	public $order_items           = [];
	public $Cache                 = null;
	
	public $Order                 = null;
	
	public  $location_set_by_user = false;
	
	private $currencyID           = null;
	private $Currency             = null;
	private $taxLocationID        = null;
	
	private $shippingAddress      = null;
	private $billingAddress       = null;
	private $shippingID			  = null;
	
	
	private $sale_enabled         = false;
	private $trade_enabled        = false;

	public static function fetch()
	{
		if (!isset(self::$instance)) self::$instance = new PerchShop_Runtime;
        return self::$instance;
	}

	public function __construct()
	{
		$this->api = new PerchAPI(1.0, 'perch_shop');
		$this->Cache = PerchShop_Cache::fetch();

		$this->init_currency_id();
	}

	public function reset_after_logout()
	{
		$this->cart_id               = null;
		$this->Cart                  = null;
		$this->cart_items            = [];
		$this->order_items           = [];
		
		$this->Order                 = null;
		
		$this->location_set_by_user  = false;
		
		$this->currencyID            = null;
		$this->Currency              = null;
		$this->taxLocationID         = null;
		
		$this->shippingAddress       = null;
		$this->billingAddress        = null;
		$this->shippingID            = null;
		
		
		$this->sale_enabled          = false;
		$this->trade_enabled         = false;

		$this->init_currency_id();
	}

	private function session_is_active()
	{
		return session_status() === PHP_SESSION_ACTIVE;
	}

	private function init_currency_id()
	{
		if ($this->session_is_active()) {
			$this->init_cart();
			$this->currencyID = (int) $this->Cart->get_cart_field('currencyID');
		}

		if (!$this->currencyID) {
			// currency defaults
			$Settings = $this->api->get('Settings');
			$this->currencyID = (int) $Settings->get('perch_shop_default_currency')->val();
		}
	}

	public function get_currency_id()
	{
		return $this->currencyID;
	}

	public function get_currency()
	{
		if ($this->Currency) {
			return $this->Currency;
		}

		$Currencies = new PerchShop_Currencies($this->api);
		$this->Currency = $Currencies->find($this->currencyID);
		return $this->Currency;
	}

	public function sale_enabled()
	{
		return $this->sale_enabled;
	}

	public function activate_sales()
	{
		$Sales = new PerchShop_Sales($this->api);
		$active_sales = $Sales->get_currently_active();
		if (PerchUtil::count($active_sales)) {
			$this->enable_sale_pricing();
		}
	}

	public function enable_sale_pricing()
	{
		$this->init_cart();
		if ($this->Cart->get_pricing_mode()!='sale') {
			$this->Cache->expire_like('cart.');
			$this->Cart->set_pricing_mode('sale');
		}
	
		$this->sale_enabled = true;
	}

	public function trade_enabled()
	{
		if (!PERCH_RUNWAY) return false;

		return $this->trade_enabled;
	}

	public function enable_trade_pricing()
	{
		if (PERCH_RUNWAY) {
			$this->init_cart();
			if ($this->Cart->get_pricing_mode()!='trade') {
				$this->Cache->expire_like('cart.');
				$this->Cart->set_pricing_mode('trade');
			}
			$this->trade_enabled = true;
		}
	}

	public function get_custom($class, $opts=array())
	{
		$c = 'PerchShop_'.$class;
		$Factory = new $c($this->api);

		if (isset($opts['template'])) {
			$opts['template'] = 'shop/'.$opts['template'];
		}

		$where_callback 	  = (is_callable([$Factory, 'standard_where_callback']) ? [$Factory, 'standard_where_callback'] : null);
		$pretemplate_callback = (is_callable([$Factory, 'runtime_pretemplate_callback']) ? [$Factory, 'runtime_pretemplate_callback'] : null);

		$r = $Factory->get_filtered_listing($opts, $where_callback, $pretemplate_callback);

		return $r;
	}
public function add_cart_api($member_id,$product, $qty=1,$cart_id=0, $replace=false)
                            	{
                            	return $this->Cart->add_cart_api($member_id,$product, $qty,$cart_id, $replace);
                            	}
	public function add_to_cart($product, $qty=1, $replace=false)
	{
		$this->init_cart();
		$this->Cache->expire_like('cart.');
		$this->Cart->add_to_cart($product, $qty, $replace);
	}

	public function add_to_cart_from_form($SubmittedForm)
	{
		$this->init_cart();

		$product   = null;
		$qty       = 1;
		$discount_code  = null;

		if (isset($SubmittedForm->data['product']))   		$product = $SubmittedForm->data['product'];
		if (isset($SubmittedForm->data['qty']))  	  		$qty = $SubmittedForm->data['qty'];
		if (isset($SubmittedForm->data['discount_code']))  	$discount_code = $SubmittedForm->data['discount_code'];

		// find options
		
		if (PerchUtil::count($SubmittedForm->data)) {
			$options = [];
			foreach($SubmittedForm->data as $key=>$value) {
				if (substr($key, 0, 4)=='opt-') {
					$options[substr($key, 4)] = $value;
				}
			}
			if (count($options)) {
				// find the product variant that matches these options
				$Products = new PerchShop_Products($this->api);
				$Product = $Products->find_from_options($product, $options);
				if ($Product) {
					$product = $Product->id();
				}
			}
		}

		$this->Cache->expire_like('cart.');

		$this->Cart->add_to_cart($product, $qty);

		if ($discount_code) {
			$this->Cart->set_discount_code($discount_code);
		}

		$this->Cart->stash_data($SubmittedForm->data);
	}

	public function set_discount_code($code)
	{
		$this->init_cart();
		$this->Cache->expire_like('cart.');
		$this->Cart->set_discount_code($code);
	}

	public function set_discount_code_from_form($SubmittedForm)
	{
		$this->init_cart();

		$discount_code  = null;

		if (isset($SubmittedForm->data['discount_code']))  $discount_code = $SubmittedForm->data['discount_code'];

		$this->Cache->expire_like('cart.');

		if ($discount_code) {
			$this->Cart->set_discount_code($discount_code);
		}
	}

	public function set_addresses_from_form($SubmittedForm)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		if (isset($SubmittedForm->data['billing'])) {
			$this->billingAddress   = $SubmittedForm->data['billing'];
			// also set this in case no shipping address is needed. Overridden below.
			$this->shippingAddress  = $SubmittedForm->data['billing'];
		}  
		if (isset($SubmittedForm->data['shipping'])) $this->shippingAddress = $SubmittedForm->data['shipping'];

		$this->Cart->set_addresses($this->billingAddress, $this->shippingAddress);

		$this->set_location_from_address($this->billingAddress);
	}
public function set_addresses_api($memberID,$billingAddress, $shippingAddress=null)
	{

		if ($shippingAddress===null) {
			$shippingAddress = $billingAddress;
		}

		$this->billingAddress   = $billingAddress;
		$this->shippingAddress  = $shippingAddress;

		//$this->Cart->set_addresses($this->billingAddress, $this->shippingAddress);

		//$this->set_location_from_address($this->billingAddress);
				/*if ($memberID) {
        			$Customer = $this->get_customer($memberID);
        			$Addresses = new PerchShop_Addresses($this->api);
        			$Address = $Addresses->find_for_customer($Customer->id(), $addressSlug);

        			if ($Address) {
        				$Locations = new PerchShop_TaxLocations($this->api);
        				$Location = $Locations->find_matching($Address->countryID(), $Address->regionID());

        				if ($Location) {
        					$this->taxLocationID = (int)$Location->id();
        					$this->Cart->set_location($Location->id());
        				}
        			}
        		}*/
	}
	public function set_addresses($billingAddress, $shippingAddress=null)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		if ($shippingAddress===null) {
			$shippingAddress = $billingAddress;
		}

		$this->billingAddress   = $billingAddress;
		$this->shippingAddress  = $shippingAddress;

		$this->Cart->set_addresses($this->billingAddress, $this->shippingAddress);

		$this->set_location_from_address($this->billingAddress);
	}

	public function set_location_from_address($addressSlug='default', $skip_if_set=false)
	{
		if (trim($addressSlug) == '') {
			$addressSlug = 'default';
		}

		PerchUtil::mark('setting loc from adr '.$addressSlug);
		PerchUtil::debug($addressSlug);

		if ($skip_if_set && $this->taxLocationID) {
			return;
		}
		

		$memberID = perch_member_get('memberID');
		if ($memberID) {
			$Customer = $this->get_customer($memberID);
			$Addresses = new PerchShop_Addresses($this->api);
			$Address = $Addresses->find_for_customer($Customer->id(), $addressSlug);

			if ($Address) {
				$Locations = new PerchShop_TaxLocations($this->api);
				$Location = $Locations->find_matching($Address->countryID(), $Address->regionID());

				if ($Location) {
					$this->taxLocationID = (int)$Location->id();
					$this->Cart->set_location($Location->id());		
				}	
			}
		}
	}

	public function location_is_set()
	{
		return boolval($this->taxLocationID);
	}

	public function get_addresses_for_template()
	{
		$Addresses = new PerchShop_Addresses($this->api);
		$Customer = $this->get_customer();
		if (!$Customer) return false;

		return $Addresses->get_for_customer($Customer->id());
	}

	public function get_address_by_id($id)
	{
		$Customer = $this->get_customer();
		if (!$Customer) return false;


		$Addresses = new PerchShop_Addresses($this->api);
		return $Addresses->find_for_customer_by_id($Customer->id(), $id);
	}

        public function edit_address_from_form($SubmittedForm)
        {
                $Customer = $this->get_customer();
                if (!$Customer) return false;

                $Addresses = new PerchShop_Addresses($this->api);
                $Address   = false;

                $normalised_shipping = $this->normalise_shipping_data($SubmittedForm->data);

                foreach($normalised_shipping as $field=>$value) {
                        $SubmittedForm->data[$field] = $value;
                }

                if (isset($SubmittedForm->data['addressID']) && $SubmittedForm->data['addressID'] > 0) {
                        $id = (int)$SubmittedForm->data['addressID'];
                        $Address = $Addresses->find_for_customer_by_id($Customer->id(), $id);
                }

                if ($Address instanceof PerchShop_Address) {
                        PerchUtil::debug($SubmittedForm->data);
                        $Address->intelliupdate($SubmittedForm->data);
                        $Address = $Addresses->find_for_customer_by_id($Customer->id(), $Address->id());
                }else{
                        $data = $SubmittedForm->data;
                        if (isset($data['address_1']) && $data['address_1']!='') {
                                $data['customer'] = $Customer->id();
                                $data['title'] = substr($data['address_1'], 0, 24);
                                $Address = $Addresses->intellicreate($data);
                        }

                }

                if ($Address instanceof PerchShop_Address && $Address->addressSlug() === 'shipping') {
                        $this->update_member_shipping_profile_from_address($Address, $Customer->memberID());
                }

                $this->set_location_from_address('default');
        }


	public function set_cart_properties_from_form($SubmittedForm)
	{
		$attr_map = $SubmittedForm->get_attribute_map('cart-property');
		
		if (PerchUtil::count($attr_map)) {

			$props = [];

			foreach($attr_map as $fieldID=>$property) {
				if (isset($SubmittedForm->data[$fieldID])) {
					if ($SubmittedForm->data[$fieldID]!='') {
						$props[$property] = $SubmittedForm->data[$fieldID];	
					} else {
						$props[$property] = null;
					}
				}
			}

			if (PerchUtil::count($props)) {
				if (!$this->Cart) $this->init_cart();
				$this->Cart->set_properties($props);
			}
		}
	}

	public function apply_discount_to_cart($code)
	{
		$this->init_cart();
		$this->Cart->apply_discount($code);
	}


	public function init_cart()
	{
		if (!$this->cart_id) {
			$this->Cart = new PerchShop_Cart($this->api);
			$this->cart_id = $this->Cart->init();

			if ($this->location_set_by_user) {
				$this->Cart->set_location($this->taxLocationID);
			} else {
				$this->taxLocationID = (int) $this->Cart->get_cart_field('locationID');
				if (!$this->taxLocationID) {
					$this->set_location_from_address('default');	
				}
				
			}

			switch($this->Cart->get_pricing_mode()) {
				case 'sale':
					$this->sale_enabled = true;
					break;

				case 'trade':
					if (PERCH_RUNWAY) $this->trade_enabled = true;
					break;
			}
		}
	}

	public function get_shipping_options($opts=array())
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->get_shipping_options($opts, $this->Cache);
	}

	public function get_shipping_list_options($opts=array())
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->get_shipping_list_options($opts, $this->Cache);
	}


	public function get_cart($opts=array())
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->get_cart($opts, $this->Cache);
	}

	public function get_cart_for_api($opts=array())
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->get_cart_for_api($opts, $this->Cache);
	}

	public function get_cart_val($property='total', $opts=array(), $default_opts=array())
	{
		if ($this->session_is_active()) {
			$opts = PerchUtil::extend($default_opts, $opts);

			if (isset($opts['template'])) {
				$opts['template'] = 'shop/'.$opts['template'];
			}
			if (!$this->Cart) $this->init_cart();
			return $this->Cart->get_cart_val($property, $opts, $this->Cache);
		}
		
		return false;
	}

	public function get_cart_property($prop)
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->get_property($prop);
	}

	public function set_cart_property($prop, $val)
	{
		if (!$this->Cart) $this->init_cart();
		return $this->Cart->set_property($prop, $val);
	}

	public function get_cart_has_property($prop)
	{
		if (!$this->Cart) $this->init_cart();
		if ($this->Cart->get_property($prop)!==null) {
			return true;
		}

		return false;
	}

	public function empty_cart()
	{
		$this->init_cart();
		$this->Cart->destroy($this->cart_id);
		$this->cart_id = false;
		$this->Cart = false;
		$this->Cache->expire_like('cart.');
	}

	public function update_cart_from_form($SubmittedForm)
	{
		$this->Cache->expire_like('cart.');

		$discount_code  = null;
		if (isset($SubmittedForm->data['discount_code']))  $discount_code = $SubmittedForm->data['discount_code'];

		$this->init_cart();

		if ($discount_code) {
			$this->Cart->set_discount_code($discount_code);
		}

		$this->Cart->update_from_form($SubmittedForm);
	}

	public function set_location($locationID)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		$this->taxLocationID = (int)$locationID;

		$this->Cart->set_location($locationID);
	}

	public function set_location_from_form($SubmittedForm)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		$this->taxLocationID = (int)$SubmittedForm->data['location'];

		$this->Cart->set_location($SubmittedForm->data['location']);
	}

	public function set_shipping_method_from_form($SubmittedForm)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		$this->shippingID = (int)$SubmittedForm->data['shipping'];

		$this->Cart->set_shipping($SubmittedForm->data['shipping']);
	}

	public function set_currency_from_form($SubmittedForm)
	{
		$this->Cache->expire_like('cart.');

		$this->init_cart();

		$this->currencyID = (int)$SubmittedForm->data['currency'];
		$this->Currency = false;

		$this->Cart->set_currency($SubmittedForm->data['currency']);
	}

	public function set_currency($currencyCode)
	{
		$Currencies = new PerchShop_Currencies($this->api);
		$Currency = $Currencies->find_by_code($currencyCode);

		if ($Currency) {
			$this->Cache->expire_like('cart.');

			$this->init_cart();

			$this->currencyID = (int)$Currency->id();
			$this->Currency = $Currency;

			$this->Cart->set_currency($Currency->id());

			return true;
		}

		return false;
	}

	public function get_addresses()
	{
		$this->init_cart();
		
		$memberID = perch_member_get('memberID');

		$Customer = $this->get_customer($memberID);

		if ($this->addresses_are_set()) {
			$BillingAddress   = $this->get_address($Customer, $this->billingAddress);
			$ShippingAddress  = $this->get_address($Customer, $this->shippingAddress);	
		}else{
			$BillingAddress   = $this->get_address($Customer, 'default');
			$ShippingAddress  = $this->get_address($Customer, 'shipping');
			if (!$ShippingAddress) $ShippingAddress = $BillingAddress;
		}

		return [$BillingAddress, $ShippingAddress];
	}
	public function order_booking($orderID, $opts=[])
	{
	$memberID = perch_member_get('memberID');
    		PerchUtil::debug('Member ID: '.$memberID);

    		$Customer = $this->get_customer($memberID);

	}

	public function confirmPayment_for_api($memberID,$paymentIntentId,$gateway="stripe") {

	   $Gateway = PerchShop_Gateways::get($gateway);
    	$paymentIntent= $Gateway->get_payment_intent_data($paymentIntentId);
    	$Orders = new PerchShop_Orders($this->api);
 $status = $paymentIntent['status'] ?? 'unknown';
    	if ($status === 'succeeded') {
    	$Order=$Orders->get_one_by('orderGatewayRef', $paymentIntentId);

    		$Order->finalize_as_paid();
    		}
    	return $paymentIntent;
	}

	public function  createPaymentIntent_for_api($memberID,$payment_method,$order_id){

	   $Gateway = PerchShop_Gateways::get("stripe");
        	$details= $Gateway->createPaymentIntent($memberID,$payment_method,$order_id);
        	return $details;
	}
		public function checkout_api($memberID,$cartID,$gateway, $payment_opts=[])
    	{

	       	$Cart=new PerchShop_Cart($this->api);
			$Cart=$Cart->init_api($cartID,$memberID);
			if($Cart){


		    $Customer = $this->get_customer($memberID);
//echo "Customer";print_r($Customer->id());
			$BillingAddress   = $this->get_address($Customer, 'default');
			$Addresses = new PerchShop_Addresses($this->api);

            		if ($Customer) {
            		//$ShippingAddress  = $this->get_address($Customer, 'shipping');
            			$ShippingAddress = $Addresses->find_for_customer($Customer->id(), 'shipping');
//echo "ShippingAddress";print_r($ShippingAddress);
            			$BillingAddress=$ShippingAddress;
            		}


		//	$ShippingAddress  = $this->get_address($Customer, 'shipping');

			//if (!$ShippingAddress) $ShippingAddress = $BillingAddress;

		if ($Customer && $BillingAddress) {

	$Orders = new PerchShop_Orders($this->api);

			$Order  = $Orders->create_from_cart($Cart, $gateway, $Customer, $BillingAddress, $ShippingAddress,true);

			if ($Order) {
				// Mark cart as completed so it is not reused
				$db = PerchDB::fetch();
				$db->update(PERCH_DB_PREFIX.'shop_cart', ['cartCompleted' => 1], 'cartID', $cartID);
				return  $Order->id() ;
			}

		}else{
		echo "Customer or Address or Shipping missing";
			PerchUtil::debug('Customer or Address or Shipping missing', 'error');
			return false;
		}
		}else{ echo 'no matching cart';
         			PerchUtil::debug('no matching cart', 'error');
         			return false;
         		}
    	}
public function checkout_questionnaire($orderIdForQuestionnaire)
	{ echo "checkout_questionnaire"; echo $orderIdForQuestionnaire;
	echo "SESSION";print_r($_SESSION);
if (empty($_SESSION['questionnaire_saved']) && $orderIdForQuestionnaire) {
    if (isset($_SESSION['questionnaire-reorder']) && !empty($_SESSION['questionnaire-reorder'])) {
        unset($_SESSION['questionnaire-reorder']['nextstep']);

        perch_member_add_questionnaire($_SESSION['questionnaire-reorder'], 're-order', $orderIdForQuestionnaire);
        $_SESSION['questionnaire_saved'] = true;
    }

    if (isset($_SESSION['questionnaire']) && !isset($_SESSION['questionnaire-reorder']["dose"])) {
        $userId = $_SESSION['step_data']['user_id'];
        $metadata = [
            'user_id'    => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'registered' => date('Y-m-d H:i:s')
        ];
        $logDir = '/var/www/html/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        if (!is_dir($logDir) && !mkdir($logDir, 0755, true)) {
            die("Failed to create log directory: $logDir");
        }

        $_SESSION['questionnaire']["multiple_answers"] = "No";

        if (isset($_SESSION['answer_log'])) {
            $rawLog = is_array($_SESSION['answer_log']) ? $_SESSION['answer_log'] : [];

            if (file_put_contents("{$logDir}/{$userId}_raw_log.json", json_encode([
                'metadata' => $metadata,
                'log' => $rawLog
            ], JSON_PRETTY_PRINT)) === false) {
                die("Failed to write log file.");
            }

            $summary = perch_members_summarise_answer_log($rawLog);
            $grouped = $summary['grouped'];

            if (!empty($summary['has_changes'])) {
                $_SESSION['questionnaire']["multiple_answers"] = "Yes-" . "https://" . $_SERVER['HTTP_HOST'] . "/perch/addons/apps/perch_members/questionnaire_logs/?userId=" . $userId;
            }
            $_SESSION['questionnaire']["documents"] = "https://" . $_SERVER['HTTP_HOST'] . "/perch/addons/apps/perch_members/edit/?id=" . perch_member_get('id');
            //print_r( $_SESSION['questionnaire']);
            perch_member_add_questionnaire($_SESSION['questionnaire'], 'first-order', $orderIdForQuestionnaire);

            if (file_put_contents("{$logDir}/{$userId}_grouped_log.json", json_encode([
                'metadata' => $metadata,
                'grouped_log' => $grouped
            ], JSON_PRETTY_PRINT)) === false) {
                die("Failed to write log file.");
            }
            // Optional: clear the session log
            unset($_SESSION['answer_log']);
        }

        $_SESSION['questionnaire_saved'] = true;
    }
}
	}


	public function get_cart_api($memberID)
	{
		$db = PerchDB::fetch();
		$sql = 'SELECT cartID FROM '.PERCH_DB_PREFIX.'shop_cart WHERE memberID='.$db->pdb((int)$memberID).' AND cartCompleted=0 ORDER BY cartID DESC LIMIT 1';
		$cart_id = $db->get_value($sql);

		if (!$cart_id) {
			return ['cart_id' => null, 'items' => []];
		}

		$Cart = new PerchShop_Cart($this->api);
		$data = $Cart->calculate_cart_for_api((int)$cart_id);

		return ['cart_id' => (string)$cart_id, 'raw_items' => isset($data['items']) ? $data['items'] : []];
	}

	public function clear_cart_api($memberID)
	{
		$db = PerchDB::fetch();
		$sql = 'SELECT cartID FROM '.PERCH_DB_PREFIX.'shop_cart WHERE memberID='.$db->pdb((int)$memberID).' AND cartCompleted=0 ORDER BY cartID DESC LIMIT 1';
		$cart_id = $db->get_value($sql);

		if ($cart_id) {
			$db->update(PERCH_DB_PREFIX.'shop_cart', ['cartCompleted' => 1], 'cartID', (int)$cart_id);
		}

		return true;
	}

	public function checkout($gateway, $payment_opts=[])
	{
		$this->init_cart();
		PerchUtil::debug('Checking out with '.$gateway);

		$memberID = perch_member_get('memberID');
		PerchUtil::debug('Member ID: '.$memberID);

		$Customer = $this->get_customer($memberID);

		if ($this->addresses_are_set()) {
			$BillingAddress   = $this->get_address($Customer, $this->billingAddress);
			$ShippingAddress  = $this->get_address($Customer, $this->shippingAddress);
		}else{
			$BillingAddress   = $this->get_address($Customer, 'default');
			$ShippingAddress  = $this->get_address($Customer, 'shipping');
			if (!$ShippingAddress) $ShippingAddress = $BillingAddress;
		}

		$Orders = new PerchShop_Orders($this->api);


		if ($Customer && $BillingAddress) {

			$Order  = $Orders->create_from_cart($this->Cart, $gateway, $Customer, $BillingAddress, $ShippingAddress);

			if ($Order) {
				$this->Order = $Order;
                  $this->checkout_questionnaire($Order->id());
				PerchShop_Session::set('shop_order_id', $Order->id());

				$Gateway = PerchShop_Gateways::get($gateway);

				$result = $Order->take_payment($Gateway->payment_method, $payment_opts);


				PerchUtil::debug($result);
			}

		}else{
			PerchUtil::debug('Customer or Address or Shipping missing', 'error');
		}
	}

	public function get_customer_id()
	{
		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);
		return $Customer->id();
	}
	public function get_order_details($gateway, $order_id){
	 $Gateway = PerchShop_Gateways::get($gateway);
	$details= $Gateway->get_revolut_order_details($order_id);
	return $details;
	 }

	public function complete_payment($gateway="", $get=array(), $post=array(), $server="", $gateway_opts=array())
	{

		$this->init_cart();
		PerchUtil::debug('Runtime complete_payment for '.$gateway);

		$Orders = new PerchShop_Orders($this->api);
		$Order  = false;

		$Gateway = PerchShop_Gateways::get($gateway);

		//echo "callback_looks_valid"; print_r($Gateway->callback_looks_valid($get, $post));
		if ($Gateway->callback_looks_valid($get, $post)) {
			$Order = $Gateway->get_order_from_env($Orders, $get, $post);

			if ($Order) {
				$this->Order = $Order;
				$args   = $Gateway->get_callback_args($get, $post);

				$result = $Gateway->action_payment_callback($Order, $args, $gateway_opts);
	      //  echo "action_payment_callback"; print_r($result);

				if ($result) {
					PerchUtil::debug('Completing order');
					return $Order->complete_payment($args, $gateway_opts);

				}else{
					return $result;
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

	public function get_value_of_shipped_goods()
	{
		$this->init_cart();
		return $this->Cart->get_value_of_shipped_goods();
	}

	public function get_files($opts)
	{
		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);

		if (!$Customer) return '';

		$Files = new PerchShop_ProductFiles($this->api);
		return $Files->get_for_customer($Customer, $opts);
	}

	public function customer_has_purchased_file($fileID)
	{
		$memberID = perch_member_get('memberID');
		$Customer = $this->get_customer($memberID);

		if (!$Customer) return false;

		$Files = new PerchShop_ProductFiles($this->api);
		return $Files->customer_has_purchased_file($Customer, $fileID);
	}

	public function get_file_path_and_bucket($fileID)
	{
		$Files = new PerchShop_ProductFiles($this->api);
		return $Files->get_file_path_and_bucket($fileID);
	}

	public function get_active_order()
	{ echo "get_active_order";
		print_r(PerchShop_Session::get('shop_order_id'));
		if (PerchShop_Session::is_set('shop_order_id')) {
			$orderID = PerchShop_Session::get('shop_order_id');
			$Orders = new PerchShop_Orders($this->api);
			$Order = $Orders->find((int)$orderID);
			if ($Order) {
				return $Order;
			}
		}

		return false;
	}

	public function get_orders($opts)
	{

		if( isset($opts["api"])){
		$memberID =$opts["user_id"];
		}else{
		$this->init_cart();
		$memberID = perch_member_get('memberID');
		}

		$Customer = $this->get_customer($memberID);
		$db       = PerchDB::fetch();
		$Orders   = new PerchShop_Orders($this->api);


		// Get the listing
		$r = $Orders->get_filtered_listing($opts, function(PerchQuery $Query) use ($opts, $Customer, $db){

			$Statuses = new PerchShop_OrderStatuses($this->api);

			$Query->where[] = ' customerID='.$db->pdb($Customer->id()).' ';
			$Query->where[] = ' orderStatus IN ('.$db->implode_for_sql_in($Statuses->get_status_and_above('paid')).') ';

			// filter for a single
			if (isset($opts['orderID'])) {
				// We do this here because standard filter functions convert numbers to floats, which 
				// fails with overly large values. Sigh.
				$Query->where[] = ' orderID='.$db->pdb($opts['orderID']).' ';
			}

			return $Query;
		});

		return $r;
	}
	public function get_order($opts)
	{
		$this->init_cart();
    		$memberID   = perch_member_get('memberID');
    		$Customer   = $this->get_customer($memberID);
    		$db         = PerchDB::fetch();
			$Orders = new PerchShop_Orders($this->api);
			$Order = $Orders->find((int)$opts['orderID']);



		  $r = false;


		  $Template = $this->api->get("Template");
          $Template->set($opts["template"], 'shop');

        $r = $Template->render($Order);

		return $r;
	}
public function send_monthly_notification( $Customer,$message){
 $Orders = new PerchShop_Orders($this->api);

return $Orders->send_monthly_notification( $Customer,$message);
}
public function get_package_future_items($opts){

    $customerID = $this->get_customer_id();

 $r = false;
    $PackageItems = new PerchShop_PackageItems($this->api);
    $packages = $PackageItems->get_for_customer($customerID);
    $data  = [];
    $today = strtotime(date('Y-m-d'));
    $handled_prepaid = [];

    if (PerchUtil::count($packages)) {
        foreach ($packages as $Package) {
            $date   = $Package->billingDate();
            $status = strtolower((string)$Package->paymentStatus());
            $billing_type = strtolower((string)$Package->packageBillingType());
            $fields = PerchUtil::json_safe_decode($Package->productDynamicFields(), true);

            $price = null;
            if (isset($fields['price'])) {
                $price = $fields['price'];
                if (is_array($price) && isset($price['_default'])) {
                    $price = $price['_default'];
                }
            }

            if ($date !== null && $date !== '') {
                $ts = strtotime($date);

                if ($ts !== false && $ts >= $today) {
                    $data[] = [
                        'id'          => $Package->itemID(),
                        'price'       => $price,
                        'item'        => $Package->productVariantDesc(),
                        'packageDate' => $date,
                        'due'         => ($ts <= $today ? 1 : 0),
                    ];
                }

                continue;
            }

            if ($billing_type === 'prepaid' && $status === 'pending') {
                $package_uuid = $Package->packageID();
                if ($package_uuid && isset($handled_prepaid[$package_uuid])) {
                    continue;
                }

                if ($package_uuid) {
                    $handled_prepaid[$package_uuid] = true;
                }

                $display_date = $Package->nextBillingDate();
                if (!$display_date || strtotime($display_date) === false) {
                    $display_date = date('Y-m-d');
                }

                $data[] = [
                    'id'          => $Package->itemID(),
                    'price'       => $price,
                    'item'        => $Package->productVariantDesc(),
                    'packageDate' => $display_date,
                    'due'         => 1,
                ];
            }
        }
    }
 $Template = $this->api->get("Template");
          $Template->set($opts["template"], 'shop');


        if (PerchUtil::count($data)) {
                       $r = $Template->render_group($data, true);
         }




		return $r;
		}
	public function get_package_item($opts)
    	{	 $r =false;
    	$PerchShop_PackageItems = new PerchShop_PackageItems($this->api);
    	 $Item = $PerchShop_PackageItems->getItem((int)$opts["itemID"]);

                $fields = PerchUtil::json_safe_decode($Item->productDynamicFields(), true);
  $data = [
                        'id'       => $Item->itemID(),
                        'title'    => $Item->title(),
                         'productID'    => $Item->productID(),
                         'sku'    => $Item->sku(),
                        'price'=>$fields["price"]["_default"],
                          'month'    => $Item->month(),
                        'quantity' => $Item->qty(),
                    ];

                                if ($data) {

                                  if ($opts['skip-template']) {
                                       return $data;
                                    }else{
                                  $Template = $this->api->get("Template");
                                          $Template->set("shop/".$opts["template"], 'shop');


                                                       $r = $Template->render($data, true);
                                                       }

                                }
    return $r;
}
	public function get_package_items($opts)
    	{
    	 $Packages = new PerchShop_Packages($this->api);
    	    if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
                       $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
                       }
            $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);


            if (!$Package) {
                return false;
            }

            $Items    = $Package->get_unpaid_items();
            $Products = new PerchShop_Products($this->api);
            $data     = [];
                	  $r = false;
            if (is_array($Items)) {
                foreach ($Items as $Item) {
                    $title = '';

                    if ($Item->variantID()) {
                        $Product = $Products->find($Item->variantID());
                        if ($Product) {
                            $title = $Product->title();
                        }
                    } elseif ($Item->productID()) {
                        $Product = $Products->find($Item->productID());
                        if ($Product) {
                            $title = $Product->title();
                        }
                    }

                    $data[] = [
                        'id'       => $Item->itemID(),
                        'title'    => $title,
                          'sku'    => $Item->sku(),
                        'price'=> $Product->price(),
                          'month'    => $Item->month(),
                        'quantity' => $Item->qty(),
                    ];
                }
		  $Template = $this->api->get("Template");
          $Template->set("shop/".$opts["template"], 'shop');


        if (PerchUtil::count($data)) {
                       $r = $Template->render_group($data, true);
         }
            }



		return $r;
    	}
	public function get_order_items($opts)
	{
		$this->init_cart();
		$memberID   = perch_member_get('memberID');
		$Customer   = $this->get_customer($memberID);
		$db         = PerchDB::fetch();
		$OrdersItems     = new PerchShop_OrderItems($this->api);

		$OrdersItems = $OrdersItems->find_runtime_for_customer($opts['orderID'], $Customer);



		$r = false;


		  $Template = $this->api->get("Template");
          $Template->set($opts["template"], 'shop');


        if (PerchUtil::count($OrdersItems)) {
                       $r = $Template->render_group($OrdersItems, true);
         }
		return $r;
	}

	public function get_email_content($id, $secret)
	{
		$Emails = new PerchShop_Emails($this->api);
		$Email  = $Emails->find($id);

		if ($Email && $Email->emailSecret()==$secret) {
			$Template = $this->api->get('Template');
			$Template->set('shop/emails/'.$id.'.html', 'shop');
			return $Template->render($Email);
		}
	}

	public function register_member_login($Event)
	{
		$this->init_cart();
		$memberID   = perch_member_get('memberID');
		$this->Cart->set_member($memberID);

		$Customers = new PerchShop_Customers($this->api);
		$Customer = $Customers->find_by_memberID($memberID);
		if ($Customer) {
			$this->Cart->set_customer($Customer->id());	
			$this->set_location_from_address('default');
			 if (session_status() === PHP_SESSION_NONE) {
                                            session_start();
                                    }
   if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
              $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
              }
                                   if (isset($_SESSION['perch_shop_package_id']) ) {
                                              $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
                                            $Packages = new PerchShop_Packages($this->api);
                                            $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);

                                            if ($Package) {
                                             $Package->set_customer($Customer->id());
                                                  //  $Package->update(['customerID' => $Customer->id()]);
                                            }
                                    }
		}
	}

	public function addresses_are_set()
	{
		if ($this->billingAddress && $this->shippingAddress) return true;

		$addresses = $this->Cart->get_addresses();
		if ($addresses) {
			$this->billingAddress = $addresses['billingAddress'];
			$this->shippingAddress = $addresses['shippingAddress'];
			return true;
		}

		return false;
	}
        private function resolve_affiliate_referrer($value = '')
        {
                $referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $value);

                if ($referrer === '' && isset($_SESSION['affiliate_referrer'])) {
                        $referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_SESSION['affiliate_referrer']);
                }

                if ($referrer === '' && isset($_COOKIE['affiliate_referrer'])) {
                        $referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_COOKIE['affiliate_referrer']);
                }

                return $referrer;
        }

        public function register_customer_from_api( $memberID,$data)
        {
        $Customers = new PerchShop_Customers($this->api);
                        $Customer = $Customers->create_from_api( $memberID,$data);


                        $referrer = $this->resolve_affiliate_referrer($data['referrer'] ?? '');
                        $data = [];


                                                $data['email_address'] =  $Customer->email();
                                                $data['fields']=array();
                                                $data['fields']['FirstName']=$Customer->first_name();
                                                $data['fields']['LastName']=$Customer->last_name();
                                                $data['status']  = "subscribed";
                                                $data['tags']= ['registration-app'];

                         perch_emailoctopus_subscribe($data);
                          perch_member_add_tag('pending-docs');

                          if ($referrer !== '') {
                                perch_member_register_referral($Customer->memberID(), $referrer);
                          }

                          return true;
                          // perch_member_register_referral($Customer->memberID(), $SubmittedForm->data['referrer']);

        }
        public function register_customer_from_form($SubmittedForm)
        {
                $Session = PerchMembers_Session::fetch();

		$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'email', 'password','phone','dob','gender','affID','referrer'], ['password']);
		//,'postcode','city','country','shipping_address_1'], ['password']);

		$MembersForm->redispatched = true;
		$MembersForm->redispatch('perch_members');

		if ($Session->logged_in) {
			$Customers = new PerchShop_Customers($this->api);
			$Customer = $Customers->create_from_form($SubmittedForm);


			$data = [];


                        			$data['email_address'] =  $Customer->email();
                        			$data['fields']=array();
                        			$data['fields']['FirstName']=$Customer->first_name();
                        			$data['fields']['LastName']=$Customer->last_name();
                        			$data['status']  = "subscribed";
                        			$data['tags']= ['registration'];

                         perch_emailoctopus_subscribe($data);
                          perch_member_add_tag('pending-docs');
                          $referrer = $this->resolve_affiliate_referrer($SubmittedForm->data['referrer'] ?? '');

                          if ($referrer !== '') {
                                $SubmittedForm->data['referrer'] = $referrer;
                                perch_member_register_referral($Customer->memberID(), $referrer);
                          }

                          $this->sync_comms_member($Customer->memberID());

   if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
              $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
              }

              if(isset($_SESSION['perch_shop_package_id'])){

                                            $Packages = new PerchShop_Packages($this->api);
                                            $Package  = $Packages->find_by_uuid($_SESSION['perch_shop_package_id']);

                                            if ($Package) {
                                             $Package->set_customer($Customer->id());
                                                  //  $Package->update(['customerID' => $Customer->id()]);
                                            }

                                    }
                          //perch_member_add_tag($branch);
		}

	}

        public function update_customer_from_form($SubmittedForm)
        {
                $Session = PerchMembers_Session::fetch();

                if ($Session->logged_in) {

			$MembersForm = $SubmittedForm->duplicate(['first_name', 'last_name', 'email', 'token'], ['token']);
			$MembersForm->redispatch('perch_members');

			$Customers = new PerchShop_Customers($this->api);
			$Customer = $Customers->find_from_logged_in_member();
			$Customer->update_from_form($SubmittedForm);
			$this->sync_comms_member($Customer->memberID());

			$this->set_location_from_address($this->billingAddress);
		}

        }

        private function sync_comms_member($memberID)
        { echo "sync_comms_member1";
                if (!defined('PERCH_PATH')) {
                        return;
                }

                require_once PERCH_PATH . '/addons/apps/api/routes/lib/comms_sync.php';

                if (function_exists('comms_sync_member')) {
                        comms_sync_member((int)$memberID);
                }
        }

        private function get_shipping_field_names()
        {
                return ['first_name', 'last_name', 'company', 'address_1', 'address_2', 'postcode', 'city', 'county', 'country', 'phone', 'instructions'];
        }

        private function normalise_shipping_value($value)
        {
                if (is_string($value)) {
                        return trim($value);
                }

                if (is_null($value)) {
                        return '';
                }

                if (is_scalar($value)) {
                        return (string)$value;
                }

                return null;
        }

        private function normalise_shipping_data($data)
        {
                if (!is_array($data)) {
                        return [];
                }

                $fields = $this->get_shipping_field_names();
                $out    = [];

                foreach($fields as $field) {
                        if (!array_key_exists($field, $data)) {
                                continue;
                        }

                        if ($field === 'country') {
                                $value = $data[$field];

                                if (is_string($value)) {
                                        $value = trim($value);
                                } elseif (is_null($value)) {
                                        $value = '';
                                } elseif (is_scalar($value)) {
                                        $value = (string)$value;
                                } else {
                                        continue;
                                }

                                if ($value === '' || !is_numeric($value)) {
                                        continue;
                                }

                                $countryID = (int)$value;
                                if ($countryID <= 0) {
                                        continue;
                                }

                                $out[$field] = (string)$countryID;
                                continue;
                        }

                        $value = $this->normalise_shipping_value($data[$field]);
                        if ($value === null) {
                                continue;
                        }

                        $out[$field] = $value;
                }

                return $out;
        }

        private function extract_shipping_data_from_address(PerchShop_Address $Address)
        {
                $dynamic_fields = PerchUtil::json_safe_decode($Address->addressDynamicFields(), true);

                if (!is_array($dynamic_fields)) {
                        $dynamic_fields = [];
                }

                if (!isset($dynamic_fields['country']) || $dynamic_fields['country'] === '') {
                        $dynamic_fields['country'] = $Address->countryID();
                }

                return $this->normalise_shipping_data($dynamic_fields);
        }

        public function update_member_shipping_profile($memberID, array $shippingData)
        {
                $memberID = (int)$memberID;
                if ($memberID <= 0) {
                        return false;
                }

                $normalised = $this->normalise_shipping_data($shippingData);

                if (!PerchUtil::count($normalised)) {
                        return false;
                }

                $required = ['first_name', 'last_name', 'address_1', 'postcode', 'country'];

                foreach($required as $field) {
                        if (!isset($normalised[$field]) || $normalised[$field] === '') {
                                return false;
                        }
                }

                $profileData = [];

                foreach($normalised as $field=>$value) {
                        $profileData['shipping_'.$field] = $value;
                }

                if (!PerchUtil::count($profileData)) {
                        return false;
                }

                perch_member_api_update_profile($memberID, $profileData);

                return true;
        }

        public function update_member_shipping_profile_from_address(PerchShop_Address $Address, $memberID)
        {
                $shippingData = $this->extract_shipping_data_from_address($Address);

                return $this->update_member_shipping_profile($memberID, $shippingData);
        }

        public function update_shipping_address_for_api($Customer, array $shippingData, $countryID)
        {
                if (!$Customer instanceof PerchShop_Customer) {
                        return false;
                }

		$countryID = (int) $countryID;
		if ($countryID <= 0) {
			return false;
		}

		$Addresses = new PerchShop_Addresses($this->api);
		$ShippingAddress = $Addresses->find_for_customer((int) $Customer->id(), 'shipping');

		$storageData = $shippingData;
		$storageData['country'] = $countryID;
		$storageData['customer'] = (int) $Customer->id();

		$addressPayload = [
			'addressDynamicFields' => PerchUtil::json_safe_encode($storageData),
			'countryID' => $countryID,
			'customerID' => (int) $Customer->id(),
		];

		if ($ShippingAddress instanceof PerchShop_Address) {
			if (!$ShippingAddress->update($addressPayload)) {
				return false;
			}

			return $ShippingAddress;
		}

		$createPayload = $addressPayload;
		$createPayload['addressTitle'] = 'shipping';
		$createPayload['addressSlug'] = 'shipping';

		$ShippingAddress = $Addresses->create($createPayload);

		if (!$ShippingAddress instanceof PerchShop_Address) {
			return false;
		}

		return $ShippingAddress;
	}

	public function get_customer_details()
	{
		$Customer = $this->get_customer();
		$out = $Customer->to_array();

		$Billing  = $this->get_address($Customer, 'default');
		if ($Billing) {
			$out = array_merge($out, $Billing->to_array());	
		}

		$Shipping = $this->get_address($Customer, 'shipping');
		if ($Shipping) {
			$ship = $Shipping->to_array();
			if (PerchUtil::count($ship)) {
				foreach($ship as $key=>$val) {
					$out['shipping_'.$key] = $val;
				}
			}
		}

		return $out;
	}

	public function get_product_id($slug)
	{
		$Products = new PerchShop_Products($this->api);
		$Product = $Products->get_one_by('productSlug', $slug);

		if ($Product) return $Product->id();

		return false;
	}

	public function get_product_prices($productID)
	{
		$Products = new PerchShop_Products($this->api);
		$Product  = $Products->find((int) $productID);

		if (!$Product) {
			return false;
		}

		$price       = $this->resolve_price_value($Product->price());
		$sale_price  = $this->resolve_price_value($Product->sale_price());
		$trade_price = $this->resolve_price_value($Product->trade_price());

		$pricing_mode = 'standard';
		if ($this->trade_enabled()) {
			$pricing_mode = 'trade';
		} elseif ($this->sale_enabled()) {
			$pricing_mode = 'sale';
		}

		$current_price = $price;

		if ($pricing_mode === 'trade' && $trade_price !== null && $trade_price !== '') {
			$current_price = $trade_price;
		} elseif (($pricing_mode === 'sale' || $Product->on_sale()) && $sale_price !== null && $sale_price !== '') {
			$current_price = $sale_price;
		}

		$Currency = $this->get_currency();
		$format = function ($value) use ($Currency) {
			if ($value === null || $value === '') {
				return '';
			}

			if ($Currency) {
				return $Currency->get_formatted((float) $value);
			}

			return $value;
		};

		return [
			'price'          => $format($price),
			'sale_price'     => $format($sale_price),
			'trade_price'    => $format($trade_price),
			'current_price'  => $format($current_price),
		];
	}

	private function get_customer($memberID=false)
	{
		if (!$memberID) $memberID = perch_member_get('id');

		$Customers = new PerchShop_Customers($this->api);
		$Customer = $Customers->find_by_memberID($memberID);

		if (!$Customer) {

			// does customer exist against another Member? (e.g. for anon login)
			$Customer = $Customers->find_from_logged_in_member();

			if ($Customer) {
				$Customer->update_locally(['memberID'=>$memberID]);

				$Addresses = new PerchShop_Addresses($this->api);
				$Addresses->deprecate_default_address($Customer->id());
				$Addresses->create_from_logged_in_member($Customer->id());

				return $Customer;
			}

			$Customer = $Customers->create_from_logged_in_member();
		}

		return $Customer;
	}

	private function get_address($Customer, $address_type='default')
	{

		$Address   = null;
		$Addresses = new PerchShop_Addresses($this->api);

		if ($Customer) {
			$Address = $Addresses->find_for_customer($Customer->id(), $address_type);
		}

		if (!$Address) {
			PerchUtil::debug("no address");

			if (!$address_type != 'default') {
				$Address = $Addresses->create_from_default($Customer->id(), $address_type);				
			}

			if (!$Address) {
				$Address = $Addresses->create_from_logged_in_member($Customer->id(), $address_type);
			}
		}

		return $Address;
	}

	private function get_shipping($shipping_method)
	{
		$Shippings = new PerchShop_Shippings($this->api);
		return $Shippings->get_one_by('shippingSlug', $shipping_method);
	}
	public function track_order($opts)
    	{
    		$this->init_cart();
    		$memberID   = perch_member_get('memberID');
    		$Customer   = $this->get_customer($memberID);
    		$db         = PerchDB::fetch();
    		$Orders    = new PerchShop_Orders($this->api);
         	$Order = $Orders->find((int)$opts['orderID']);
              $r = false;
         	    $orders_pharmacy=$Order->getPharmacyOrderbyOrderid($Order->id());

                             if (PerchUtil::count($orders_pharmacy)) {
           $r=$Order->getOrderPharmacyDetails($orders_pharmacy[0]["pharmacy_orderID"]);

            }



    		/*$r = false;


    		  $Template = $this->api->get("Template");
              $Template->set($opts["template"], 'shop');


            if (PerchUtil::count($OrdersItems)) {
                           $r = $Template->render_group($OrdersItems, true);
             }*/
    		return $r;
    	}




	public function customer_has_paid_order($memberID=false)
 	{

 	if($memberID){
 	   $Customer = $this->get_customer($memberID);
 	}else{
 	     $Customer = $this->get_customer();
 	}


        		$Orders = new PerchShop_Orders($this->api);
        //	$Customer = $Customers->find_from_logged_in_member();
   $orders = $Orders->findAll_for_customer($Customer);

                if (!PerchUtil::count($orders)) {

                return false;
                }
                return true;
    //  return $Orders->customer_has_paid_order($Customer);



 	}

	private function resolve_price_value($price)
	{
		if (is_array($price)) {
			$currencyID = $this->get_currency_id();

			if (isset($price[$currencyID])) {
				return $price[$currencyID];
			}

			if (isset($price['_default'])) {
				return $price['_default'];
			}

			$first = reset($price);
			return $first;
		}

		return $price;
	}
}
