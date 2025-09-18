<?php

class PerchShop_Orders extends PerchShop_Factory
{
	public $api_method             = 'orders';
	public $api_list_method        = 'orders';
	public $singular_classname     = 'PerchShop_Order';
	public $static_fields          = ['orderStatus', 'orderTotal', 'orderGateway', 'orderCurrency', 'orderExchangeRate', 'customerID', 'orderGatewayRef'];
	public $remote_fields          = ['customer', 'gateway', 'status', 'subtotal', 'shipping_price', 'total', 'currency', 'currency_code', 'exchange_rate', 'ship_to', 'bill_to', 'shipping'];

	protected $table               = 'shop_orders';
	protected $pk                  = 'orderID';
	#protected $index_table         = 'shop_admin_index';
	protected $master_template	   = 'shop/orders/order.html';

	protected $default_sort_column = 'orderCreated';
	protected $created_date_column = 'orderCreated';

	protected $event_prefix = 'shop.order';

	private $customerID = false;


	public function find_runtime_for_customer($orderID, $Customer)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT * FROM '.$this->table.' 
				WHERE customerID='.$this->db->pdb((int)$Customer->id()).' 
					AND orderID='.$this->db->pdb((int)$orderID).' 
					AND orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')';
		return $this->return_instance($this->db->get_row($sql));
	}

	public function findAll_for_customer( $Customer)
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE customerID='.$this->db->pdb((int)$Customer->id()).'
					AND orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')';

		return $this->return_instances($this->db->get_rows($sql));
	}
			public function is_order_send_to_pharmacy( $orderId)
        	{
        	 $sql = 'SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'orders_match_pharmacy WHERE orderID='.$this->db->pdb($orderId).' AND pharmacy_orderID !=""';

              $count = $this->db->get_count($sql);
               if($count){ return true;  }
               return false;

        	}

		public function customer_has_paid_order( $Customer)
    	{
    		$Statuses = new PerchShop_OrderStatuses($this->api);
    		$sql = 'SELECT * FROM '.$this->table.'	WHERE customerID='.$this->db->pdb((int)$Customer->id()).' AND orderStatus="paid")';

         	//$rows=$this->return_instance($this->db->get_rows($sql));
         	   $rows = $this->db->get_rows($sql);

              $count = $this->db->get_count($sql);

    					 if (!$count) {

    					 return false;
    					 }

    		return true;
    	}

	public function create_from_cart($Cart, $gateway, $Customer, $BillingAddress, $ShippingAddress,$api=false)
	{
		if($api){
		$cart_data = $Cart->calculate_cart_for_api($Cart->cartID);
		}else{
		$cart_data = $Cart->calculate_cart();
		}



		// check shipping
		if (!isset($cart_data['shipping_id'])) {
			$cart_data['shipping_id'] = null;
		} 
	if (!isset($cart_data['shipping_tax'])) {
			$cart_data['shipping_tax'] = null;
		}
	if (!isset($cart_data['shipping_tax_rate'])) {
       	$cart_data['shipping_tax_rate'] = null;
    	}
     if (!isset($cart_data['shipping_with_tax'])) {
         	$cart_data['shipping_with_tax'] = null;
      		}
     if (!isset($cart_data['shipping_without_tax'])) {
           		$cart_data['shipping_without_tax'] = null;
        		}
		$data = [
			'orderStatus'               => 'created',
			'orderGateway'              => $gateway,
			'orderTotal'                => $cart_data['grand_total'],
			'currencyID'                => $cart_data['currency_id'],
			'orderItemsSubtotal'        => $cart_data['total_items'],
			'orderItemsTax'             => $cart_data['total_items_tax'],
			'orderItemsTotal'           => $cart_data['total_items'] + $cart_data['total_items_tax'],
			'orderShippingSubtotal'     => $cart_data['shipping_without_tax'],
			'orderShippingDiscounts'    => $cart_data['total_shipping_discount'],
			'orderShippingTax'          => $cart_data['shipping_tax'],
			'orderShippingTaxDiscounts' => $cart_data['total_shipping_tax_discount'],
			'orderShippingTotal'        => $cart_data['shipping_with_tax'],
			'orderDiscountsTotal'       => $cart_data['total_discounts'],
			'orderTaxDiscountsTotal'    => $cart_data['total_tax_discount'],
			'orderSubtotal'             => $cart_data['total_items_with_shipping'] - $cart_data['total_discounts'],
			'orderTaxTotal'             => $cart_data['total_tax'],
			'orderItemsRefunded'        => 0,
			'orderTaxRefunded'          => 0,
			'orderShippingRefunded'     => 0,
			'orderTotalRefunded'        => 0,
			'orderTaxID'                => ($Customer->customerTaxIDStatus()=='valid' ? $Customer->customerTaxID() : null),
			'orderShippingWeight'       => $cart_data['shipping_weight'],
			'orderCreated'              => gmdate('Y-m-d H:i:s'),
			'orderPricing'              => $Cart->get_cart_field('cartPricing'),
			'orderDynamicFields'        => $Cart->get_cart_field('cartProperties'),
			'customerID'                => $Customer->id(),
			'shippingID'                => $cart_data['shipping_id'],
			'orderShippingTaxRate'      => $cart_data['shipping_tax_rate'],
			'orderBillingAddress'       => $BillingAddress->id(),
			'orderShippingAddress'      => $ShippingAddress->id(),
		];
		if($api){
		$data['orderPricing'] = $Cart->cartPricing;
        $data['orderDynamicFields']='';
		}
		//echo "C";print_r($data);

		$Order = $this->create($data);
//echo "D";print_r($Order);
		if (is_object($Order)) {
		//echo "E";
			$Order->freeze_addresses();
			$Order->copy_order_items_from_cart($Cart, $cart_data);
			$Cart->add_order_id_to_stashed_data($Order->id());


		}
//echo "G";
		$Perch = Perch::fetch();
		$Perch->event('shop.order_create', $Order);
		//echo "F";
        $Perch->event('shop.order_status_update', $Order, 'created');
//echo "W";
		return $Order;
		
	}

	public function insert_order_for_booking($orderid,$customerID)
	{

          //  $OrderItems = new PerchShop_OrderItems();
                //    $items = $OrderItems->get_by('orderID', $orderid);
	  $tmp2['orderid'] = $orderid;
			   $tmp2['customerid']=$customerID;
			    //$this->db->insert(PERCH_DB_PREFIX.'events_bookings_orders', $tmp2);
                /*    $order_items = [];

                    if (PerchUtil::count($items)) {
                    	foreach($items as $Item) {
                    		//$order_items[]  = $Item->to_array();
                    		  $tmp2['itemid'] = $Item->itemID();
                    		 $this->db->insert(PERCH_DB_PREFIX.'events_bookings_orders', $tmp2);
                    	}
                    }*/



	}

	public function find_with_gateway_ref($str)
	{
		$sql = 'SELECT * FROM '.$this->table.'
				WHERE orderGatewayRef LIKE '.$this->db->pdb('%'.$str.'%').'
				LIMIT 1';
		$row = $this->db->get_row($sql);
		return $this->return_instance($row);
	}

	public function get_with_products($product_ids)
	{
		$product_ids = array_map("intval", $product_ids);
		$sql = 'SELECT o.*, cd.cartData, c.*
				FROM '.$this->table.' o, '.PERCH_DB_PREFIX.'shop_cart_data cd, '.PERCH_DB_PREFIX.'shop_customers c
				WHERE o.orderID=cd.orderID AND o.customerID=c.customerID
					AND o.orderStatus='.$this->db->pdb('paid').'
					AND cd.productID IN ('.$this->db->implode_for_sql_in($product_ids).')';
		$rows = $this->db->get_rows($sql);

		return $this->return_instances($rows);
	}
