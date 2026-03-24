<?php
    if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
        PerchUtil::redirect($API->app_path());
    }

    /**
     * Fetch a Stripe API resource by path.
     *
     * @param string $resource_path Example: products/prod_xxx
     * @param string $secret_key    Stripe secret key.
     *
     * @return array{ok:bool,data:array,error:string}
     */
    if (!function_exists('perch_shop_products_fetch_stripe_resource')) {
        function perch_shop_products_fetch_stripe_resource($resource_path, $secret_key)
        {
            $resource_path = ltrim((string) $resource_path, '/');
            $secret_key = trim((string) $secret_key);

            if ($resource_path === '' || $secret_key === '') {
                return ['ok' => false, 'data' => [], 'error' => 'Missing Stripe API resource path or key.'];
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/' . $resource_path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ':');

            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);

                return ['ok' => false, 'data' => [], 'error' => 'Stripe request failed: ' . $error];
            }

            $status_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = PerchUtil::json_safe_decode((string) $response, true);

            if (!is_array($decoded)) {
                return ['ok' => false, 'data' => [], 'error' => 'Stripe returned an invalid response.'];
            }

            if ($status_code >= 400 || !empty($decoded['error'])) {
                $message = 'Stripe request failed.';

                if (isset($decoded['error']['message']) && trim((string) $decoded['error']['message']) !== '') {
                    $message = (string) $decoded['error']['message'];
                }

                return ['ok' => false, 'data' => $decoded, 'error' => $message];
            }

            return ['ok' => true, 'data' => $decoded, 'error' => ''];
        }
    }

    $Products = new PerchShop_Products($API);
    $Form = $API->get('Form');

    $products = $Products->all();
    $product_options = [
        ['value' => '', 'label' => $Lang->get('Select a product')],
    ];

    if (PerchUtil::count($products)) {
        foreach ($products as $ListProduct) {
            $product_options[] = [
                'value' => (string) $ListProduct->id(),
                'label' => $ListProduct->sku(),
            ];
        }
    }

    $selected_product_id = '';
    if (PerchUtil::get('id')) {
        $selected_product_id = trim((string) PerchUtil::get('id'));
    } elseif ($Form->submitted()) {
        $selected_product_id = trim((string) PerchUtil::post('productID'));
    }

    $message = false;
    $selected_product = false;
    $stripe_product_id_test = '';
    $stripe_product_id = '';
    $stripe_price_id_test = '';
    $stripe_price_id = '';
    $stripe_remote_details = [];
    $stripe_remote_errors = [];

    if ($selected_product_id !== '') {
        $selected_product = $Products->find($selected_product_id);

        if (!$selected_product) {
            $selected_product_id = '';
            $message = $HTML->failure_message('The selected product could not be found.');
        }
    }

    if ($Form->submitted() && PerchUtil::post('btnSubmit') && $selected_product) {
        $dynamic_fields = PerchUtil::json_safe_decode($selected_product->productDynamicFields(), true);
        if (!is_array($dynamic_fields)) {
            $dynamic_fields = [];
        }

        $stripe_product_id_test = trim((string) PerchUtil::post('stripe_product_id_test'));
        $stripe_product_id = trim((string) PerchUtil::post('stripe_product_id'));
        $stripe_price_id_test = trim((string) PerchUtil::post('stripe_price_id_test'));
        $stripe_price_id = trim((string) PerchUtil::post('stripe_price_id'));

        $dynamic_fields['stripe_product_id_test'] = $stripe_product_id_test;
        $dynamic_fields['stripe_product_id'] = $stripe_product_id;
        $dynamic_fields['stripe_price_id_test'] = $stripe_price_id_test;
        $dynamic_fields['stripe_price_id'] = $stripe_price_id;

        $updated = $selected_product->update([
            'productDynamicFields' => PerchUtil::json_safe_encode($dynamic_fields),
        ]);

        if ($updated) {
            $message = $HTML->success_message('Stripe IDs were saved for %s.', $selected_product->productTitle());
        } else {
            $message = $HTML->failure_message('Sorry, this product could not be updated.');
        }
    }

    if ($selected_product) {
        $product_dynamic_fields = PerchUtil::json_safe_decode($selected_product->productDynamicFields(), true);
        if (!is_array($product_dynamic_fields)) {
            $product_dynamic_fields = [];
        }

        $stripe_product_id_test = (string) ($product_dynamic_fields['stripe_product_id_test'] ?? '');
        $stripe_product_id = (string) ($product_dynamic_fields['stripe_product_id'] ?? '');
        $stripe_price_id_test = (string) ($product_dynamic_fields['stripe_price_id_test'] ?? '');
        $stripe_price_id = (string) ($product_dynamic_fields['stripe_price_id'] ?? '');

        $Gateway = PerchShop_Gateways::get('stripe');
        $config = PerchShop_Config::get('gateways', 'stripe');
        $stripe_secret_key = '';

        if ($Gateway && $config) {
            $stripe_secret_key = trim((string) $Gateway->get_api_key($config));
        }

        $stripe_ids_map = [
            'stripe_product_id_test' => $stripe_product_id_test,
            'stripe_product_id' => $stripe_product_id,
            'stripe_price_id_test' => $stripe_price_id_test,
            'stripe_price_id' => $stripe_price_id,
        ];

        foreach ($stripe_ids_map as $field_name => $stripe_id) {
            if ($stripe_id === '') {
                continue;
            }

            $resource_type = (strpos($field_name, 'price') !== false) ? 'prices' : 'products';
            $result = perch_shop_products_fetch_stripe_resource($resource_type . '/' . urlencode($stripe_id), $stripe_secret_key);

            if ($result['ok']) {
                $stripe_remote_details[$field_name] = $result['data'];
            } else {
                $stripe_remote_errors[$field_name] = $result['error'];
            }
        }
    }
