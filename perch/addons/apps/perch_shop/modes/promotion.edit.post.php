<?php	
    if (is_object($Promotion)) {
        $title = $Lang->get('Editing Promotion ‘%s’', $HTML->encode($Promotion->promoTitle()));
    }else{
        $title = $Lang->get('Creating a New Promotion');
    }

    echo $HTML->title_panel([
        'heading' => $title,
    ], $CurrentUser);

    /* ----------------------------------------- SMART BAR ----------------------------------------- */
       include('_promo_smartbar.php');
    /* ----------------------------------------- /SMART BAR ----------------------------------------- */

    $template_help_html = $Template->find_help();
    if ($template_help_html) {
        echo $HTML->heading2('Help');
        echo '<div class="template-help">' . $template_help_html . '</div>';
    }
    
    echo $HTML->heading2('Promotion');    
    
    /* ---- FORM ---- */
    echo $Form->form_start('edit');

        echo $Form->fields_from_template($Template, $details);
        echo $HTML->heading2('Stripe');
        echo $Form->select_field('stripe_create_coupon', $Lang->get('Create coupon and promotion code in Stripe'), [
            ['label' => 'No', 'value' => '0'],
            ['label' => 'Yes', 'value' => '1'],
        ], $stripe_create_coupon);
        echo $Form->text_field('stripe_coupon_percent_off', $Lang->get('Stripe coupon % off'), $stripe_coupon_percent_off, 's');
        echo $Form->select_field('stripe_coupon_duration', $Lang->get('Stripe coupon duration'), [
            ['label' => 'Once', 'value' => 'once'],
            ['label' => 'Forever', 'value' => 'forever'],
            ['label' => 'Repeating', 'value' => 'repeating'],
        ], $stripe_coupon_duration);
        echo $Form->text_field('stripe_promotion_code', $Lang->get('Stripe promotion code (optional)'), $stripe_promotion_code, 'm');
        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

    echo $Form->form_end();
    /* ---- /FORM ---- */
        
    echo $HTML->main_panel_end();
  
