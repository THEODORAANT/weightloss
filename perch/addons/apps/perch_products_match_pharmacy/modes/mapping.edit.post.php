<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Edit Mapping')
    ], $CurrentUser);

    if ($message) echo $message;

    echo $Form->form_start();
    echo $Form->text_field('productID', 'Product ID', isset($details['productID']) ? $details['productID'] : '');
    echo $Form->text_field('pharmacy_productID', 'Pharmacy Product ID', isset($details['pharmacy_productID']) ? $details['pharmacy_productID'] : '');
    echo $Form->text_field('pharmacy_name', 'Pharmacy Name', isset($details['pharmacy_name']) ? $details['pharmacy_name'] : '');
    echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    echo $Form->form_end();
?>