public function get_by_properties($details,$Paging=false,$statuses=null){
//echo "get_by_properties";
//print_r($details);
$sort_val = null;
        $sort_dir = null;


        if ($statuses === null) {
            $statuses = ['paid'];
        }

        if (!is_array($statuses)) {
            $statuses = [$statuses];
        }

        $statuses = array_filter($statuses, function($status) {
            return $status !== null && $status !== '';
        });

        if (!count($statuses)) {
            $statuses = ['paid'];
        }


                if ($Paging && $Paging->enabled()) {
            $selectsql = $Paging->select_sql();
            list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
        }else{
            $selectsql = 'SELECT';
        }

        $selectsql .=  '  o.*, c.*, pkg.billing_type, CONCAT(customerFirstName, " ", customerLastName) AS customerName ';
         $fromsql =  '      FROM ' . $this->table .' o LEFT JOIN '.PERCH_DB_PREFIX.'shop_packages pkg ON pkg.orderID=o.orderID, '.PERCH_DB_PREFIX.'shop_customers c ';
                $wheresql = ' WHERE o.customerID=c.customerID
                                AND o.orderDeleted IS NULL
                                AND o.orderStatus IN ('.$this->db->implode_for_sql_in($statuses).')';
                	if($details["sendtopharmacy"]!=""){
                	if($details["sendtopharmacy"]=="yes"){
                	 // $selectsql .= ' ,p.* ';
                	    $fromsql .=  ','.PERCH_DB_PREFIX.'orders_match_pharmacy p ';
                	     $wheresql .= '	AND p.orderID =o.orderID AND p.pharmacy_orderID!=""';
                	}elseif($details["sendtopharmacy"]=="no"){
                                    	 // $selectsql .= ' ,p.* ';
                                    	    $fromsql .=  ','.PERCH_DB_PREFIX.'orders_match_pharmacy p ';
                                    	     $wheresql .= '	AND p.orderID =o.orderID AND p.pharmacy_orderID=""';
                                    	}
                	}
if($details["pendingdocs"]!=""){
                	if($details["pendingdocs"]=="yes"){
                	 // $selectsql .= ' ,p.* ';
                	    $fromsql .=  ','.PERCH_DB_PREFIX.'members_documents d ';
                	     $wheresql .= '	AND d.memberID =c.memberID AND d.documentStatus!="accepted"';
                	}elseif($details["pendingdocs"]=="no"){
                                    	   $fromsql .=  ','.PERCH_DB_PREFIX.'members_documents d ';
                                                        	     $wheresql .= '	AND d.memberID =c.memberID AND d.documentStatus="accepted"';
                                    	}
                	}
$sql= $selectsql. $fromsql.$wheresql;


	    if ($sort_val) {
                    $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
                } else {
        	        if (isset($this->default_sort_column)) {
        	            $sql .= ' ORDER BY o.orderCreated DESC ';
        	        }
        	    }

                if ($Paging && $Paging->enabled()) {
                    $sql .=  ' '.$Paging->limit_sql();
                }

                $results = $this->db->get_rows($sql);

                if ($Paging && $Paging->enabled()) {
                    $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
                }

                return $this->return_instances($results);
}
	public function get_admin_listing($status=array('paid'), $Paging=false)
	{
		$sort_val = null;
        $sort_dir = null;


		if ($Paging && $Paging->enabled()) {
            $sql = $Paging->select_sql();
            list($sort_val, $sort_dir) = $Paging->get_custom_sort_options();
        }else{
            $sql = 'SELECT';
        }

        $sql .= ' o.*, c.*, p.billing_type, CONCAT(customerFirstName, " ", customerLastName) AS customerName
                FROM ' . $this->table .' o
                JOIN '.PERCH_DB_PREFIX.'shop_customers c ON o.customerID=c.customerID
                LEFT JOIN '.PERCH_DB_PREFIX.'shop_package_items pkg ON pkg.orderID=o.orderID
                LEFT JOIN '.PERCH_DB_PREFIX.'shop_packages p  ON pkg.packageID = p.uuid OR (p.billing_type = "prepaid" AND p.orderID = o.orderID)
                WHERE o.orderDeleted IS NULL
                        AND o.orderStatus IN ('.$this->db->implode_for_sql_in($status).')';


		if ($sort_val) {
            $sql .= ' ORDER BY '.$sort_val.' '.$sort_dir;
        } else {
	        if (isset($this->default_sort_column)) {
	            $sql .= ' ORDER BY o.orderCreated DESC ';
	        }
	    }

        if ($Paging && $Paging->enabled()) {
            $sql .=  ' '.$Paging->limit_sql();
        }

        $results = $this->db->get_rows($sql);

        if ($Paging && $Paging->enabled()) {
            $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
        }

        return $this->return_instances($results);
	}
    public function send_monthly_notification( $Customer,$message)
    {

          $Email = $this->api->get('Email');
                  $Email->set_template('shop/emails/package_reminder.html', 'shop');
                $Email->set('first_name', $Customer->first_name());
                 $Email->set('message',$message);
               $Email->subject('Upcoming Payment Reminder');
              $Email->senderName(PERCH_EMAIL_FROM_NAME);
        	        $Email->senderEmail(PERCH_EMAIL_FROM);

               $Email->recipientEmail($Customer->customerEmail());
               $Email->body($message);

               return $Email->send();
    }
	public function get_dashboard_widget()
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT o.orderID, o.orderCreated, o.orderInvoiceNumber, o.orderTotal, o.currencyID, o.orderExchangeRate, c.customerID, c.customerFirstName, c.customerLastName, cur.*
				FROM '.PERCH_DB_PREFIX.'shop_orders o, '.PERCH_DB_PREFIX.'shop_customers c, '.PERCH_DB_PREFIX.'shop_currencies cur
				WHERE o.customerID=c.customerID AND o.currencyID=cur.currencyID
					AND o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
					AND o.orderDeleted IS NULL 
				ORDER BY o.orderCreated DESC
				LIMIT 10';
		$rows = $this->db->get_rows($sql);

		$out = [];
		$out['items'] = $rows;


		return $out;
	}

	public function get_revenue_dashboard_widget()
	{
		$Statuses = new PerchShop_OrderStatuses($this->api);
		$sql = 'SELECT DATE_FORMAT(o.orderCreated, "%Y-%m-01") AS orderMonth, SUM(o.orderTotal / o.orderExchangeRate) AS revenue
				FROM '.PERCH_DB_PREFIX.'shop_orders o
				WHERE o.orderStatus IN ('.$this->db->implode_for_sql_in($Statuses->get_status_and_above('paid')).')
					AND o.orderDeleted IS NULL 
				GROUP BY orderMonth
				ORDER BY o.orderCreated DESC
				LIMIT 10';
		$rows = $this->db->get_rows($sql);

		$out = [];
		$out['items'] = $rows;


		return $out;

	}
}
