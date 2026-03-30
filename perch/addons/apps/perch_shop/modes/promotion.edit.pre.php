<?php
	if (!function_exists('perch_shop_create_stripe_coupon_and_promo_code')) {
		function perch_shop_create_stripe_coupon_and_promo_code($secret_key, $coupon_fields, $promo_fields)
		{
			$result = [
				'ok' => false,
				'coupon' => null,
				'promotion_code' => null,
				'error' => '',
			];

			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => 'https://api.stripe.com/v1/coupons',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_USERPWD => $secret_key . ':',
				CURLOPT_POSTFIELDS => http_build_query($coupon_fields),
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/x-www-form-urlencoded',
				],
			]);

			$coupon_response = curl_exec($ch);
			if (curl_errno($ch)) {
				$result['error'] = 'Coupon cURL error: ' . curl_error($ch);
				curl_close($ch);
				return $result;
			}
			curl_close($ch);

			$coupon_data = json_decode($coupon_response, true);
			if (!is_array($coupon_data) || !isset($coupon_data['id'])) {
				$result['error'] = 'Coupon creation failed: ' . $coupon_response;
				return $result;
			}

			$promo_fields['promotion[type]'] = 'coupon';
			$promo_fields['promotion[coupon]'] = $coupon_data['id'];

			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => 'https://api.stripe.com/v1/promotion_codes',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_USERPWD => $secret_key . ':',
				CURLOPT_POSTFIELDS => http_build_query($promo_fields),
				CURLOPT_HTTPHEADER => [
					'Content-Type: application/x-www-form-urlencoded',
				],
			]);

			$promo_response = curl_exec($ch);
			if (curl_errno($ch)) {
				$result['error'] = 'Promotion code cURL error: ' . curl_error($ch);
				curl_close($ch);
				return $result;
			}
			curl_close($ch);

			$promo_data = json_decode($promo_response, true);
			if (!is_array($promo_data) || !isset($promo_data['id'])) {
				$result['error'] = 'Promotion code creation failed: ' . $promo_response;
				return $result;
			}

			$result['ok'] = true;
			$result['coupon'] = $coupon_data;
			$result['promotion_code'] = $promo_data;

			return $result;
		}
	}

	$Promotions = new PerchShop_Promotions($API);
	
	$edit_mode = false;
	$Promotion     = false;
	$shop_id = false;
	$message   = false;
	$details   = false;
	$stripe_create_coupon = '0';
	$stripe_coupon_percent_off = '20';
	$stripe_coupon_duration = 'once';
	$stripe_promotion_code = '';

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.promos.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id = PerchUtil::get('id');
		$Promotion     = $Promotions->find($shop_id);
		$edit_mode = true;

	}else{
		if (!$CurrentUser->has_priv('perch_shop.promos.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/promotions/promotion.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {
		$stripe_create_coupon = trim((string) PerchUtil::post('stripe_create_coupon'));
		if ($stripe_create_coupon !== '1') {
			$stripe_create_coupon = '0';
		}
		$stripe_coupon_percent_off = trim((string) PerchUtil::post('stripe_coupon_percent_off'));
		$stripe_coupon_duration = trim((string) PerchUtil::post('stripe_coupon_duration'));
		$stripe_promotion_code = trim((string) PerchUtil::post('stripe_promotion_code'));

		$data = $Form->get_posted_content($Template, $Promotions, $Promotion);

		if ($stripe_create_coupon === '1') {
			$Gateway = PerchShop_Gateways::get('stripe');
			$config = PerchShop_Config::get('gateways', 'stripe');
			$stripe_secret_key = '';

			if ($Gateway && is_array($config)) {
				$stripe_secret_key = trim((string) $Gateway->get_api_key($config));
			}

			if ($stripe_secret_key === '') {
				$message = $HTML->failure_message('Stripe secret key could not be found in Shop gateway settings.');
			} else {
				$percent_off = (float) $stripe_coupon_percent_off;
				if ($percent_off <= 0 || $percent_off > 100) {
					$message = $HTML->failure_message('Stripe percent off must be greater than 0 and less than or equal to 100.');
				}
			}

			$allowed_durations = ['once', 'forever', 'repeating'];
			if (!$message && !in_array($stripe_coupon_duration, $allowed_durations, true)) {
				$message = $HTML->failure_message('Invalid Stripe coupon duration selected.');
			}

			if (!$message && $stripe_promotion_code === '') {
				$stripe_promotion_code = trim((string) ($data['discount_code'] ?? ''));
			}

			if (!$message && $stripe_promotion_code === '') {
				$message = $HTML->failure_message('Please enter a promotion code to create in Stripe.');
			}

			if (!$message) {
				$coupon_fields = [
					'percent_off' => $percent_off,
					'duration' => $stripe_coupon_duration,
				];

				$promo_fields = [
					'code' => $stripe_promotion_code,
				];

				$stripe_result = perch_shop_create_stripe_coupon_and_promo_code($stripe_secret_key, $coupon_fields, $promo_fields);

				if (!$stripe_result['ok']) {
					$message = $HTML->failure_message('Stripe error: %s', PerchUtil::html($stripe_result['error']));
				} else {
					$data['discount_code'] = (string) ($stripe_result['promotion_code']['code'] ?? $stripe_promotion_code);
				}
			}
		}
		
		if (!$message) {
			if ($Promotion) {
				$Promotion->update($data);	
				$Promotion->index($Template);
			}else{
				$Promotion = $Promotions->create($data);
				

				if ($Promotion) {
					$Promotion->index($Template);
					PerchUtil::redirect($Perch->get_page().'?id='.$Promotion->id().'&created=1');	
				}
				
			}

			if (is_object($Promotion)) {
			    $message = $HTML->success_message('Your promotion has been successfully edited. Return to %slisting%s', '<a href="'.$API->app_path('perch_shop') .'/promos">', '</a>');
			}else{
			    $message = $HTML->failure_message('Sorry, that update was not successful.');
			}
		}

	}

	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your promotion has been successfully created. Return to %s listing%s', '<a href="'. $API->app_path('perch_shop') .'/promos">', '</a>');
	}


	if (is_object($Promotion)) {
		$details = $Promotion->to_array();
	}

	if ($stripe_promotion_code === '' && is_array($details) && isset($details['discount_code'])) {
		$stripe_promotion_code = (string) $details['discount_code'];
	}
