<?php

class PerchShop_StockTrackerWebhook
{
    public static function notify_sold($Event)
    {
        $Order  = $Event->subject;
        $status = $Event->args[0];

        if ($status !== 'paid') return;

        if (!defined('STOCK_TRACKER_API_URL')) return;

        // Build webhook payload
        $API = new PerchAPI(1.0, 'perch_shop');
        $Products = new PerchShop_Products($API);
        $products = $Products->get_for_order($Order->id());

        $items = [];
        if (PerchUtil::count($products)) {
            foreach ($products as $Product) {
                // Build full title including variant description
                $title = $Product->title();
                $variantDesc = $Product->productVariantDesc();
                if ($variantDesc) {
                    $title = $title . ' ' . $variantDesc;
                }

                $items[] = [
                    'sku'           => $Product->sku(),
                    'product_title' => $title,
                    'quantity'      => (int)$Product->itemQty(),
                    'product_id'    => (int)$Product->id()
                ];
            }
        }

        $payload = [
            'order_id' => (int)$Order->id(),
            'sold_at'  => date('c'),
            'items'    => $items
        ];

        $headers = ['Content-Type: application/json'];

        // Add auth header if secret is configured
        if (defined('PERCH_WEBHOOK_SECRET')) {
            $headers[] = 'x-stock-api-key: ' . PERCH_WEBHOOK_SECRET;
        }

        // POST to stock tracker
        $url = rtrim(STOCK_TRACKER_API_URL, '/') . '/sync/orders/sold';
        $jsonPayload = json_encode($payload);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Log to file
        $logFile = '/var/www/html/logs/stock_tracker.log';
        $logEntry = date('Y-m-d H:i:s') . "\n";
        $logEntry .= "URL: {$url}\n";
        $logEntry .= "Payload: {$jsonPayload}\n";
        $logEntry .= "HTTP Code: {$httpCode}\n";
        $logEntry .= "Response: {$response}\n";
        if ($curlError) {
            $logEntry .= "cURL Error: {$curlError}\n";
        }
        $logEntry .= "---\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}
