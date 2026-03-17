<?php

class PerchShop_PharmacyOrderApiClient extends PerchAPI_Base {
    private string $apiUrl;
    private string $apiKey;

    public function __construct(string $apiUrl, string $apiKey) {
        $this->apiUrl = rtrim($apiUrl, '/');
        //https://api.myprivatechemist.com/api/orders
        $this->apiKey = $apiKey;

    }

        /**
         * Get  order.
         *
         * @param array $orderData Structured order data per API spec
         * @return array ['success' => bool, 'data' => mixed]
         */
        public function getOrderDetails( $orderNumber): array {
            $url = "{$this->apiUrl}/orders/" . urlencode($orderNumber);

            $headers = [
                "Content-Type: application/json",
                "x-api-key: {$this->apiKey}"
            ];
         $ch = curl_init();

           curl_setopt($ch, CURLOPT_URL, $url);
             curl_setopt($ch, CURLOPT_HTTPHEADER , $headers);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             $response = curl_exec($ch);

               $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                    if ($error) {
                        return ['success' => false, 'data' => "cURL error: $error"];
                    }

                    $decoded = json_decode($response, true);

                    if ($httpCode === 201) {
                        return ['success' => true, 'data' => $decoded];
                    }

                    return ['success' => true, 'data' => $decoded ?? "HTTP error code: $httpCode"];

}
    /**
     * Create a new order.
     *
     * @param array $orderData Structured order data per API spec
     * @return array ['success' => bool, 'data' => mixed]
     */
    public function createOrder(array $orderData): array {
        $url = "{$this->apiUrl}/orders";

        $headers = [
            "Content-Type: application/json",
            "x-api-key: {$this->apiKey}"
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($orderData)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'data' => "cURL error: $error"];
        }

        $decoded = json_decode($response, true);

        if ($httpCode === 201) {
            return ['success' => true, 'data' => $decoded];
        }

        return ['success' => false, 'data' => $decoded ?? "HTTP error code: $httpCode"];
    }

    function addOrderPharmacytodb( $data){
		$db         = PerchDB::fetch();


		try{
			$pharmacy_order_id = isset($data['pharmacy_orderID']) ? trim((string) $data['pharmacy_orderID']) : '';

			if ($pharmacy_order_id !== '') {
				$existing_by_pharmacy_id = $db->get_value('SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'orders_match_pharmacy WHERE pharmacy_orderID='.$db->pdb($pharmacy_order_id));
				if ((int)$existing_by_pharmacy_id > 0) {
					return;
				}
			}

			if (isset($data['orderID'])) {
				$existing_for_order = $db->get_value('SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'orders_match_pharmacy WHERE orderID='.$db->pdb((int)$data['orderID']).' AND pharmacy_orderID!=""');
				if ((int)$existing_for_order > 0 && $pharmacy_order_id !== '') {
					return;
				}
			}

			$columns = implode(", ", array_keys($data)); // Columns as a string
			$values = "'" . implode("', '", array_map('addslashes', array_values($data))) . "'";
			$insert_query ="INSERT INTO ".PERCH_DB_PREFIX."orders_match_pharmacy ($columns) VALUES ($values); ";

			$db->execute($insert_query);
		}catch (Exception $e) {
			echo"getMessage";
			echo $e->getMessage();
			exit;
		}

    }
}
