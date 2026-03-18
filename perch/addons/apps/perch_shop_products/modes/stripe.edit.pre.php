<?php
    if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
        PerchUtil::redirect($API->app_path());
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
                'label' => $ListProduct->productTitle(),
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
    $stripe_product_id = '';
    $stripe_price_id = '';

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

        $stripe_product_id = trim((string) PerchUtil::post('stripe_product_id'));
        $stripe_price_id = trim((string) PerchUtil::post('stripe_price_id'));

        $dynamic_fields['stripe_product_id'] = $stripe_product_id;
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
        $product_details = $selected_product->to_array();
        $stripe_product_id = (string) ($product_details['stripe_product_id'] ?? $stripe_product_id);
        $stripe_price_id = (string) ($product_details['stripe_price_id'] ?? $stripe_price_id);
    }
