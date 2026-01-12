<?php
	$Products = new PerchShop_Products($API);
	
	$edit_mode  	= false;
	$Product    	= false;
	$shop_id  	= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_shop.products.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$shop_id           = PerchUtil::get('id');
		$Product           = $Products->find($shop_id);
		$edit_mode         = true;
	}

	if (!is_object($Product) || !$Product->is_variant()) {
		PerchUtil::redirect($API->app_path('perch_shop_products'));
	}

	// Template
	$Template   = $API->get('Template');
	$Template->set('shop/products/variant.html', 'shop');
	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);

	if ($Form->submitted()) {

		$data        = $Form->get_posted_content($Template, $Products, $Product);
		$search_text = $Form->get_search_text();

		if (is_object($Product)) {
			$Product->update($data);
			$Product->index($Template);	
			$Product->update_search_text($search_text);
		}

		if (is_object($Product)) {
		    $message = $HTML->success_message('Your product has been successfully edited. Return to %sproduct listing%s', '<a href="'.$API->app_path('perch_shop_products') .'">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}

	if (is_object($Product)) {
		$details = $Product->to_array();
	}
