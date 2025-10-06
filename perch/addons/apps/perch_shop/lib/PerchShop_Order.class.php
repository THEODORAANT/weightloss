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
    private static $pharmacyTableMetadata = null;

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
        public function getOrderPharmacyDetails($orderNumber)
        {
                $orderNumber = trim((string)$orderNumber);

                if ($orderNumber === '') {
                        return [];
                }

                $history = $this->getPharmacyHistoryFromDatabase($orderNumber);

                if (PerchUtil::count($history)) {
                        $this->syncPharmacyHistorySummaries($history);

                        return $this->buildPharmacyHistoryResult($history);
                }

                $pharmacy_api = new PerchShop_PharmacyOrderApiClient('https://api.myprivatechemist.com/api', '4a1f7a59-9d24-4e38-a3ff-9f8be74c916b');
                $response = $pharmacy_api->getOrderDetails($orderNumber);

                if (!isset($response['success']) || !$response['success']) {
                        return [];
                }

                $data = $response['data'];

                if (!is_array($data)) {
                        return $data;
                }

                $history = [];

                if (isset($data['history']) && is_array($data['history'])) {
                        foreach ($data['history'] as $history_row) {
                                if (!is_array($history_row)) {
                                        continue;
                                }

                                $entry = $this->normalizePharmacyHistoryEntry($history_row);

                                if ($this->historyEntryHasData($entry)) {
                                        $history[] = $entry;
                                }
                        }
                } else {
                        $entry = $this->normalizePharmacyHistoryEntry($data);

                        if ($this->historyEntryHasData($entry)) {
                                $history[] = $entry;
                        }
                }

                if (PerchUtil::count($history)) {
                        return $this->buildPharmacyHistoryResult($history);
                }

                return $data;
        }

        private function getPharmacyHistoryFromDatabase($orderNumber)
        {
                $metadata = $this->getPharmacyTableMetadata();

                if (!$metadata) {
                        return [];
                }

                $db           = $this->db;
                $table        = $metadata['table'];
                $column_map   = $metadata['column_map'];
                $order_column = $metadata['order_column'];

                if (!isset($column_map['pharmacy_orderid'])) {
                        return [];
                }

                $order_sql = '';

                if (!empty($order_column)) {
                        $order_sql = ' ORDER BY `'.$order_column.'` DESC';
                }

                $sql = 'SELECT * FROM `'.$table.'`'
                        .' WHERE `'.$column_map['pharmacy_orderid'].'`='.$db->pdb($orderNumber)
                        .$order_sql;

                try {
                        $rows = $db->get_rows($sql);
                } catch (Exception $e) {
                        return [];
                }

                if (!PerchUtil::count($rows)) {
                        return [];
                }

                $history = [];

                foreach ($rows as $row) {
                        $entry = $this->normalizePharmacyHistoryEntry($row, $order_column);

                        if ($this->historyEntryHasData($entry)) {
                                $history[] = $entry;
                        }
                }

                return $history;
        }

        private function normalizePharmacyHistoryEntry(array $row, $order_column = null)
        {
                $lower_row = array_change_key_case($row, CASE_LOWER);

                $status = $this->findFirstValueForKeys($lower_row, $this->getPharmacyStatusKeys());
                $dispatch_date = $this->findFirstValueForKeys($lower_row, $this->getPharmacyDispatchKeys());
                $tracking_no = $this->findFirstValueForKeys($lower_row, $this->getPharmacyTrackingKeys());
                $message = $this->findFirstValueForKeys($lower_row, ['pharmacy_message', 'message', 'notes']);

                $payload = $this->extractPharmacyPayload($lower_row);

                if (is_array($payload)) {
                        $payload_lower = array_change_key_case($payload, CASE_LOWER);

                        if ($status === null) {
                                $status = $this->findFirstValueForKeys($payload_lower, $this->getPharmacyStatusPayloadKeys());
                        }

                        if ($dispatch_date === null) {
                                $dispatch_date = $this->findFirstValueForKeys($payload_lower, $this->getPharmacyDispatchPayloadKeys());
                        }

                        if ($tracking_no === null) {
                                $tracking_no = $this->findFirstValueForKeys($payload_lower, $this->getPharmacyTrackingPayloadKeys());
                        }

                        if ($message === null) {
                                $message = $this->findFirstValueForKeys($payload_lower, ['message', 'notes']);
                        }
                }

                if ($message !== null && is_string($message) && ($status === null || $dispatch_date === null || $tracking_no === null)) {
                        $parsed = $this->parsePharmacyMessageString($message);

                        if ($status === null && isset($parsed['status'])) {
                                $status = $parsed['status'];
                        }

                        if ($dispatch_date === null && isset($parsed['dispatchDate'])) {
                                $dispatch_date = $parsed['dispatchDate'];
                        }

                        if ($tracking_no === null && isset($parsed['trackingNo'])) {
                                $tracking_no = $parsed['trackingNo'];
                        }
                }

                $recorded_at = $this->findFirstValueForKeys($lower_row, ['updated_at', 'modified_at', 'created_at', 'created', 'timestamp', 'logged_at']);

                if ($recorded_at === null && $order_column) {
                        $order_key = strtolower($order_column);

                        if (isset($lower_row[$order_key]) && $lower_row[$order_key] !== '' && $lower_row[$order_key] !== null) {
                                $recorded_at = $lower_row[$order_key];
                        } elseif (isset($row[$order_column]) && $row[$order_column] !== '' && $row[$order_column] !== null) {
                                $recorded_at = $row[$order_column];
                        }
                }

                $entry = [
                        'status'       => $status,
                        'dispatchDate' => $dispatch_date,
                        'trackingNo'   => $tracking_no,
                        'message'      => $message,
                        'recordedAt'   => $recorded_at,
                        'raw'          => $row,
                ];

                if (is_array($payload) && PerchUtil::count($payload)) {
                        $entry['payload'] = $payload;
                }

                return $entry;
        }

        private function historyEntryHasData(array $entry)
        {
                foreach (['status', 'dispatchDate', 'trackingNo', 'message'] as $key) {
                        if (isset($entry[$key]) && $entry[$key] !== null && $entry[$key] !== '') {
                                return true;
                        }
                }

                if (isset($entry['payload']) && is_array($entry['payload']) && PerchUtil::count($entry['payload'])) {
                        return true;
                }

                if (isset($entry['raw']) && is_array($entry['raw']) && PerchUtil::count($entry['raw'])) {
                        return true;
                }

                return false;
        }

        private function buildPharmacyHistoryResult(array $history)
        {
                if (!PerchUtil::count($history)) {
                        return [];
                }

                $latest = $history[0];

                $result = [
                        'history'      => $history,
                        'status'       => isset($latest['status']) ? $latest['status'] : null,
                        'dispatchDate' => isset($latest['dispatchDate']) ? $latest['dispatchDate'] : null,
                        'trackingNo'   => isset($latest['trackingNo']) ? $latest['trackingNo'] : null,
                ];

                if (isset($latest['message'])) {
                        $result['message'] = $latest['message'];
                }

                if (isset($latest['recordedAt'])) {
                        $result['recordedAt'] = $latest['recordedAt'];
                }

                return $result;
        }

        private function syncPharmacyHistorySummaries(array $history)
        {
                if (!PerchUtil::count($history)) {
                        return;
                }

                $metadata = $this->getPharmacyTableMetadata();

                if (!$metadata) {
                        return;
                }

                $table           = $metadata['table'];
                $id_column       = $metadata['id_column'];
                $status_column   = $metadata['status_column'];
                $dispatch_column = $metadata['dispatch_column'];
                $tracking_column = $metadata['tracking_column'];

                if (!$id_column || (!($status_column || $dispatch_column || $tracking_column))) {
                        return;
                }

                foreach ($history as $entry) {
                        if (!isset($entry['raw']) || !is_array($entry['raw'])) {
                                continue;
                        }

                        $raw = $entry['raw'];

                        if (!array_key_exists($id_column, $raw)) {
                                continue;
                        }

                        $updates = [];

                        if ($status_column && isset($entry['status']) && $entry['status'] !== null && $entry['status'] !== '') {
                                $current = array_key_exists($status_column, $raw) ? $raw[$status_column] : null;

                                if ($this->pharmacyValuesDiffer($current, $entry['status'])) {
                                        $updates[$status_column] = $entry['status'];
                                }
                        }

                        if ($dispatch_column && isset($entry['dispatchDate']) && $entry['dispatchDate'] !== null && $entry['dispatchDate'] !== '') {
                                $current = array_key_exists($dispatch_column, $raw) ? $raw[$dispatch_column] : null;

                                if ($this->pharmacyValuesDiffer($current, $entry['dispatchDate'])) {
                                        $updates[$dispatch_column] = $entry['dispatchDate'];
                                }
                        }

                        if ($tracking_column && isset($entry['trackingNo']) && $entry['trackingNo'] !== null && $entry['trackingNo'] !== '') {
                                $current = array_key_exists($tracking_column, $raw) ? $raw[$tracking_column] : null;

                                if ($this->pharmacyValuesDiffer($current, $entry['trackingNo'])) {
                                        $updates[$tracking_column] = $entry['trackingNo'];
                                }
                        }

                        if (!PerchUtil::count($updates)) {
                                continue;
                        }

                        try {
                                $this->db->update($table, $updates, $id_column, $raw[$id_column]);
                        } catch (Exception $e) {
                                // Silently ignore update failures; history retrieval should continue.
                        }
                }
        }

        private function extractPharmacyPayload(array $row)
        {
                $candidates = ['payload', 'pharmacy_payload', 'response_payload', 'data', 'pharmacy_data', 'pharmacy_message', 'message'];

                foreach ($candidates as $candidate) {
                        if (!array_key_exists($candidate, $row)) {
                                continue;
                        }

                        $value = $row[$candidate];

                        if (is_array($value)) {
                                return $value;
                        }

                        if (!is_string($value)) {
                                continue;
                        }

                        $trimmed = trim($value);

                        if ($trimmed === '') {
                                continue;
                        }

                        $decoded = json_decode($trimmed, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                return $decoded;
                        }

                        $unserialized = @unserialize($trimmed);

                        if ($unserialized !== false && is_array($unserialized)) {
                                return $unserialized;
                        }
                }

                return null;
        }

        private function getPharmacyTableMetadata()
        {
                if (self::$pharmacyTableMetadata === false) {
                        return null;
                }

                if (self::$pharmacyTableMetadata !== null) {
                        return self::$pharmacyTableMetadata;
                }

                $table = 'p4_orders_match_pharmacy';

                try {
                        $columns = $this->db->get_rows('SHOW COLUMNS FROM `'.$table.'`');
                } catch (Exception $e) {
                        self::$pharmacyTableMetadata = false;
                        return null;
                }

                if (!PerchUtil::count($columns)) {
                        self::$pharmacyTableMetadata = false;
                        return null;
                }

                $column_map = [];

                foreach ($columns as $column) {
                        if (!isset($column['Field'])) {
                                continue;
                        }

                        $field = $column['Field'];
                        $column_map[strtolower($field)] = $field;
                }

                if (!isset($column_map['pharmacy_orderid'])) {
                        self::$pharmacyTableMetadata = false;
                        return null;
                }

                $metadata = [
                        'table'           => $table,
                        'columns'         => $columns,
                        'column_map'      => $column_map,
                        'order_column'    => $this->findFirstColumnInMap($column_map, ['updated_at', 'modified_at', 'created_at', 'created', 'id']),
                        'id_column'       => $this->findFirstColumnInMap($column_map, ['id']),
                        'status_column'   => $this->findFirstColumnInMap($column_map, $this->getPharmacyStatusKeys()),
                        'dispatch_column' => $this->findFirstColumnInMap($column_map, $this->getPharmacyDispatchKeys()),
                        'tracking_column' => $this->findFirstColumnInMap($column_map, $this->getPharmacyTrackingKeys()),
                ];

                self::$pharmacyTableMetadata = $metadata;

                return self::$pharmacyTableMetadata;
        }

        private function findFirstColumnInMap(array $column_map, array $candidates)
        {
                foreach ($candidates as $candidate) {
                        $key = strtolower($candidate);

                        if (isset($column_map[$key])) {
                                return $column_map[$key];
                        }
                }

                return null;
        }

        private function getPharmacyStatusKeys()
        {
                return ['status', 'pharmacy_status', 'order_status', 'status_text'];
        }

        private function getPharmacyDispatchKeys()
        {
                return ['dispatchdate', 'dispatch_date', 'dispatched_at', 'dispatcheddate'];
        }

        private function getPharmacyTrackingKeys()
        {
                return ['trackingno', 'tracking_no', 'trackingnumber', 'tracking_number', 'trackingref', 'tracking_reference'];
        }

        private function getPharmacyStatusPayloadKeys()
        {
                return ['status', 'orderstatus', 'status_text'];
        }

        private function getPharmacyDispatchPayloadKeys()
        {
                return ['dispatchdate', 'dispatch_date', 'dispatcheddate'];
        }

        private function getPharmacyTrackingPayloadKeys()
        {
                return ['trackingno', 'tracking_no', 'trackingnumber', 'tracking_number', 'trackingref', 'tracking_reference'];
        }

        private function pharmacyValuesDiffer($current, $new)
        {
                $current_normalised = $this->normalisePharmacyComparisonValue($current);
                $new_normalised     = $this->normalisePharmacyComparisonValue($new);

                if ($new_normalised === null || $new_normalised === '') {
                        return false;
                }

                if ($current_normalised === null || $current_normalised === '') {
                        return true;
                }

                return $current_normalised !== $new_normalised;
        }

        private function normalisePharmacyComparisonValue($value)
        {
                if ($value === null) {
                        return null;
                }

                if (is_string($value)) {
                        $value = trim($value);

                        return $value;
                }

                if (is_scalar($value)) {
                        return trim((string)$value);
                }

                return null;
        }

        private function parsePharmacyMessageString($message)
        {
                if (!is_string($message)) {
                        return [];
                }

                $text = trim($message);

                if ($text === '') {
                        return [];
                }

                $details = [];

                $patterns = [
                        'status'       => '/status\s*[:=-]\s*([^;\r\n]+)/i',
                        'dispatchDate' => '/dispatch(?:ed)?\s*date\s*[:=-]\s*([^;\r\n]+)/i',
                        'trackingNo'   => '/tracking(?:\s*(?:no|number|#))?\s*[:=-]\s*([^;\r\n]+)/i',
                ];

                foreach ($patterns as $key => $pattern) {
                        if (preg_match($pattern, $text, $match)) {
                                $details[$key] = trim($match[1]);
                        }
                }

                if (isset($details['status']) && isset($details['dispatchDate']) && isset($details['trackingNo'])) {
                        return $details;
                }

                $segments = preg_split('/[;\r\n]+/', $text);

                foreach ($segments as $segment) {
                        if (strpos($segment, ':') !== false) {
                                list($key, $value) = array_map('trim', explode(':', $segment, 2));
                        } elseif (strpos($segment, '=') !== false) {
                                list($key, $value) = array_map('trim', explode('=', $segment, 2));
                        } else {
                                continue;
                        }

                        $normalized = strtolower(str_replace([' ', '-', '_'], '', $key));

                        switch ($normalized) {
                                case 'status':
                                case 'orderstatus':
                                        if (!isset($details['status'])) {
                                                $details['status'] = $value;
                                        }
                                        break;
                                case 'dispatchdate':
                                case 'dispatcheddate':
                                case 'dispatch':
                                        if (!isset($details['dispatchDate'])) {
                                                $details['dispatchDate'] = $value;
                                        }
                                        break;
                                case 'trackingno':
                                case 'trackingnumber':
                                case 'tracking':
                                case 'trackingref':
                                        if (!isset($details['trackingNo'])) {
                                                $details['trackingNo'] = $value;
                                        }
                                        break;
                        }
                }

                return $details;
        }

        private function findFirstValueForKeys(array $data, array $keys)
        {
                foreach ($keys as $key) {
                        if (!array_key_exists($key, $data)) {
                                continue;
                        }

                        $value = $data[$key];

                        if ($value === null || $value === '') {
                                continue;
                        }

                        return is_string($value) ? trim($value) : $value;
                }

                return null;
        }

        public function isReorder($Customer){
		$Orders = new PerchShop_Orders($this->api);
        //	$Customer = $Customers->find_from_logged_in_member();
            $orders = $Orders->findAll_for_customer($Customer);

                if (PerchUtil::count($orders)) {

                return false;
                }
                return true;
	}
	public function sendOrdertoPharmacy( $Customer){
	   $pharmacy_api = new PerchShop_PharmacyOrderApiClient('https://api.myprivatechemist.com/api', '4a1f7a59-9d24-4e38-a3ff-9f8be74c916b');
       	$Countries  = new PerchShop_Countries($this->api);
       	$Addresses  = new PerchShop_Addresses($this->api);
       $ShippingAddr = $Addresses->find((int)$this->orderShippingAddress());
           	$Members = new PerchMembers_Members($this->api);
           	$Member = $Members->find($Customer->memberID());
      $Products = new PerchShop_Products($this->api);

        $OrderItems = new PerchShop_OrderItems($this->api);
         $Orders = new PerchShop_Orders($this->api);
        $items = $OrderItems->get_by('orderID', $this->id());
            $reorder= $Orders->customer_has_paid_order( $Customer);
                $order_items = [];
                    $questions_items=[];
               $questionnaire_type="first-order";
                 $orders = $Orders->findAll_for_customer($Customer);

                                              if (PerchUtil::count($orders) && PerchUtil::count($orders)>=2) {

                                  					      $questionnaire_type="re-order";
                                  					 }

             // echo "reorder **";print_r($reorder);

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


                $questionnaireID = null;
                $dynamicFields  = PerchUtil::json_safe_decode($this->orderDynamicFields(), true);

                if (is_array($dynamicFields)) {
                    if (isset($dynamicFields['questionnaires']) && is_array($dynamicFields['questionnaires'])) {
                        if (!empty($dynamicFields['questionnaires'][$questionnaire_type])) {
                            $questionnaireID = (int)$dynamicFields['questionnaires'][$questionnaire_type];
                        }
                    } elseif (!empty($dynamicFields['questionnaire_qid'])) {
                        $questionnaireID = (int)$dynamicFields['questionnaire_qid'];
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

                $questionnaire = $this->db->get_rows($sql_questionnaire);

                if (PerchUtil::count($questionnaire)) {
                    foreach ($questionnaire as $questiondet) {
                        if (isset($questiondet["question_text"]) && isset($questiondet["answer_text"])) {
                            if ($questiondet["question_text"] != "" && $questiondet["answer_text"] != "") {
                                $questions_items[] = [
                                    "question" => $questiondet["question_text"],
                                    "answer" => $questiondet["answer_text"],
                                ];
                            }
                        }
                    }
                }


        /*echo "order_items";
	print_r($order_items);
                      echo "ShippingAdr";
	print_r($ShippingAddr);*/
         $orderData = [
             "customer" => [
                 "name" => $Customer->customerFirstName()." ".$Customer->customerLastName(),
                 "email" => $Customer->customerEmail(),
                 "phone" =>$Member->phone(),
                  "gender" =>$Member->gender(),
                 "dob" => $Member->dob(),
                 "addressLine1" => $ShippingAddr->get('address_1')!=null ? $ShippingAddr->get('address_1') : '',
                 "addressLine2" => $ShippingAddr->get('address_2')!=null ? $ShippingAddr->get('address_2') : '',
                 "city" =>  $ShippingAddr->get('city'),
                 "county" => $ShippingAddr->get('county')!=null ? $ShippingAddr->get('county') : '',
                 "postCode" =>$ShippingAddr->get('postcode'),
                 "country" => $ShippingAddr->get_country_name()
             ],
             "items" => $order_items
             ,
             "shipping" => [
                 "addressLine1" => $ShippingAddr->get('address_1'),
                 "addressLine2" => $ShippingAddr->get('address_2')!=null ? $ShippingAddr->get('address_2') : '',
                 "city" => $ShippingAddr->get('city'),
                 "county" => $ShippingAddr->get('county')!=null ? $ShippingAddr->get('county') : '',
                 "postCode" => $ShippingAddr->get('postcode'),
                 "country" => $ShippingAddr->get_country_name()
             ],
             "assessment"=>$questions_items,
             "notes" => ""
         ];
           //print_r($orderData);
           $response =[];
         $response = $pharmacy_api->createOrder($orderData);
                             //  echo "response";
         	//print_r($response);
         if($response["success"]){
    $pharmacy_data = [
               'orderID'    => $this->id(),
               'pharmacy_orderID'    => $response["data"]["orderNumber"],
               'pharmacy_message' =>$response["data"]["message"],
           ];
         	}else{
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
    }

    public function send_order_email(PerchShop_Email $ShopEmail)
    {
        PerchUtil::debug('Sending customer email');

    	$Customers = new PerchShop_Customers($this->api);
    	$Customer = $Customers->find($this->customerID());

    	$Members = new PerchMembers_Members($this->api);
    	$Member = $Members->find($Customer->memberID());



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
